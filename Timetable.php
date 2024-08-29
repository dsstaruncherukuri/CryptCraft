<?php
session_start();

// Function to display records where the logged-in user exists
function displayRecords($conn, $loggedInUser) {
    $query = "SELECT user1, user2, date, time FROM connector WHERE user1='$loggedInUser' OR user2='$loggedInUser'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        echo "<table class='styled-table'>";
        echo "<tr><th>User 1</th><th>User 2</th><th>Date (YYYY/MM/DD)</th><th>Time</th></tr>";
        while($row = mysqli_fetch_assoc($result)) {
            echo "<tr><td>" . $row["user1"] . "</td><td>" . $row["user2"] . "</td><td>" . $row["date"] . "</td><td>" . $row["time"] . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "No records found.";
    }
}

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit;
}

require_once "config.php";

// Retrieve logged in user's username
$loggedInUser = $_SESSION['username'];

// Display records where the logged-in user exists
displayRecords($conn, $loggedInUser);

mysqli_close($conn);
?>

<?php
// Generate dynamic CSS styles
$dynamicStyles = "
    .styled-table {
        width: 100%;
        border-collapse: collapse;
        margin: 25px 0;
        font-family: Arial, sans-serif;
    }
    
    .styled-table th {
        background-color: #009879;
        color: white;
        font-size: 16px;
        font-weight: bold;
    }
    
    .styled-table th,
    .styled-table td {
        padding: 12px 15px;
        border: 1px solid #dddddd;
        text-align: left;
    }
    
    .styled-table tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    
    .styled-table tr:hover {
        background-color: #ddd;
    }
    
    .styled-table td {
        font-size: 14px;
    }
";

// Output dynamic CSS styles
echo "<style>$dynamicStyles</style>";
?>
