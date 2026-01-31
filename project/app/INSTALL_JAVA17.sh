#!/bin/bash

# Install Java 17 for Android Build
echo "Installing Java 17..."

# Update package list
sudo apt update

# Install Java 17 JDK
sudo apt install -y openjdk-17-jdk

# Verify installation
echo ""
echo "Checking Java version..."
java -version

# Set Java 17 as default (optional)
echo ""
echo "Setting Java 17 as default..."
sudo update-alternatives --config java

# Update gradle.properties
echo ""
echo "Updating gradle.properties..."
cd src-capacitor/android
if ! grep -q "org.gradle.java.home" gradle.properties; then
    echo "" >> gradle.properties
    echo "# Java 17 configuration" >> gradle.properties
    echo "org.gradle.java.home=/usr/lib/jvm/java-17-openjdk-amd64" >> gradle.properties
    echo "✅ Added Java 17 path to gradle.properties"
else
    echo "⚠️  Java 17 path already exists in gradle.properties"
fi

echo ""
echo "✅ Java 17 installation complete!"
echo "Now you can run: npm run build:android"

