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
    <title>Coordinate Encryption and Decryption</title>
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
        <h1>Coordinate Encryption and Decryption</h1>

        <div class="box">
            <h2>Encrypt Coordinates</h2>
            <form id="encryptionForm" onsubmit="return false;">
                <label for="latitude">Latitude:</label><br>
                <input type="text" id="latitude" name="latitude" required pattern="-?([1-8]?[0-9](\.\d+)?|90(\.0+)?)"><br><br>

                <label for="longitude">Longitude:</label><br>
                <input type="text" id="longitude" name="longitude" required pattern="-?((1[0-7][0-9](\.\d+)?)|([1-9]?[0-9](\.\d+)?)|180(\.0+)?)"><br><br>

                <label for="pin">PIN (shared key for AES-256):</label><br>
                <input type="text" id="pin" name="pin" required pattern="[1-9][0-9]*"><br><br>

                <button onclick="encryptCoordinates()">Encrypt Coordinates</button>
            </form>

            <div id="encryptedResult" class="result"></div>
        </div>

        <div class="box">
            <h2>Decrypt Coordinates</h2>
            <form id="decryptionForm" onsubmit="return false;">
                <label for="coordinatesToDecrypt">Encrypted Coordinates:</label><br>
                <input type="text" id="coordinatesToDecrypt" name="coordinatesToDecrypt" required><br><br>

                <label for="pinDecrypt">PIN (shared key for AES-256):</label><br>
                <input type="text" id="pinDecrypt" name="pinDecrypt" required pattern="[1-9][0-9]*"><br><br>

                <button onclick="decryptCoordinates()">Decrypt Coordinates</button>
            </form>

            <div id="decryptedResult" class="result"></div>
        </div>

        <div class="footer">
            <a href="coordinates2.php">Go to Next Page</a>
        </div>
    </div>

    <script>
        function encryptCoordinates() {
            var latitude = document.getElementById('latitude').value.trim();
            var longitude = document.getElementById('longitude').value.trim();
            var pin = document.getElementById('pin').value.trim();

            if (!latitude.match(/^(-?([1-8]?[0-9](\.\d+)?|90(\.0+)?))$/) || !longitude.match(/^(-?((1[0-7][0-9](\.\d+)?)|([1-9]?[0-9](\.\d+)?)|180(\.0+)?))$/)) {
                alert('Please enter valid coordinates.');
                return;
            }

            if (!pin.match(/^[1-9][0-9]*$/)) {
                alert('PIN must be a non-zero number.');
                return;
            }

            var coordinates = latitude + ',' + longitude;
            var encryptedCoordinates = CryptoJS.AES.encrypt(coordinates, pin).toString();

            document.getElementById('encryptedResult').innerHTML = '<strong>Encrypted Coordinates:</strong><br>' + encryptedCoordinates;
        }

        function decryptCoordinates() {
            var encryptedCoordinates = document.getElementById('coordinatesToDecrypt').value.trim();
            var pin = document.getElementById('pinDecrypt').value.trim();

            if (!pin.match(/^[1-9][0-9]*$/)) {
                alert('PIN must be a non-zero number.');
                return;
            }

            var decryptedBytes = CryptoJS.AES.decrypt(encryptedCoordinates, pin);
            var decryptedCoordinates = decryptedBytes.toString(CryptoJS.enc.Utf8);

            if (decryptedCoordinates) {
                document.getElementById('decryptedResult').innerHTML = '<strong>Decrypted Coordinates:</strong><br>' + decryptedCoordinates;
            } else {
                document.getElementById('decryptedResult').innerHTML = '<div class="error">Incorrect PIN or decryption failed.</div>';
            }
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
</body>
</html>
