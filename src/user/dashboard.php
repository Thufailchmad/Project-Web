<?php
require_once '../koneksi/koneksi.php';

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
  <title>Home</title>
  <link rel="shortcut icon" href="../images/img-listrik.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
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

    .kategori-bar {
      background-color: #e0e0e0;
      padding: 10px;
      border-radius: 5px;
      text-align: center;
    }

    .product-card {
      background-color: #ffffff;
      border-radius: 5px;
      overflow: hidden;
      margin-bottom: 20px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      text-decoration: none;
      display: block;
    }

    .product-image {
      height: 250px;
      width: 100%;
      background-color: #ccc;
      background-size: cover !important;
      background-position: center !important;
      object-fit: cover;
    }

    .product-card .product-image {
      height: 250px;
      width: 100%;
      background-size: cover !important;
      background-position: center !important;
      object-fit: cover;
    }

    .product-info {
      background-color:  #00bfff;
      color: white;
      padding: 10px;
      text-align: center;
    }

    .product-info span {
      display: block;
      margin-bottom: 5px;
    }

    .product-info .product-name {
      font-weight: bold;
      font-size: 1.1em;
    }

    .product-info .product-category {
      font-size: 0.9em;
      color: #ffffff;
    }

    .product-info .product-price {
      font-weight: bold;
      color: #ffffff;
    }
  </style>
</head>

<body>

  <div class="container px-4">
    <!-- Header Section -->
    <div class="row mb-3">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="logo">
            <img src="../images/img-listrik.png" alt="">
          </div>
          <div class="flex-grow-1 mx-3">
            <form action="" method="GET">
              <input type="text" name="search" class="form-control mb-2" placeholder="Search..."
                value="<?php echo htmlspecialchars($search); ?>">
            </form>
            <div class="kategori-bar">Kategori</div>
          </div>
          <div class="d-flex align-items-center gap-2">
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

            <!-- Tombol history -->
            <button class="btn btn-outline-secondary position-relative me-2" onclick="window.location.href='history.php'">
            <img src="../images/history.png" alt="Keranjang" style="width: 24px; height: 24px;"> 
          </button>

            <!-- Tombol Login & Register -->
            <button class="btn btn-primary" onclick="window.location.href='../auth/login.php'">Logout</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Product Cards Section -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
      <?php
      if ($result->rowCount() > 0) {
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
          echo '<div class="col">';
          echo '  <a href="desk_item.php?id=' . htmlspecialchars($row['id']) . '" class="product-card">';
          echo '    <div class="product-image">';
          echo '        <img src="http://localhost/tokolistrik/src/assets/' . htmlspecialchars($row['photo']) . '" alt="Item Image" class="w-100 h-100 object-fit-cover">';
          echo '    </div>';
          echo '    <div class="product-info">';
          echo '      <div class="mb-2">';

          $categories = explode(',', $row['category']);
          foreach ($categories as $category) {
            echo '<span class="">' . htmlspecialchars(trim($category)) . '</span>';
          }

          echo '      </div>';
          echo '      <span class="product-name">' . htmlspecialchars($row['name']) . '</span>';
          echo '      <span class="product-price">Rp ' . number_format($row['price'], 0, ',', '.') . '</span>';
          echo '    </div>';
          echo '  </a>';
          echo '</div>';
        }
      } else {
        echo "<div class='col-12'><p class='text-center'>Tidak ada produk yang ditemukan.</p></div>";
      }
      ?>
    </div>
    <!-- Tampilkan pesan jika tidak ada hasil pencarian -->
    <?php if (!empty($search) && $result->rowCount() == 0): ?>
      <div class="alert alert-info text-center mt-3">
        Tidak ada produk yang cocok dengan pencarian "<?php echo htmlspecialchars($search); ?>"
      </div>
    <?php endif; ?>
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

  <!-- JavaScript -->
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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>