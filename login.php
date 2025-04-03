<?php
header('Access-Control-Allow-Origin: *');
define('SITE_ROOT', realpath(dirname(__FILE__)));

session_start();

if(!empty($_SESSION) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'])
    header("Location: index.php");
    
include_once "./config/database.php";
include_once "./controller/functions.php";

if (isset($_POST['logSub'])) {
    $unameErr = $pwdErr = '';
    $uname = isset($_POST['username']) && trim($_POST['username'] != '') ? mysqli_real_escape_string($con, trim($_POST['username'])) : '';
    $pwd = isset($_POST['password']) && trim($_POST['password'] != '') ? mysqli_real_escape_string($con, trim($_POST['password'])) : '';

    if ($uname == '')
        $unameErr = "Username is required";

    if ($pwd == '')
        $pwdErr = "Password is required";

    if ($unameErr == '' && $pwdErr == '') {
        $checkIsUser = json_decode(select_query($con, "*", "user_master", "enabled='1' AND email_id='" . $uname . "'", "", "", ""));

        if (!empty($checkIsUser) && $checkIsUser != '') {
            foreach ($checkIsUser as $user) {
                if ($user->password == $pwd) {
                    $getDepartment = json_decode(select_query($con, "*", "user_department_map_master", "uid=" . $user->id, "", "", ""));

                    $_SESSION['logged_in'] = 1;
                    $_SESSION['uid'] = $user->id;
                    $_SESSION['category'] = $user->category;
                    $_SESSION['name'] = ucwords($user->first_name . " " . $user->last_name);
                    $_SESSION['department'] = $getDepartment[0]->department;

                    header("Location: index.php");
                }
                $pwdErr = "Please enter correct password";
            }
        } else {
            $unameErr = "Username not found. Please check your username and try again";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Supply Chain</title>

    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/sweetalert2.min.css" type="text/css">
    <link rel="stylesheet" href="./assets/css/login-style.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">

    <script src="./assets/js/font-awesome.js" crossorigin="anonymous"></script>
    <script src="./assets/js/sweetalert2.min.js"></script>
    <script src="./assets/js/jquery.js"></script>
</head>

<body>
    <!-- Main Content -->
    <div class="container-fluid">
        <div class="row main-content bg-success text-center">
            <div class="col-md-4 text-center company__info">
                <span class="company__logo">
                    <h2><span class="fas fa-truck-loading"></span></h2>
                </span>
                <h4 class="company_title">Supply Chain</h4>
            </div>
            <div class="col-md-8 col-xs-12 col-sm-12 login_form">
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <form class="form-group" method="POST" action="">
                            <div class="row">
                                <input type="text" name="username" class="form__input" placeholder="Enter Username" <?php echo isset($uname) && $uname != '' ? 'value="' . $uname . '"' : ''; ?>>
                                <?php echo isset($unameErr) && $unameErr != '' ? '<small class="text-danger">' . $unameErr . '</small>' : ''; ?>
                            </div>
                            <div class="row">
                                <!-- <span class="fa fa-lock"></span> -->
                                <input type="password" name="password" class="form__input" placeholder="Enter Password">
                                <?php echo isset($pwdErr) && $pwdErr != '' ? '<small class="text-danger">' . $pwdErr . '</small>' : ''; ?>
                            </div>
                            <!-- <div class="row">
                                <input type="checkbox" name="remember_me" id="remember_me" class="form-check">
                                <label for="remember_me">Remember Me!</label>
                            </div> -->
                            <div class="row">
                                <input type="submit" name="logSub" value="Login" class="btn mx-auto">
                            </div>
                        </form>
                    </div>
                    <!-- <div class="row justify-content-center">
                        <p>Don't have an account?<br><a href="#">Register Here</a></p>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <div class="container-fluid text-center footer">
        Made in &hearts; by <a href="http://venturesystems.in/">Venture Systems</a></p>
    </div>
</body>

</html>