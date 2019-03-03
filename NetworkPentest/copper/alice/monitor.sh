ls -la
date > monitor.txt
echo "=========================================" >> monitor.txt
echo "ps -aux" >> monitor.txt
ps -aux >> monitor.txt
echo "=========================================" >> monitor.txt
echo "df -h" >> monitor.txt
df -h >> monitor.txt
cp ./monitor.txt /logs
exit
