from .. import ServiceFrame
import requests
import structlog
import docker

logger = structlog.get_logger()

class ServiceCheck(ServiceFrame.ServiceFrame):
    def __init__(self, serviceInfo):
        ServiceFrame.ServiceFrame.__init__(self, serviceInfo)
        self.flag = "gigem{3y3_SQL_n0w_6b95d3035a3755a}"

    def checkService(self):
        msg = "ServiceCheck Started"
        logger.info("nosql_server", msg=msg, serviceInfo=self.serviceInfo.__dict__)
        try:
            url = 'http://{}:{}/'.format(self.serviceInfo.serviceHost,self.serviceInfo.servicePort)
            index = requests.get(url)
            if '<title>TAMUctf</title>' not in index.text:
                msg = 'Failed ServiceCheck: {}'.format(index.text)
                logger.info("flask_server", msg=msg, serviceInfo=self.serviceInfo.__dict__)
                return False

            data = {"username": "admin", "password": "945IYMib!u@u"}
            login = requests.post(url + "login", json=data) 

            if 'Login Failed' in login.text:
                msg = 'Failed ServiceCheck'
                logger.info("nosql_server", msg=msg, serviceInfo=self.serviceInfo.__dict__)
                return False

            msg = 'Passed ServiceCheck'
            logger.info("nosql_server", msg=msg, serviceInfo=self.serviceInfo.__dict__)

            return True

        except Exception as e:
            msg = 'Failed ServiceCheck: {}'.format(e)
            logger.info("nosql_server", msg=msg, serviceInfo=self.serviceInfo.__dict__)
            return False

    def getLogs(self):
        client = docker.from_env(version="auto")
        container = client.containers.get(self.serviceInfo.serviceName)
        return container.logs()
