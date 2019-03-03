from .. import ServiceFrame
import requests
import structlog
import docker

logger = structlog.get_logger()

class ServiceCheck(ServiceFrame.ServiceFrame):
    def __init__(self, serviceInfo):
        ServiceFrame.ServiceFrame.__init__(self, serviceInfo)
        self.flag = "gigem{br0k3n_fl4sk_2d88bb862569}"

    def checkService(self):
        msg = "ServiceCheck Started"
        logger.info("flask_server", msg=msg, serviceInfo=self.serviceInfo.__dict__)
        try:
            url = 'http://{}:{}/'.format(self.serviceInfo.serviceHost,self.serviceInfo.servicePort)
            index = requests.get(url)
            if 'Welcome to my new FaaS! (Flask as a Service)' not in index.text:
                msg = 'Failed ServiceCheck: {}'.format(index.text)
                logger.info("flask_server", msg=msg, serviceInfo=self.serviceInfo.__dict__)
                return False

            data = {'chem1': 'asdf', 'chem2': 'fdsa'}
            chem = requests.post(url + 'science', data=data)
            
            if 'The result of combining asdf and fdsa is:' not in chem.text:
                msg = 'Failed ServiceCheck'
                logger.info("flask_server", msg=msg, serviceInfo=self.serviceInfo.__dict__)
                return False

            msg = 'Passed ServiceCheck'
            logger.info("flask_server", msg=msg, serviceInfo=self.serviceInfo.__dict__)

            return True

        except Exception as e:
            msg = 'Failed ServiceCheck: {}'.format(e)
            logger.info("flask_server", msg=msg, serviceInfo=self.serviceInfo.__dict__)
            return False

    def getLogs(self):
        client = docker.from_env(version="auto")
        container = client.containers.get(self.serviceInfo.serviceName)
        return container.logs()
