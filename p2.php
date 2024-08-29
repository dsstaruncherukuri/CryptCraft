<?php
session_start();
require_once "config.php";

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Check if session variables p and g are set
if (isset($_SESSION['p']) && isset($_SESSION['g'])) {
    // Retrieve the values of p and g
    $p = $_SESSION['p'];
    $g = $_SESSION['g'];
} else {
    // If session variables are not set, handle appropriately
    $p = "N/A";
    $g = "N/A";
}

// Define variables to hold error messages
$errors = [];

// Check if the form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the private key is provided
    if (isset($_POST["private"]) && $_POST["private"] !== "") {
        $private = $_POST["private"];

        // Validate the private key
        if (!is_numeric($private) || $private <= 0 || $private >= $p) {
            $errors[] = "Private key should be a positive integer less than $p.";
            header("Location: welcome.php");
        }

        // If there are no errors, proceed with the key exchange
        if (empty($errors)) {
            // Continue with the key exchange process
            $_SESSION['private_key'] = $private;

            // Update the flag in the database to 1
            $stmt = $conn->prepare("UPDATE connector SET flag = 1 WHERE id = ?");
            $stmt->bind_param("i", $_SESSION['connection_id']);
            $stmt->execute();
            $stmt->close();

            // Ensure session id is set
            $_SESSION['connection_id'] = $_SESSION['connection_id'];

            // Redirect to connectbtw.php
            header("Location: connectbtw.php");
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
    <title>P2</title>
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
            <label for="private">Enter Private Data (1 to <?php echo $p - 1; ?>):</label><br>
            <input type="text" id="private" name="private"><br><br>
        </form>
    </div>

    <!-- Display the values of p and g -->
    <div class="container">
        <h3>Session Variables p and g:</h3>
        <p>Value of p: <?php echo $p; ?></p>
        <p>Value of g: <?php echo $g; ?></p>
        <p>Value of id: <?php echo $_SESSION['connection_id']; ?></p>
    </div>

    <p id="timer"></p>

    <script>
        // Set the date we're counting down to (20 seconds from now)
        var countDownDate = new Date().getTime() + 12000; // 20 seconds in milliseconds

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
                document.getElementById("timer").innerHTML = "Form is being submitted."; // Change the message
                document.getElementById("privateForm").submit(); // Submit the form
            }
        }, 1000); // Update every 1 second
    </script>
</body>
</html>
