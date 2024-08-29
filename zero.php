<?php

// Function to update the flag value in the database
function updateFlagToZero($connection, $connectionId) {
    $sql = "UPDATE connector SET flag = 0 WHERE id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $connectionId);
    $stmt->execute();
    $stmt->close();
}

// Check if the session contains the connection ID
if (!isset($_SESSION['connection_id'])) {
    // Handle the case where the connection ID is not set in the session
    echo "Connection ID not found in session.";
    exit();
}

// Assuming you have a database connection
require_once "config.php";

// Check if the page was reloaded or accessed via the back button
if (isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0') {
    // Update the flag to 0 in the database
    updateFlagToZero($conn, $_SESSION['connection_id']);

    // Redirect to welcome.php
    header("Location: welcome.php");
    exit();
}
?>
