<?php
session_start();
require '../koneksi/koneksi.php';

if (!isset($_SESSION['user']['id'])) {
    header('Location: ../auth/login.php');
    exit();
}

try {
    $conn = dbConnection();
    $userId = (int)$_SESSION['user']['id'];

    // Fungsi untuk menghapus item dari keranjang
    function deleteFromCart($conn, $cartId, $userId)
    {
        // Validasi kepemilikan keranjang
        $checkStmt = $conn->prepare("
            SELECT COUNT(*) as count 
            FROM chart 
            WHERE id = ? AND userId = ?
        ");
        $checkStmt->execute([$cartId, $userId]);
        $check = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($check['count'] == 0) {
            throw new Exception('Anda tidak memiliki izin menghapus item ini');
        }

        // Hapus item dari keranjang
        $deleteStmt = $conn->prepare("
            DELETE FROM chart 
            WHERE id = ? AND userId = ?
        ");
        $result = $deleteStmt->execute([$cartId, $userId]);

        return $result;
    }

    // Fungsi untuk update kuantitas
    function updateCartQuantity($conn, $cartId, $quantity, $userId)
    {
        // Validasi kuantitas
        if ($quantity < 1) {
            throw new Exception('Kuantitas minimal 1');
        }

        // Cek stok produk
        $checkStockStmt = $conn->prepare("
            SELECT i.stock 
            FROM chart c
            JOIN item i ON c.itemId = i.id
            WHERE c.id = ? AND c.userId = ?
        ");
        $checkStockStmt->execute([$cartId, $userId]);
        $productStock = $checkStockStmt->fetch(PDO::FETCH_ASSOC);

        if (!$productStock || $quantity > $productStock['stock']) {
            throw new Exception('Stok tidak mencukupi');
        }

        // Update kuantitas
        $updateStmt = $conn->prepare("
            UPDATE chart 
            SET quantity = ? 
            WHERE id = ? AND userId = ?
        ");
        $result = $updateStmt->execute([$quantity, $cartId, $userId]);

        return $result;
    }

    // Fungsi checkout
    function checkout($userId, $conn)
    {
        $conn->beginTransaction();

        try {
            // Ambil item di keranjang
            $cartItemsStmt = $conn->prepare("
                SELECT itemId, quantity
                FROM chart 
                WHERE userId = ?
            ");
            $cartItemsStmt->execute([$userId]);
            $cartItems = $cartItemsStmt->fetchAll(PDO::FETCH_ASSOC);

            // Validasi dan kurangi stok
            foreach ($cartItems as $item) {
                $updateStockStmt = $conn->prepare("
                    UPDATE item 
                    SET stock = stock - ? 
                    WHERE id = ? AND stock >= ?
                ");
                $updateStockStmt->execute([
                    $item['quantity'],
                    $item['itemId'],
                    $item['quantity']
                ]);

                // Periksa apakah stok mencukupi
                if ($updateStockStmt->rowCount() == 0) {
                    throw new Exception("Stok tidak mencukupi untuk produk");
                }
            }

            // Hitung total amount
            $totalAmountStmt = $conn->prepare("
                SELECT SUM(i.price * c.quantity) AS total 
                FROM chart c
                JOIN item i ON c.itemId = i.id
                WHERE c.userId = ?
            ");
            $totalAmountStmt->execute([$userId]);
            $totalAmount = $totalAmountStmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Insert transaksi
            $historyStmt = $conn->prepare("
                INSERT INTO history 
                (userId, total, date) 
                VALUES (?, ?, NOW())
            ");
            $historyStmt->execute([$userId, $totalAmount]);
            $historyId = $conn->lastInsertId();

            // Insert detail item ke historyhasitem
            $detailStmt = $conn->prepare("
                INSERT INTO historyhasitem 
                (historyId, itemId, quantity) 
                VALUES (?, ?, ?)
            ");

            foreach ($cartItems as $item) {
                $detailStmt->execute([
                    $historyId,
                    $item['itemId'],
                    $item['quantity']
                ]);
            }

            // Hapus keranjang
            $deleteCartStmt = $conn->prepare("
                DELETE FROM chart 
                WHERE userId = ?
            ");
            $deleteCartStmt->execute([$userId]);

            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    // Proses AJAX request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');

        try {
            if (isset($_POST['action'])) {
                switch ($_POST['action']) {
                    case 'delete':
                        if (!isset($_POST['cartId'])) {
                            throw new Exception('ID keranjang tidak valid');
                        }
                        $result = deleteFromCart($conn, $_POST['cartId'], $userId);
                        echo json_encode(['success' => $result]);
                        break;

                    case 'update':
                        if (!isset($_POST['cartId']) || !isset($_POST['quantity'])) {
                            throw new Exception('Parameter tidak lengkap');
                        }
                        $result = updateCartQuantity($conn, $_POST['cartId'], $_POST['quantity'], $userId);
                        echo json_encode(['success' => $result]);
                        break;

                    case 'checkout':
                        $result = checkout($userId, $conn);
                        echo json_encode(['success' => $result]);
                        break;

                    default:
                        throw new Exception('Aksi tidak valid');
                }
                exit();
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            exit();
        }
    }

    // Ambil item keranjang
    $cartStmt = $conn->prepare("
        SELECT 
            c.id AS cart_id, 
            c.itemId, 
            i.name, 
            i.photo, 
            i.price, 
            c.quantity,
            (i.price * c.quantity) AS total_price
        FROM chart c
        JOIN item i ON c.itemId = i.id
        WHERE c.userId = ?
    ");
    $cartStmt->execute([$userId]);
    $cartItems = $cartStmt->fetchAll(PDO::FETCH_ASSOC);

    // Hitung total harga
    $totalHarga = array_sum(array_column($cartItems, 'total_price'));
} catch (Exception $e) {
    // Tangani error
    error_log($e->getMessage());
    die("Terjadi kesalahan: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja</title>
    <link rel="shortcut icon" href="../images/img-listrik.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        h2 a {
            text-decoration: none;
            color: black;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2><a href="dashboard.php">Keranjang Belanja</a></h2>

        <div id="alertContainer"></div>

        <div class="row">
            <div class="col-md-8">
                <!-- Daftar Item Keranjang -->
                <?php if (empty($cartItems)): ?>
                    <div class="alert alert-info">Keranjang kosong</div>
                <?php else: ?>
                    <?php foreach ($cartItems as $item): ?>
                        <div class="card mb-3" id="cart-item-<?= $item['cart_id'] ?>">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-2">
                                        <img src="../assets/<?= $item['photo'] ?>" class="img-fluid">
                                    </div>
                                    <div class="col-md-4">
                                        <h5><?= $item['name'] ?></h5>
                                        <p>Harga: Rp <?= number_format($item['price'], 0, ',', '.') ?></p>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number"
                                            class="form-control quantity-input"
                                            data-cart-id="<?= $item['cart_id'] ?>"
                                            value="<?= $item['quantity'] ?>"
                                            min="1"
                                            max="10">
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <p>Total: Rp <?= number_format($item['total_price'], 0, ',', '.') ?></p>
                                        <button class="btn btn-danger delete-cart-item"
                                            data-cart-id="<?= $item['cart_id'] ?>">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Ringkasan Pesanan</h5>
                        <hr>
                        <p>Total Barang: <span id="total-items"><?= array_sum(array_column($cartItems, 'quantity')) ?></span></p>
                        <p>Total Harga: Rp <span id="total-harga"><?= number_format($totalHarga, 0, ',', '.') ?></span></p>

                        <?php if (!empty($cartItems)): ?>
                            <button id="checkout-btn" class="btn btn-success w-100">
                                Checkout
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fungsi untuk menampilkan alert
            function showAlert(message, type = 'success') {
                const alertContainer = document.getElementById('alertContainer');
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
                alertDiv.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                alertContainer.appendChild(alertDiv);
            }

            // Fungsi untuk mengirim permintaan AJAX
            function sendAjaxRequest(action, data) {
                return fetch('keranjang.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=${action}&${new URLSearchParams(data).toString()}`
                    })
                    .then(response => response.json())
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('Terjadi kesalahan', 'danger');
                    });
            }

            // Event listener untuk update kuantitas
            document.querySelectorAll('.quantity-input').forEach(input => {
                input.addEventListener('change', function() {
                    const cartId = this.dataset.cartId;
                    const quantity = this.value;

                    sendAjaxRequest('update', {
                            cartId,
                            quantity
                        })
                        .then(response => {
                            if (response.success) {
                                location.reload();
                            } else {
                                showAlert(response.message || 'Gagal update kuantitas', 'danger');
                                // Kembalikan ke nilai semula
                                this.value = this.defaultValue;
                            }
                        });
                });
            });

            // Event listener untuk hapus item
            document.querySelectorAll('.delete-cart-item').forEach(button => {
                button.addEventListener('click', function() {
                    const cartId = this.dataset.cartId;

                    if (confirm('Apakah Anda yakin ingin menghapus item ini?')) {
                        sendAjaxRequest('delete', {
                                cartId
                            })
                            .then(response => {
                                if (response.success) {
                                    // Hapus item dari DOM
                                    const cartItem = document.getElementById(`cart-item-${cartId}`);
                                    if (cartItem) {
                                        cartItem.remove();

                                        // Update total item dan harga
                                        const totalItemsSpan = document.getElementById('total-items');
                                        const totalHargaSpan = document.getElementById('total-harga');

                                        let currentItems = parseInt(totalItemsSpan.textContent);
                                        totalItemsSpan.textContent = currentItems - 1;

                                        // Jika tidak ada item, sembunyikan tombol checkout
                                        if (currentItems - 1 === 0) {
                                            const checkoutBtn = document.getElementById('checkout-btn');
                                            const summaryCard = checkoutBtn.closest('.card');
                                            checkoutBtn.remove();

                                            const emptyCartMessage = document.createElement('div');
                                            emptyCartMessage.className = 'alert alert-info';
                                            emptyCartMessage.textContent = 'Keranjang kosong';
                                            summaryCard.querySelector('.card-body').appendChild(emptyCartMessage);
                                        }

                                        showAlert('Item berhasil dihapus dari keranjang');
                                    }
                                } else {
                                    showAlert(response.message || 'Gagal menghapus item', 'danger');
                                }
                            });
                    }
                });
            });

            // Event listener untuk checkout
            const checkoutBtn = document.getElementById('checkout-btn');
            if (checkoutBtn) {
                checkoutBtn.addEventListener('click', function() {
                    if (confirm('Apakah Anda yakin ingin melakukan checkout?')) {
                        sendAjaxRequest('checkout', {})
                            .then(response => {
                                if (response.success) {
                                    showAlert('Checkout berhasil!', 'success');
                                    setTimeout(() => {
                                        window.location.href = 'keranjang.php';
                                    }, 2000);
                                } else {
                                    showAlert(response.message || 'Checkout gagal', 'danger');
                                }
                            });
                    }
                });
            }
        });
    </script>
</body>

</html>