<?php
session_start();
require_once "config.php";

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
    <title>Text Encryption and Decryption</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 600px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .box {
            margin-bottom: 30px;
        }

        .box h2 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
        }

        form {
            margin-bottom: 10px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input[type=text], input[type=number] {
            width: calc(100% - 20px);
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
            margin-top: 10px;
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

        .footer {
            text-align: center;
            margin-top: 20px;
        }

        .footer a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Text Encryption and Decryption</h1>

        <div class="box">
            <h2>Encrypt Text</h2>
            <form id="encryptionForm" onsubmit="return false;">
                <label for="textToEncrypt">Text to Encrypt:</label><br>
                <input type="text" id="textToEncrypt" name="textToEncrypt" required><br><br>

                <label for="tempPin">Temperature (PIN for AES-256):</label><br>
                <input type="number" id="tempPin" name="tempPin" required><br><br>

                <button onclick="encryptText()">Encrypt Text</button>
            </form>

            <div id="encryptedResult" class="result"></div>
        </div>

        <div class="box">
            <h2>Decrypt Text</h2>
            <form id="decryptionForm" onsubmit="return false;">
                <label for="textToDecrypt">Encrypted Text:</label><br>
                <input type="text" id="textToDecrypt" name="textToDecrypt" required><br><br>

                <label for="tempPinDecrypt">Temperature (PIN for AES-256):</label><br>
                <input type="number" id="tempPinDecrypt" name="tempPinDecrypt" required><br><br>

                <button onclick="decryptText()">Decrypt Text</button>
            </form>

            <div id="decryptedResult" class="result"></div>
        </div>
    </div>

    <script>
        function encryptText() {
            var textToEncrypt = document.getElementById('textToEncrypt').value.trim();
            var tempPin = document.getElementById('tempPin').value.trim();

            if (!textToEncrypt || isNaN(tempPin) || tempPin <= 0) {
                alert('Please enter valid text and temperature.');
                return;
            }

            var encryptedText = CryptoJS.AES.encrypt(textToEncrypt, tempPin).toString();

            document.getElementById('encryptedResult').innerHTML = '<strong>Encrypted Text:</strong><br>' + encryptedText;
        }

        function decryptText() {
            var encryptedText = document.getElementById('textToDecrypt').value.trim();
            var tempPin = document.getElementById('tempPinDecrypt').value.trim();

            if (!encryptedText || isNaN(tempPin) || tempPin <= 0) {
                alert('Please enter valid encrypted text and temperature.');
                return;
            }

            var decryptedBytes = CryptoJS.AES.decrypt(encryptedText, tempPin);
            var decryptedText = decryptedBytes.toString(CryptoJS.enc.Utf8);

            if (decryptedText) {
                document.getElementById('decryptedResult').innerHTML = '<strong>Decrypted Text:</strong><br>' + decryptedText;
            } else {
                document.getElementById('decryptedResult').innerHTML = '<div class="error">Incorrect temperature or decryption failed.</div>';
            }
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
</body>
</html>
