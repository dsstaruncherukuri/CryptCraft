<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imageFile']) && isset($_POST['pin']) && isset($_POST['conversionType'])) {
    $uploadDir = 'uploads/';
    $uploadFile = $uploadDir . basename($_FILES['imageFile']['name']);

    if (move_uploaded_file($_FILES['imageFile']['tmp_name'], $uploadFile)) {
        $imagePath = $uploadFile;
        $pin = $_POST['pin'];
        $conversionType = $_POST['conversionType'];
        $textToProcess = '';

        if ($_POST['action'] == 'encrypt') {
            $textToProcess = $_POST['textToEncrypt'];
        } elseif ($_POST['action'] == 'decrypt') {
            $textToProcess = $_POST['textToDecrypt'];
        }

        // Read image content
        $imageContent = file_get_contents($imagePath);

        // Perform conversion based on selected type
        if ($conversionType === 'base64') {
            $convertedData = base64_encode($imageContent); // Convert to Base64
            // Append PIN to the front of converted data
            $finalKey = $pin . $convertedData;
        } elseif ($conversionType === 'hash') {
            $convertedData = hash('sha256', $imageContent); // Hash with SHA-256
            // Append PIN to the end of converted data
            $finalKey = $convertedData . $pin;
        }

        // Perform AES-256 encryption or decryption
        if ($_POST['action'] == 'encrypt') {
            $encryptedText = openssl_encrypt($textToProcess, 'AES-256-CBC', $finalKey, 0, $finalKey);
            echo $encryptedText;
        } elseif ($_POST['action'] == 'decrypt') {
            $decryptedText = openssl_decrypt($textToProcess, 'AES-256-CBC', $finalKey, 0, $finalKey);
            echo $decryptedText;
        }

        // Clean up uploaded file
        unlink($imagePath);
    } else {
        echo 'Failed to upload the image.';
    }
} else {
    echo 'Invalid request.';
}
?>