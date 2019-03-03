## Challenge
Recently, the office put up a private webserver to store important information about the newest research project for the company. This information was to be kept confidential, as it's release could mean a large loss for everyone in the office.

Just as the research was about to be published, a competing firm published information eerily similar. Too similar...

Time to take a look through the office network logs to figure out what happened.

1. Network Enumeration:
   1. What is the IP address of the private webserver?
   2. How many hosts made contact with the private webserver that day?

2. Discovering an Event:
   1. What is the IP address of the host exfiltrating data?
   2. For how long did the exfiltration happen? (Round to the nearest second. Format: MM:SS) 
   3. What protocol/s was used to exfiltrate data? (Alphabetical order, all caps, comma separated)

3. Assessing of File:
   1. What is the name of the stolen file?
   2. What is the md5sum of the stolen file?

4. Retrieving the Stolen Data:
   1. What compression encoding was used for the data?
   2. What is the name and type of the decompressed file? (Format: NAME.TYPE e.g. tamuctf.txt)

## CTFd
Each logical grouping for the questions should be revealed after the previous logical grouping is completely answered.

## Solution
* Use the filter ```ip.src == 192.168.11.4 && ip.dst == 192.168.11.7 && (dns or http or icmp)``` in wireshark or using ```tshark -r capture.pcap -Y "<FILTER HERE>" -w newfile.pcap``` and save the file as newfile.pcap
* In the very first packet of newfile.pcap, hex decode ICMP data. This will show ```SEx4IRV.746f74616c6c795f6e6f7468696e672e706466.REGISTER.6156eab6691f32b8350c45b3fc4aadc1```. Hex decode the hex data between the first and second dots for the filename. The Hex data at the end after the 3rd dot is the md5sum.
* Run ```python3 solver.py```
* Decompress using ```tar -xzvf transferredData.tar.gz``` 
* Use ```file``` or ```binwalk``` to determine compression and filetype

## Answers
1.
   1. 192.168.11.4
   2. 13
2.
   1. 192.168.11.7
   2. 11:09    (23:35:49.285991 - 23:24:40.525678)
   3. DNS, HTTP, ICMP
3.
   1. totally_nothing.pdf
   2. 6156eab6691f32b8350c45b3fc4aadc1
4.
   1. gzip
   2. stuff.elf
