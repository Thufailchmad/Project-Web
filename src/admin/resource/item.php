<?php
require_once '../koneksi/koneksi.php';

$validImageExtension = ['jpg', 'jpeg', 'png'];

$target_dir = "../assets/img";  // direktori untuk menyimpan gambar
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

try {
    $db = dbConnection();
} catch (PDOException $e) {
    die("Koneksi ke database gagal: " . $e->getMessage());
}

function getAllItems($db)
{
    $query = "SELECT * FROM item";
    return $db->query($query);
}

// Tambah Item
if (isset($_POST['add_item'])) {
    $barcode = $_POST['barcode'];
    $name = $_POST['name'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $newImageName = "";

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $fileName = $_FILES["photo"]["name"];
        $fileSize = $_FILES["photo"]["size"];
        $tmpName = $_FILES["photo"]["tmp_name"];
        $imageExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($imageExtension, $validImageExtension) && $fileSize <= 1000000) {
            // Tambahkan timestamp untuk menghindari nama file yang sama
            $timestamp = time();
            $newImageName = 'img/' . $timestamp . '_' . $fileName;
            $targetFilePath = $target_dir . '/' . $timestamp . '_' . $fileName;

            if (move_uploaded_file($tmpName, $targetFilePath)) {
                try {
                    $query = "INSERT INTO item (barcode, name, category, stock, price, description, photo) VALUES (:barcode, :name, :category, :stock, :price, :description, :photo)";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':barcode', $barcode);
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':category', $category);
                    $stmt->bindParam(':stock', $stock);
                    $stmt->bindParam(':price', $price);
                    $stmt->bindParam(':description', $description);
                    $stmt->bindParam(':photo', $newImageName);

                    if ($stmt->execute()) {
                        echo "<script>alert('Item berhasil ditambahkan!'); window.location.href='admin/index.php?item';</script>";
                    } else {
                        echo "<script>alert('Error: Gagal menambahkan item');</script>";
                    }
                } catch (PDOException $e) {
                    echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
                }
            } else {
                echo "<script>alert('Failed to upload image');</script>";
            }
        } else {
            echo "<script>alert('Invalid image extension or size too large');</script>";
        }
    } else {
        echo "<script>alert('Please upload an image');</script>";
    }
}

