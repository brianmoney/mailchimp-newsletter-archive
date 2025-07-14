<?php
/**
 * Build Package Script for Mailchimp Newsletter Archive Plugin
 * 
 * Creates a distribution package for WordPress.org or self-hosting
 * 
 * Usage: php build-package.php [version]
 * Example: php build-package.php 1.0.0
 */

if ($argc < 2) {
    echo "Usage: php build-package.php [version]\n";
    echo "Example: php build-package.php 1.0.0\n";
    exit(1);
}

$version = $argv[1];
$package_name = "mailchimp-newsletter-archive-$version";

// Validate version format
if (!preg_match('/^\d+\.\d+\.\d+$/', $version)) {
    echo "Error: Version must be in format X.Y.Z (e.g., 1.0.0)\n";
    exit(1);
}

echo "Building package: $package_name\n";

// Create build directory
$build_dir = "build/$package_name";
if (!is_dir('build')) {
    mkdir('build', 0755, true);
}

if (is_dir($build_dir)) {
    echo "Removing existing build directory...\n";
    rmdir_recursive($build_dir);
}

mkdir($build_dir, 0755, true);

// Files and directories to include
$include_paths = [
    'admin/',
    'includes/',
    'languages/',
    'public/',
    'LICENSE.txt',
    'README.txt',
    'uninstall.php',
    'plugin-name.php',
    'mailchimp-newsletter-archive.php'
];

// Files to exclude
$exclude_patterns = [
    '/\.git/',
    '/node_modules/',
    '/vendor/',
    '/build/',
    '/\.DS_Store/',
    '/Thumbs\.db/',
    '/\.vscode/',
    '/\.idea/',
    '/\.env/',
    '/composer\.(json|lock)/',
    '/package\.(json|lock)/',
    '/webpack\.config\.js/',
    '/\.eslintrc/',
    '/\.prettierrc/',
    '/phpunit\.xml/',
    '/tests/',
    '/docs/',
    '/DISTRIBUTION\.md/',
    '/version-bump\.php/',
    '/build-package\.php/',
    '/copy_template\.php/'
];

echo "Copying files...\n";

foreach ($include_paths as $path) {
    if (is_file($path)) {
        copy_file($path, $build_dir . '/' . basename($path));
    } elseif (is_dir($path)) {
        copy_directory($path, $build_dir . '/' . $path, $exclude_patterns);
    }
}

// Create zip file
$zip_file = "build/$package_name.zip";
echo "Creating zip file: $zip_file\n";

$zip = new ZipArchive();
if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    echo "Error: Could not create zip file\n";
    exit(1);
}

add_directory_to_zip($zip, $build_dir, $package_name);
$zip->close();

echo "Package created successfully!\n";
echo "Zip file: $zip_file\n";
echo "Extracted files: $build_dir\n";

// Helper functions
function copy_file($source, $destination) {
    $dir = dirname($destination);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    copy($source, $destination);
    echo "Copied: $source\n";
}

function copy_directory($source, $destination, $exclude_patterns) {
    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }
    
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($files as $file) {
        $file_path = $file->getRealPath();
        $relative_path = str_replace($source, '', $file_path);
        $target_path = $destination . $relative_path;
        
        // Check if file should be excluded
        $should_exclude = false;
        foreach ($exclude_patterns as $pattern) {
            if (preg_match($pattern, $file_path)) {
                $should_exclude = true;
                break;
            }
        }
        
        if ($should_exclude) {
            continue;
        }
        
        if ($file->isDir()) {
            if (!is_dir($target_path)) {
                mkdir($target_path, 0755, true);
            }
        } else {
            copy_file($file_path, $target_path);
        }
    }
}

function add_directory_to_zip($zip, $dir, $base_path = '') {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($files as $file) {
        $file_path = $file->getRealPath();
        $relative_path = $base_path . '/' . str_replace($dir, '', $file_path);
        
        if ($file->isDir()) {
            $zip->addEmptyDir($relative_path);
        } else {
            $zip->addFile($file_path, $relative_path);
        }
    }
}

function rmdir_recursive($dir) {
    if (!is_dir($dir)) {
        return;
    }
    
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            rmdir_recursive($path);
        } else {
            unlink($path);
        }
    }
    rmdir($dir);
} 