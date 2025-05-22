<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index2.php");
    exit;
}
 
// Include config file
include "config.php";
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            // Redirect user to welcome page
                            header("location: index2.php");
                        } else{
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else{
                    // Username doesn't exist, display a generic error message
                    $login_err = "Invalid username or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Login</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="qrlogo.jpg">
    <style>
        body{ 
            font: 14px sans-serif; 
            position: relative;
            min-height: 100vh;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url(bg.jpg);
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            z-index: -1;
        }
        .wrapper{ 
            width: 450px; 
            padding: 40px;
            background-color: rgba(255, 255, 255, 0.95);
            box-shadow: 
                0 0 20px rgba(0, 0, 0, 0.2),
                0 0 40px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            backdrop-filter: blur(5px);
            margin-top: 5%;
        }
        div{
            margin: auto;
            margin-top: 2%;
        }
        h2.login,p,label{
            color: #333;
        }
        h2.login{
            font-family: monospace;
            color: #00519B;
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }
        p,label{ 
            font-family: verdana;
        }
        .img {
            width: 100%;
            height: 100%;
            position: relative;
            border-radius: 8px;
        }
        a{
            color: #00519B;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        a:hover {
            color: #003366;
        }
        .wrapper {
            animation: fadeIn 1s ease-out forwards;
        }
        .fade-in {
            opacity: 0;
            animation-name: fadeIn;
            animation-duration: 1s;
            animation-delay: 0.5s;
            animation-fill-mode: forwards;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .login{
            color: maroon;
        }
        .form-control {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #00519B;
            box-shadow: 0 0 0 0.2rem rgba(0, 81, 155, 0.25);
        }
        .btn-primary {
            background-color: maroon;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #800000;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .alert {
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .invalid-feedback {
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
        }
        hr {
            border: none;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="wrapper"> <img src="indexlogo2.jpg" width="90%" height="90%" class="img"><hr>
        <h2 class="login" style="color:black">Login</h2>
        <p>Please fill in your credentials to login.</p>

        <?php 
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }        
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group" style="color:maroon">
                <input type="submit" class="btn btn-primary" value="Login" style="background-color:maroon;border:none;">
            </div>
            <p>Don't have an account?<a href="register.php" style="color:#006DD2">&nbspClick Here</a>.</p>
        </form>
    </div>
</body>
</html>