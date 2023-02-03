<?php
require_once 'funtion.php';

$username   = "";
$password   = "";
$error = '';

$db = new Connection();
$conn = $db->connect();

if(isset($_COOKIE['cookie_admin'])){
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username == '' or $password == '') {
        $error = "Silakan masukkan username dan password!";
    } else {
        $admin = new Admin($conn);
        $result = $admin->login($username, md5($password));

        if ($result->rowCount() == 1) {
            $user = $result->fetch();

            $cookie_name = "cookie_admin";
            $cookie_value = $user['id'];
            $cookie_time = time() + (60 * 60 * 24);
            setcookie($cookie_name, $cookie_value, $cookie_time, "/");

            $cookie_name = "cookie_name";
            $cookie_value = $user['name'];
            $cookie_time = time() + (60 * 60 * 24);
            setcookie($cookie_name, $cookie_value, $cookie_time, "/");

            header('Location: index.php');
            exit;
        } else {
            $error = "Username atau kata sandi salah";
        }
    }
} else {
    $error = '';
}
?>


<!DOCTYPE html>
<html lang="en">

<?php $title='Login'; include "./templates/header.php";?>

<body class="bg-gradient-primary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-6 col-lg-6 col-md-6">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                    <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 m-0" style="font-weight: bold;">myWash!</h1>
                                        <p class="mt-0 mb-4">Cucian numpuk ga usah was was!</p>
                                    </div>
                                    <form id="loginform" class="user needs-validation" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" role="form" autocomplete="off">
                                        <?php if($error) { ?>
                                            <div class="alert alert-danger" role="alert">
                                                <?php echo $error; ?>
                                            </div>
                                        <?php } ?>
                                        <div class="form-group">
                                            <input
                                                type="text"
                                                class="form-control form-control-user"
                                                id="username"
                                                value="<?php echo $username ?>"
                                                aria-describedby="emailHelp"
                                                name="username"
                                                placeholder="Masukan username...">
                                            <div class="invalid-feedback">
                                                Masukan username!
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <input
                                                type="password"
                                                name="password"
                                                value="<?php echo $password ?>"
                                                class="form-control form-control-user"
                                                id="exampleInputPassword"
                                                placeholder="Masukan kata sandi">
                                            <div class="invalid-feedback">
                                                Masukan kata sandi!
                                            </div>
                                        </div>
                                        <button class="btn btn-primary btn-user btn-block" type="submit">Login</button>
                                    </form>
                                    <hr>
                                    <div class="text-center mt-2">
                                        <a class="small" href="register.php">Buat Akun Baru!</a>
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