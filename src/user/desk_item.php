<?php

use function PHPSTORM_META\type;

require_once '../koneksi/koneksi.php';
session_start();
$useragent = $_SERVER['HTTP_USER_AGENT'];
if ($useragent == "android") {
    header('Content-Type: application/json');
}

// Proses tambah ke keranjang
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    
    try {
        $db = dbConnection();
        // Validasi input
        $itemId = (int)$_POST['item_id'];
        $quantity = (int)$_POST['quantity'];
        if ($useragent == "android") {
            $userId = (int)$_POST["user_id"];
        }else{
            $userId = (int)$_SESSION['user'] ['id'];
        }
        
        // Cek stok produk
        $stokQuery = "SELECT stock FROM item WHERE id = :itemId";
        $stokStmt = $db->prepare($stokQuery);
        $stokStmt->execute(['itemId' => $itemId]);
        $produk = $stokStmt->fetch(PDO::FETCH_ASSOC);

        if ($quantity > $produk['stock']) {
            if ($useragent == "android") {
                echo json_encode([
                'status' => "Stock tidak cukup",
                'message' => "Stok tidak mencukupi. Tersedia hanya {$produk['stock']} item."
            ]);
            exit();
            }
            // Pesan error jika quantity melebihi stok
            $_SESSION['error'] = "Stok tidak mencukupi. Tersedia hanya {$produk['stock']} item.";
            header('Location: desk_item.php?id=' . $itemId);
            exit();
        }

        // Cek apakah item sudah ada di keranjang
        $cekQuery = "SELECT * FROM chart WHERE userId = :userId AND itemId = :itemId";
        $cekStmt = $db->prepare($cekQuery);
        $cekStmt->execute([
            'userId' => $userId,
            'itemId' => $itemId
        ]);

        if ($cekStmt->rowCount() > 0) {
            // Jika item sudah ada, update quantity
            $updateQuery = "UPDATE chart SET quantity = quantity + :quantity WHERE userId = :userId AND itemId = :itemId";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->execute([
                'quantity' => $quantity,
                'userId' => $userId,
                'itemId' => $itemId
            ]);
        } else {
            $insertQuery = "INSERT INTO chart (userId, itemId, quantity) VALUES (:userId, :itemId, :quantity)";
            $insertStmt = $db->prepare($insertQuery);
            $insertStmt->execute([
                'userId' => $userId,
                'itemId' => $itemId,
                'quantity' => $quantity
            ]);
        }

        if ($useragent == "android") {
            echo json_encode([
                'status' => 'success',
                'message' => 'Produk berhasil ditambahkan',
            ]);
            exit();
        }
        // Set pesan sukses
        $_SESSION['success'] = "Produk berhasil ditambahkan ke keranjang!";

        // Redirect dengan pesan sukses
        header('Location: desk_item.php?id=' . $itemId);
        exit();
    } catch (PDOException $e) {
        if ($useragent == "android") {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal menambahkan ke keranjang',
            ]);
            exit();
        }
        // Tangani error
        $_SESSION['error'] = "Gagal menambahkan ke keranjang: " . $e->getMessage();
        header('Location: desk_item.php?id=' . $_POST['item_id']);
        exit();
    }
}

