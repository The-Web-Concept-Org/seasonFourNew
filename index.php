<?php
include_once 'php_action/db_connect.php';
include_once 'includes/functions.php';
include_once 'includes/head.php';

// Auto login with remember_me cookie
if (!isset($_SESSION['userId']) && isset($_COOKIE['remember_user'])) {
    $user_id = $_COOKIE['remember_user'];
    $user = fetchRecord($connect, "users", "user_id", $user_id);

    if ($user) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['userId'] = $user['user_id'];
        $_SESSION['branch_id'] = $user['branch_id'];
        header('location: dashboard.php');
        exit;
    }
}

if (isset($_SESSION['userId'])) {
    header('location: dashboard.php');  
    exit;
}

$msg = $sts = "";
if ($_POST) {    
    $username = $_POST['username'];
    $password = $_POST['pass'];

    if (empty($username) || empty($password)) {
        if ($username == "") {
            $msg = "Username is required";
            $sts = "danger";
        } 

        if ($password == "") {
            $msg = "Password is required";
            $sts = "danger";
        }
    } else {
        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = $connect->query($sql);

        if ($result->num_rows == 1) {
            $password = md5($password);
            $mainSql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
            $mainResult = $connect->query($mainSql);

            if ($mainResult->num_rows == 1) {
                $value = $mainResult->fetch_assoc();
                $user_id = $value['user_id'];
                $branch_id = $value['branch_id'];

                $_SESSION['user_id'] = $user_id;
                $_SESSION['userId'] = $user_id;
                $_SESSION['branch_id'] = $branch_id;

                if (isset($_POST['remember_me'])) {
                    setcookie("remember_user", $user_id, time() + (86400 * 30), "/");
                }

                header('location: dashboard.php');
                exit;
            } else {
                $msg = "Incorrect email/password combination";
                $sts = "danger";
            }
        } else {
            $msg = "Email does not exist";
            $sts = "danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
</head>
<body class="light">
<div class="container vh-100">
    <div class="row align-items-center h-100">
        <div class="col-lg-4 col-md-5 col-10 mx-auto text-center bg-white shadow rounded">
            <form action="" method="POST">
                <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="index.php">
                    <img src="img/logo/<?=$get_company['logo']?>" style="width: 100px;height: 100px;">
                </a>
                <h1 class="h4 mb-3"><?=$get_company['name']?></h1>
                <h1 class="h6 mb-3">Sign in</h1>
                <?=@getMessage($msg, $sts)?></h1>
                <div class="form-group">
                    <label for="inputEmail" class="sr-only">Email address</label>
                    <input type="text" id="inputEmail" class="form-control form-control-lg" placeholder="Email address" required name="username" autofocus>
                </div>
                <div class="form-group">
                    <label for="inputPassword" class="sr-only">Password</label>
                    <input type="password" name="pass" id="inputPassword" class="form-control form-control-lg" placeholder="Password" required>
                </div>
                <div class="checkbox mb-3">
                    <label>
                        <input type="checkbox" name="remember_me" value="1"> Stay logged in
                    </label>
                </div>
                <button class="btn btn-lg btn-admin btn-block" type="submit">Signin</button>
                <p class="mt-5 mb-3 text-muted">TWC&copy;2025</p>
            </form>
        </div>
    </div>
</div>
<?php include_once 'includes/foot.php'; ?>
</body>
</html>
