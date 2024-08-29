<?php
session_start();
require_once "config.php";
 
if (isset($_SESSION["username"])) {
  header("location: welcome.php");
  exit;
}

$username = $password = "";
$err = "";

if ($_SERVER['REQUEST_METHOD'] == "POST"){
    if(empty(trim($_POST['username'])) || empty(trim($_POST['password'])))
    {
        $err = "Please enter username + password";
        echo '<script type="text/javascript">
            window.onload = function () { alert("Please enter your username and password to continue."); }
        </script>';
    }
    else{
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
    }


if(empty($err))
{
    echo '<script type="text/javascript">
            window.onload = function () { alert("Something went wrong... cannot redirect!"); }
        </script>';
    $sql = "SELECT id, username, password FROM user WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $param_username);
    $param_username = $username;
    
    
    if(mysqli_stmt_execute($stmt)){
        mysqli_stmt_store_result($stmt);
        if(mysqli_stmt_num_rows($stmt) == 1)
                {
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt))
                    {
                        if(password_verify($password, $hashed_password))
                        {
                            session_start();
                            $_SESSION["username"] = $username;
                            $_SESSION["id"] = $id;
                            $_SESSION["loggedin"] = true;

                            header("location: welcome.php");
                            
                        }
                    }

                }

    }

} 
   


}


?>





<!DOCTYPE html>

<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Login</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <link rel="stylesheet" href="style1.css">
  </head>
  <body>
    <div class="center">
      <h1>Login</h1>
      <form method="post">
        <div class="txt_field">
          <input type="text" name="username" class="form-control" id="username" autocomplete="off" required>
          <span></span>
          <label>Username</label>
        </div>
        <div class="txt_field">
          <input name="password" type="password" class="form-control" id="exampleInputPassword1" autocomplete="off" required>
          <span></span>
          <label>Password</label>
        </div>
        <center><div class="g-recaptcha" data-sitekey="6LeW1k8lAAAAAMr0Pjy123UQzeYVgUH5i4OEDTC6"></div></center>
        <br>
        <input type="submit" value="Login" name="login" id="login">
        <div class="signup_link">
          Not a member? <a href="register.php">Signup</a>
        </div>
      </form>
    </div>

  </body>
  <script>
   $(document).on('click','#login', function(){
    var response = grecaptcha.getResponse();
    if(response.length==0){
      alert("Please check the captcha box.");
      return false;
      
    }
   });
  </script>
</html>
