import pika
import json
import sys
import os
import socket
import binascii

def callback(ch, method, properties, body):
    print json.loads(body)['msg']
    ch.basic_ack(delivery_tag = method.delivery_tag)
    sys.exit()
    return


credentials = pika.PlainCredentials('guest', 'guest')
parameters = pika.ConnectionParameters('172.17.0.2')

connection = pika.BlockingConnection(parameters)
channel = connection.channel()
channel.queue_declare(queue='attackQueue', durable=True)


userinfo = binascii.hexlify(os.urandom(32)).decode('ascii')

#Stupid hack I found to get host ip
s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
s.connect(("8.8.8.8", 80))
hostname = s.getsockname()[0]
print(hostname)
s.close()

# A lot of this I won't need anymore
service = {
    'chal': 'SQL',
    'userInfo': userinfo,
    'serviceHost': hostname,
}

print "Pushing: {}".format(service)
channel.basic_publish(exchange='',
                      routing_key='attackQueue',
                      body=json.dumps(service))


connection = pika.BlockingConnection(pika.ConnectionParameters('172.17.0.2'))
userChannel = connection.channel()
userChannel.exchange_declare(exchange='resultX', exchange_type='direct')
userChannel.queue_declare(queue='resultQueue', durable=True)


userChannel.queue_bind(exchange='resultX',
                       queue='resultQueue',
                       routing_key=userinfo)

userChannel.basic_consume(callback, queue='resultQueue' )
userChannel.start_consuming()

userChannel.close()

