<?php
session_start();
require_once "config.php";

// Check if the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the logged-in username and the passed username
$loggedInUsername = $_SESSION['username'];
$passedUsername = $_SESSION['passed_username'];
$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['encrypt'])) {
        // Check for the row in destructive table with user1 as logged in user and user2 as passed username
        $sql = "SELECT id FROM destructive WHERE user1 = ? AND user2 = ?";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $loggedInUsername, $passedUsername);

            if ($stmt->execute()) {
                $stmt->store_result();
                
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($userId);
                    $stmt->fetch();
                    $_SESSION['user_id'] = $userId;
                    header("Location: encryptdesert.php");
                    exit();
                } else {
                    $message = "No record found for encryption.";
                }
            } else {
                $message = "Oops! Something went wrong. Please try again later.";
            }
            $stmt->close();
        } else {
            $message = "ERROR: Could not prepare query: $sql. " . $conn->error;
        }
    } elseif (isset($_POST['decrypt'])) {
        // Check for the row in destructive table with user1 as passed username and user2 as logged in user
        $sql = "SELECT id FROM destructive WHERE user1 = ? AND user2 = ?";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $passedUsername, $loggedInUsername);

            if ($stmt->execute()) {
                $stmt->store_result();
                
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($userId);
                    $stmt->fetch();
                    $_SESSION['user_id'] = $userId;
                    header("Location: decryptdesert.php");
                    exit();
                } else {
                    $message = "No record found for decryption.";
                }
            } else {
                $message = "Oops! Something went wrong. Please try again later.";
            }
            $stmt->close();
        } else {
            $message = "ERROR: Could not prepare query: $sql. " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encrypt or Decrypt</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, blue, orange);
            color: #333;
        }
        .container {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }
        button {
            background: #6e8efb;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
            margin: 10px;
        }
        button:hover {
            background: #5a75e3;
        }
        h1 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #444;
        }
        p {
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Encrypt or Decrypt</h1>
        <form method="POST">
            <button type="submit" name="encrypt">Encrypt</button>
            <button type="submit" name="decrypt">Decrypt</button>
        </form>
        <?php
        if (!empty($message)) {
            echo '<p>' . $message . '</p>';
        }
        ?>
    </div>
</body>
</html>
