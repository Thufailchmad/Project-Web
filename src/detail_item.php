<?php
require_once 'koneksi/koneksi.php';

// Pastikan ada parameter ID item yang dikirim
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

try {
    $db = dbConnection();

    // Query untuk mengambil detail item berdasarkan ID
    $query = "SELECT * FROM item WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->execute(['id' => $_GET['id']]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        header('Location: index.php');
        exit();
    }
} catch (PDOException $e) {
    die("Koneksi ke database gagal: " . $e->getMessage());
}

try {
    $db = dbConnection();

    $search = isset($_GET['search']) ? $_GET['search'] : '';

    // Query untuk mengambil item dengan pencarian
    if (!empty($search)) {
        $query = "SELECT * FROM item WHERE name LIKE :search OR category LIKE :search";
        $stmt = $db->prepare($query);
        $stmt->execute(['search' => "%$search%"]);
        $result = $stmt;
    } else {
        // Jika tidak ada pencarian, tampilkan semua item
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
    <title>Detail Produk</title>
    <link rel="shortcut icon" href="images/img-listrik.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4;
        }

        .logo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #ffffff;
        }

        .logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .product-detail-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .product-image-large {
            width: 100%;
            height: 400px;
            background-size: cover;
            background-position: center;
        }

        .product-info-detail {
            padding: 20px;
        }

        .product-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .product-price {
            font-size: 28px;
            font-weight: bold;
            color: #f28b82;
            margin: 10px 0;
        }

        .product-description {
            color: #666;
            line-height: 1.6;
        }

        .btn-custom {
            background-color: #f28b82;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #e57373;
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
                <img src="images/img-listrik.png" alt="">
            </div>
            <div class="flex-grow-1 mx-3">
                <form action="" method="GET">
                    <input type="text" name="search" class="form-control mb-2" placeholder="Search..."
                        value="<?php echo htmlspecialchars($search); ?>">
                </form>
                <div class="kategori-bar">Kategori</div>
            </div>
            <button class="btn btn-secondary" onclick="window.location.href='auth/login.php'">Login & Register</button>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="product-image-large">
                    <img src="http://localhost/tokolistrik/src/assets/<?php echo htmlspecialchars($item['photo']); ?>"
                        alt="Gambar Produk"
                        class="w-100 h-100 object-fit-cover">
                </div>
            </div>
            <div class="col-md-6">
                <div class="product-detail-container product-info-detail">
                    <!-- Informasi Produk -->
                    <h1 class="product-title"><?php echo htmlspecialchars($item['name']); ?></h1>

                    <!-- Kategori -->
                    <div class="mb-2">
                        <span class="badge bg-secondary"></span>
                        <span class="badge bg-secondary"><?php echo htmlspecialchars($item['category']); ?></span>
                    </div>

                    <!-- Harga -->
                    <div class="product-price">
                        Rp <?php echo number_format($item['price'], 0, ',', '.'); ?>
                    </div>

                    <!-- Deskripsi Produk -->
                    <div class="product-description mb-4">
                        <?php echo htmlspecialchars($item['description'] ?? 'Tidak ada deskripsi tersedia.'); ?>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="d-flex gap-2">
                        <button class="btn btn-custom flex-grow-1">
                            <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                        </button>
                        <button class="btn btn-outline-secondary">
                            <i class="bi bi-heart"></i> Wich List
                        </button>
                    </div>

                    <!-- Informasi Tambahan -->
                    <div class="mt-4">
                        <h5>Informasi Produk</h5>
                        <ul class="list-unstyled">
                            <li><strong>Stok:</strong> <?php echo $item['stock'] ?? 'Tidak tersedia'; ?></li>
                            <li><strong>Kondisi:</strong> <?php echo $item['condition'] ?? 'Baru'; ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi -->
    <div class="modal fade" id="konfirmasiModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Silahkan Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Silahkan Login terlebih Dahulu
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" href="auth/login.php">Login</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tambahkan logika JavaScript tambahan jika diperlukan
        document.querySelector('.btn-custom').addEventListener('click', function() {
            var konfirmasiModal = new bootstrap.Modal(document.getElementById('konfirmasiModal'));
            konfirmasiModal.show();
        });
    </script>
</body>

</html>