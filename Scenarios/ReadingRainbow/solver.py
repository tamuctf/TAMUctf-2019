from scapy.all import *

packets = rdpcap('newfile.pcap')
theData = []
buf = []

def getHTTPData(packet):
    allData = packet.load[packet.load.find('data=')+5:].decode('hex')
    theData.append(allData)

def getICMPData(packet):
    allData = packet[ICMP].load.decode('hex')
    theData.append(allData)

def getDNSData(packet):
    global buf
    dnsqr = packet[DNSQR].qname
    data = dnsqr.split(".")[1]
    jobid = dnsqr.split(".")[0]
    if jobid in data.decode('hex'):
        buf = []
    if data not in buf:
        buf.append(data)
    if(len(dnsqr) < 68):
        theData.append(''.join(buf).decode('hex'))

for packet in packets:
    if 'http' in packet.summary():
        retriever = getHTTPData
    if packet.haslayer(ICMP):
        retriever = getICMPData
    if packet.haslayer(DNSQR):
        retriever = getDNSData
    retriever(packet)

fileData = ""
for data in theData:
    splitData = data.split('.')
    if splitData[2] != 'REGISTER' and splitData[2] != 'DONE':
        fileData += data.split('.')[2]

f = open('transferredData.tar.gz','w')
f.write(fileData.decode('hex'))
f.close()
