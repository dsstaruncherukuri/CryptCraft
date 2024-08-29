<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image and Text Encryption/Decryption</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
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

        form {
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        label {
            font-weight: bold;
        }

        input[type=file], input[type=number], select, textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #45a049;
        }

        .result {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #f0f0f0;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Image and Text Encryption/Decryption</h1>

        <div class="form-container">
            <form id="encryptionForm" action="process.php" method="post" enctype="multipart/form-data">
                <label for="imageFile">Upload Image:</label><br>
                <input type="file" id="imageFile" name="imageFile" accept="image/*" required><br><br>

                <label for="pin">PIN (non-zero number):</label><br>
                <input type="number" id="pin" name="pin" required min="1"><br><br>

                <label for="conversionType">Choose Conversion Type:</label><br>
                <select id="conversionType" name="conversionType">
                    <option value="base64">Base64</option>
                    <option value="hash">Hash</option>
                </select><br><br>

                <label for="textToEncrypt">Text to Encrypt:</label><br>
                <textarea id="textToEncrypt" name="textToEncrypt" rows="4" cols="50" required></textarea><br><br>

                <button type="submit" name="action" value="encrypt">Encrypt</button>
            </form><br><br>

            <form id="decryptionForm" action="process.php" method="post" enctype="multipart/form-data">
                <label for="imageFileDecrypt">Upload Image:</label><br>
                <input type="file" id="imageFileDecrypt" name="imageFile" accept="image/*" required><br><br>

                <label for="pinDecrypt">PIN (non-zero number):</label><br>
                <input type="number" id="pinDecrypt" name="pin" required min="1"><br><br>

                <label for="conversionTypeDecrypt">Choose Conversion Type:</label><br>
                <select id="conversionTypeDecrypt" name="conversionType">
                    <option value="base64">Base64</option>
                    <option value="hash">Hash</option>
                </select><br><br>

                <label for="textToDecrypt">Text to Decrypt:</label><br>
                <textarea id="textToDecrypt" name="textToDecrypt" rows="4" cols="50" required></textarea><br><br>

                <button type="submit" name="action" value="decrypt">Decrypt</button>
            </form>
        </div>

        <div id="result" class="result"></div>
    </div>

    <script>
        // Function to handle encryption using AES-256
        function encryptData(data, key) {
            return CryptoJS.AES.encrypt(data, key).toString();
        }

        // Function to handle decryption using AES-256
        function decryptData(ciphertext, key) {
            var bytes  = CryptoJS.AES.decrypt(ciphertext, key);
            return bytes.toString(CryptoJS.enc.Utf8);
        }

        // Form submission handling
        document.getElementById('encryptionForm').addEventListener('submit', function(event) {
            event.preventDefault();
            var formData = new FormData(this);

            var reader = new FileReader();
            reader.onload = function(e) {
                var imageContent = e.target.result;
                var pin = formData.get('pin');
                var conversionType = formData.get('conversionType');
                var textToEncrypt = formData.get('textToEncrypt');

                // Perform conversion based on selected type
                var convertedData;
                if (conversionType === 'base64') {
                    convertedData = btoa(imageContent); // Convert to Base64
                } else if (conversionType === 'hash') {
                    convertedData = CryptoJS.SHA256(imageContent).toString(); // Hash with SHA-256
                }

                // Append PIN to the converted data to derive final key
                var finalKey = convertedData + pin;

                // Encrypt the text using the final key
                var encryptedText = encryptData(textToEncrypt, finalKey);

                // Display result
                document.getElementById('result').innerHTML = '<h2>Encrypted Text:</h2><p>' + encryptedText + '</p>';
            };
            reader.readAsDataURL(formData.get('imageFile'));
        });

        // Form submission handling for decryption
        document.getElementById('decryptionForm').addEventListener('submit', function(event) {
            event.preventDefault();
            var formData = new FormData(this);

            var reader = new FileReader();
            reader.onload = function(e) {
                var imageContent = e.target.result;
                var pin = formData.get('pin');
                var conversionType = formData.get('conversionType');
                var textToDecrypt = formData.get('textToDecrypt');

                // Perform conversion based on selected type
                var convertedData;
                if (conversionType === 'base64') {
                    convertedData = btoa(imageContent); // Convert to Base64
                } else if (conversionType === 'hash') {
                    convertedData = CryptoJS.SHA256(imageContent).toString(); // Hash with SHA-256
                }

                // Append PIN to the converted data to derive final key
                var finalKey = convertedData + pin;

                // Decrypt the text using the final key
                var decryptedText = decryptData(textToDecrypt, finalKey);

                // Display result
                document.getElementById('result').innerHTML = '<h2>Decrypted Text:</h2><p>' + decryptedText + '</p>';
            };
            reader.readAsDataURL(formData.get('imageFile'));
        });
    </script>
</body>
</html>
