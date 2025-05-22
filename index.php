<?php
include "config.php";
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>My.IIT </title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: NiceSchool
  * Template URL: https://bootstrapmade.com/nice-school-bootstrap-education-template/
  * Updated: May 10 2025 with Bootstrap v5.3.6
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

      <a href="index.php" class="logo d-flex align-items-center">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <!-- <img src="assets/img/logo.webp" alt=""> -->
        <i class="bi bi-buildings" style="color:maroon"></i>
        <h1 class="sitename">My.IIT</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="index.php" class="active" >Home</a></li>
          <li class="dropdown"><a href=""><span>Student Feedback </span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="registrar.php">Registrar</a></li>
              <li><a href="library.php">Library</a></li>
              <li><a href="finance.php">Finance</a></li>
              
            </ul>
          </li>

          <li><a href="track.php">Track Request</a></li>
          <li><a href="admin.php" class="text-maroon">Admin Panel</a></li>
          <li class="dropdown ms-auto"><a href="#"><i class="bi bi-person-circle"></i> <span>Profile</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
          <li><a href="login.php" class="btn btn-outline-primary" style="border-color:maroon;color:white;background-color:maroon;padding:0.375rem 1.5rem;font-size:0.9rem;transition:all 0.3s ease" onmouseover="this.style.backgroundColor='white';this.style.color='maroon'" onmouseout="this.style.backgroundColor='maroon';this.style.color='white'">Login</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

    </div>
  </header>

  <main class="main">

   <!-- Hero Section -->
   <section id="hero" class="hero section dark-background">

    <div class="hero-container">
      <video autoplay="" muted="" loop="" playsinline="" class="video-background">
        <source src="assets/img/education/vid2.mp4" type="video/mp4">
      </video>
      <div class="overlay"></div>
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-7" data-aos="zoom-out" data-aos-delay="100">
            <div class="hero-content">
              <h1>Student Feedback Form</h1>
              <p>Student feedback is vital in shaping and improving the services provided by the Registrar, Library, and Finance offices. Your insights help us enhance efficiency, address concerns, and ensure a better experience for all students. We encourage you to share your thoughts to help us serve you better.</p>
              
            </div>
          </div>
          <div class="col-lg-5" data-aos="zoom-out" data-aos-delay="200">
            <div class="stats-card">
              <div class="stats-header">
                <h3>Why Your Feedback Matters</h3>
                <div class="decoration-line"></div>
              </div>
              <div class="stats-grid">
                <div class="stat-item">
                  <div class="stat-icon">
                    <i class="bi bi-chat-dots"></i>
                  </div>
                  <div class="stat-content">
                    <h4>85%</h4>
                    <p>Service improvements from student feedback</p>
                  </div>
                </div>
                <div class="stat-item">
                  <div class="stat-icon">
                    <i class="bi bi-lightning-charge"></i>
                  </div>
                  <div class="stat-content">
                    <h4>2x</h4>
                    <p>Faster issue resolution</p>
                  </div>
                </div>
                <div class="stat-item">
                  <div class="stat-icon">
                    <i class="bi bi-emoji-smile"></i>
                  </div>
                  <div class="stat-content">
                    <h4>30%</h4>
                    <p>Increase in student satisfaction</p>
                  </div>
                </div>
                <div class="stat-item">
                  <div class="stat-icon">
                    <i class="bi bi-arrow-repeat"></i>
                  </div>
                  <div class="stat-content">
                    <h4>Continuous</h4>
                    <p>Improvements driven by your voice</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="event-ticker"style="background-color:maroon;border:none">
      <div class="container" >
        <div class="row gy-4">
          <div class="col-md-6 col-xl-4 col-12 ticker-item">
            <i class="bi bi-mortarboard text-white" style="font-size: 2rem;"></i>&nbsp&nbsp
            <b><span class="title">University Registrar Office</span></b>
            <a href="https://www.msuiit.edu.ph/offices/registrar/index.php" class="btn-register">Visit</a>
          </div>
          <div class="col-md-6 col-12 col-xl-4 ticker-item">
            <i class="bi bi-book text-white" style="font-size: 2rem;"></i>&nbsp&nbsp
            <b><span class="title">University Library Office</span></b>
            <a href="https://www.msuiit.edu.ph/offices/library/index.php" class="btn-register">Visit</a>
          </div>
          <div class="col-md-6 col-12 col-xl-4 ticker-item">
            <i class="bi bi-cash-stack text-white" style="font-size: 2rem;"></i>&nbsp&nbsp
            <b><span class="title">University Finance Office</span></b>
            <a href="https://www.msuiit.edu.ph/offices/cashier/index.php" class="btn-register">Visit</a>
          </div>
        </div>
      </div>
    </div>

  </section><!-- /Hero Section -->

  </main>

  <footer id="footer" class="footer position-relative dark-background">

    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-4 col-md-6 footer-about">
          <a href="index.php" class="logo d-flex align-items-center">
            <span class="sitename">My.IIT</span>
          </a>
          <div class="footer-contact pt-3">
            <p>Andres Bonifacio Ave</p>
            <p>Iligan City, 9200 Lanao del Norte</p>
            <p class="mt-3"><strong>Phone:</strong> <span>(063) 223 8641</span></p>
            
          </div>
          <div class="social-links d-flex mt-4">
            <a href=""><i class="bi bi-twitter-x"></i></a>
            <a href=""><i class="bi bi-facebook"></i></a>
            <a href=""><i class="bi bi-instagram"></i></a>
            <a href=""><i class="bi bi-linkedin"></i></a>
          </div>
        </div>

      </div>
    </div>

   

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>