<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Function to fetch the p and g values from the database based on the connection ID
function getPGValuesFromDatabase($conn, $connectionId) {
    $stmt = $conn->prepare("SELECT p, g FROM connector WHERE id = ?");
    $stmt->bind_param("i", $connectionId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row;
}

// Get the connection ID from the session
if (isset($_SESSION['connection_id'])) {
    $connectionId = $_SESSION['connection_id'];
    // Fetch the p and g values from the database based on the connection ID
    $pg_values = getPGValuesFromDatabase($conn, $connectionId);
} else {
    // If no connection ID is provided in the session
    echo "No connection ID provided.";
    exit(); // Stop further execution
}

// Define variables to hold error messages
$errors = [];

// Check if the form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the private key is provided
    if (isset($_POST["private"]) && $_POST["private"] !== "") {
        $private = $_POST["private"];

        // Validate the private key
        if (!is_numeric($private) || $private <= 0 || $private >= $pg_values['p']) {
            $errors[] = "Private key should be a positive integer less than " . $pg_values['p'] . ".";
            header("Location: welcome.php");
        }

        // If there are no errors, proceed with the key exchange
        if (empty($errors)) {
            // Continue with the key exchange process
            // Redirect to connect3.php
            $_SESSION['private_key'] = $private;
            $_SESSION['p'] = $pg_values['p'];
            $_SESSION['g'] = $pg_values['g'];
            $_SESSION['connection_id'] = $connectionId;

            // Update the record with flag as 2 in the connector table
            $updateStmt = $conn->prepare("UPDATE connector SET flag = 2 WHERE id = ?");
            $updateStmt->bind_param("i", $connectionId);
            $updateStmt->execute();
            $updateStmt->close();

            header("Location: connectbtww.php");
            exit();
        }
    } else {
        $errors[] = "Private key is required.";
        header("Location: welcome.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        h3 {
            color: #009879;
            margin-bottom: 10px;
        }

        p {
            margin-bottom: 5px;
            color: #555;
        }

        ul {
            padding-left: 20px;
            margin-bottom: 10px;
        }

        li {
            color: #900;
            margin-bottom: 5px;
        }

        form {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"] {
            width: 200px;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Display the fetched p and g values -->
        <h3>Values of p and g:</h3>
        <p>Value of p: <?php echo $pg_values['p']; ?></p>
        <p>Value of g: <?php echo $pg_values['g']; ?></p>
        <p>Value of id: <?php echo $_SESSION['connection_id']; ?></p>

        <!-- Display the range of valid private keys -->
        <p>Range of valid private keys: 1 to <?php echo $pg_values['p'] - 1; ?></p>

        <!-- Display any errors -->
        <?php if (!empty($errors)): ?>
            <div>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Form to submit the private key -->
        <form id="privateForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <label for="private">Enter Private Data:</label>
            <input type="text" id="private" name="private">
        </form>
    </div>

    <p id="timer"></p>

    <script>
        // Set the date we're counting down to (20 seconds from now)
        var countDownDate = new Date().getTime() + 15000; // 15 seconds in milliseconds

        // Update the count down every 1 second
        var x = setInterval(function() {
            // Get today's date and time
            var now = new Date().getTime();
            
            // Find the distance between now and the count down date
            var distance = countDownDate - now;
            
            // Time calculations for seconds
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            // Output the result in an element with id="timer"
            document.getElementById("timer").innerHTML = "Redirecting in " + seconds + "s";
            
            // If the count down is over, submit the form
            if (distance <= 0) {
                clearInterval(x); // Clear the interval to stop the timer
                document.getElementById("privateForm").submit(); // Submit the form
            }
        }, 1000); // Update every 1 second
    </script>
</body>
</html>
