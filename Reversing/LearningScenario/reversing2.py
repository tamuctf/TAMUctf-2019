from datetime import datetime

# ASCII values: so potential solution would be to notice and convert Fqaa (which is the flag)
Fqaa = [103, 105, 103, 101, 109, 123, 100, 101, 99, 111, 109, 112, 105, 108, 101, 125] 
XidT = [83, 117, 112, 101, 114, 83, 101, 99, 114, 101, 116, 75, 101, 121]


def main():
	print "Clock.exe"
	input = raw_input(">: ").strip()
	kUIl = ""
	for i in XidT:
		kUIl += chr(i)
	
	if input == kUIl:
		alYe = ""
		for i in Fqaa:
			alYe += chr(i)
		print alYe
		
	else:
		print datetime.now()



if __name__ == '__main__':
	main()