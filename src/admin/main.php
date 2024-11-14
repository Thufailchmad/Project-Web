<?php
require_once '../koneksi/koneksi.php'; // Koneksi ke database

// Mendapatkan koneksi database
try {
    $db = dbConnection(); // Memanggil fungsi untuk mendapatkan koneksi
} catch (PDOException $e) {
    die("Koneksi ke database gagal: " . $e->getMessage());
}

// Ambil semua penjualan dari tabel history
function getAllSales($db)
{
    $query = "SELECT * FROM history ORDER BY date DESC";
    return $db->query($query);
}

// Ambil detail barang berdasarkan ID penjualan
function getOrderItems($db, $historyId)
{
    $query = "SELECT * FROM historyhasitem WHERE historyId = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$historyId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Ambil semua penjualan untuk ditampilkan
$sales = getAllSales($db);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Penjualan</title>
    <link rel="stylesheet" href="../assets-frontend/css/style.css">
    <link rel="stylesheet" href="../assets-frontend/css/4.5.2-bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>History Penjualan</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>User ID</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $sales->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                        <td><?php echo htmlspecialchars($row['total']); ?></td>
                        <td><?php echo htmlspecialchars($row['userId']); ?></td>
                        <td>
                            <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#orderDetailsModal<?php echo $row['id']; ?>">Lihat Detail</button>

                            <!-- Modal untuk menampilkan detail barang yang dipesan -->
                            <div class="modal fade" id="orderDetailsModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="orderDetailsModalLabel">Detail Barang yang Dipesan (ID: <?php echo $row['id']; ?>)</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Item ID</th>
                                                        <th>Kuantitas</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    // Ambil detail barang untuk penjualan ini
                                                    $orderItems = getOrderItems($db, $row['id']);
                                                    if ($orderItems) {
                                                        foreach ($orderItems as $item) {
                                                            echo "<tr>
                                                                <td>" . htmlspecialchars($item['id']) . "</td>
                                                                <td>" . htmlspecialchars($item['itemId']) . "</td>
                                                                <td>" . htmlspecialchars($item['quantity']) . "</td>
                                                            </tr>";
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='3'>Tidak ada barang yang dipesan.</td></tr>";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="../assets-frontend/js/bootstrap.bundle.min.js"></script>
</body>
</html>