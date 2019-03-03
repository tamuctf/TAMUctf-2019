# coding: utf-8
"""Grader server that recieves submissions on a message queue"""

from .api import GradeRequest, ProcessUpdate
from select import EPOLLIN, EPOLLERR, EPOLLHUP
from signal import SIGTERM, SIGINT
from subprocess import PIPE
import base64
import grp
import logging
import os
import pika
import pwd
import select
import shutil
import signal
import subprocess
import sys
import threading
import time

MQ_SERVER = os.environ.get("MQ_SERVER", "localhost")
MQ_PORT = int(os.environ.get("MQ_PORT", 5671))
MQ_SSL = os.environ.get("MQ_SSL", "true").lower() == "true"
CA_CERT = os.environ.get("CA_CERT", None)
LOG_LEVEL = os.environ.get('LOG_LEVEL', 'INFO').upper()
TASK_GROUP = os.environ.get('TASK_GROUP', 'student')

logging.basicConfig(format="[%(levelname)7s %(asctime)s %(name)s] %(message)s", datefmt="%m-%d %H:%M:%S")
logger = logging.getLogger('grader')
logger.setLevel(LOG_LEVEL)

STDOUT = "stdout"
STDERR = "stderr" 

def setup_user(user):
    try:
        pwentry = pwd.getpwnam(user)
    except KeyError:
        subprocess.run(("adduser", "-D", "-G", TASK_GROUP, "-s", "/bin/nologin", user), check=True)
        pwentry = pwd.getpwnam(user)
    return pwentry.pw_uid, pwentry.pw_gid

def demote(uid, gid):
    def preexec():
        os.setgid(gid)
        os.setuid(uid)
    return preexec

testdir = os.path.join(os.path.dirname(__file__), "tests")

class Task:
    """Task represents a grading task inclusing the process and callback info"""

    def __init__(self, req, queue, id):
        self.req = req
        self.queue = queue
        self.id = id
        self.proc = None

    @property
    def stdout(self):
        return self.proc and self.proc.stdout

    @property
    def stderr(self):
        return self.proc and self.proc.stderr

    def start(self):
        uid, gid = setup_user(self.req.user)
        homedir = os.path.join("/home/", self.req.user)
        test = f"test_{self.req.assignment}.py"
        script = f"{self.req.assignment}.py"
        os.stat(os.path.join(testdir, test))

        if not os.path.isfile(os.path.join(homedir, test)):
            logger.info("copying test_%s.py as user %s", self.req.assignment, self.req.user)
            subprocess.run(('/bin/cp', os.path.join(testdir, test), os.path.join(homedir, test)), preexec_fn=demote(uid, gid), check=True)

        # Ensure the user can modify this file.
        logger.info("writing %s.py as user %s", self.req.assignment, self.req.user)
        subprocess.run(('/bin/touch', os.path.join(homedir, script)), preexec_fn=demote(uid, gid), check=True)
        with open(os.path.join(homedir, script), 'w') as file:
            file.write(self.req.code)
            shutil.chown(os.path.join(homedir, script), user=self.req.user, group=TASK_GROUP)

        self.proc = subprocess.Popen(('/usr/local/bin/python', '-m', 'pytest', '-q', test), preexec_fn=demote(uid, gid), stdout=PIPE, stderr=PIPE, cwd=homedir)
        return self

    def poll(self):
        return self.proc and self.proc.poll()

    def kill(self):
        if self.proc:
            self.proc.kill()
    
    def wait(self):
        if self.proc:
            self.proc.wait()

