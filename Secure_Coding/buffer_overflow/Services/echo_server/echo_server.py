from .. import ServiceFrame
import docker
import requests
import structlog
import subprocess
from pwn import *

logger = structlog.get_logger()

class ServiceCheck(ServiceFrame.ServiceFrame):
    def __init__(self, serviceInfo):
        ServiceFrame.ServiceFrame.__init__(self, serviceInfo)
        self.flag = "gigem{check_that_buffer_size_baby}"

    def checkService(self):
        msg = "ServiceCheck Started"
        logger.info("echo_server", msg=msg, serviceInfo=self.serviceInfo.__dict__)
        p = remote(self.serviceInfo.serviceHost,self.serviceInfo.servicePort)
        p.send('APPLES' + '\n')
        try:
            value = p.recv()
            if 'APPLES' in value:
                msg = 'Passed ServiceCheck'
                logger.info("echo_server", msg=msg, serviceInfo=self.serviceInfo.__dict__)
                return True
            else:
                msg = 'Failed ServiceCheck'
                logger.info("echo_server", msg=msg, serviceInfo=self.serviceInfo.__dict__)
                return False
        except: # NOQA
            msg = "SOMETHING BROKE!!! EVERYBODY STAY CALM!! STAY! CALM!!"
            logger.info("echo_server", msg=msg, serviceInfo=self.serviceInfo.__dict__)
            return False
        return False

    def getLogs(self):
        client = docker.from_env(version="auto")
        container = client.containers.get(self.serviceInfo.serviceName)
        return container.logs()
