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
    <title>CRYPTCRAFT</title>
    <style>
        * {
            box-sizing: border-box;
        }
        @font-face {
            font-family: 'Cynatar';
            src: url('Cynatar.otf') format('truetype');
        }
        body {
            background-color: black;
            color: white;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 98vh;
            margin: 0;
        }
        .zoom {
            transition: transform .2s;
        }
        .zoom:hover {
            -ms-transform: scale(1.5); /* IE 9 */
            -webkit-transform: scale(1.5); /* Safari 3-8 */
            transform: scale(1.5);
        }
        .link-list {
            list-style: none;
            padding: 0;
        }
        .link-list li {
            margin: 18px 0;
        }
        .link-list a {
            color: white;
            text-decoration: none;
            font-size: 24px;
            display: block;
            text-align: center;
            position: relative;
            font-family: 'Cynatar', Arial, sans-serif;
        }
        .link-list a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            display: block;
            margin: auto;
            background: white;
            transition: width .3s;
            left: 0;
            right: 0;
            bottom: 0;
        }
        .link-list a:hover::after {
            width: 100%;
        }
        /* Logout button style */
        .logout-btn {
            margin-top: 20px;
        }
        .logout-btn button {
            background-color: transparent;
            border: none;
            color: white;
            font-family: 'Cynatar', Arial, sans-serif;
            font-size: 24px;
            cursor: pointer;
        }
        .logout-btn button:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <img src="logo.png" alt="CRYPTCRAFT" class="zoom">
    <ol class="link-list">
        <li class="zoom"><a href="temporal.php">Temporal Encryption</a></li>
        <li class="zoom"><a href="urlendecode.php">Image Password Encryption</a></li>
        <li class="zoom"><a href="temperature.php">Environmental and Geolocation based Encryption</a></li>
        <li class="zoom"><a href="decoy.php">Decoy Encryption</a></li>
        <li class="zoom"><a href="destruct.php">Destruct Encryption</a></li>
    </ol>

    <!-- Logout button -->
    <div class="logout-btn">
        <form action="logout.php" method="post">
            <button type="submit" name="logout">Logout</button>
        </form>
    </div>
</body>
</html>
