<?php
session_start();
require_once "config.php";

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo "0"; // Invalid response
    exit();
}

// Ensure session id is set
if (!isset($_SESSION['connection_id'])) {
    echo "0"; // Invalid response
    exit();
}

$connection_id = $_SESSION['connection_id'];

// Retrieve the value of the flag from the database
$stmt = $conn->prepare("SELECT flag FROM connector WHERE id = ?");
$stmt->bind_param("i", $connection_id);
$stmt->execute();
$stmt->bind_result($flag);
$stmt->fetch();
$stmt->close();

echo $flag;
?>
