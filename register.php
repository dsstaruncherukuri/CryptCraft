<?php
require_once "config.php";

$username = $password = $confirm_password = $flag = "";
$username_err = $password_err = $confirm_password_err = $flag_err = "";

if ($_SERVER['REQUEST_METHOD'] == "POST"){

    if(empty(trim($_POST["username"]))){
        $username_err = "Username cannot be blank";
        echo '<script type="text/javascript">

            window.onload = function () { alert("Username cannot be empty. Please enter the username."); }

        </script>';
    }
    else{
        $sql = "SELECT id FROM user WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if($stmt)
        {
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            $param_username = trim($_POST['username']);

            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1)
                {
                    $username_err = "This username is already taken"; 
                    echo '<script type="text/javascript">
            window.onload = function () { alert("Username already taken. Enter another username to continue."); }
        </script>';
                }
                else{
                    $username = trim($_POST['username']);
                }
            }
            else{
                echo "Something went wrong";
                echo '<script type="text/javascript">
            window.onload = function () { alert("Something went wrong. Please try later."); }
        </script>';
            }
        }
    mysqli_stmt_close($stmt);
}

$uppercase = preg_match('@[A-Z]@', trim($_POST['password']));
$lowercase = preg_match('@[a-z]@', trim($_POST['password']));
$number    = preg_match('@[0-9]@', trim($_POST['password']));
$specialChars = preg_match('@[^\w]@', trim($_POST['password']));


if(empty(trim($_POST['password']))){
    $password_err = "Password cannot be blank";
    echo '<script type="text/javascript">
            window.onload = function () { alert("Password cannot be blank"); }
        </script>';
}
elseif(strlen(trim($_POST['password'])) < 8 || !$uppercase || !$lowercase || !$number || !$specialChars){
    $password_err = "Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.";
    echo '<script type="text/javascript">
            window.onload = function () { alert("Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character."); }
        </script>';
}

else{
    $password = trim($_POST['password']);
    $flag=0;
}

if(trim($_POST['password']) !=  trim($_POST['confirm_password'])){
    $password_err = "Passwords should match";
    echo '<script type="text/javascript">
            window.onload = function () { alert("Passwords should match"); }
        </script>';
}

$randomNumber = mt_rand(1000, 9999);
$ciphering = "AES-128-CTR";
$iv_length = openssl_cipher_iv_length($ciphering);
$options = 0;
$encryption_iv = '1234567891011121';
$encryption_key = "DEEHECHKAY";
$encryption = openssl_encrypt($randomNumber, $ciphering, $encryption_key, $options, $encryption_iv);
if(empty($username_err) && empty($password_err) && empty($confirm_password_err))
{
    $sql = "INSERT INTO user (username, password, flag) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt)
    {
        mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_password, $flag);

        $param_username = $username;
        $param_password = password_hash($password, PASSWORD_DEFAULT);
        $param_flag = $flag; 
        if (mysqli_stmt_execute($stmt))
        {
            header("location: login.php");
        }
        else{
            echo "Something went wrong... cannot redirect!";
            echo '<script type="text/javascript">
            window.onload = function () { alert("Something went wrong... cannot redirect!"); }
        </script>';
        }
    
    mysqli_stmt_close($stmt);
}
}
mysqli_close($conn);
}

?>







<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body {
  font-family: Arial, Helvetica, sans-serif;
  background-image:url('https://wallpapers.com/images/hd/cool-photos-background-1920-x-1080-9cnqcjs5o3rb8raa.jpg');
}
* {
  box-sizing: border-box;
}

.container {
  padding: 25px;
  background-color: white;
}

input[type=text], input[type=password] , input[type=email]{
  width: 100%;
  padding: 15px;
  margin: 5px 0 22px 0;
  display: inline-block;
  border: none;
  background: #f1f1f1;
}

input[type=text]:focus, input[type=password]:focus {
  background-color: #ddd;
  outline: none;
}

hr {
  border: 1px solid #f1f1f1;
  margin-bottom: 25px;
}

.registerbtn {
  background-color: rgb(7, 108, 139) ;
  color: white;
  padding: 16px 20px;
  margin: 8px 0;
  border: none;
  cursor: pointer;
  width: 50%;
  opacity: 0.9;
}

.registerbtn:hover {
  opacity: 1;
}

a {
  color: dodgerblue;
}

.signin {
  background-color: #f1f1f1;
  text-align: left;
}
</style>

</head>
<body  style="padding-left: 200px;padding-right: 200px;">
    <br><br><br>
<form method="post" action="">
  <div class="container">
    <h1>Register</h1>
    <hr>
    <label for="inputEmail4"><b>Username</b></label><br>
    <input type="text" class="form-control" placeholder="Enter Username" name="username" id="username" autocomplete="off" required>
<br>
    <label for="psw"><b>Password (It must contain atleast 8 characters long 1 symbol, 1 numeric and a Capital Letter)</b></label><br>
    <input type="password" class="form-control" placeholder="Enter Password" name="password" id="password" autocomplete="off" required>
<br>
<label for="psw"><b> Re-enter Password</b></label><br>
    <input type="password" class="form-control" placeholder="confirm_password" name="confirm_password" id="confirm_password" autocomplete="off" required>
<br>
   <center> <input type="submit" value="Submit" class="registerbtn" id="submit"></center>
  </div>
  
  <div class="container signin">
    <center><p>Already have an account? <a href="login.php">Sign in</a>.</p></center>
  </div>
</form>
<br>
<br>
<Br>
</body>
</html>
