# 📱 Complete Android App Build Guide

## ⚠️ IMPORTANT: Java 17 Required

Your system currently has **Java 11**, but Android Gradle Plugin 8.7+ requires **Java 17**.

## Step 1: Install Java 17

```bash
# Install Java 17
sudo apt update
sudo apt install openjdk-17-jdk

# Verify installation
java -version
# Should show: openjdk version "17.x.x"
```

**If Java 17 is installed but not default:**

```bash
# Set Java 17 as default
sudo update-alternatives --config java
# Select Java 17 from the list

# Or set JAVA_HOME
export JAVA_HOME=/usr/lib/jvm/java-17-openjdk-amd64
export PATH=$JAVA_HOME/bin:$PATH
```

## Step 2: Configure Gradle (if needed)

Edit `src-capacitor/android/gradle.properties`:

```properties
# Uncomment and set path if Java 17 is not default
org.gradle.java.home=/usr/lib/jvm/java-17-openjdk-amd64
```

## Step 3: Build the App

### Quick Build (All-in-One)

```bash
cd project/app

# Install dependencies (first time only)
npm install

# Build web assets + sync Capacitor
npm run build:android

# Open Android Studio
npm run android:open
```

### Manual Step-by-Step

```bash
cd project/app

# 1. Build web assets
npm run build

# 2. Sync Capacitor
cd src-capacitor
npx cap sync android

# 3. Open Android Studio
npx cap open android
```

## Step 4: Build in Android Studio

1. **Wait for Gradle Sync** (may take a few minutes first time)

2. **If Gradle Sync Fails:**
   - File → Invalidate Caches → Restart
   - Check Java version: File → Project Structure → SDK Location
   - Ensure Java 17 is selected

3. **Build Debug APK:**
   - Build → Build Bundle(s) / APK(s) → Build APK(s)
   - Wait for completion
   - APK location: `android/app/build/outputs/apk/debug/app-debug.apk`

4. **Build Release APK (for distribution):**
   - Build → Generate Signed Bundle / APK
   - Follow wizard to create keystore (first time)
   - Select "APK" or "Android App Bundle (AAB)"
   - Release APK: `android/app/build/outputs/apk/release/app-release.apk`

## Step 5: Install on Device

### Method 1: From Android Studio
- Click green "Run" button
- Select connected device
- App will install and launch

### Method 2: Manual Install
```bash
# Connect device via USB
adb devices

# Install APK
adb install android/app/build/outputs/apk/debug/app-debug.apk
```

## Step 6: Test the App

1. ✅ Test call log functionality
2. ✅ Test customer search
3. ✅ Test customer create
4. ✅ Test first call log auto-load
5. ✅ Test "Create New Customer" button

## Troubleshooting

### Error: "Android Gradle plugin requires Java 17"

**Solution:**
```bash
# Install Java 17
sudo apt install openjdk-17-jdk

# Set in gradle.properties
cd src-capacitor/android
echo "org.gradle.java.home=/usr/lib/jvm/java-17-openjdk-amd64" >> gradle.properties
```

### Error: "Gradle sync failed"

**Solution:**
1. File → Invalidate Caches → Restart
2. Build → Clean Project
3. Build → Rebuild Project

### Error: "Plugin not found"

**Solution:**
```bash
cd src-capacitor
npx cap sync android
```

### Build takes too long

**Solution:**
- First build always takes longer (downloads dependencies)
- Subsequent builds are faster
- Use `--offline` flag if needed: `./gradlew assembleRelease --offline`

## Build Commands Reference

```bash
# Development (with dev server)
npm run android

# Production build + sync
npm run build:android

# Open Android Studio
npm run android:open

# Build release APK (from Android Studio)
# Build → Generate Signed Bundle / APK
```

## File Locations

- **Debug APK:** `src-capacitor/android/app/build/outputs/apk/debug/app-debug.apk`
- **Release APK:** `src-capacitor/android/app/build/outputs/apk/release/app-release.apk`
- **Release AAB:** `src-capacitor/android/app/build/outputs/bundle/release/app-release.aab`

## Next Steps After Build

1. ✅ Test on physical device
2. ✅ Test all features
3. ✅ Generate signed release APK
4. ✅ Prepare for Play Store (if ready)

## Quick Checklist

- [ ] Java 17 installed
- [ ] Node.js v20+ installed
- [ ] Android Studio installed
- [ ] Android SDK installed
- [ ] Dependencies installed (`npm install`)
- [ ] Web assets built (`npm run build`)
- [ ] Capacitor synced (`npx cap sync android`)
- [ ] Gradle sync successful
- [ ] APK built successfully
- [ ] App tested on device

