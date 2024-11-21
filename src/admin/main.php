<?php
require_once '../koneksi/koneksi.php'; 

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

// Ambil nama item berdasarkan ID item
function getItemName($db, $itemId)
{
    $query = "SELECT name FROM item WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$itemId]);
    return $stmt->fetchColumn(); 
}

//mengambil nama user dari userId
function getNamaUser($db, $userId) {
    $query = "SELECT name FROM user WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$userId]);
    return $stmt->fetchColumn();
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
                    <th>Nama User</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $id = 1;
                while ($row = $sales->fetch(PDO::FETCH_ASSOC)): ?>
                    <?php $userName = getNamaUser($db, $row['userId']); ?>
                    <tr>
                        <td><?php echo htmlspecialchars($id++); ?></td>
                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                        <td><?php echo htmlspecialchars($row['total']); ?></td>
                        <td><?php echo htmlspecialchars($userName); ?></td>
                        <td>
                            <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#orderDetailsModal<?php echo $row['id']; ?>">Lihat Detail</button>

                            <!-- Modal untuk menampilkan detail barang yang dipesan -->
                            <div class="modal fade" id="orderDetailsModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="orderDetailsModalLabel">Detail Barang yang Dipesan </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Nama Item</th>
                                                        <th>Kuantitas</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $orderItems = getOrderItems($db, $row['id']);
                                                    if ($orderItems) {
                                                        foreach ($orderItems as $item) {
                                                            $itemName = getItemName($db, $item['itemId']);
                                                            echo "<tr>
                                                                <td>" . htmlspecialchars($item['id']) . "</td>
                                                                <td>" . htmlspecialchars($itemName) . "</td>
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