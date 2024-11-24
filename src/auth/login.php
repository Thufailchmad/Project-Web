<?php
require_once('./../../vendor/autoload.php');
use Firebase\JWT\JWT;
use Dotenv\Dotenv;
require '../koneksi/koneksi.php';

$dotenv = Dotenv::createImmutable('../..');
$dotenv->load();
$useragent = $_SERVER['HTTP_USER_AGENT'];
session_start();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $error = [];

    if ($useragent == "android") {
        header('Content-Type: application/json');
    }

    $db = dbConnection();

    if (!isset($_POST['email']) || $_POST['email'] == '') {
        $error['email'] = "Email tidak boleh kosong";
    }

    if (!isset($_POST['password']) || $_POST['password'] == '') {
        $error['password'] = "Password tidak boleh kosong";
    }

    if (count($error) > 0) {
        if ($useragent == "android") {
            echo json_encode([
                'status' => "Login Gagal",
                'error' => $error
            ]);
            exit();
        }
        $str = urldecode(serialize($error));
        header("location: login.php?error=$str");
        exit();
    }

    try {
        $query = $db->prepare("SELECT * FROM user WHERE email = :email");
        $query->bindParam('email', $_POST['email']);
        $query->execute();
        if ($query->rowCount() == 0) {
            if ($useragent == "android") {
                echo json_encode([
                    'status' => 'Login Gagal',
                    'message' => 'Email atau password salah',
                ]);
                exit();
            }
            header("location: login.php?unregistered=true");
            exit();
        }
        $user = $query->fetch(PDO::FETCH_NAMED);
    } catch (Exception $e) {
        if ($useragent == "android") {
            echo json_encode([
                'status' => 'Login Gagal',
                'message' => 'Email atau password salah',
            ]);
            exit();
        }
        header("location: login.php");
        exit();
    }

    if (!password_verify($_POST['password'], $user['password'])) {
        if ($useragent == "android") {
            echo json_encode([
                'status' => 'Login Gagal',
                'message' => 'Email atau password salah',
            ]);
            exit();
        }
        header("location: login.php?password=true");
        exit();
    }

    if ($useragent == "android") {
        $exp = time() + 3600;
        unset($user['password']);

        $payload = [
            'user' => $user,
            'exp' => $exp
        ];

        $access_token = JWT::encode($payload, $_ENV['JWT_SECRET'], $_ENV['JWT_ALG']);
        echo json_encode([
            'status' => 'Login Berhasil',
            'access_token' => $access_token,
            'expiry' => $exp,
            "user_id"=>$user['id']
        ]);
        exit();
    }

    unset($user["password"]);
    $_SESSION["user"] = $user;
    if($user["role"] == 2){
        header("location: ../user/dashboard.php");
        exit();
    }else{
        header("location: ../admin");
        exit();
    }
}

$error = isset($_GET['error']) ? (unserialize(urldecode($_GET['error']))) : "";
$wrong = isset($_GET['wrong']) ? $_GET['wrong'] : false;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
   
    <link rel="stylesheet" href="../assets-frontend/css/style.css">
    <link rel="stylesheet" href="../assets-frontend/css/4.5.2-bootstrap.min.css">

    <style>
        .centered-form {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .margin-top {
            margin-top: 180px;
        }

        .side-image {
            background-size: cover;
            background-position: center;
            height: 100vh;
        }
    </style>
</head>
<body class="login-page">
    <div class="container">
        <div class="row margin-top">

            <div class="col-md-6 side-image">
                <img src="../assets-frontend/images/img-listrik.png" class="img-fluid" alt="login-image" width="50%" height="50%">
            </div>
            <div class="col-md-6">
            <div class="card custom-card">
                    <div class="card-header">
                        Login
                    </div>
                    <div class="text-danger"><?php echo isset($_GET["unregistered"])? "Email Tidak Terdaftar": "" ?></div>
                    <div class="text-danger"><?php echo isset($_GET["password"])? "Email atau Password Salah" : ""?></div>
                    <div class="card-body">
                        <form method="post" action="">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email">
                                <div class="text-danger"><?php echo isset($error['email']) ? $error['email'] : ""; ?></div>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password">
                                <div class="text-danger"><?php echo isset($error['password']) ? $error['password'] : ""; ?></div>
                            </div>
                            <div class="form-group">
                                <div class="text-center">
                                    <a href="#">Forgot Password?</a>
                                </div>
                            </div>
                            <button type="submit" name="login" class="btn btn-primary btn-block">Login</button>
                            <div class="form-group mt-4">
                                <div class="text-center">
                                    <span>Don't have an account?</span>
                                    <a href="register.php">Register Here</a>
                                </div>
                            </div>
                        </form>
                        <?php if ($wrong) { echo "<div class='alert alert-danger'>Email atau password salah</div>"; } ?>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>

    <script src="../assets-frontend/js/jquery-3.5.1.js"></script>
    <script src="../assets-frontend/js/popper.min.js"></script>
    <script src="../assets-frontend/js/bootstrap.bundle.min.js"></script>

</body>
</html>
