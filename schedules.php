<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Update Time and Date</title>
<style>
  body {
    font-family: Arial, sans-serif;
    background-color: #f0f0f0;
    margin: 0;
    padding: 0;
  }

  form {
    max-width: 400px;
    margin: 50px auto;
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  }

  label {
    display: block;
    margin-bottom: 10px;
    font-weight: bold;
  }

  input[type="text"], input[type="time"], input[type="date"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
  }

  input[type="submit"] {
    background-color: #009879;
    color: #fff;
    border: none;
    padding: 12px 20px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
  }

  input[type="submit"]:hover {
    background-color: #007b67;
  }

  .format-info {
    font-size: 12px;
    color: #999;
  }
</style>
</head>
<body>
  <form action="update.php" method="post">
    <label for="username">Username of 2nd user:</label>
    <input type="text" id="username" name="username"><br><br>
    <label for="date">Date (dd/mm/yyyy):</label>
    <input type="date" id="date" name="date" required><br><br>
    <label for="time">Date and Time (hh:mm:ss):</label>
<input type="text" id="time" name="time" pattern="[0-9]{2}:[0-9]{2}:[0-9]{2}" required>
    <input type="submit" value="Submit">
  </form>
</body>
</html>
