## Android Challenge - Flag Encoded in strings.xml
In practice, many (not-so-smart) Android developers will put encryption keys, API keys, and other secrets directly into the strings.xml file. This is usually the result of not being aware of the security implications. 

###### Example Scenario
Bob the Android developer just developed a new application that uses a 3rd Party Maps API. Bob is able to use the Maps API for free up to 2,000 requests per day, which he is not anticipating reaching. Because Bob is not very smart, he puts his API key the application uses directly into the strings.xml file.

Now an evil hacker developers their own application but wants to use the Maps API for free as well. However, the hacker anticipates needing 10,000 API requests per day which costs a lot of money. Instead of paying for these requests, the hacker downloads Bob's application, finds his API key in the strings.xml file, and uses this as his own. 

A month passes, Bob gets a bill for thousands of dollars for the many API requests attributed to his key. Since Bob is not a very good developer, he does not have a very good job, and Bob is unable to pay the money. Don't be like Bob. 

### Solution

I would recommend downloading **apktool** or **jadx** to decompile the APK. Once inside the APK, just search for strings.xml. There will be an entry called "flag" which contains a base64 encoded string. The flag is decoded into **gigem{infinite_gigems}**
