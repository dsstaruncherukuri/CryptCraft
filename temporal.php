<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

date_default_timezone_set('Asia/Calcutta');
// Fetching the current date (day) along with time in 24 hour format
$currentHour = date('d H'); // Format: dd HH
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRYPTCRAFT</title>
    <style>
        * {
            box-sizing: border-box;
        }
        @font-face {
            font-family: '0arame';
            src: url('Arame-Regular.ttf') format('truetype');
        }
        body {
            background-image: url('1920x1080-clock-wallpapers-216128-2161246-2503557.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: white;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            padding: 20px;
        }
        h1 {
            font-family: '0arame', Arial, sans-serif;
            color: black;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 600px;
            margin-bottom: 20px;
        }
        .textbox, .small-text {
            width: 100%;
            max-width: 100%;
            padding: 15px;
            margin-bottom: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            background-color: #333;
            color: white;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-bottom: 40px;
        }
        .button-group button {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            background-color: #4CAF50; /* Green */
            color: white;
            transition: background-color .3s;
        }
        .button-group button:hover {
            background-color: #45a049;
        }
        .button-group button.decrypt {
            background-color: #f44336; /* Red */
        }
        .button-group button.decrypt:hover {
            background-color: #e53935;
        }
    </style>
</head>
<body>
    <h1>T E M P O R A L</h1>
    <h2 id="currentHour"></h2>
    <script>
    function handleEncryptDecrypt(isEncrypt) {
        // Get the current date and hour from the server-side PHP
        var currentHour = "<?php echo $currentHour; ?>".split(' ');
        var currentDay = parseInt(currentHour[0]);
        var currentHour = parseInt(currentHour[1]);

        // Get the values from the text boxes
        var container = isEncrypt ? document.getElementById('encryptContainer') : document.getElementById('decryptContainer');
        var textBox = container.querySelector('.textbox').value;
        var smallText = container.querySelector('.small-text').value;

        if (textBox && smallText) {
            var num = textBox;
            var key = smallText;

            // Validate key to ensure it consists of only numbers and is not equal to "0"
            if (!/^\d+$/.test(key) || key === "0") {
                alert("Key must contain only numbers and cannot be 0.");
                return;
            }

            var totalKey = key + currentDay + currentHour;
            var action = isEncrypt ? "encrypt" : "decrypt";

            // Create a form and submit it to result.php
            var form = document.createElement("form");
            form.method = "POST";
            form.action = "result.php";

            var textField = document.createElement("input");
            textField.type = "hidden";
            textField.name = "text";
            textField.value = textBox;

            var keyField = document.createElement("input");
            keyField.type = "hidden";
            keyField.name = "key";
            keyField.value = totalKey;

            var actionField = document.createElement("input");
            actionField.type = "hidden";
            actionField.name = "action";
            actionField.value = action;

            form.appendChild(textField);
            form.appendChild(keyField);
            form.appendChild(actionField);

            document.body.appendChild(form);
            form.submit();
        } else {
            alert('Please enter both values.');
        }
    }
</script>


    <div id="encryptContainer" class="container">
        <textarea class="textbox" placeholder="Enter text here..."></textarea>
        <input class="small-text" type="text" placeholder="Enter key here...">
        <div class="button-group">
            <button class="encrypt" onclick="handleEncryptDecrypt(true)">Encrypt</button>
        </div>
    </div>
    <div id="decryptContainer" class="container">
        <textarea class="textbox" placeholder="Enter text here..."></textarea>
        <input class="small-text" type="text" placeholder="Enter key here...">
        <div class="button-group">
            <button class="decrypt" onclick="handleEncryptDecrypt(false)">Decrypt</button>
        </div>
    </div>
</body>
</html>
