<?php
session_start();
require_once "config.php";

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Initialize variables
$message = "";
$loggedInUsername = $_SESSION['username'];

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);

    // Convert both usernames to lowercase for case-insensitive comparison
    $loggedInUsernameLower = strtolower($loggedInUsername);
    $usernameLower = strtolower($username);

    // Check if the entered username is the same as the logged-in user's username (case insensitive)
    if ($usernameLower === $loggedInUsernameLower) {
        $message = "You cannot check your own username.";
    } else {
        // Prepare a select statement to check if the entered username exists
        $sql = "SELECT id, username FROM user WHERE LOWER(username) = ?";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_username);

            // Set parameters
            $param_username = $usernameLower;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Store result
                $stmt->store_result();

                // Check if username exists
                if ($stmt->num_rows == 1) {
                    // Fetch the actual username
                    $stmt->bind_result($userId, $actualUsername);
                    $stmt->fetch();

                    // Check if a record exists between the logged-in user and the entered username
                    $sql = "SELECT id FROM destructive WHERE user1 = ? AND user2 = ?";

                    if ($stmt = $conn->prepare($sql)) {
                        // Bind variables to the prepared statement as parameters
                        $stmt->bind_param("ss", $loggedInUsername, $actualUsername);

                        // Attempt to execute the prepared statement
                        if ($stmt->execute()) {
                            // Store result
                            $stmt->store_result();

                            // Check if a record exists
                            if ($stmt->num_rows == 1) {
                                // Fetch the id of the existing record
                                $stmt->bind_result($id);
                                $stmt->fetch();
                                $_SESSION['user_id'] = $id;
                            } else {
                                // Insert a new record into the destructive table
                                $sql = "INSERT INTO destructive (user1, user2) VALUES (?, ?)";

                                if ($stmt = $conn->prepare($sql)) {
                                    // Bind variables to the prepared statement as parameters
                                    $stmt->bind_param("ss", $loggedInUsername, $actualUsername);

                                    // Attempt to execute the prepared statement
                                    if ($stmt->execute()) {
                                        // Get the id of the new record
                                        $new_id = $stmt->insert_id;
                                        $_SESSION['user_id'] = $new_id;
                                    } else {
                                        $message = "Oops! Something went wrong. Please try again later.";
                                    }
                                } else {
                                    $message = "ERROR: Could not prepare query: $sql. " . $conn->error;
                                }
                            }

                            // Store the passed username in session and redirect to ede.php if a user_id is set in the session
                            if (isset($_SESSION['user_id'])) {
                                $_SESSION['passed_username'] = $actualUsername;
                                header("Location: ede.php");
                                exit();
                            }
                        } else {
                            $message = "Oops! Something went wrong. Please try again later.";
                        }

                        // Close statement
                        $stmt->close();
                    } else {
                        $message = "ERROR: Could not prepare query: $sql. " . $conn->error;
                    }
                } else {
                    $message = "No user found with username '$username'.";
                }
            } else {
                $message = "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        } else {
            $message = "ERROR: Could not prepare query: $sql. " . $conn->error;
        }
    }

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Username Input Form</title>
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
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            font-size: 16px;
            color: #555;
        }
        input[type="text"] {
            width: 100%;
            padding: 12px 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus {
            border-color: #6e8efb;
            outline: none;
        }
        input[type="submit"] {
            background: #6e8efb;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        input[type="submit"]:hover {
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
        <h1>Enter Your Username</h1>
        <p>Please provide your username to continue</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" onsubmit="return validateSearch()">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <input type="submit" value="Submit">
        </form>
        <?php
        if (!empty($message)) {
            echo '<p>' . $message . '</p>';
        }
        ?>
    </div>

    <script>
    function validateSearch() {
        var loggedInUsername = "<?php echo addslashes(strtolower($_SESSION['username'])); ?>";
        var searchInput = document.getElementById('username').value.toLowerCase();
        
        if (searchInput === loggedInUsername) {
            alert("You cannot check your own username.");
            return false; // Prevent form submission
        }
        return true; // Allow form submission
    }
    </script>
</body>
</html>
