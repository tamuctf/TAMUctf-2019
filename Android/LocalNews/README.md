# Android - Triggering a Local Broadcast Receiver

This challenge is one of the more difficult Android challenges. It involves knowledge of Android's Broadcast Receiver components as well as the **LocalBroadcastManager** object. The difficult aspect of this challenge is that the player must understand that they need to:
1. Write a custom broadcast receiver that responds to a system wide event (such as toggling Airplane mode). This broadcast receiver should then use the **LocalBroadcastManager** object to send a local broadcast.
2. The user should identify the local broadcast that the application is listening for by searching in the strings.xml resource file
3. The custom broadcast receiver should be compiled into a standalone APK. Then using any desired tools, extract the custom broadcast receiver Smali code, package it into the original challenge APK, and re-compile. 
4. In order to install the newly patched APK, the user will have to sign the application. This is straight forward and there are plenty of tutorials on Google for signing your own application. 
5. Run the application in an Android emulator, trigger your custom broadcast receiver which in turns sends a local broadcast which outputs the flag to logcat. 

Note that this is a fairly extensive process and will require some thinking and understanding of how Android operates. 

### *Notes*
The solution files contain:
- a keystore file for signing the application
- a "malicious" APK which contains the smali code for the custom broadcast receiver

Below I will walk through the process of finding the flag. Before we start, you should have already installed:
1. Android Studio
    - including the Android emulator 
2. apktool
3. Jadx (or any other Java Decompiler of your choosing)
3. jarsigner

### Hints

1. Look in the strings.xml file for a Broadcast Filter string
2. See if you can write a custom Broadcast Receiver and inject it into the original APK

## Solution

#### Video Walkthrough
Below is a video walkthrough for solving this challenge. (The video is unlisted on YouTube so only people with the link can view it)

https://youtu.be/JmNkj5OHcPI


First we should open **broadcast-obfuscated.apk** in Jadx. Upon looking in com.tamu.ctf.hidden/MainActivity.java, we should notice immediately that a Local Broadcast Receiver is logging the flag to LogCat. The Local Broadcast receiver implies that the broadcast should originate from the *same* application i.e. the same process space. So firing up ADB and sending a broadcast will not do us any good here.

We should also not that the flag is obfuscated. We could try to reverse engineer the obfuscation library, but this would take much longer than the intended solution. Instead, let's see if we can find what broadcast the LocalBroadcastManager is listening for. 

In the *onCreate* method of the MainActivity, you should see something along the lines of **filter.addAction(getString(C0012R.string.hidden_action))**. So we can see that the LocalBroadcastManager is filtering for an action corresponding to some string. Using what we know about the Android APK structure, you should be able to quickly find the action in the **strings.xml** file. Typically I like to use **apktool** to decompile the APK, and then find the */res/values/strings.xml* file. 
We can run the command **java -jar apktool d broadcast-obfuscated -o ./broadcast-obfuscated-decompiled** in order to depackage the APK. From here, just look in the res/values/strings.xml file and grep for **hidden_action**. Here you will discover that the LocalBroadcastReceiver is registered for the action **"com.tamu.ctf.hidden.START"**.

So now we've gotten far enough to know how to trigger the LocalBroadcastReceiver, the question is: how do we get the application to send this broadcast? Sifting through the APK more you won't find any hints or logic that will trigger this broadcast. The best we can do is resort to writing our own, and the patching the original APK. The caveat with this challenge should be apparent by now. Normally we could just use ADB to send a system wide broadcast that would trigger this receiver. However, since the developer is using the LocalBroadcastManager object, the receiver is protected. 

###### Writing our own Broadcast Receiver

1. Create a new Android Studio project. 
2. Create a custom .java file
- Send a broadcast using the LocalBroadcastManager Object.
```
package com.tamu.ctf.hidden;


import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.support.v4.content.LocalBroadcastManager;

public class myReceiver extends BroadcastReceiver {

    @Override
    public void onReceive(Context context, Intent intent) {
        Intent i = new Intent("com.tamu.ctf.hidden.START");
        LocalBroadcastManager.getInstance(context).sendBroadcast(i);
    }

}
```

Here we have our own custom BroadcastReceiver which (when activated) will send a LocalBroadcast with the action **com.tamu.ctf.hidden.START**. Note that we still need to inject this code into the original APK. Compile the APK using Android Studio by selecting Build -> Build APK. 

Now decompile the newly built APK using the apktool method described earlier. You should be able to find the Smali code in at the path **smali/com/tamu/ctf/hidden/myReceiver.smali**. Copy this smali file and paste it into the correct location of the **obfuscated/** directory we created earlier with apktool. Note that the myReceiver.smali code should be copied to **obfuscated/smali/com/tamu/ctf/hidden/**. 

One last change we need to make is to ensure that our custom BroadcastReceiver can be triggered by some system-wide event. To do this, we should register the receiver in the **AndroidManifest.xml** file. Open this file in the **obfuscated/** directory and edit it to: 

```
<?xml version="1.0" encoding="utf-8" standalone="no"?><manifest xmlns:android="http://schemas.android.com/apk/res/android" package="com.tamu.ctf.hidden">
    <application android:allowBackup="true" android:debuggable="true" android:icon="@mipmap/ic_launcher" android:label="@string/app_name" android:supportsRtl="true" android:theme="@style/AppTheme">
        <activity android:name="com.tamu.ctf.hidden.MainActivity">
            <intent-filter>
                <action android:name="android.intent.action.MAIN"/>
                <category android:name="android.intent.category.LAUNCHER"/>
            </intent-filter>
        </activity>
        
        <receiver android:name="myReceiver" >
            <intent-filter>
                <action android:name="android.intent.action.AIRPLANE_MODE" />
            </intent-filter>
        </receiver>
        
    </application>
</manifest>
```

So we have registered our custom broadcast receiver to respond to the toggling of Airplane mode. Now we just need to recomple the directory into a new APK. To do this, use **java -jar apktool b obfuscated -o .\obfuscated-patched.apk**

Before we fire this newly patched APK up in an emulator, we need to sign the APK. You can learn how to easily create a keystore file, and sign the APK yourself using the many online tutorials out there.

###### First Create a Keystore
```
keytool -genkey -v -keystore my-release-key.keystore -alias alias_name -keyalg RSA -keysize 2048 -validity 10000
```

###### Sign the Apk Using the Keystore File
```
jarsigner -verbose -sigalg SHA256withRSA -digestalg SHA1 -keystore my-release-key.keystore apkfile.apk android
```

Once the APK is signed, you will be able to install it on a physical device / emulator. 

Once you have signed the APK, you can install it on any running emulator or device via ADB: **adb install obfuscated-patched.apk**. After installing the application, open the app in the emulator (or on your physical device if you're using one) and toggle the airplane mode button. If you look at logcat in Android Studio you should see the flag displayed: **Flag: gigem{hidden_81aeb013bea}**
