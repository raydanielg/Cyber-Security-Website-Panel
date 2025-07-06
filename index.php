<?php
$configfile = 'config.php';
if (!file_exists($configfile)) {
    echo '<meta http-equiv="refresh" content="0; url=install" />';
    exit();
}

include "config.php";

if(!isset($_SESSION)) {
    session_start();
}

if (isset($_SESSION['sec-username'])) {
    $uname = $_SESSION['sec-username'];
    if ($uname == $settings['username']) {
        echo '<meta http-equiv="refresh" content="0; url=dashboard.php" />';
        exit;
    }
}

$_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_SPECIAL_CHARS);
$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

$error = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Ray Daniel">
    <meta name="generator" content="Project SECURITY" />
    <meta name="robots" content="noindex, nofollow">
    <title>Project SECURITY &rsaquo; Admin Panel</title>

    <!-- CSS -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.7.1/css/all.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/img/favicon.png">
    
    <style>
        body {
            background-color: #f0f2f5;
        }
        .login-container {
            margin-top: 100px;
            width: 300px;
            margin-left: auto;
            margin-right: auto;
        }
        .login-box {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .login-header {
            text-align: center;
            margin-bottom: 20px;
            color: #343a40;
        }
        .form-control {
            border-radius: 3px;
            margin-bottom: 10px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            border-radius: 3px;
            padding: 8px 15px;
            width: 100%;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .alert-danger {
            border-radius: 3px;
            margin-bottom: 10px;
        }
        .remember-me {
            margin-top: 10px;
            margin-bottom: 10px;
        }
        #error-notification {
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
        }
    </style>
</head>
<body class="login-page">
    <div id="error-notification">
        <?php
        if (isset($_POST['signin'])) {
            $ip = addslashes(htmlentities($_SERVER['REMOTE_ADDR']));
            if ($ip == "::1") {
                $ip = "127.0.0.1";
            }
            @$date = @date("d F Y");
            @$time = @date("H:i");

            $username = mysqli_real_escape_string($mysqli, $_POST['username']);
            $password = hash('sha256', $_POST['password']);

            if ($username == $settings['username'] && $password == $settings['password']) {

                $checklh = $mysqli->query("SELECT id FROM `psec_logins` WHERE `username`='$username' AND ip='$ip' AND date='$date' AND time='$time' AND successful='1'");
                if (mysqli_num_rows($checklh) == 0) {
                    $log = $mysqli->query("INSERT INTO `psec_logins` (username, ip, date, time, successful) VALUES ('$username', '$ip', '$date', '$time', '1')");
                }

                $_SESSION['sec-username'] = $username;

                echo '<meta http-equiv="refresh" content="0;url=dashboard.php">';
            } else {
                $checklh = $mysqli->query("SELECT id FROM `psec_logins` WHERE `username`='$username' AND ip='$ip' AND date='$date' AND time='$time' AND successful='0'");
                if (mysqli_num_rows($checklh) == 0) {
                    $log = $mysqli->query("INSERT INTO `psec_logins` (username, ip, date, time, successful) VALUES ('$username', '$ip', '$date', '$time', '0')");
                }

                echo '
                <div class="alert alert-danger">
                      <i class="fas fa-exclamation-circle"></i> The entered <strong>Username</strong> or <strong>Password</strong> is incorrect.
                </div>';
                $error = 1;
            }
        }
        ?>
    </div>
    <div class="container login-container">
        <div class="login-box">
            <div class="login-header">
                <h4>Admin Login</h4>
            </div>
            <form action="" method="post">
                <input type="username" name="username" class="form-control" placeholder="Username" required>
                <input type="password" name="password" class="form-control" placeholder="Password" required>
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember Me</label>
                </div>
                <button type="submit" name="signin" class="btn btn-primary">Sign In</button>
            </form>
            <p class="mb-0 text-center">Ray Daniel</p>
        </div>
    </div>
</body>
</html>