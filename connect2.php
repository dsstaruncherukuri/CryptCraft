<?php
session_start();
require_once "config.php";

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Ensure session id is set
if (!isset($_SESSION['connection_id'])) {
    header("Location: error.php");
    exit();
}

$connection_id = $_SESSION['connection_id'];

// Start a timer for 15 seconds
sleep(25);

// Fetch the values of private p and g from the database
$stmt = $conn->prepare("SELECT p, g FROM connector WHERE id = ?");
$stmt->bind_param("i", $connection_id);
$stmt->execute();
$stmt->bind_result($p, $g);
$stmt->fetch();
$stmt->close();

// Retrieve the private key from the session
$private_key = $_SESSION['private_key'];

// Calculate the public key for the user
$public_key = bcpowmod($g, $private_key, $p); // g^private_key mod p

// Store the public key in the database
$stmt_update = $conn->prepare("UPDATE connector SET public2 = ?, flag = 3 WHERE id = ?");
$stmt_update->bind_param("si", $public_key, $connection_id);
$stmt_update->execute();
$stmt_update->close();

// Redirect to the next page after 15 seconds
header("Refresh: 25; url=public2.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connecting...</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        h3 {
            color: #009879;
            margin: 20px 0;
            font-size: 24px;
        }

        #timer {
            font-size: 18px;
            color: #555;
        }

        /* Loader animation */
        .loader {
            border: 6px solid #f3f3f3;
            border-top: 6px solid #009879;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="loader"></div>
    <h3>Connecting, please wait...</h3>
    <p id="timer"></p>

    <script>
        // Set the date we're counting down to (15 seconds from now)
        var countDownDate = new Date().getTime() + 25000; // 15 seconds in milliseconds

        // Update the count down every 1 second
        var x = setInterval(function() {
            // Get today's date and time
            var now = new Date().getTime();
            
            // Find the distance between now and the count down date
            var distance = countDownDate - now;
            
            // Time calculations for seconds and minutes
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            var minutes = Math.floor(distance / (1000 * 60));
            
            // Output the result in an element with id="timer"
            document.getElementById("timer").innerHTML = "Redirecting in " + minutes + "m " + seconds + "s";
            
            // If the count down is over, redirect the user
            if (distance <= 0) {
                clearInterval(x); // Clear the interval to stop the timer
                window.location.href = "public2.php";
            }
        }, 1000); // Update every 1 second
    </script>
</body>
</html>
