# coding: utf-8
try:
    from .api import ProcessUpdate, GradeRequest
except ImportError:
    from api import ProcessUpdate, GradeRequest

from flask import Flask, render_template, request, g
from flask_login import LoginManager, UserMixin, login_user, logout_user, login_required, current_user
from flask_socketio import SocketIO, join_room
from werkzeug.local import LocalProxy
import base64
import logging
import os
import pika
import sys
import threading
import uuid

MQ_SERVER = os.environ.get("MQ_SERVER", "localhost")
MQ_PORT = int(os.environ.get("MQ_PORT", 5671))
MQ_SSL = os.environ.get("MQ_SSL", "true").lower() == "true"
CA_CERT = os.environ.get("CA_CERT", None)
LOG_LEVEL = os.environ.get('LOG_LEVEL', 'INFO').upper()
HOMEWORK_USER = os.environ.get('HOMEWORK_USER', "alice")
SECRET_KEY = os.environ.get('SECRET_KEY', os.urandom(32))

logging.basicConfig(format="[%(levelname)7s %(asctime)s %(name)s] %(message)s", datefmt="%m-%d %H:%M:%S")
logger = logging.getLogger('web')
logger.setLevel(LOG_LEVEL)

app = Flask(__name__)
app.secret_key = SECRET_KEY
login_manager = LoginManager()
login_manager.init_app(app)
socketio = SocketIO(app)

class LonelyUser(UserMixin):
    name = HOMEWORK_USER
    id = HOMEWORK_USER

lonely_user = LonelyUser()

@login_manager.user_loader
def load_user(user_id):
    if user_id == lonely_user.get_id():
        return lonely_user
    else:
        return None

class GraderClient:
    prefetch_count = 1
    consume_interval = 0.1

    def __init__(self, conn_params, queue='submissions'):
        # Set up call list.
        self.callbacks = {}

        # Set up the publishing channel.
        self.conn_params = conn_params
        self.queue = queue
        self.conn = pika.BlockingConnection(self.conn_params)
        self.chan = self.conn.channel()
        self.chan.queue_declare(self.queue)
        self._send_lock = threading.Lock()

        # Set up and start the consumer thread.
        self._closing = threading.Event()
        self._ready = threading.Event()
        self.resp_queue = None
        self.consumer = socketio.start_background_task(target=self.consume_updates)
        self._ready.wait()

    def consume_updates(self):
        # Consume updates uses it's own connection as they cannot be shared.
        with pika.BlockingConnection(self.conn_params) as conn, conn.channel() as chan:
            self.resp_queue = chan.queue_declare(exclusive=True).method.queue
            chan.basic_qos(prefetch_count=self.prefetch_count)
            self._ready.set()
            for method, props, body in chan.consume(self.resp_queue, no_ack=True, inactivity_timeout=sys.float_info.epsilon):
                if self._closing.is_set():
                    logger.info("closing consumption loop")
                    chan.cancel()
                    break

                if any(val is None for val in (method, props, body)):
                    socketio.sleep(self.consume_interval)
                    continue
                
                logger.debug("consumed message from from response queue: {\n  method: %s,\n  props: %s,\n  body: %s", method, props, body)
                reqid = props.correlation_id
                callback = self.callbacks.get(reqid, None)
                if callback is None:
                    logger.warning('discarding message with no registered callback %s', reqid)
                    continue

                try:
                    update = ProcessUpdate.unmarshal(body)
                except ValueError as err:
                    logger.error("discarding invalid message body in response queue: %r", err)
                    continue

                try:
                    callback(update)
                except ConnectionError as err:
                    logger.warning("connection lost to requestor of %s: %r", reqid, err)
                    self.callbacks.pop(reqid)
                    continue

                if update.ret is not None:
                    logger.info("request %s completed with code %s", reqid, update.ret)

    def submit(self, req, callback):
        reqid, marshalled = str(uuid.uuid4()), req.marshal()
        logger.info("publishing code submission %s: %s", reqid, marshalled)
        with self._send_lock:
            self.callbacks[reqid] = callback
            self.chan.publish('', self.queue, marshalled, properties=pika.BasicProperties(
                reply_to = self.resp_queue,
                correlation_id = reqid,
            ))

    def is_alive(self):
        return self.conn.is_open

    def close(self):
        self._closing.set()
        if self.chan.is_open:
            self.chan.close()
        if self.conn.is_open:
            self.conn.close()

def get_grader():
    if 'pika_params' not in g:
        if MQ_SSL:
            ssl_opts = {'ca_certs': CA_CERT}
        else:
            ssl_opts = None

        g.pika_params = pika.ConnectionParameters(
            host=MQ_SERVER,
            port=MQ_PORT,
            ssl=MQ_SSL,
            ssl_options=ssl_opts,
            heartbeat=0,
            blocked_connection_timeout=60,
            connection_attempts=10,
            retry_delay=3
        )

    grader = g.get('grader', None)
    if grader is not None and not grader.is_alive():
        g.grader.close()
        g.pop('grader')
        grader = None

    if grader is None:
        logger.info("new grader connected to message queue as %s:%d", MQ_SERVER, MQ_PORT)
        g.grader = GraderClient(g.pika_params)

    return g.grader

grader = LocalProxy(get_grader)

@app.route('/', methods=("GET",))
def index():
    if not current_user.is_authenticated:
        login_user(lonely_user)
    return render_template('index.html')

@socketio.on('connect')
def connect():
    join_room("alice")
    logger.info("client session %s connected", request.sid)

@socketio.on('disconnect')
def connect():
    logger.info("client session %s disconnected", request.sid)

@socketio.on('submit')
def message(data):
    logger.info("recieved code submission from %s", request.sid)
    if not 'code' in data:
        logger.error("invalid submission does not include code", sid)
        return False

    req = GradeRequest(user="alice", assignment="assignment_one", code=data['code'])
    def callback(update):
        logger.info("sending update to %s", "alice")
        socketio.emit('update', update.asdict(), room="alice")

    grader.submit(req, callback)

if __name__ == "__main__":
    socketio.run(app, debug=True, host='127.0.0.1', port=8080)