// Update Item
if (isset($_POST['update_item'])) {
    $id = $_POST['id'];
    $barcode = $_POST['barcode'];
    $name = $_POST['name'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    try {
        // Cek apakah ada file foto baru yang diupload
        if ($_FILES["photo"]["error"] == 0) {
            $fileName = $_FILES["photo"]["name"];
            $fileSize = $_FILES["photo"]["size"];
            $tmpName = $_FILES["photo"]["tmp_name"];
            $imageExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (in_array($imageExtension, $validImageExtension) && $fileSize <= 1000000) {
                $timestamp = time();
                $newImageName = 'img/' . $timestamp . '_' . $fileName;
                $targetFilePath = $target_dir . '/' . $timestamp . '_' . $fileName;

                if (move_uploaded_file($tmpName, $targetFilePath)) {
                    // Update dengan foto baru
                    $query = "UPDATE item SET barcode=:barcode, name=:name, category=:category, stock=:stock, price=:price, description=:description, photo=:photo WHERE id=:id";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':photo', $newImageName);
                } else {
                    echo "<script>alert('Failed to upload new image');</script>";
                    return;
                }
            } else {
                echo "<script>alert('Invalid image extension or size too large');</script>";
                return;
            }
        } else {
            // Update tanpa mengubah foto
            $query = "UPDATE item SET barcode=:barcode, name=:name, category=:category, stock=:stock, price=:price, description=:description WHERE id=:id";
            $stmt = $db->prepare($query);
        }

        // Binding parameter
        $stmt->bindParam(':barcode', $barcode);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            echo "<script>alert('Data berhasil diperbarui'); window.location.href='admin/index.php?item';</script>";
        } else {
            echo "<script>alert('Error: Gagal memperbarui data');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}

// Hapus Item
if (isset($_POST['delete_item_id'])) {
    $id = $_POST['delete_item_id'];

    try {
        // Ambil nama foto sebelum menghapus
        $stmt = $db->prepare("SELECT photo FROM item WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        // Hapus file foto dari direktori
        if (!empty($item['photo'])) {
            $filePath = $target_dir . '/' . basename($item['photo']);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Hapus data dari database
        $stmt = $db->prepare("DELETE FROM item WHERE id = :id");
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            echo "<script>alert('Item berhasil dihapus!'); window.location.href='admin/index.php?item';</script>";
        } else {
            echo "<script>alert('Item gagal dihapus!');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}

$result = getAllItems($db);
?>

<body>
    <div class="nk-content nk-content-fluid">
        <div class="container-xl wide-xl">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <div class="nk-block-des text-soft">
                                <strong>Data Item</strong>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><em class="icon ni ni-plus"></em> Add Item</button>
                        </div>
                    </div>
                </div>

                <div class="nk-block">
                    <div class="row g-gs">
                        <table class="datatable-init table table-bordered table-hover" style="width: 100%;">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Barcode</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Stock</th>
                                    <th>Price</th>
                                    <th>Description</th>
                                    <th>Photo</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $id = 1;
                                while ($row = $result->fetch(PDO::FETCH_ASSOC)):
                                ?>
                                    <tr>
                                        <td><?php echo $id++; ?></td>
                                        <td><?php echo htmlspecialchars($row['barcode']); ?></td>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                                        <td><?php echo htmlspecialchars($row['stock']); ?></td>
                                        <td><?php echo htmlspecialchars($row['price']); ?></td>
                                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                                        <td>
                                            <?php
                                            echo !empty($row['photo'])
                                                ? '<img src="http://localhost/tokolistrik/src/assets/' . htmlspecialchars($row['photo']) . '" alt="Item Image" width="50" height="50">'
                                                : 'Tidak ada foto';
                                            ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>">Edit</button>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="delete_item_id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus item ini?');">Delete</button>
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- Modal Edit Item -->
                                    <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST" enctype="multipart/form-data">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editModalLabel">Edit Item</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                        <div class="mb-3">
                                                            <label for="barcode" class="form-label">Barcode</label>
                                                            <input type="text" class="form-control" name="barcode" value="<?php echo htmlspecialchars($row['barcode']); ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="name" class="form-label">Name</label>
                                                            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="category" class="form-label">Category</label>
                                                            <input type="text" class="form-control" name="category" value="<?php echo htmlspecialchars($row['category']); ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="stock" class="form-label">Stock</label>
                                                            <input type="number" class="form-control" name="stock" value="<?php echo htmlspecialchars($row['stock']); ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="price" class="form-label">Price</label>
                                                            <input type="text" class="form-control" name="price" value="<?php echo htmlspecialchars($row['price']); ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="description" class="form-label">Description</label>
                                                            <textarea class="form-control" name="description" required><?php echo htmlspecialchars($row['description']); ?></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="photo" class="form-label">Upload Photo</label>
                                                            <input type="file" class="form-control" name="photo">
                                                            <small class="form-text text-muted">Leave blank to keep the current photo.</small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" name="update_item" class="btn btn-primary">Update Item</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add Item -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">Add Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="barcode" class="form-label">Barcode</label>
                            <input type="text" class="form-control" name="barcode" required>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <input type="text" class="form-control" name="category" required>
                        </div>
                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number" class="form-control" name="stock" required>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="text" class="form-control" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" name="description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="photo" class="form-label">Upload Photo</label>
                            <input type="file" class="form-control" name="photo" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_item" class="btn btn-primary">Add Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets-frontend/js/bootstrap.bundle.min.js"></script>
</body>

</html>