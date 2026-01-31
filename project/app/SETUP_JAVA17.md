# 🔧 Java 17 Setup Guide

## Current Issue
Your system has Java 11, but Android Gradle Plugin 8.7+ requires **Java 17**.

## Solution Options

### Option 1: Install Java 17 (Recommended)

```bash
# Ubuntu/Debian
sudo apt update
sudo apt install openjdk-17-jdk

# Verify installation
java -version
# Should show: openjdk version "17.x.x"
```

### Option 2: Use Existing Java 17 (if installed)

1. **Find Java 17 location:**
   ```bash
   ls -la /usr/lib/jvm/ | grep java
   # Look for java-17-openjdk-amd64 or similar
   ```

2. **Update gradle.properties:**
   Edit `src-capacitor/android/gradle.properties`:
   ```properties
   org.gradle.java.home=/usr/lib/jvm/java-17-openjdk-amd64
   ```

### Option 3: Set JAVA_HOME Environment Variable

```bash
# Add to ~/.bashrc or ~/.zshrc
export JAVA_HOME=/usr/lib/jvm/java-17-openjdk-amd64
export PATH=$JAVA_HOME/bin:$PATH

# Reload shell
source ~/.bashrc  # or source ~/.zshrc

# Verify
java -version
```

## After Installing Java 17

1. **Verify:**
   ```bash
   java -version
   # Should show version 17
   ```

2. **Update gradle.properties** (if needed):
   ```bash
   cd project/app/src-capacitor/android
   echo "org.gradle.java.home=/usr/lib/jvm/java-17-openjdk-amd64" >> gradle.properties
   ```

3. **Try building again:**
   ```bash
   cd project/app
   npm run build:android
   ```

## Multiple Java Versions

If you have multiple Java versions:

```bash
# List available Java versions
sudo update-alternatives --config java

# Select Java 17
# Then verify
java -version
```

