<?php
/**
 * Version Bump Script for Mailchimp Newsletter Archive Plugin
 * 
 * Usage: php version-bump.php [new-version]
 * Example: php version-bump.php 1.0.1
 */

if ($argc < 2) {
    echo "Usage: php version-bump.php [new-version]\n";
    echo "Example: php version-bump.php 1.0.1\n";
    exit(1);
}

$new_version = $argv[1];

// Validate version format
if (!preg_match('/^\d+\.\d+\.\d+$/', $new_version)) {
    echo "Error: Version must be in format X.Y.Z (e.g., 1.0.1)\n";
    exit(1);
}

echo "Bumping version to: $new_version\n";

// Files to update
$files = [
    'plugin-name.php',
    'mailchimp-newsletter-archive.php',
    'README.txt'
];

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "Warning: $file not found, skipping...\n";
        continue;
    }
    
    $content = file_get_contents($file);
    $original_content = $content;
    
    // Update version in plugin header
    $content = preg_replace(
        '/Version:\s*\d+\.\d+\.\d+/',
        "Version:           $new_version",
        $content
    );
    
    // Update version constant
    $content = preg_replace(
        "/define\(\s*'MAILCHIMP_NEWSLETTER_ARCHIVE_VERSION',\s*'[^']*'\s*\);/",
        "define( 'MAILCHIMP_NEWSLETTER_ARCHIVE_VERSION', '$new_version' );",
        $content
    );
    
    // Update stable tag in README
    $content = preg_replace(
        '/Stable tag:\s*\d+\.\d+\.\d+/',
        "Stable tag: $new_version",
        $content
    );
    
    if ($content !== $original_content) {
        file_put_contents($file, $content);
        echo "Updated: $file\n";
    } else {
        echo "No changes needed: $file\n";
    }
}

echo "\nVersion bump complete!\n";
echo "Don't forget to:\n";
echo "1. Test the plugin thoroughly\n";
echo "2. Update the changelog in README.txt\n";
echo "3. Create a git tag: git tag v$new_version\n";
echo "4. Push the tag: git push origin v$new_version\n"; 