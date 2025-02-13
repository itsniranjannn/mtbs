<?php
// Set upload directory
$uploadDir = 'uploads/';

// Check if directory exists, and create it if not
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true); // Create directory with full permissions
}

// Handle file upload
if (isset($_FILES['fileToUpload'])) {
    $uploadFile = $uploadDir . basename($_FILES['fileToUpload']['name']);

    // Move the uploaded file to the designated directory
    if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $uploadFile)) {
        echo "File is valid, and was successfully uploaded to: $uploadFile";
    } else {
        echo "Possible file upload attack!";
    }
} else {
    echo "No file uploaded.";
}
?>
