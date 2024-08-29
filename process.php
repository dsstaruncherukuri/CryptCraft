<?php
session_start();
require_once "config.php";

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Check if the form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the necessary data is provided in the POST request
    if (isset($_POST["selected_p"]) && isset($_POST["selected_g"])) {
        // Retrieve the selected p and g values from the POST data
        $selected_p = $_POST["selected_p"];
        $selected_g = $_POST["selected_g"];
        
        // Retrieve the user ID from the session
        $user_id = $_SESSION['connection_id']; // Assuming you store the user's ID in the session
        
        // Prepare and execute the SQL UPDATE statement to update p and g values
        $stmt_update = $conn->prepare("UPDATE connector SET p = ?, g = ? WHERE id = ?");
        $stmt_update->bind_param("iii", $selected_p, $selected_g, $user_id);
        
        if ($stmt_update->execute()) {
            // Update successful, set the session variables and redirect to p2.php
            $_SESSION['p'] = $selected_p;
            $_SESSION['g'] = $selected_g;
            header("Location: p2.php");
            exit();
        } else {
            // Error occurred during update, handle appropriately (e.g., display an error message)
            echo "Error updating record: " . $conn->error;
        }
        
        // Close the prepared statement
        $stmt_update->close();
    } else {
        // If the required data is not provided, handle appropriately (e.g., display an error message)
        echo "Error: selected_p or selected_g not set in POST data";
    }
} 
?>