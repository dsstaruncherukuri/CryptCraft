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

// Retrieve the connection ID from the session
$connection_id = $_SESSION['connection_id'];


// Retrieve the public key 1 from the database based on the connection ID
$stmt = $conn->prepare("SELECT public1 FROM connector WHERE id = ?");
$stmt->bind_param("i", $connection_id);
$stmt->execute();
$stmt->bind_result($publicKey1);
$stmt->fetch();
$stmt->close();
$_SESSION['public_key1'] = $publicKey1;
// Check if the public key 1 is retrieved successfully
if (!$publicKey1) {
    // Handle the case where the public key 1 is not found
    echo "Public key 1 not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public Key 1</title>
    <script>
        // Countdown timer function
        function startCountdown(seconds) {
            var countdownElement = document.getElementById("countdown");
            var remainingSeconds = seconds;
            var countdownInterval = setInterval(function() {
                countdownElement.textContent = "Redirecting in " + remainingSeconds + " seconds...";
                remainingSeconds--;
                if (remainingSeconds < 0) {
                    clearInterval(countdownInterval);
                    window.location.href = 'shared_key_2.php';
                }
            }, 1000);
        }

        // Start countdown on page load
        window.onload = function() {
            startCountdown(10); // Start countdown for 10 seconds
        };
    </script>
</head>
<body>
    <h1>Public Key 1</h1>
    <p>The public key 1 retrieved from the database is: <?php echo htmlspecialchars($publicKey1); ?></p>
    <p id="countdown">Redirecting in 10 seconds...</p>
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

        #countdown {
            color: #900;
            font-weight: bold;
        }
    </style>
