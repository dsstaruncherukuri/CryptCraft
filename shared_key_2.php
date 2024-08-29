<?php
session_start();
require_once "config.php";

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Check if the session contains the connection ID
if (!isset($_SESSION['connection_id'])) {
    // Handle the case where the connection ID is not set in the session
    echo "Connection ID not found in session.";
    exit();
}

// Check if the session contains the necessary keys
if (!isset($_SESSION['public_key1']) || !isset($_SESSION['private_key']) || !isset($_SESSION['p'])) {
    echo "Necessary keys not found in session.";
    exit();
}

// Retrieve the values from the session
$public_key1 = $_SESSION['public_key1'];
$private_key1 = $_SESSION['private_key'];
$p = $_SESSION['p'];

// Calculate the shared secret key using the Diffie-Hellman formula
// First, calculate the exponentiation: $public_key1 raised to the power of $private_key1
$exponentiation_result = bcpow($public_key1, $private_key1);

// Then, perform the modular operation
$shared_secret_key = bcmod($exponentiation_result, $p);

// Update the flag value in the database
$updateStmt = $conn->prepare("UPDATE connector SET flag = 0 WHERE id = ?");
$updateStmt->bind_param("i", $_SESSION['connection_id']);
$updateStmt->execute();
$updateStmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shared Secret Key</title>
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
    <h1>Shared Secret Key</h1>
    <p>The shared secret key is: <span id="sharedSecretKey"><?php echo $shared_secret_key; ?></span></p>
    <button onclick="copyToClipboard()">Copy to Clipboard</button>
    <p id="timer"></p>
</body>
</html>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        h1 {
            color: #009879;
            margin-bottom: 20px;
        }

        p {
            color: #333;
            margin-bottom: 10px;
        }

        button {
            background-color: #009879;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #007a63;
        }

        #timer {
            color: #900;
            font-weight: bold;
        }
    </style>
