<?php
$dir = '/Applications/XAMPP/xamppfiles/htdocs/boys_clothing_ecommerce/Uploads/';
echo "Is directory: " . (is_dir($dir) ? 'Yes' : 'No') . "<br>";
echo "Is writable: " . (is_writable($dir) ? 'Yes' : 'No') . "<br>";
echo "Permissions: " . substr(sprintf('%o', fileperms($dir)), -4) . "<br>";
echo "Owner: " . posix_getpwuid(fileowner($dir))['name'] . "<br>";
echo "Group: " . posix_getgrgid(filegroup($dir))['name'] . "<br>";
// Test file creation
$testFile = $dir . 'test.txt';
if (file_put_contents($testFile, 'Test content')) {
    echo "Test file created successfully.<br>";
    unlink($testFile);
} else {
    echo "Failed to create test file.<br>";
}
?>