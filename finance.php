<?php
include "config.php";
// Initialize the session
session_start();

$success_message = "";

if (isset($_SESSION['feedback_success']) && $_SESSION['feedback_success']) {
    echo "<script>window.addEventListener('DOMContentLoaded', function() {alert('Feedback submitted successfully!');});</script>";
    unset($_SESSION['feedback_success']);
}

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}

if(isset($_POST['submit'])) {
    $studentName = mysqli_real_escape_string($link, trim($_POST['studentName']));
    $studentId = mysqli_real_escape_string($link, trim($_POST['studentId']));
    $email = mysqli_real_escape_string($link, trim($_POST['email']));
    $service = mysqli_real_escape_string($link, trim($_POST['service']));
    $rating = mysqli_real_escape_string($link, trim($_POST['rating']));
    $feedback = mysqli_real_escape_string($link, trim($_POST['feedback']));
    $status = 'Pending';
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo '<div class="alert alert-danger">Invalid email format</div>';
    } else {
        $sql = "INSERT INTO `finance` (studentName, studentId, email, service, rating, feedback, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "sssssss", $studentName, $studentId, $email, $service, $rating, $feedback, $status);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['feedback_success'] = true;
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                echo '<div class="alert alert-danger">Error submitting feedback. Please try again.</div>';
            }
            mysqli_stmt_close($stmt);
        } else {
            echo '<div class="alert alert-danger">Database error. Please try again later.</div>';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Finance Feedback</title>
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

<body class="admissions-page">

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

      <a href="index.php" class="logo d-flex align-items-center">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <!-- <img src="assets/img/logo.webp" alt=""> -->
        <i class="bi bi-buildings"></i>
        <h1 class="sitename">My.IIT</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="index.php">Home</a></li>
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
            <ul>
              <li><a href="#"><i class="bi bi-person"></i> My Profile</a></li>
              <li><a href="#"><i class="bi bi-gear"></i> Settings</a></li>
              <li><a href="#"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
          </li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

    </div>
  </header>

  <main class="main">

    <!-- Page Title -->
    <div class="page-title dark-background" style="background-image: url(assets/img/education/b2.png);">
      <div class="container position-relative">
        <h1>Finance Feedback</h1>
        <p>Share your experience and feedback regarding tuition payments, scholarships, and other finance office services.</p>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.php">Home</a></li>
            <li class="current">Finance Feedback</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

    <!-- Finance Feedback Section -->
    <section id="finance-feedback" class="section">
      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row justify-content-center">
          <!-- Finance Info Card (Left Side) -->
          <div class="col-lg-5 mb-4 mb-lg-0">
            <div class="card h-100 shadow-sm border-0">
              <div class="card-body">
                <div class="text-center mb-3">
                  <i class="bi bi-cash-coin" style="font-size: 2.5rem; color: #800000;"></i>
                </div>
                <h4 class="card-title text-center mb-3">University Finance Office</h4>
                <ul class="list-unstyled mb-3">
                  <li class="mb-2"><i class="bi bi-geo-alt-fill me-2 text-primary"></i>Andres Bonifacio Avenue, Tibanga,
                  9200 Iligan City, Philippines</li>
                  <li class="mb-2"><i class="bi bi-envelope-fill me-2 text-primary"></i>ovcaf@g.msuiit.edu.ph</li>
                  <li class="mb-2"><i class="bi bi-telephone-fill me-2 text-primary"></i>(063) 221-4058</li>
                  <li class="mb-2"><i class="bi bi-clock-fill me-2 text-primary"></i>Mon–Fri: 8:00am – 5:00pm</li>
                </ul>
                <p>
                  The Finance Office handles tuition payments, scholarships, billing, refunds, and other student financial services. Our team is dedicated to helping you manage your educational finances smoothly and efficiently.
                </p>
               
              </div>
            </div>
          </div>
          <!-- Feedback Form (Right Side) -->
          <div class="col-lg-7">
            <div class="card shadow">
              <div class="card-body">
                <h2 class="mb-4 text-center">
                  <i class="bi bi-clipboard2-check-fill me-2 text-primary"></i>
                  Finance Feedback Form
                </h2>
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                  <div class="mb-3">
                    <label for="studentName" class="form-label"><i class="bi bi-person-fill me-2 text-primary"></i>Student Name</label>
                    <input type="text" name="studentName" class="form-control" id="studentName" required>
                  </div>
                  <div class="mb-3">
                    <label for="studentId" class="form-label"><i class="bi bi-card-list me-2 text-primary"></i>Student ID</label>
                    <input type="text" name="studentId" class="form-control" id="studentId" required>
                  </div>
                  <div class="mb-3">
                    <label for="email" class="form-label"><i class="bi bi-envelope-at-fill me-2 text-primary"></i>Email Address</label>
                    <input type="email" name="email" class="form-control" id="email" required>
                  </div>
                  <div class="mb-3">
                    <label for="service" class="form-label"><i class="bi bi-gear-fill me-2 text-primary"></i>Service Used</label>
                    <select name="service" class="form-select" id="service" required>
                      <option value="" disabled selected>Select a service</option>
                      <option value="Tuition Payment">Tuition Payment</option>
                      <option value="Scholarship Application">Scholarship Application</option>
                      <option value="Billing Inquiry">Billing Inquiry</option>
                      <option value="Refund Request">Refund Request</option>
                      <option value="Others">Others</option>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label class="form-label"><i class="bi bi-star-fill me-2 text-warning"></i>How would you rate the service?</label>
                    <div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="rating" id="rating1" value="1" required>
                        <label class="form-check-label" for="rating1"><i class="bi bi-star-fill text-warning"></i> 1</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="rating" id="rating2" value="2">
                        <label class="form-check-label" for="rating2"><i class="bi bi-star-fill text-warning"></i> 2</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="rating" id="rating3" value="3">
                        <label class="form-check-label" for="rating3"><i class="bi bi-star-fill text-warning"></i> 3</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="rating" id="rating4" value="4">
                        <label class="form-check-label" for="rating4"><i class="bi bi-star-fill text-warning"></i> 4</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="rating" id="rating5" value="5">
                        <label class="form-check-label" for="rating5"><i class="bi bi-star-fill text-warning"></i> 5</label>
                      </div>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="feedback" class="form-label"><i class="bi bi-chat-dots-fill me-2 text-primary"></i>Feedback / Comments</label>
                    <textarea name="feedback" class="form-control" id="feedback" rows="4" required></textarea>
                  </div>
                  
                  <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg" name="submit">
                      <i class="bi bi-send-fill me-2"></i>Submit Feedback
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

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