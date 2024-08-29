<?php
session_start();

require 'config.php';

date_default_timezone_set('Asia/Kolkata');

function decryptText($encryptedText, $key) {
    list($encrypted_data, $iv) = explode('::', base64_decode($encryptedText), 2);
    return openssl_decrypt($encrypted_data, "aes-256-cbc", $key, 0, $iv);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['decrypt'])) {
    // Retrieve form data
    $key = $_POST['decrypt_key'];

    // Retrieve user id from session
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];

        // Retrieve encrypted text, time1, and counter from the database
        $sql = "SELECT encrypted_text, time2, counter FROM destructive WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $encryptedText, $time1, $counter);

        // Fetch the results
        mysqli_stmt_fetch($stmt);

        // Close the statement after fetching results
        mysqli_stmt_close($stmt);

        if (empty($encryptedText)) {
            echo "<script>alert('Encrypted text is not present.');</script>";
        } else {
            // Check if system time is less than time1
            if (strtotime(date("Y-m-d H:i:s")) < strtotime($time1)) {
                // Decrypt the text
                $decryptedText = decryptText($encryptedText, $key);
                if ($decryptedText !== false && ctype_print($decryptedText)) {
                    $correct_attempts = (int)$counter[0];
                    $incorrect_attempts = (int)$counter[1];
                    $correct_attempts++;

                    if ($correct_attempts < 3) {
                        $newCounter = $correct_attempts . $incorrect_attempts;
                        $sql = "UPDATE destructive SET counter = ? WHERE id = ?";
                        $stmt = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param($stmt, "si", $newCounter, $userId);
                        mysqli_stmt_execute($stmt);
                        $_SESSION['decrypted_text'] = $decryptedText;
                        $_SESSION['row_id'] = $userId;
                        echo " You have " . (3 - $correct_attempts) . " correct attempts left.";
                    } else {
                        // Delete encrypted text after 3 correct attempts
                        $sql = "UPDATE destructive SET encrypted_text = NULL, counter = '00' WHERE id = ?";
                        $stmt = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param($stmt, "i", $userId);
                        mysqli_stmt_execute($stmt);
                        echo "<script>alert('Maximum number of correct attempts exceeded! Encrypted text has been deleted.');</script>";
                        session_destroy();
                        echo "<script>window.location.href = 'home.php';</script>";
                    }
                } else {
                    $correct_attempts = (int)$counter[0];
                    $incorrect_attempts = (int)$counter[1];
                    $incorrect_attempts++;

                    if ($incorrect_attempts < 3) {
                        $newCounter = $correct_attempts . $incorrect_attempts;
                        $sql = "UPDATE destructive SET counter = ? WHERE id = ?";
                        $stmt = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param($stmt, "si", $newCounter, $userId);
                        mysqli_stmt_execute($stmt);
                        echo "Incorrect key. You have " . (3 - $incorrect_attempts) . " attempts left.";
                    } else {
                        // Delete encrypted text after 3 incorrect attempts
                        $sql = "UPDATE destructive SET encrypted_text = NULL, counter = '00' WHERE id = ?";
                        $stmt = mysqli_prepare($conn, $sql);
                        mysqli_stmt_bind_param($stmt, "i", $userId);
                        mysqli_stmt_execute($stmt);
                        echo "<script>alert('Attempts exceeded! Encrypted text has been deleted.');</script>";
                        session_destroy();
                        echo "<script>window.location.href = 'home.php';</script>";
                    }
                    // Clear the decrypted text from session if the key was incorrect
                    unset($_SESSION['decrypted_text']);
                }
            } else {
                // Delete encrypted text after time limit exceeded
                $sql = "UPDATE destructive SET encrypted_text = NULL, counter = '00' WHERE id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "i", $userId);
                mysqli_stmt_execute($stmt);
                echo "Time limit exceeded!";
            }
        }
    } else {
        echo "User ID not found in session.";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Decrypt Text</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        .container {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 400px;
            box-sizing: border-box;
        }

        h2 {
            color: #4a90e2;
            margin-bottom: 10px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            color: #555;
            text-align: left;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            margin-bottom: 20px;
            border: 1px solid #d1d3e2;
            border-radius: 8px;
            background-color: #f8f9fc;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4a90e2;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            padding: 12px 20px;
            font-size: 16px;
            margin-top: 20px;
            transition: background-color 0.3s ease;
            width: 100%;
            box-sizing: border-box;
        }

        input[type="submit"]:hover {
            background-color: #357ab7;
        }

        .message {
            margin-top: 20px;
            font-size: 14px;
            color: #e74a3b;
        }

        .form-section {
            margin-bottom: 40px;
        }

        .form-section:last-child {
            margin-bottom: 0;
        }

        .copy-button {
            display: block;
            margin-top: 10px;
            background-color: #4a90e2;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            padding: 8px 12px;
            font-size: 14px;
            transition: background-color 0.3s ease;
            text-align: center;
        }

        .copy-button:hover {
            background-color: #357ab7;
        }

        .timer {
            margin-top: 10px;
            font-size: 14px;
            color: #333;
        }
    </style>
    <script>
        function validateForm() {
            var key = document.getElementById('decrypt_key').value;
            if (key.trim() === "") {
                alert("Please enter the decryption key.");
                return false;
            }
            return true;
        }

        function startTimer(timerElementId, messageElementId) {
            var countdown = 10;
            var timerElement = document.getElementById(timerElementId);
            timerElement.style.display = 'block';
            timerElement.textContent = "Time left to view: " + countdown + " seconds";

            var timerInterval = setInterval(function() {
                countdown--;
                timerElement.textContent = "Time left to view: " + countdown + " seconds";

                if (countdown <= 0) {
                    clearInterval(timerInterval);
                    var messageElement = document.getElementById(messageElementId);
                    messageElement.style.display = 'none';
                }
            }, 1000); // 1 second interval
        }

        window.onload = function() {
            <?php if (isset($_SESSION['decrypted_text'])): ?>
                startTimer('decryptedTimer', 'decryptedTextMessage');
            <?php endif; ?>
        }

        function copyToClipboard(textElementId) {
            var copyText = document.getElementById(textElementId);
            var textArea = document.createElement("textarea");
            textArea.value = copyText.textContent;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand("Copy");
            textArea.remove();
            alert("Text copied to clipboard!");
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="form-section">
            <h2>Decrypt Text</h2>
            <form method="post" onsubmit="return validateForm()">
                <label for="decrypt_key">Key:</label>
                <input type="text" id="decrypt_key" name="decrypt_key" required>
                <input type="submit" name="decrypt" value="Decrypt">
            </form>
            <?php if (isset($_SESSION['decrypted_text'])): ?>
                <div id="decryptedTextMessage" class="message">
                    <p id="decryptedText">Decrypted Text: <?php echo htmlspecialchars($_SESSION['decrypted_text']); ?></p>
                    <p>Row ID: <?php echo htmlspecialchars($_SESSION['row_id']); ?></p>
                    <button class="copy-button" onclick="copyToClipboard('decryptedText')">Copy to Clipboard</button>
                    <p id="decryptedTimer" class="timer" style="display: none;"></p>
                </div>
            <?php endif; ?>
        </div>

        <div class="message">
            <?php
            if (isset($message)) {
                echo $message;
            }
            ?>
        </div>

        <div class="form-section">
            <h2>User Information</h2>
            <?php if (isset($_SESSION['user_id'])): ?>
                <p>User ID: <?php echo htmlspecialchars($_SESSION['user_id']); ?></p>
            <?php else: ?>
                <p>User ID not set in session.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
