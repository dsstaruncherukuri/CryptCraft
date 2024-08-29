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
    <title>Desert Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            text-align: center;
            padding: 50px;
        }
        .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: inline-block;
            padding: 30px;
            max-width: 400px;
            width: 100%;
        }
        h1 {
            color: #4CAF50;
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-size: 18px;
        }
        input[type="text"] {
            padding: 10px;
            font-size: 16px;
            width: calc(100% - 22px);
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #4CAF50;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
    <script>
        function redirect(action) {
            const form = document.getElementById('desertForm');
            if (action === 'encrypt') {
                form.action = 'encryptdesert.php';
            } else if (action === 'decrypt') {
                form.action = 'decryptdesert.php';
            }
            form.submit();
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Desert Page</h1>
        <form id="desertForm" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username">
            <br><br>
            <button type="button" onclick="redirect('encrypt')">Encrypt</button>
            <button type="button" onclick="redirect('decrypt')">Decrypt</button>
        </form>
    </div>
</body>
</html>
