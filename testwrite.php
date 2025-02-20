<?php
$file = 'test.txt';
if (file_put_contents($file, 'test')) {
    echo "Success: Can write to file.";
} else {
    echo "Error: Cannot write to file.";
}
?>