class Grader:
    """Grader consumes requests from the given queue, kicks off tasks, and publishes their output"""
    epoll_timeout = 0.5
    task_limit = 500
    prefetch_count = 5

    def __init__(self, conn, queue="submissions"):
        self.conn = conn
        self.queue = queue
        self.tasks = {}
        self.filenums = {}
        self.epoll = select.epoll()
        self._closing = threading.Event()
        self._closed = threading.Event()

    def setup(self, chan):
        chan.queue_declare(self.queue)
        chan.basic_qos(prefetch_count=5)

    def run(self):
        with self.conn.channel() as chan:
            self.setup(chan)
            while not self._closing.is_set():
                if len(self.tasks) < self.task_limit:
                    # Comsume immediate requests only if tasks are running, otherwise block indefinitly.
                    wait = sys.float_info.epsilon if self.tasks else None
                    logger.debug("consuming requests from queue with timeout %s", wait)
                    for method, props, body in chan.consume(queue=self.queue, inactivity_timeout=wait):
                        if any(val is None for val in (method, props, body)):
                            break

                        if self.process(method, props, body):
                            chan.basic_ack(delivery_tag=method.delivery_tag)
                        else:
                            chan.basic_reject(delivery_tag=method.delivery_tag, requeue=False)

                        # Resuming consumption would dealock, so go start that task!
                        if len(self.tasks) > self.task_limit or wait is None:
                            break

                # Poll all tasks and their output streams for updates.
                if self.tasks:
                    logger.debug("polling tasks for updates")
                    for task, update in self.poll_tasks(timeout=self.epoll_timeout):
                        body = update.marshal()
                        logger.debug("publishing task %s update to %s: %r", task.id, task.queue, body)
                        chan.publish('', task.queue, body, properties=pika.BasicProperties(
                            correlation_id = task.id
                        ))

            # Send the kill signal and wait for any tasks to finish.
            for task, update in self.poll_tasks(kill=True):
                body = update.marshal()
                logger.debug("publishing final task %s update to %s: %r", task.id, task.queue, body)
                chan.publish('', task.queue, body, properties=pika.BasicProperties(
                    correlation_id = task.id
                ))
        self._closed.set()

    def poll_tasks(self, timeout=0, kill=False):
        if kill:
            for task in self.tasks.values():
                task.kill()

        for id, task in tuple(self.tasks.items()):
            if kill:
                task.wait()
            ret = task.poll()
            if ret is not None:
                logger.info("task %s completed with code %d", id, ret)
                yield task, ProcessUpdate(
                    stdout = task.stdout.read().decode() or None,
                    stderr = task.stderr.read().decode() or None,
                    ret = ret
                )
                self.tasks.pop(id)
        
        updates = {}
        for fd, event in self.epoll.poll(timeout):
            task, stream = self.filenums[fd]
            logger.debug("recieved event %d from fd %d (task %s %s)", event, fd, task.id, stream)

            # Read from fds with availible data.
            if event & select.EPOLLIN:
                update = updates.get(task, None)
                if update is None:
                    update = ProcessUpdate()
                    updates[task] = update
                if stream == STDOUT:
                    update.stdout = task.stdout.read().decode()
                if stream == STDERR:
                    update.stderr = task.stderr.read().decode()

            # Stop watching any fds that are in closed or error state.
            if event & (EPOLLHUP | EPOLLERR):
                self.epoll.unregister(fd)
                self.filenums.pop(fd)

        yield from updates.items()

    def process(self, method, props, body):
        if logger.isEnabledFor(logging.DEBUG):
            logger.debug(
                "Processing message: {\n  method: %s\n  props: %s\n  body: %s\n}",
                method, props, base64.b64encode(body)
            )

        # Validate that we have information to return this request.
        reqid, resp_queue = props.correlation_id, props.reply_to
        if not reqid  or not resp_queue:
            logger.warning("discarding message with invalid metadata: %s, %s", method, props)
            return

        # Validate that the correlation id is unique.
        if reqid in self.tasks:
            logger.warning("discarding message with duplicate request id: %s", id)
            return

        # Unmarhsal the request.
        try:
            req = GradeRequest.unmarshal(body)
        except ValueError as err:
            logger.warning("discarding invalid request %r", err)
            return

        logger.info('kicking off task %s for %s', reqid, req.user)
        try:
            self.register(Task(req, resp_queue, reqid).start())
            return True
        except OSError as err:
            logger.error('task %s failed to start: %r', reqid, err)
            return False

    def register(self, task):
        try:
            os.set_blocking(task.stdout.fileno(), False)
            self.epoll.register(task.stdout, EPOLLIN)
        except OSError as err:
            logger.error("failed to register %s: %r", task.id, err)
            return
        
        try:
            os.set_blocking(task.stderr.fileno(), False)
            self.epoll.register(task.stderr, EPOLLIN)
        except OSError as err:
            self.epoll.unregister(p.stdout)
            logger.error("failed to register %s: %r", task.id, err)
            return

        self.filenums[task.stdout.fileno()] = (task, STDOUT)
        self.filenums[task.stderr.fileno()] = (task, STDERR)
        self.tasks[task.id] = task
        logger.info("registered task %s", task.id)

    def stop(self):
        self._closing.set()
        
def main():
    # Ensure the group for new users exists.
    try:
        grp.getgrnam(TASK_GROUP)
    except KeyError:
        subprocess.run(('addgroup', TASK_GROUP), check=True)
        logger.info("created user group %s", TASK_GROUP)

    if MQ_SSL:
        ssl_opts = {'ca_certs': CA_CERT}
    else:
        ssl_opts = None

    conn = pika.BlockingConnection(pika.ConnectionParameters(
        host=MQ_SERVER,
        port=MQ_PORT,
        ssl=MQ_SSL,
        ssl_options=ssl_opts,
        heartbeat=0,
        blocked_connection_timeout=60,
        connection_attempts=10,
        retry_delay=3
    ))
    logger.info("connected to message queue at %s:%d", MQ_SERVER, MQ_PORT)

    grader = Grader(conn)

    def stop_handler(signum, frame):
        if not grader._closing.is_set():
            logger.info("shutting down: recieved signal %d", signum)
            grader.stop()
        else:
            sys.exit(1)

    # Set up signal handlers to gracefully stop the server.
    signal.signal(SIGTERM, stop_handler)
    signal.signal(SIGINT, stop_handler)

    with conn:
        grader.run()
