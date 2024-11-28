<?php
require_once '../koneksi/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $error = [];
    $db = dbConnection();
    $useragent = $_SERVER['HTTP_USER_AGENT'];

    if ($useragent == "android") {
        header('Content-Type: application/json');
    }

    if (!isset($_POST['email']) || $_POST['email'] == '') {
        $error['email'] = "Email tidak boleh kosong";
    }

    if (!isset($_POST['name']) || $_POST['name'] == '') {
        $error['name'] = "Nama tidak boleh kosong";
    }

    if (!isset($_POST['address']) || $_POST['address'] == '') {
        $error['address'] = "Alamat tidak boleh kosong";
    }

    if (!isset($_POST['password']) || $_POST['password'] == '') {
        $error['password'] = "Password tidak boleh kosong";
    }

    if (!isset($_POST['rePassword']) || $_POST['rePassword'] == '') {
        $error['rePassword'] = "Mohon masukkan ulang password";
    }

    if ($_POST['password'] !== $_POST['rePassword']) {
        $error['rePassword'] = "Password dan konfirmasi password tidak cocok";
    }

    

    if (count($error) > 0) {
        if ($useragent == "android") {
            echo json_encode([
                'status' => "Register Gagal, Silahkan Coba Lagi",
                'error' => $error
            ]);
            exit();
        }
        $str = urldecode(serialize($error));
        header("location: .?error=$str");
        exit();
    }

    try {
        $query = $db->prepare("SELECT * FROM user WHERE email = :email");
        $query->bindParam(':email', $_POST['email']);
        $query->execute();

        if ($query->rowCount() > 0) {
            if ($useragent == "android") {
                echo json_encode([
                    'status' => 'Email telah terdaftar',
                    'message' => 'Email telah terdaftar',
                ]);
                exit();
            }
            header("location: register.php?registered=true");
            exit();
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'Register Gagal, Silahkan Coba Lagi',
            'message' => $e->getMessage(),
        ]);
        exit();
    }

    try {
        $query = $db->prepare("INSERT INTO user (name, email, password, address, role) VALUES (:name, :email, :password, :address, 2)");
        $query->bindValue(':name', $_POST['name']);
        $query->bindValue(':email', $_POST['email']);
        $query->bindValue(':address', $_POST['address']);
        $query->bindValue(':password', password_hash($_POST['password'], PASSWORD_BCRYPT));
        $query->execute();

        if ($useragent == "android") {
            echo json_encode([
                'status' => 'Register Berhasil',
                'message' => 'Email telah terdaftar',
            ]);
            exit();
        }
        header("location: login.php");
        exit();
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'Register Gagal',
            'message' => $e->getMessage(),
        ]);
        exit();
    }
}

$error = isset($_GET['error']) ? (unserialize(urldecode($_GET['error']))) : "";
$registered = isset($_GET['registered']) ? $_GET['registered'] : false;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
    <link rel="shortcut icon" href="../images/img-listrik.png">
    <link rel="stylesheet" href="../assets-frontend/css/4.5.2-bootstrap.min.css">
    <link rel="stylesheet" href="../assets-frontend/css/style.css">
    <style>
        /* body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f9f4;
            position: relative;
        }

        .background-circle {
            position: absolute;
            width: 700px;
            height: 700px;
            background: radial-gradient(circle at center, rgba(255,0,0,0), rgba(255,0,0,1));
            border-radius: 50%;
            top: 30%;
            right: 20%;
            z-index: -1;
            opacity: 0.8;
        } */

        .centered-form {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .margin-top {
            margin-top: 150px;
        }

        .side-image {
            background-size: cover;
            background-position: center;
            height: 100vh;
        }
    </style>
</head>

<body>
    <div class="background-circle"></div>
    <div class="container">
        <div class="row margin-top">

            <div class="col-md-6 side-image">
                <img src="../assets-frontend/images/img-listrik.png" class="img-fluid" alt="register-image" width="50%" height="50%">
            </div>

            <div class="col-md-6">
                <div class="card custom-card">
                    <div class="card-header">
                        Register
                    </div>
                    <?php if ($registered) {
                            echo "<div class='alert alert-warning'>Email telah terdaftar</div>";
                        } ?>
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
                                <?php echo isset($error['name']) ? "<small class='text-danger'>{$error['name']}</small>" : ""; ?>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                                <?php echo isset($error['email']) ? "<small class='text-danger'>{$error['email']}</small>" : ""; ?>
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <input type="text" class="form-control" id="address" name="address" placeholder="Enter your address" required>
                                <?php echo isset($error['address']) ? "<small class='text-danger'>{$error['address']}</small>" : ""; ?>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                                <?php echo isset($error['password']) ? "<small class='text-danger'>{$error['password']}</small>" : ""; ?>
                            </div>
                            <div class="form-group">
                                <label for="rePassword">Confirm Password</label>
                                <input type="password" class="form-control" id="rePassword" name="rePassword" placeholder="Confirm your password" required>
                                <?php echo isset($error['rePassword']) ? "<small class='text-danger'>{$error['rePassword']}</small>" : ""; ?>
                            </div>

                            <button type="submit" name="register" class="btn btn-primary btn-block">Register</button>
                            <div class="form-group mt-4">
                                <div class="text-center">
                                    <span>Already have an account?</span>
                                    <a href="login.php">Login Here</a>
                                </div>
                            </div>
                        </form>
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