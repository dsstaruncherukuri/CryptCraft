<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imageFile'])) {
    $uploadDir = 'uploads/'; // Directory where uploaded files will be stored
    $uploadFile = $uploadDir . basename($_FILES['imageFile']['name']);
    
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }
    
    // Move the uploaded file to the uploads directory
    if (move_uploaded_file($_FILES['imageFile']['tmp_name'], $uploadFile)) {
        $imagePath = $uploadFile;
        $base64String = imageToBase64String($imagePath);
        
        // Display the Base64 string
        echo '<h2>Base64 String:</h2>';
        echo '<textarea rows="10" cols="80">' . $base64String . '</textarea>';
    } else {
        echo '<p>Failed to upload the image.</p>';
    }
} else {
    echo '<p>No image uploaded or invalid request.</p>';
}

function imageToBase64String($imagePath) {
    // Read the image file into binary data
    $imageData = file_get_contents($imagePath);
    // Encode the binary data to Base64
    $base64String = base64_encode($imageData);
    return $base64String;
}
?>
