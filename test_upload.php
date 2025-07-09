<?php
// Test file to check upload directory and permissions
$uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/product_images/';

echo "Upload Directory Test Results:<br><br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Upload Directory: " . $uploadDir . "<br>";
echo "Directory exists: " . (is_dir($uploadDir) ? 'YES' : 'NO') . "<br>";
echo "Directory is writable: " . (is_writable($uploadDir) ? 'YES' : 'NO') . "<br>";
echo "Directory permissions: " . substr(sprintf('%o', fileperms($uploadDir)), -4) . "<br>";

// Create directory if it doesn't exist
if (!is_dir($uploadDir)) {
    echo "Attempting to create directory...<br>";
    if (mkdir($uploadDir, 0777, true)) {
        echo "Directory created successfully!<br>";
    } else {
        echo "Failed to create directory.<br>";
    }
}

// Test file creation
$testFile = $uploadDir . 'test.txt';
if (file_put_contents($testFile, 'test content')) {
    echo "Test file created successfully!<br>";
    unlink($testFile); // Clean up
    echo "Test file deleted.<br>";
} else {
    echo "Failed to create test file.<br>";
}

echo "<br>Current working directory: " . getcwd() . "<br>";
echo "Script file: " . __FILE__ . "<br>";
?>
