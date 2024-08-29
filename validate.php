<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Validate</title>
</head>
<body>
<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Retrieve search data from the URL
if (isset($_GET['search'])) {
    $loggedInUsername = $_SESSION['username'];
    $searchData = $_GET['search'];

    // Prepare SQL statement to check if search data exists in the username column
    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->bind_param("s", $searchData);

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any rows are returned
    if ($result->num_rows > 0) {
        // Username exists in the database
        // Check if a connection already exists in either configuration
        $checkStmt = $conn->prepare("SELECT * FROM connector WHERE (user1 = ? AND user2 = ?) OR (user1 = ? AND user2 = ?)");
        $checkStmt->bind_param("ssss", $loggedInUsername, $searchData, $searchData, $loggedInUsername);

        // Execute the query
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        // If a connection exists, redirect to connect.php
        if ($checkResult->num_rows > 0) {
            // Retrieve user's column from the connector table
            $getColumnStmt = $conn->prepare("SELECT id, user1 FROM connector WHERE (user1 = ? AND user2 = ?) OR (user1 = ? AND user2 = ?)");
            $getColumnStmt->bind_param("ssss", $loggedInUsername, $searchData, $searchData, $loggedInUsername);
            $getColumnStmt->execute();
            $columnResult = $getColumnStmt->get_result();

            if ($columnResult->num_rows > 0) {
                $row = $columnResult->fetch_assoc();
                $userColumn = $row['user1'];
                $connectionId = $row['id'];

                // Store connection ID in session
                $_SESSION['connection_id'] = $connectionId;

                // Redirect based on the user's column
                if ($userColumn == $loggedInUsername) {
                    header("Location: connect.php");
                } else {
                    header("Location: user2.php");
                }
                exit();
            }
            $getColumnStmt->close();
        } else {
            // No existing connection, insert a new record
            $insertStmt = $conn->prepare("INSERT INTO connector (user1, user2, p, g, public1, public2) VALUES (?, ?, 0, 0, 0, 0)");
            $insertStmt->bind_param("ss", $loggedInUsername, $searchData);

            if ($insertStmt->execute()) {
                // Retrieve connection ID
                $connectionId = $insertStmt->insert_id;

                // Store connection ID in session
                $_SESSION['connection_id'] = $connectionId;

                // Redirect to appropriate page
                header("Location: connect.php");
                exit();
            } else {
                echo '<script>alert("Error creating record in connector table.");</script>';
                header("refresh:0;url=welcome.php");
            }
            $insertStmt->close();
        }
        $checkStmt->close();
    } else {
        // Username not found in the database
        echo '<script>
                alert("Sorry, username does not exist.");
                window.location.href = "welcome.php";
              </script>';
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    // No search data provided
    echo "No search data provided.";
}
?>


</body>
</html>
