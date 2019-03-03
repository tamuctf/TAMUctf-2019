from .. import ServiceFrame
import docker
import requests
import structlog

logger = structlog.get_logger()


class ServiceCheck(ServiceFrame.ServiceFrame):
    def __init__(self, serviceInfo):
        ServiceFrame.ServiceFrame.__init__(self, serviceInfo)
        self.flag = "gigem{the_best_damn_sql_anywhere}"

    def checkService(self):
        url = "http://{}/login.php".format(self.serviceInfo.serviceHost)
        msg = "Attempting to open {}".format(url)
        logger.info("SQLiSimple", msg=msg, serviceInfo=self.serviceInfo.__dict__)
        try:
            data = {'username': 'admin', 'password': 'tS&LjHue6Z&m*&JeTU#U%btyA8gmJXh'}
            r = requests.post(url, data=data)
            if 'admin' not in r.text:
                msg = "Incorrect Response {}".format(r.text)
                logger.info("SQLiSimple", msg=msg, serviceInfo=self.serviceInfo.__dict__)
                return False
            msg = "Succesfully open {}: {}".format(url, r.text)
            logger.info("SQLiSimple", msg=msg, serviceInfo=self.serviceInfo.__dict__)
            return True

        except: # NOQA
            msg = "Failed to open {}: {}".format(url, data)
            logger.info("SQLiSimple", msg=msg, serviceInfo=self.serviceInfo.__dict__)
            return False
        return False

    def getLogs(self):
        client = docker.from_env(version="auto")
        container = client.containers.get(self.serviceInfo.serviceName)
        tarstream, stat = container.get_archive('/var/log/apache2/error.log')
        return str(tarstream.read())
