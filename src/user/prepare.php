<?php
require "./../koneksi/koneksi.php";
header('Content-Type: application/json');

$db = dbConnection();
$user_id = $_GET["user_id"];

try {
    $querry = $db->prepare("SELECT COUNT(*) AS history FROM history WHERE userId = $user_id");
    $querry->execute();
    $history = $querry->fetch(PDO::FETCH_NAMED);
} catch (Exception $e) {
    echo json_encode([
        'status'=>false,
        'error'=> $e->getMessage()
    ]);
    exit();
}

try {
    $querry = $db->prepare("SELECT COUNT(*) AS cart FROM chart WHERE userId = $user_id");
    $querry->execute();
    $cart = $querry->fetch(PDO::FETCH_NAMED);
} catch (Exception $e) {
    echo json_encode([
        'status'=>false,
        'error'=> $e->getMessage()
    ]);
    exit();
}

echo json_encode([
    'cart'=>$cart["cart"],
    'history'=>$history["history"]
]);
exit();
?>