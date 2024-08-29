<?php
session_start();
require_once "config.php";

// Redirect user to login page if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Check if session contains the connection ID
if (!isset($_SESSION['connection_id'])) {
    header("Location: error.php");
    exit();
}

// Check if session contains the necessary keys
if (!isset($_SESSION['public_key2']) || !isset($_SESSION['private_key']) || !isset($_SESSION['p'])) {
    header("Location: error.php");
    exit();
}

// Retrieve the values from the session
$public_key2 = $_SESSION['public_key2'];
$private_key1 = $_SESSION['private_key'];
$p = $_SESSION['p'];

// Fetch public_key1 from the database based on the connection ID
$stmt = $conn->prepare("SELECT public1 FROM connector WHERE id = ?");
$stmt->bind_param("i", $_SESSION['connection_id']);
$stmt->execute();
$stmt->bind_result($public_key1);
$stmt->fetch();
$stmt->close();

// Check if public_key1 is retrieved successfully
if (!$public_key1) {
    // Handle the case where public_key1 is not found
    echo "Public key 1 not found.";
    exit();
}

// First, calculate the exponentiation: $public_key2 raised to the power of $private_key1
$exponentiation_result = bcpow($public_key2, $private_key1);

// Then, perform the modular operation
$shared_secret_key = bcmod($exponentiation_result, $p);

// Update the flag value to 0 in the connector table
$update_stmt = $conn->prepare("UPDATE connector SET flag = 0 WHERE id = ?");
$update_stmt->bind_param("i", $_SESSION['connection_id']);
$update_stmt->execute();
$update_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shared Secret Key</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }
        p {
            font-size: 18px;
            color: #666;
            margin-bottom: 15px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        #timer {
            font-size: 16px;
            color: #999;
        }
    </style>
    <script>
        // Function to copy the shared secret key to the clipboard
        function copyToClipboard() {
            var copyText = document.getElementById("sharedSecretKey");
            navigator.clipboard.writeText(copyText.textContent).then(function() {
                alert("Copied to clipboard!");
            }, function(err) {
                alert("Failed to copy text: " + err);
            });
        }

        // Set the countdown duration (17 seconds)
        var countdownDuration = 17;

        // Function to update the timer on the page
        function updateTimer() {
            var timerElement = document.getElementById("timer");
            if (countdownDuration > 0) {
                timerElement.innerHTML = "Redirecting in " + countdownDuration + " seconds...";
                countdownDuration--;
                setTimeout(updateTimer, 1000);
            } else {
                window.location.href = 'welcome.php';
            }
        }

        // Start the timer when the page loads
        window.onload = function() {
            updateTimer();
        };
    </script>
</head>
<body>
    <div class="container">
        <h1>Shared Secret Key</h1>
        <p>The shared secret key calculated using the Diffie-Hellman formula is: <span id="sharedSecretKey"><?php echo $shared_secret_key; ?></span></p>
        <button onclick="copyToClipboard()">Copy to Clipboard</button>
        <p id="timer">Redirecting in 17 seconds...</p>
    </div>
</body>
</html>

