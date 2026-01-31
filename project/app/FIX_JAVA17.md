# 🔧 Fix Java 17 Issue

## Current Problem
Java 17 is not installed, but `gradle.properties` is trying to use it.

## Solution Options

### Option 1: Install Java 17 (Recommended for CLI builds)

```bash
sudo apt update
sudo apt install -y openjdk-17-jdk

# Verify installation
java -version
# Should show: openjdk version "17.x.x"

# Then uncomment in gradle.properties:
# org.gradle.java.home=/usr/lib/jvm/java-17-openjdk-amd64
```

### Option 2: Use Android Studio's Bundled JDK (Easiest)

**This is the recommended approach!**

1. **Open Android Studio:**
   ```bash
   cd project/app
   npm run android:open
   ```

2. **Configure JDK in Android Studio:**
   - File → Project Structure → SDK Location
   - Set "JDK Location" to Android Studio's bundled JDK
   - Usually located at: `~/Android/Sdk/jbr` or similar
   - Or use: `/opt/android-studio/jbr` (if installed system-wide)

3. **Build from Android Studio:**
   - Build → Build Bundle(s) / APK(s) → Build APK(s)
   - Android Studio will use its bundled Java 17 automatically

### Option 3: Comment Out gradle.properties (Temporary)

If you want to build from Android Studio only:

1. **Comment out the Java 17 line** (already done):
   ```properties
   # org.gradle.java.home=/usr/lib/jvm/java-17-openjdk-amd64
   ```

2. **Build from Android Studio:**
   - Android Studio will use its own JDK automatically

## Quick Fix (Right Now)

Since Java 17 path is commented out, you can:

1. **Build from Android Studio:**
   ```bash
   cd project/app
   npm run android:open
   ```

2. **In Android Studio:**
   - Wait for Gradle sync
   - Build → Build Bundle(s) / APK(s) → Build APK(s)

## Verify Java Version

```bash
# Check current Java
java -version

# Check if Java 17 exists
ls -la /usr/lib/jvm/ | grep 17

# If Java 17 is installed, verify path
ls -la /usr/lib/jvm/java-17-openjdk-amd64
```

## After Installing Java 17

1. **Uncomment in gradle.properties:**
   ```properties
   org.gradle.java.home=/usr/lib/jvm/java-17-openjdk-amd64
   ```

2. **Verify path exists:**
   ```bash
   ls -la /usr/lib/jvm/java-17-openjdk-amd64
   ```

3. **Try build again:**
   ```bash
   npm run build:android
   ```

