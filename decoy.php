<?php
session_start();
require_once "config.php";

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iterative Encryption and Decryption</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            width: 80%;
            max-width: 600px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .form-container {
            margin-bottom: 20px;
        }
        .form-container label {
            font-weight: bold;
        }
        .form-container input[type="text"], .form-container input[type="number"] {
            width: calc(100% - 20px);
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-container button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .form-container button:hover {
            background-color: #45a049;
        }
        .result {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #f0f0f0;
            word-wrap: break-word;
        }
        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Iterative Encryption and Decryption</h1>

        <!-- Encryption Form -->
        <div class="form-container">
            <h2>Encryption</h2>
            <label for="textToEncrypt">Text to Encrypt:</label><br>
            <input type="text" id="textToEncrypt" required><br><br>

            <label for="encryptionKey">Encryption Key:</label><br>
            <input type="text" id="encryptionKey" required><br><br>

            <label for="numberOfLayers">Number of Layers:</label><br>
            <input type="number" id="numberOfLayers" min="1" required><br><br>

            <button onclick="encryptText()">Encrypt</button>
        </div>

        <!-- Encrypted Result -->
        <div id="encryptedResult" class="result" style="display: none;"></div>

        <!-- Decryption Form -->
        <div class="form-container">
            <h2>Decryption</h2>
            <label for="textToDecrypt">Encrypted Text:</label><br>
            <input type="text" id="textToDecrypt" required><br><br>

            <label for="decryptionKey">Decryption Key:</label><br>
            <input type="text" id="decryptionKey" required><br><br>

            <label for="numberOfLayersDecrypt">Number of Layers:</label><br>
            <input type="number" id="numberOfLayersDecrypt" min="1" required><br><br>

            <button onclick="decryptText()">Decrypt</button>
        </div>

        <!-- Decrypted Result -->
        <div id="decryptedResult" class="result" style="display: none;"></div>
    </div>

    <script>
    function encryptText() {
        var plainText = document.getElementById('textToEncrypt').value.trim();
        var encryptionKey = document.getElementById('encryptionKey').value.trim();
        var numberOfLayers = parseInt(document.getElementById('numberOfLayers').value, 10);

        if (!plainText || !encryptionKey || isNaN(numberOfLayers) || numberOfLayers < 1) {
            alert('Please enter valid text, encryption key, and number of layers.');
            return;
        }

        var encryptedText = plainText;
        for (var i = 0; i < numberOfLayers; i++) {
            encryptedText = btoa(encryptedText + encryptionKey); // Example of simple iterative encryption (Base64)
        }
        
        document.getElementById('encryptedResult').innerText = `Encrypted Text: ${encryptedText}`;
        document.getElementById('encryptedResult').style.display = 'block';
    }

    function decryptText() {
        var encryptedText = document.getElementById('textToDecrypt').value.trim();
        var decryptionKey = document.getElementById('decryptionKey').value.trim();
        var numberOfLayers = parseInt(document.getElementById('numberOfLayersDecrypt').value, 10);

        if (!encryptedText || !decryptionKey || isNaN(numberOfLayers) || numberOfLayers < 1) {
            alert('Please enter valid encrypted text, decryption key, and number of layers.');
            return;
        }

        var decryptedText = encryptedText;

        // Decrypt up to specified number of layers
        for (var i = 0; i < numberOfLayers; i++) {
            try {
                decryptedText = atob(decryptedText).slice(0, -decryptionKey.length); // Example of simple iterative decryption (Base64)
            } catch (e) {
                alert('Decryption error: ' + e.message);
                return;
            }
        }

        // Check if the number of layers matches the actual encryption rounds
        if (numberOfLayers < countEncryptionLayers(encryptedText, decryptionKey)) {
            // Generate gibberish hashes for remaining decryption steps
            var gibberish = '';
            for (var j = numberOfLayers; j < 100; j++) { // Limit gibberish generation to 100 layers
                gibberish += generateGibberishHash();
            }
            decryptedText += gibberish;
        }

        document.getElementById('decryptedResult').innerText = `Decrypted Text: ${decryptedText}`;
        document.getElementById('decryptedResult').style.display = 'block';
    }

    function countEncryptionLayers(text, encryptionKey) {
        var count = 0;
        var tempText = text;
        while (tempText.includes(encryptionKey)) {
            tempText = atob(tempText).slice(0, -encryptionKey.length);
            count++;
        }
        return count;
    }

    function generateGibberishHash() {
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var gibberish = '';

        for (var i = 0; i < 10; i++) { // You can adjust the length of each gibberish hash
            gibberish += characters.charAt(Math.floor(Math.random() * characters.length));
        }

        return gibberish + ' ';
    }
</script>

</body>
</html>
