# Polytar
This problem is meant to teach the player about polyglots by giving them a polyglot and allowing them to find the hidden flag.

## Intro
Bender B. Rodriguez was caught with a flashdrive with only a single file on it. We think it may contain valuable information. His area of research is PDF files, so it's strange that this file is a PNG.

## File hierarchy:
```
art.png
base64 message (not a file, contains music video link)
polyglot.pdf
confused.docx (zip file)
	not_the_flag.txt
	image1.jpg (fry.jpg in media folder)
		polyception.pdf
		flag (base64 encoded)
```

## Solve
1. Open file as jpg
2. Try stego, it won't work
3. Realize filesize is too large for this single image
```
ls -l art.jpg
-rw-r--r-- 1 dylan dylan 3518869 Sep 20 16:25 art.png
```
4. Try to open it as a pdf by changing extension to .pdf, it works
```
mv art.png art.pdf
```
5. Experiment with it to see what other filetypes are there
6. Realize there is a .zip file
7. `unzip` the file (alternatively run `binwalk -e art.png` for similar result)
```
unzip art.png 
Archive:  art.png
warning [art.png]:  3430685 extra bytes at beginning or within zipfile
  (attempting to process anyway)
  inflating: _rels/.rels             
  inflating: docProps/app.xml        
  inflating: docProps/core.xml       
  inflating: word/_rels/document.xml.rels  
  inflating: word/settings.xml       
  inflating: word/fontTable.xml      
  inflating: word/media/image1.png   
  inflating: word/document.xml       
  inflating: word/styles.xml         
  inflating: [Content_Types].xml     
 extracting: not_the_flag.txt        
```
8. Open not_the_flag.txt and be disappointed
9. Realize unzipped file is an unzipped word document
10. Reconstruct word doc and open it (unnecessary, but can be done)
11. Realize there is an image (in media folder)
12. Realize this image is also a polyglot (filesize)
13. Open image as pdf and be disappointed
14. Run 
```
strings image1.png
...
0000078310 00000 n 
0000078651 00000 n 
0000078748 00000 n 
trailer
<</Size 15/Root 13 0 R
/Info 14 0 R
/ID [ <58EFC502C219CB9F304DC0DCAD2F055A>
<58EFC502C219CB9F304DC0DCAD2F055A> ]
/DocChecksum /FF9F529E3C0D15498FC918762A204019
startxref
78923
%%EOF
ZmxhZ3tQMGxZdEByX0QwX3kwdV9HM3RfSXRfTjB3P30K

```
15. Realize last line is base64 encoded
16. Run
```
echo "ZmxhZ3tQMGxZdEByX0QwX3kwdV9HM3RfSXRfTjB3P30K" | base64 --decode 
flag{P0lYt@r_D0_y0u_G3t_It_N0w?}
```

Flag: flag{P0lYt@r_D0_y0u_G3t_It_N0w?}

Running `strings art.png` will result in a large mess, but there is an easter-egg base64 message that redirects to a cool youtube music video.
