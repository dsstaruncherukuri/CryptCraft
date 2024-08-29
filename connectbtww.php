<?php
// Start session to store user data
session_start();

// Include configuration file
require_once "config.php";

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Retrieve username from session
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome and Timer Pages</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            text-align: center;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        h1 {
            color: #009879;
            margin-bottom: 20px;
        }

        p {
            color: #666;
            margin-bottom: 10px;
        }

        .timer-container {
            text-align: center;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .timer-container p {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo $username; ?>!</h1>
        <p>This is a basic PHP page demonstrating session and require_once functionalities.</p>
        <p>You are logged in as <?php echo $username; ?>.</p>
        <p>Feel free to explore the content!</p>
    </div>

    <div class="timer-container">
        <h3>Timer Page</h3>
        <p>Timer for 25 seconds:</p>
        <p id="timer"></p>
    </div>

    <script>
        // Set the date we're counting down to (25 seconds from now)
        var countDownDate = new Date().getTime() + 20000; // 25 seconds in milliseconds

        // Update the count down every 1 second
        var x = setInterval(function() {
            // Get today's date and time
            var now = new Date().getTime();
            
            // Find the distance between now and the count down date
            var distance = countDownDate - now;
            
            // Time calculations for seconds
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            // Output the result in an element with id="timer"
            document.getElementById("timer").innerHTML = "Redirecting in " + seconds + "s";
            
            // If the count down is over, redirect the user to connect3.php
            if (distance <= 0) {
                clearInterval(x); // Clear the interval to stop the timer

                // Redirect to connect3.php after 25 seconds
                window.location.href = "connect3.php";
            }
        }, 1000); // Update every 1 second
    </script>
</body>
</html>

