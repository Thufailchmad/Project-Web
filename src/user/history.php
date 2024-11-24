<?php
session_start();
require_once '../koneksi/koneksi.php';

try {
    $conn = dbConnection();
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// Pastikan menggunakan session user yang sudah ada
$userId = $_SESSION['user']['id'];

// Query untuk mengambil history belanja dengan join ke tabel users
$query = "SELECT 
            h.id, 
            h.date, 
            h.total, 
            u.name AS user_name
          FROM 
            history h
          JOIN 
            user u ON h.userId = u.id
          WHERE 
            h.userId = :userId
          ORDER BY 
            h.date DESC";

try {
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query error: " . $e->getMessage());
}

function getOrderItems($conn, $historyId) {
    $query = "SELECT 
                hi.itemId, 
                i.name AS item_name, 
                hi.quantity,
                i.price
              FROM 
                historyhasitem hi
              JOIN 
                item i ON hi.itemId = i.id
              WHERE 
                hi.historyId = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$historyId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Belanja</title>
    <link rel="shortcut icon" href="../images/img-listrik.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .card {
            margin-bottom: 20px;
        }
        .total-label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4 text-center">Riwayat Belanja</h1>
        
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th>ID Transaksi</th>
                            <th>Tanggal</th>
                            <th>Total Belanja</th>
                            <th>Nama Pengguna</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $id = 1;
                        if (!empty($result)): ?>
                            <?php foreach ($result as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($id++); ?></td>
                                    <td><?php echo htmlspecialchars($row['date']); ?></td>
                                    <td>Rp. <?php echo number_format($row['total'], 0, ',', '.'); ?></td>
                                    <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                                    <td>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal<?php echo $row['id']; ?>">
                                                    Detail
                                                </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada riwayat belanja</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Detail Transaksi -->
    <?php foreach ($result as $row): ?>
        <div class="modal fade" id="detailModal<?php echo $row['id']; ?>" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Transaksi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Tanggal:</strong> <?php echo htmlspecialchars($row['date']); ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Total Belanja:</strong> Rp. <?php echo number_format($row['total'], 0, ',', '.'); ?>
                            </div>
                        </div>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nama Item</th>
                                    <th>Kuantitas</th>
                                    <th>Harga</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $orderItems = getOrderItems($conn, $row['id']);
                                foreach ($orderItems as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                        <td>Rp. <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                                        <td>Rp. <?php echo number_format($item['quantity'] * $item['price'], 0, ',', '.'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div> -->
                </div>
            </div>
        </div>
    <?php endforeach; ?>
        
        <div class="text-center mt-4">
            <a href="dashboard.php" class="btn btn-primary">Kembali ke Dashboard</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn = null;
?>