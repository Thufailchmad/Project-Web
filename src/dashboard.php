<?php require "./middlewares/auth.middleware.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
     <link href="assets-frontend/css/sec.css" rel="stylesheet">
     
    
</head>
<body class="dashboard-page">

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">
      <a href="index.html" class="logo d-flex align-items-center me-auto">      
        <img src="assets-frontend/img/mybimo.png" alt="MyBimo Logo" style="height: 40px;">
    </a>
      <nav id="navmenu" class="navmenu">      
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>
      <a class="btn-getstarted" href="auth/login.php">Log Out</a>
      <a class="btn-getstarted" href="#about">Get Started</a>
    </div>
  </div>
 
  </header>

    <!-- First Section -->
    <section id="first" class="first section dark-background">
  

      <div class="container">
        <div class="row gy-4">
          <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center" data-aos="zoom-out">
            <h1>Empowering Learning,<br>Anytime, Anywhere <br>with MyBimo!</h1>
            <h5 >improve your English on MyBimo</h5>
          </div>
          <div class="col-lg-6 order-1 order-lg-2   " data-aos="zoom-out" data-aos-delay="200">
            <img src="assets-frontend/img/logodashboard1.png" class="img-fluid animated" alt="">
          </div>
        </div>
      </div>

    </section><!-- /First Section -->

   <!-- Button Box -->
<div class="button-box">
  <button type="button" data-toggle="modal" data-target="#imageModal">
    <img src="assets-frontend/img/buttondashboard/button1.png" alt="Button 1">
  </button>
  <button type="button" data-toggle="modal" data-target="#imageModal">
    <img src="assets-frontend/img/buttondashboard/button2.png" alt="Button 2">
  </button>
  <button type="button" data-toggle="modal" data-target="#imageModal">
    <img src="assets-frontend/img/buttondashboard/button3.png" alt="Button 3">
  </button>
  <button type="button" data-toggle="modal" data-target="#imageModal">
    <img src="assets-frontend/img/buttondashboard/button4.png" alt="Button 4">
  </button>
  <button type="button" data-toggle="modal" data-target="#imageModal">
    <img src="assets-frontend/img/buttondashboard/button5.png" alt="Button 5">
  </button>
</div>

<!-- Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="imageModalLabel">Download Aplikasi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <!-- Gambar dalam Pop-up -->
        <img src="assets-frontend/img/popup.png" alt="Aplikasi Preview" class="img-fluid">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        <a href="link-to-your-app-download" class="btn btn-primary" download>Download Aplikasi</a>
      </div>
    </div>
  </div>
