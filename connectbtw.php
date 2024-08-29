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

// Define a function to check the flag value
function checkFlag($conn, $connection_id) {
    $stmt = $conn->prepare("SELECT flag FROM connector WHERE id = ?");
    $stmt->bind_param("i", $connection_id);
    $stmt->execute();
    $stmt->bind_result($flag);
    $stmt->fetch();
    $stmt->close();
    return $flag;
}

$flag = checkFlag($conn, $connection_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connecting...</title>
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
            color: #333;
        }

        h3 {
            color: #009879;
            margin-bottom: 20px;
        }

        #timer {
            font-size: 18px;
            color: #666;
            margin-bottom: 20px;
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
        // Set the date we're counting down to (50 seconds from now)
        var countDownDate = new Date().getTime() + 25000; // 50 seconds in milliseconds

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
            
            // If the count down is over, check the flag value
            if (distance <= 0) {
                clearInterval(x); // Clear the interval to stop the timer

                // Make an AJAX request to check the flag value
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "check_flag.php", true);
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        var flag = parseInt(xhr.responseText);
                        if (flag === 2) {
                            window.location.href = "connect2.php";
                        } else {
                            window.location.href = "welcome.php";
                        }
                    }
                };
                xhr.send();
            }
        }, 1000); // Update every 1 second
    </script>
</body>
</html>

