# 🚀 Quick Build Instructions

## ⚠️ CRITICAL: Install Java 17 First

Your system currently has **Java 11**, but Android Gradle Plugin requires **Java 17**.

## Step 1: Install Java 17

Run this command in your terminal:

```bash
cd project/app
sudo apt update
sudo apt install -y openjdk-17-jdk
```

Or use the provided script:

```bash
cd project/app
./INSTALL_JAVA17.sh
```

## Step 2: Verify Java 17

```bash
java -version
# Should show: openjdk version "17.x.x"
```

## Step 3: Build the App

```bash
cd project/app

# Build web assets + sync Capacitor
npm run build:android

# Open Android Studio
npm run android:open
```

## Step 4: Build APK in Android Studio

1. Wait for Gradle sync to complete
2. Build → Build Bundle(s) / APK(s) → Build APK(s)
3. APK will be in: `src-capacitor/android/app/build/outputs/apk/debug/app-debug.apk`

## If Java 17 Installation Fails

If you can't install Java 17 system-wide, you can:

1. **Download Java 17 manually:**
   - Download from: https://adoptium.net/
   - Extract to a folder (e.g., `/opt/java-17`)

2. **Update gradle.properties:**
   ```properties
   org.gradle.java.home=/opt/java-17
   ```

3. **Or use Android Studio's bundled JDK:**
   - Android Studio → File → Project Structure → SDK Location
   - Use Android Studio's JDK (usually includes Java 17)

## Troubleshooting

### Still seeing Java 11 error?

1. Check gradle.properties has the correct path:
   ```bash
   cat src-capacitor/android/gradle.properties | grep java.home
   ```

2. Verify Java 17 path exists:
   ```bash
   ls -la /usr/lib/jvm/java-17-openjdk-amd64
   ```

3. Set JAVA_HOME environment variable:
   ```bash
   export JAVA_HOME=/usr/lib/jvm/java-17-openjdk-amd64
   export PATH=$JAVA_HOME/bin:$PATH
   ```

## Next Steps After Build

1. ✅ Test app on physical device
2. ✅ Test call log functionality  
3. ✅ Test customer search/create
4. ✅ Generate signed release APK

