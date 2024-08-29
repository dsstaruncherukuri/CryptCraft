<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit;
}

require_once "config.php";

// Retrieve logged in user's username
$loggedInUser = $_SESSION['username'];

// Retrieve form data
$secondUser = $_POST['username'];
$date = $_POST['date']; // Retrieve date from the date field
$time = $_POST['time']; // Retrieve time from the time field

// Check if both users exist in the database
$query = "SELECT COUNT(*) AS count FROM connector WHERE (user1='$loggedInUser' AND user2='$secondUser') OR (user1='$secondUser' AND user2='$loggedInUser')";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$count = $row['count'];

if ($count == 0) {
    echo "<script>alert('Users not found!');</script>";
    echo "<script>window.location.href='index.php';</script>";
} else {
    // Update date and time for the users
    $updateQuery = "UPDATE connector SET date='$date', time='$time' WHERE (user1='$loggedInUser' AND user2='$secondUser') OR (user1='$secondUser' AND user2='$loggedInUser')";
    mysqli_query($conn, $updateQuery);
    echo "<script>alert('Date and time updated successfully!');</script>";
    header("Location: welcome.php"); // Redirect to welcome page
}

mysqli_close($conn);
?>
