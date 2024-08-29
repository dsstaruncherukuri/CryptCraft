<?php

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imageFile'])) {
    $uploadDir = 'uploads/';
    $uploadFile = $uploadDir . basename($_FILES['imageFile']['name']);

    if (move_uploaded_file($_FILES['imageFile']['tmp_name'], $uploadFile)) {
        $imagePath = $uploadFile;
        $hashString = imageToHash($imagePath);

        echo '<h2>Hash String (SHA-256):</h2>';
        echo '<textarea rows="2" cols="64">' . $hashString . '</textarea>';
    } else {
        echo '<p>Failed to upload the image.</p>';
    }
} else {
    echo '<p>No image uploaded or invalid request.</p>';
}

function imageToHash($imagePath) {
    $imageData = file_get_contents($imagePath);
    return hash('sha256', $imageData);
}
?>
