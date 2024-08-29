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
    // Handle the case where the connection ID is not set in the session
    header("Location: error.php");
    exit();
}

// Retrieve the connection ID from the session
$connection_id = $_SESSION['connection_id'];

// Retrieve the public2 key from the database based on the connection ID
$stmt = $conn->prepare("SELECT public2 FROM connector WHERE id = ?");
$stmt->bind_param("i", $connection_id);
$stmt->execute();
$stmt->bind_result($public_key2);
$stmt->fetch();
$stmt->close();
$_SESSION['public_key2'] = $public_key2;
// Check if public2 key is found
if (!$public_key2) {
    // Handle the case where the public2 key is not found
    header("Location: error.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public Key 2</title>
    <script>
        // Set the countdown duration (10 seconds)
        var countdownDuration = 10;

        // Function to update the timer on the page
        function updateTimer() {
            var timerElement = document.getElementById("timer");
            if (countdownDuration > 0) {
                timerElement.innerHTML = "Redirecting in " + countdownDuration + " seconds...";
                countdownDuration--;
                setTimeout(updateTimer, 1000);
            } else {
                window.location.href = 'shared_secret.php';
            }
        }

        // Start the timer when the page loads
        window.onload = function() {
            updateTimer();
        };
    </script>
</head>
<body>
    <h1>Public Key 2</h1>
    <p>The public key 2 retrieved from the database is: <?php echo htmlspecialchars($public_key2); ?></p>
    <p id="timer">Redirecting in 10 seconds...</p>
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
            margin-bottom: 10px;
            color: #555;
        }

        #timer {
            color: #900;
            font-weight: bold;
        }
    </style>
