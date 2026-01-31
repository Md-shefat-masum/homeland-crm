# 🚀 Quick Build Steps for Android App

## Prerequisites Check

1. **Java 17** installed:
   ```bash
   java -version
   # Should show version 17 or higher
   ```

2. **Node.js** installed:
   ```bash
   node -v
   # Should show v20 or higher
   ```

3. **Android Studio** installed with:
   - Android SDK (API 33+)
   - Android SDK Build-Tools
   - Android SDK Platform-Tools

## Build Process

### Method 1: Automated Build (Recommended)

```bash
cd project/app

# Step 1: Install dependencies (if not done)
npm install

# Step 2: Build and sync
npm run build:android

# Step 3: Open in Android Studio
npm run android:open
```

### Method 2: Manual Step-by-Step

```bash
cd project/app

# Step 1: Build web assets
npm run build

# Step 2: Sync Capacitor
cd src-capacitor
npx cap sync android

# Step 3: Open Android Studio
npx cap open android
```

## In Android Studio

1. **Wait for Gradle Sync** to complete
2. **Set Java 17** (if not set):
   - File → Project Structure → SDK Location
   - Set JDK location to Java 17
   - Or edit `gradle.properties` and uncomment:
     ```
     org.gradle.java.home=/path/to/java-17
     ```

3. **Build APK:**
   - Build → Build Bundle(s) / APK(s) → Build APK(s)
   - Wait for build to complete
   - APK will be in: `android/app/build/outputs/apk/debug/app-debug.apk`

4. **Build Release APK:**
   - Build → Generate Signed Bundle / APK
   - Follow the wizard to create signed APK

## Testing on Device

1. **Enable USB Debugging** on your Android device
2. **Connect device** via USB
3. **Run from Android Studio:**
   - Click the green "Run" button
   - Select your device
   - App will install and launch

4. **Or install APK manually:**
   ```bash
   adb install android/app/build/outputs/apk/debug/app-debug.apk
   ```

## Troubleshooting

### Java Version Error
```bash
# Check Java version
java -version

# If not Java 17, install it:
sudo apt install openjdk-17-jdk

# Set JAVA_HOME
export JAVA_HOME=/usr/lib/jvm/java-17-openjdk-amd64

# Or add to gradle.properties:
echo "org.gradle.java.home=/usr/lib/jvm/java-17-openjdk-amd64" >> src-capacitor/android/gradle.properties
```

### Gradle Sync Failed
1. File → Invalidate Caches → Restart
2. Clean: Build → Clean Project
3. Rebuild: Build → Rebuild Project

### Plugin Not Found
```bash
cd src-capacitor
npx cap sync android
```

## Build Commands Summary

```bash
# Development build (with dev server)
npm run android

# Production build + sync
npm run build:android

# Open Android Studio
npm run android:open

# Build release APK (from Android Studio)
# Build → Generate Signed Bundle / APK
```

## Next Steps

1. ✅ Test app on physical device
2. ✅ Test call log functionality
3. ✅ Test customer search/create
4. ✅ Generate signed release APK
5. ✅ Upload to Play Store (when ready)

