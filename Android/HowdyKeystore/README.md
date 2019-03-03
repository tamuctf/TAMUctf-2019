## Java Keystore Cracking

This challenge tests a competitors ability to crack a Java keystore file. These files are commonly used when signing Android applications, and cracking the passwords of these files can allow hackers to perform nefarious actions. For instance, a hacker may be able to sign and publish an app under your developer account if they are successful in obtaining and cracking the keystore file.

#### Description
You are given the **howdyapp.keystore** file. Knowing what you know about Aggies, try and crack the file. It may be beneficial to string together common phrases that an Aggie might use. You should submit a flag in the form **gigem{password}**


I recommend using a really useful Java Keystore cracking tool: https://github.com/MaxCamillo/android-keystore-password-recover

#### Solution
Using the Android password recovery tool in the link above and a dictionary containing the words **{howdy, gigem}** you can crack the file using the following command: 

**java -jar Android_Keystore_Password_Recover_1.07.jar -m 3 -k howdyapp.keystore -d dict.txt**


The password is **Howdygigem1** so the flag is **gigem{Howdygigem1}**
