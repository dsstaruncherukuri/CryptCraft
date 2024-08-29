<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get the connection ID from the session
if (isset($_SESSION['connection_id'])) {
    $connectionId = $_SESSION['connection_id'];
    // Display the connection ID
    echo "Connection ID: $connectionId";
} else {
    echo "No connection ID provided.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User 1 Page</title>
</head>
<body>
<!-- Your HTML content here -->
</body>
</html>