</div>
<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.4.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

    <!-- Second Section -->
    <section id="second">
      <div>
      <h1 id="buath1" class="feature">
        <i id="bintangnya" class="fa fa-star" aria-hidden="true"></i> Interesting features on MyBimo!
      </h1>
      </div>
    
      <div class="row row-cols-1 row-cols-md-5 g-4">
        <div class="col">
          <div class="card h-100">
            <img src="assets-frontend/img/Card/card1.png" class="card-img-top" alt="...">
            <div class="feature">
              <div class="card-body">
                <h5 class="card-title">Vocabulary</h5>
                <p class="card-text">
                  <i class="fa fa-star" aria-hidden="true"></i> Interactive and fun vocabulary learning.<br>
                  <i class="fa fa-star" aria-hidden="true"></i> Daily new words and quizzes.<br>
                  <i class="fa fa-star" aria-hidden="true"></i> Boosts speaking and comprehension skills.
                </p>
                <a href="#" class="btn btn-orange" data-toggle="modal" data-target="#imageModal">See Details</a>
              </div>
            </div>
            <div class="card-footer">
              <small class="text-body-secondary">Last updated 3 mins ago</small>
            </div>
          </div>
        </div>

    <div class="col">
      <div class="card h-100">
        <img src="assets-frontend/img/Card/card2.png" class="card-img-top" alt="">
        <div class="feature">
        <div class="card-body">
          <h5 class="card-title">Grammar</h5>
          <p class="card-text">
            <i class="fa fa-star" aria-hidden="true"></i> Learn grammar with real-life examples.<br>
            <i class="fa fa-star" aria-hidden="true"></i> Easy-to-understand explanations.<br>
            <i class="fa fa-star" aria-hidden="true"></i> Practical exercises for all levels.
          </p>
          <a href="#" class="btn btn-orange" data-toggle="modal" data-target="#imageModal">See Details</a>
        </div>
        </div>
        <div class="card-footer">
          <small class="text-body-secondary">Last updated 3 mins ago</small>
        </div>
      </div>
    </div>

    <div class="col">
      <div class="card h-100">
        <img src="assets-frontend/img/Card/card3.png" class="card-img-top" alt="">
        <div class="feature">
        <div class="card-body">
          <h5 class="card-title">Listening</h5>
          <p class="card-text">
            <i class="fa fa-star" aria-hidden="true"></i> Improve comprehension with real conversations.<br>
            <i class="fa fa-star" aria-hidden="true"></i> Exercises tailored for all levels.<br>
            <i class="fa fa-star" aria-hidden="true"></i> Boosts listening speed and accuracy.
          </p>
          <a href="#" class="btn btn-orange" data-toggle="modal" data-target="#imageModal">See Details</a>
          </div>
        </div>
        <div class="card-footer">
          <small class="text-body-secondary">Last updated 3 mins ago</small>
        </div>
      </div>
    </div>

    <div class="col">
      <div class="card h-100">
        <img src="assets-frontend/img/Card/card4.png" class="card-img-top" alt="">
        <div class="feature">
        <div class="card-body">
          <h5 class="card-title">Reading</h5>
          <p class="card-text">
            <i class="fa fa-star" aria-hidden="true"></i> Extensive reading materials and exercises.<br>
            <i class="fa fa-star" aria-hidden="true"></i> Enhance speed and comprehension.<br>
            <i class="fa fa-star" aria-hidden="true"></i> Learn contextual understanding effectively.
          </p>
          <a href="#" class="btn btn-orange" data-toggle="modal" data-target="#imageModal">See Details</a>
          </div>
        </div>
        <div class="card-footer">
          <small class="text-body-secondary">Last updated 3 mins ago</small>
        </div>
      </div>
    </div>

    <div class="col">
      <div class="card h-100">
        <img src="assets-frontend/img/Card/card5.png" class="card-img-top" alt="">
        <div class="feature">
        <div class="card-body">
          <h5 class="card-title">Pronouns</h5>
          <p class="card-text">
            <i class="fa fa-star" aria-hidden="true"></i> Learn correct pronoun usage in context.<br>
            <i class="fa fa-star" aria-hidden="true"></i> Structured practice for all types of pronouns.<br>
            <i class="fa fa-star" aria-hidden="true"></i> Boosts sentence accuracy and fluency.
          </p>
          <a href="#" class="btn btn-orange" data-toggle="modal" data-target="#imageModal">See Details</a>
          </div>
        </div>
        <div class="card-footer">
          <small class="text-body-secondary">Last updated 3 mins ago</small>
        </div>
      </div>
    </div>
  </div>

  
</section><!-- /Second Section -->
<!-- Third Section -->
<section id="third">
  <h1 id="h1satunya" class="feature">
    <i id="bintanglagi" class="fa fa-star" aria-hidden="true"></i> Interesting features on MyBimo!
  </h1>

  <div class="carousel-box">
    <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel" style="width: 100%; height: auto;">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img src="assets-frontend/img/carousel/carousel1.jpg" class="d-block w-100" alt="">
        </div>
        <div class="carousel-item">
          <img src="assets-frontend/img/carousel/carousel2.png" class="d-block w-100" alt="">
        </div>
        <div class="carousel-item">
          <img src="assets-frontend/img/carousel/carousel3.png" class="d-block w-100" alt="">
        </div>
      </div>

      <!-- Carousel control buttons -->
      <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
  </div>
</section><!-- /Third Section -->

<section><!-- Fourth Section -->

<div class="card-container">
        <img src="assets-frontend/img/card2/card1.png" alt="Card Image" class="card-image">
        <a href="#" class="button-overlay">Lihat Detail</a>
    </div>

</section><!-- /Fourth Section -->  

<!-- Link Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>