// Ambil detail item
try {
    $db = dbConnection();

    $query = "SELECT * FROM item WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->execute(['id' => $_GET['id']]);
    $item = $stmt->fetch(PDO::FETCH_NAMED);

    if (!$item) {
        if ($useragent == "android") {
            echo json_encode([
                'status' => 'error',
                'message' => 'Item tidak tersedia',
            ]);
            exit();
        }
        header('Location: dashboard.php');
        exit();
    }

    if ($useragent == "android") {
        echo json_encode([
            'status' => 'ok',
            'item' => $item,
        ]);
        exit();
    }
} catch (PDOException $e) {
    die("Koneksi ke database gagal: " . $e->getMessage());
}
try {
    $db = dbConnection();

    $search = isset($_GET['search']) ? $_GET['search'] : '';

    if (!empty($search)) {
        $query = "SELECT * FROM item WHERE name LIKE :search OR category LIKE :search";
        $stmt = $db->prepare($query);
        $stmt->execute(['search' => "%$search%"]);
        $result = $stmt;
    } else {
        $query = "SELECT * FROM item";
        $result = $db->query($query);
    }
} catch (PDOException $e) {
    die("Koneksi ke database gagal: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deskripsi Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .logo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .product-image {
            height: 500px;
            object-fit: cover;
            width: 100%;
        }

        .product-details {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .product-price {
            color: #f28b82;
            font-size: 28px;
            font-weight: bold;
        }

        .product-description {
            line-height: 1.6;
            color: #666;
        }

        .kategori-bar {
            background-color: #e0e0e0;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="logo">
            <a href="dashboard.php">
                <img src="../images/img-listrik.png" alt="">
            </a>
            </div>
            <div class="flex-grow-1 mx-3">
                <form action="" method="GET">
                    <input type="text" name="search" class="form-control mb-2" placeholder="Search..."
                        value="<?php echo htmlspecialchars($search); ?>">
                </form>
                <div class="kategori-bar">Kategori</div>
            </div>
            <!-- Tombol Keranjang -->
            <button class="btn btn-outline-secondary position-relative me-2" onclick="window.location.href='keranjang.php'">
              <img src="../images/keranjang.png" alt="Keranjang" style="width: 24px; height: 24px;">
              <?php
              if (isset($_SESSION['user_id'])) {
                try {
                  $db = dbConnection();
                  $cartQuery = "SELECT COUNT(*) as cart_count FROM chart WHERE userId = :userId";
                  $cartStmt = $db->prepare($cartQuery);
                  $cartStmt->execute(['userId' => $_SESSION['user_id']]);
                  $cartCount = $cartStmt->fetch(PDO::FETCH_ASSOC)['cart_count'];

                  if ($cartCount > 0) {
                    echo '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">'
                      . $cartCount .
                      '<span class="visually-hidden">items in cart</span></span>';
                  }
                } catch (PDOException $e) {
                  // Tangani error jika perlu
                  $cartCount = 0;
                }
              }
              ?>
            </button>
            <button class="btn btn-secondary" onclick="window.location.href='../auth/login.php'">Logout</button>
        </div>
        <div class="row">
            <div class="col-md-6">
                <img src="../assets/<?= htmlspecialchars($item['photo']) ?>"
                    alt="<?= htmlspecialchars($item['name']) ?>"
                    class="product-image">
            </div>
            <div class="col-md-6">
                <div class="product-details">
                    <h1 class="mb-3"><?= htmlspecialchars($item['name']) ?></h1>

                    <div class="mb-3">
                        <span class="badge bg-secondary"><?= htmlspecialchars($item['category']) ?></span>
                    </div>

                    <div class="product-price mb-3">
                        Rp <?= number_format($item['price'], 0, ',', '.') ?>
                    </div>

                    <div class="product-description mb-4">
                        <?= htmlspecialchars($item['description'] ?? 'Tidak ada deskripsi tersedia.') ?>
                    </div>

                    <div class="product-info mb-4">
                        <h5>Informasi Produk</h5>
                        <ul class="list-unstyled">
                            <li><strong>Stok:</strong> <?= $item['stock'] ?? 'Tidak tersedia' ?></li>
                            <li><strong>Kondisi:</strong> <?= $item['condition'] ?? 'Baru' ?></li>
                        </ul>
                    </div>

                    <div class="d-flex gap-2">
                        <!-- Formulir Tambah ke Keranjang -->
                        <form method="POST" class="flex-grow-1">
                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                            <input type="hidden" name="quantity" value="<?= 1 ?>">
                            <div class="input-group">
                                <!-- Tombol Tambah ke Keranjang -->
                                <button
                                    type="submit"
                                    class="btn btn-danger flex-grow-1">
                                    <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Tampilkan Pesan Sukses atau Error -->
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success mt-3">
                            <?= $_SESSION['success'] ?>
                            <?php unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger mt-3">
                            <?= $_SESSION['error'] ?>
                            <?php unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Kategori -->
    <div class="modal fade" id="kategoriModal" tabindex="-1" aria-labelledby="kategoriModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="kategoriModalLabel">Pilih Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php
                    try {
                        $kategoriQuery = "SELECT DISTINCT category FROM item";
                        $kategoriStmt = $db->query($kategoriQuery);
                        $kategoris = $kategoriStmt->fetchAll(PDO::FETCH_COLUMN);
                    } catch (PDOException $e) {
                        $kategoris = [];
                    }
                    ?>
                    <div class="list-group">
                        <?php foreach ($kategoris as $kategori): ?>
                            <a href="?search=<?php echo urlencode($kategori); ?>" class="list-group-item list-group-item-action">
                                <?php echo htmlspecialchars($kategori); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Tambahkan event listener untuk kategori bar
        document.querySelector('.kategori-bar').addEventListener('click', function() {
            var kategoriModal = new bootstrap.Modal(document.getElementById('kategoriModal'));
            kategoriModal.show();
        });

        // Fungsi untuk menampilkan dropdown kategori
        function showKategori() {
            var kategoriDropdown = document.getElementById('kategoriDropdown');
            kategoriDropdown.classList.toggle('show');
        }

        // Tutup dropdown jika diklik di luar dropdown
        window.onclick = function(event) {
            if (!event.target.matches('.kategori-bar')) {
                var dropdowns = document.getElementsByClassName('dropdown-content');
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
</body>

</html>