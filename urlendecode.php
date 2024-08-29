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
    <title>URL Encryption and Decryption</title>
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
        }

        .box {
            margin-bottom: 20px;
        }

        .box h2 {
            text-align: center;
            color: #333;
        }

        form {
            margin-bottom: 10px;
        }

        label {
            font-weight: bold;
        }

        input[type=text], input[type=password] {
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
        <h1>URL Encryption and Decryption</h1>

        <div class="box">
            <h2>Encrypt URL</h2>
            <form id="encryptionForm" onsubmit="return false;">
                <label for="urlToEncrypt">URL to Encrypt:</label><br>
                <input type="text" id="urlToEncrypt" name="urlToEncrypt" required><br><br>

                <label for="pin">PIN (shared key for AES-256, must be a non-zero number):</label><br>
                <input type="text" id="pin" name="pin" required pattern="[1-9][0-9]*"><br><br>

                <button onclick="encryptURL()">Encrypt URL</button>
            </form>

            <div id="encryptedResult" class="result"></div>
        </div>

        <div class="box">
            <h2>Decrypt URL</h2>
            <form id="decryptionForm" onsubmit="return false;">
                <label for="urlToDecrypt">Encrypted URL to Decrypt:</label><br>
                <input type="text" id="urlToDecrypt" name="urlToDecrypt" required><br><br>

                <label for="pinDecrypt">PIN (shared key for AES-256, must be a non-zero number):</label><br>
                <input type="text" id="pinDecrypt" name="pinDecrypt" required pattern="[1-9][0-9]*"><br><br>

                <button onclick="decryptURL()">Decrypt URL</button>
            </form>

            <div id="decryptedResult" class="result"></div>
        </div>

        <div class="footer">
            <a href="image.php">Go to Next Page</a>
        </div>
    </div>

    <script>
        function isValidURL(url) {
            // Regular expression to check if the input is a valid URL
            var pattern = new RegExp('^(https?:\\/\\/)?' + // protocol
                                    '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // domain name
                                    '((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
                                    '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // port and path
                                    '(\\?[;&a-z\\d%_.~+=-]*)?' + // query string
                                    '(\\#[-a-z\\d_]*)?$', 'i'); // fragment locator
            return pattern.test(url);
        }

        function encryptURL() {
            var urlToEncrypt = document.getElementById('urlToEncrypt').value.trim();
            var pin = document.getElementById('pin').value.trim();

            if (!isValidURL(urlToEncrypt)) {
                alert('Please enter a valid URL.');
                return;
            }

            if (!pin.match(/^[1-9][0-9]*$/)) {
                alert('PIN must be a non-zero number.');
                return;
            }

            // Perform AES-256 encryption
            var encryptedURL = CryptoJS.AES.encrypt(urlToEncrypt, pin).toString();

            // Display encrypted URL
            document.getElementById('encryptedResult').innerHTML = '<strong>Encrypted URL:</strong><br>' + encryptedURL;
        }

        function decryptURL() {
            var encryptedURL = document.getElementById('urlToDecrypt').value.trim();
            var pin = document.getElementById('pinDecrypt').value.trim();

            if (!pin.match(/^[1-9][0-9]*$/)) {
                alert('PIN must be a non-zero number.');
                return;
            }

            // Perform AES-256 decryption
            var decryptedBytes = CryptoJS.AES.decrypt(encryptedURL, pin);
            var decryptedURL = decryptedBytes.toString(CryptoJS.enc.Utf8);

            // Display decrypted URL if PIN is correct
            if (decryptedURL) {
                document.getElementById('decryptedResult').innerHTML = '<strong>Decrypted URL:</strong><br>' + decryptedURL;
            } else {
                document.getElementById('decryptedResult').innerHTML = '<div class="error">Incorrect PIN or decryption failed.</div>';
            }
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
</body>
</html>
