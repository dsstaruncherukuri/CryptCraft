<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $text = $_POST['text'];
    $key = $_POST['key'];
    $action = $_POST['action'];

    // AES encryption and decryption functions
    function encrypt($plaintext, $key) {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $ciphertext = openssl_encrypt($plaintext, 'aes-256-cbc', $key, 0, $iv);
        return base64_encode($iv . $ciphertext);
    }

    function decrypt($ciphertext, $key) {
        $ciphertext = base64_decode($ciphertext);
        $iv_length = openssl_cipher_iv_length('aes-256-cbc');
        $iv = substr($ciphertext, 0, $iv_length);
        $ciphertext = substr($ciphertext, $iv_length);
        $decryptedText = openssl_decrypt($ciphertext, 'aes-256-cbc', $key, 0, $iv);
        if ($decryptedText === false) {
            return ""; // Return an empty string to indicate decryption failure
        } else {
            return $decryptedText;
        }
    }

    // Hash the key to ensure it is the correct length for AES-256
    $hashedKey = hash('sha256', $key, true);

    if ($action === 'encrypt') {
        $resultText = encrypt($text, $hashedKey);
        $message = "Encryption successful!";
    } else {
        $decryptedText = decrypt($text, $hashedKey);
        if ($decryptedText === "") {
            $resultText = "Decryption failed. Invalid key or corrupted data.";
            // Include the attempt to decrypt with the wrong key
            $wrongDecryptedText = openssl_decrypt(base64_decode($text), 'aes-256-cbc', $hashedKey, 0, substr(base64_decode($text), 0, openssl_cipher_iv_length('aes-256-cbc')));
            $message = "Decryption failed!";
        } else {
            $resultText = $decryptedText;
            $message = "Decryption successful!";
            $wrongDecryptedText = "";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Result</title>
    <style>
        body {
            background-color: black;
            margin: 0;
            font-family: Arial, sans-serif;
            color: white;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .result {
            padding: 20px;
            border: 1px solid white;
            border-radius: 5px;
            background-color: transparent;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
        }
        .result h1 {
            margin: 0 0 20px;
            font-family: 'Airstrike', sans-serif;
        }
        .result p {
            margin: 10px 0;
            line-height: 1.5;
            font-family: 'Airstrike', sans-serif;
        }
        .failed {
            color: red;
            font-family: 'Airstrike', sans-serif;
        }
        .result textarea {
            border: none;
            background-color: transparent;
            color: <?php echo $action === 'encrypt' ? 'white' : 'red'; ?>;
            font-family: 'Airstrike', sans-serif;
            font-size: 16px;
            margin-top: 10px;
            padding: 10px;
            width: calc(100% - 40px); /* Adjust width and margin as needed */
            margin-right: 20px; /* Adjust margin */
            box-sizing: border-box;
            resize: none;
            overflow: auto;
            height: 100px; /* Adjust height as needed */
        }
        .copy-button {
            cursor: pointer;
            background-color: white;
            color: black;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 10px;
            font-family: 'Airstrike', sans-serif;
            width: calc(100% - 40px); /* Adjust width and margin as needed */
            box-sizing: border-box;
        }
        .copy-button:hover {
            background-color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="result">
            <?php if ($action === 'encrypt') : ?>
                <textarea id="encryptedText" readonly><?php echo $resultText; ?></textarea>
                <button class="copy-button" onclick="copyToClipboard('encryptedText')">COPY ENCRYPTED TEXT</button>
            <?php else: ?>
                <p><?php echo htmlspecialchars($message); ?></p>
                <?php if ($decryptedText !== "") : ?>
                    <textarea id="decryptedText" readonly><?php echo $resultText; ?></textarea>
                    <button class="copy-button" onclick="copyToClipboard('decryptedText')">COPY DECRYPTED TEXT</button>
                <?php endif; ?>
                <?php if ($action === 'decrypt' && $message === "Decryption failed!") : ?>
                    <?php if ($wrongDecryptedText === false || $wrongDecryptedText === null) : ?>
                        <p class="failed">Incorrect Key</p>
                    <?php else: ?>
                        <p class="failed">Incorrect Key</p>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function copyToClipboard(id) {
            var textarea = document.getElementById(id);
            textarea.select();
            document.execCommand("copy");
            alert("Text copied to clipboard!");
        }
    </script>
</body>
</html>






