# 📱 Android App Build Guide

## Prerequisites

1. **Node.js** (v20 or higher)
2. **Java JDK 17** (required for Android Gradle Plugin)
3. **Android Studio** (latest version)
4. **Android SDK** (API Level 33+)

## Step-by-Step Build Process

### Step 1: Install Dependencies

```bash
cd project/app
npm install
```

### Step 2: Build Web Assets

```bash
# Production build
npm run build

# This will create optimized files in dist/ folder
```

### Step 3: Sync Capacitor

```bash
cd src-capacitor
npx cap sync android
```

This will:
- Copy web assets from `dist/` to `android/app/src/main/assets/public/`
- Update Android native code
- Register plugins

### Step 4: Open in Android Studio

```bash
npx cap open android
```

### Step 5: Configure Android Studio

1. **Set JDK 17:**
   - File → Project Structure → SDK Location
   - Set JDK location to Java 17
   - Or set `org.gradle.java.home` in `gradle.properties`

2. **Sync Gradle:**
   - Click "Sync Now" if prompted
   - Wait for Gradle sync to complete

### Step 6: Build APK/AAB

#### Option A: Build from Android Studio

1. **Build → Build Bundle(s) / APK(s) → Build APK(s)**
   - For testing: Build APK
   - For Play Store: Build Bundle (AAB)

2. **APK Location:**
   - `android/app/build/outputs/apk/release/app-release.apk`

3. **AAB Location:**
   - `android/app/build/outputs/bundle/release/app-release.aab`

#### Option B: Build from Command Line

```bash
cd src-capacitor/android
./gradlew assembleRelease

# For AAB (Play Store):
./gradlew bundleRelease
```

### Step 7: Sign the APK/AAB (Required for Release)

1. **Generate Keystore** (if not exists):
```bash
keytool -genkey -v -keystore homeland-crm-release.keystore -alias homeland-crm -keyalg RSA -keysize 2048 -validity 10000
```

2. **Configure Signing in `build.gradle`:**
   - Add signing configs (see below)

3. **Build Signed APK:**
   - Android Studio → Build → Generate Signed Bundle / APK

## Build Configuration Updates

### Update `build.gradle` for Release Signing

Add signing configs to `android/app/build.gradle`:

```gradle
android {
    signingConfigs {
        release {
            storeFile file('path/to/your/keystore.jks')
            storePassword 'your-store-password'
            keyAlias 'your-key-alias'
            keyPassword 'your-key-password'
        }
    }
    buildTypes {
        release {
            signingConfig signingConfigs.release
            minifyEnabled true
            shrinkResources true
        }
    }
}
```

### Update Version Code

In `android/app/build.gradle`:
```gradle
defaultConfig {
    versionCode 2  // Increment for each release
    versionName "1.0.1"  // Update version name
}
```

## Troubleshooting

### Java Version Error
If you see "Android Gradle plugin requires Java 17":
1. Install Java 17
2. Set JAVA_HOME: `export JAVA_HOME=/path/to/java17`
3. Or update `gradle.properties`: `org.gradle.java.home=/path/to/java17`

### Gradle Sync Failed
1. File → Invalidate Caches → Restart
2. Clean project: Build → Clean Project
3. Rebuild: Build → Rebuild Project

### Plugin Not Found
1. Run `npx cap sync android` again
2. Check `MainActivity.java` for plugin registration

## Quick Build Commands

```bash
# Full build process
npm run build && cd src-capacitor && npx cap sync android && npx cap open android

# Or use the script (if added to package.json)
npm run build:android
```

## Testing the Build

1. **Install APK on device:**
   ```bash
   adb install android/app/build/outputs/apk/release/app-release.apk
   ```

2. **Check logs:**
   ```bash
   adb logcat | grep -i "homeland\|capacitor\|calllog"
   ```

## Next Steps After Build

1. Test all features on physical device
2. Test call log functionality
3. Test customer search and create
4. Generate signed release APK/AAB
5. Upload to Play Store (if ready)

