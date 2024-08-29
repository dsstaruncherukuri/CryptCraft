<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Assuming you have a database connection
require_once "config.php";

// Function to fetch flag value from the database
function fetchFlagFromDatabase($connection, $id) {
    // Assuming your table name is "connector"
    $sql = "SELECT flag FROM connector WHERE id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['flag'];
}

// Assuming you have already started the session and stored the ID
$id = $_SESSION['connection_id'];

// Fetch flag value from the database
$flag = fetchFlagFromDatabase($conn, $id); // Use $conn instead of $connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timer Page</title>
    <style>
        body {
            background-color: black;
            color: white;
            font-family: sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        #timer {
            font-size: 36px;
        }

        #timerMessage {
            font-size: 24px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div id="timer">30</div>
    <div id="timerMessage"></div>

    <script>
        // Set initial timer value
        let timerValue = 30;
        document.getElementById("timer").textContent = timerValue;

        // Countdown timer
        const timerInterval = setInterval(() => {
            timerValue--;
            document.getElementById("timer").textContent = timerValue;
            
            // If timer reaches 0
            if (timerValue === 0) {
                clearInterval(timerInterval);

                // Make an AJAX request to check the flag value
                const xhr = new XMLHttpRequest();
                xhr.open("GET", "check_flag.php", true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const flag = parseInt(xhr.responseText);
                        // Redirect based on flag value
                        if (flag === 1) {
                            window.location.href = "p1.php";
                        } else {
                            window.location.href = "welcome.php";
                        }
                    }
                };
                xhr.send();
            }
        }, 1000);
    </script>
</body>
</html>
