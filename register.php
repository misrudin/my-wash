<?php
require_once 'funtion.php';

$db = new Connection();
$conn = $db->connect();
$username   = "";
$name   = "";
$password   = "";
$password_repeat   = "";
$error = '';

if(isset($_COOKIE['cookie_admin'])){
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $password_repeat = $_POST['password_repeat'];

    if ($name == '') {
        $error .= "<li>Silakan masukkan name!</li>";
    }
    if ($username == '') {
        $error .= "<li>Silakan masukkan username!</li>";
    }
    if ($password == '') {
        $error .= "<li>Silakan masukkan password!</li>";
    }
    if ($password_repeat == '') {
        $error .= "<li>Silakan masukkan konfirmasi!</li>";
    }
    if ($password != $password_repeat) {
        $error .= "<li>Konfirmasi password yang anda masukan tidak tepat!</li>";
    }
    if ($username != '' and $password != '' and $name != '' && $password == $password_repeat) {
        $admin = new Admin($conn);
        $row = $admin->findByUsername($username);
        $user = $row->fetch();
        if (empty($user)) {
            $result = $admin->register($name ,$username, md5($password));
            if ($result) {
                header('Location: login.php');
            } else {
                $error .= "<li>Terjadi error, silahkan coba lagi!</li>";
            }
        } else {
            $error .= "<li>Username sudah digunakan!</li>";
        }
    }
} else {
    $error = '';
}
?>

<!DOCTYPE html>
<html lang="en">

<?php $title='Register'; include "./templates/header.php";?>

<body class="bg-gradient-primary">

    <div class="container">

    <div class="row justify-content-center">

    <div class="col-xl-7 col-lg-7 col-md-7">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
            <div class="p-5">
                            <div class="text-center">
                            <div class="text-center">
                                        <h1 class="h4 text-gray-900 m-0" style="font-weight: bold;">myWash!</h1>
                                        <p class="mt-0 mb-4">Cucian numpuk ga usah was was!</p>
                                    </div>
                            </div>
                            <form id="loginform" class="user needs-validation" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" role="form" autocomplete="off">
                                        <?php if($error) { ?>
                                            <div class="alert alert-danger" role="alert">
                                                <ul class="m-0 px-3"><?php echo $error ?></ul>
                                            </div>
                                        <?php } ?>
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" id="exampleFirstName"
                                            placeholder="Nama lengkap" name="name" value="<?php echo $name ?>">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control form-control-user" id="exampleInputEmail"
                                        placeholder="Username" name="username" value="<?php echo $username ?>">
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="password" class="form-control form-control-user"
                                            id="exampleInputPassword" placeholder="Password" name="password" value="<?php echo $password ?>">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="password" class="form-control form-control-user"
                                            id="exampleRepeatPassword" placeholder="Repeat Password" name="password_repeat" value="<?php echo $password_repeat ?>">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    Register
                                </button>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="login.php">Sudah punya akun? Login!</a>
                            </div>
                        </div>
            </div>
        </div>

    </div>
    </div>
    </div>

    <?php include "./templates/script.php";?>

</body>

</html>