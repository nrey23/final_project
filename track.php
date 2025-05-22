<?php
include "config.php";
session_start();

// Handle status update
if (isset($_POST['solve_id']) && isset($_POST['office'])) {
    $office = $_POST['office'];
    $id = intval($_POST['solve_id']);
    if (in_array($office, ['registrar', 'library', 'finance'])) {
        $sql = "UPDATE `$office` SET status='Resolved' WHERE id=?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
}

// Define office and service options
$offices = [
    'registrar' => [
        'name' => 'Registrar',
        'services' => [
            'Transcript Request',
            'Enrollment Verification',
            'ID Issuance',
            'Document Authentication',
            'Others'
        ]
    ],
    'library' => [
        'name' => 'Library',
        'services' => [
            'Book Borrowing',
            'Reference Assistance',
            'Study Room Reservation',
            'Digital Resources',
            'Others'
        ]
    ],
    'finance' => [
        'name' => 'Finance',
        'services' => [
            'Tuition Payment',
            'Scholarship Application',
            'Billing Inquiry',
            'Refund Request',
            'Others'
        ]
    ]
];

$selected_office = isset($_GET['office']) ? $_GET['office'] : 'registrar';
$selected_service = isset($_GET['service']) ? $_GET['service'] : '';

$feedbacks = [];
$title = '';
if (isset($offices[$selected_office])) {
    $table = $selected_office;
    $title = $offices[$selected_office]['name'];
    $query = "SELECT * FROM `$table`";
    $params = [];
    if ($selected_service && in_array($selected_service, $offices[$selected_office]['services'])) {
        $query .= " WHERE service = ?";
        $params[] = $selected_service;
        $title = $selected_service;
    }
    $query .= " ORDER BY id DESC";
    if ($stmt = mysqli_prepare($link, $query)) {
        if ($params) {
            mysqli_stmt_bind_param($stmt, str_repeat('s', count($params)), ...$params);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $feedbacks[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Track Feedback</title>
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

<body class="academics-page">

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

          <li><a href="track.php" class="active">Track Request</a></li>
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
    <div class="page-title dark-background" style="background-image: url(assets/img/education/bg1.jpg);">
      <div class="container position-relative">
        <h1>Track Your Request</h1>
        <p>View and manage all your feedback and concerns. Filter by office and service, and resolve requests as needed.</p>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.php">Home</a></li>
            <li class="current">Track Request</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

    <!-- Track Request Section -->
  

    <div class="container mt-5">
      <h1 class="mb-4 text-center">Feedback Tracker</h1>
      <form method="get" class="row g-3 mb-4 justify-content-center">
        <div class="col-md-4">
          <label for="office" class="form-label">Select Office</label>
          <select id="office" name="office" class="form-select" required>
            <?php foreach ($offices as $key => $office): ?>
              <option value="<?php echo $key; ?>" <?php if ($selected_office === $key) echo 'selected'; ?>><?php echo $office['name']; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label for="service" class="form-label">Select Service</label>
          <select id="service" name="service" class="form-select">
            <option value="">All Services</option>
            <?php foreach ($offices[$selected_office]['services'] as $service): ?>
              <option value="<?php echo htmlspecialchars($service); ?>" <?php if ($selected_service === $service) echo 'selected'; ?>><?php echo htmlspecialchars($service); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-2 align-self-end">
          <button type="submit" class="btn btn-primary w-100">View Feedback</button>
        </div>
      </form>
      <h2 class="mb-3 text-center"><?php echo htmlspecialchars($title); ?> Feedback</h2>
      <?php if (count($feedbacks) > 0): ?>
        <div class="table-responsive">
          <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>Ticket #</th>
                <th>Student Name</th>
                <th>Student ID</th>
                <th>Email</th>
                <th>Service</th>
                <th>Rating</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($feedbacks as $i => $fb): ?>
                <tr>
                  <td><?php echo htmlspecialchars($fb['id']); ?></td>
                  <td><?php echo htmlspecialchars($fb['studentName']); ?></td>
                  <td><?php echo htmlspecialchars($fb['studentId']); ?></td>
                  <td><?php echo htmlspecialchars($fb['email']); ?></td>
                  <td><?php echo htmlspecialchars($fb['service']); ?></td>
                  <td>
                    <?php for ($j=0; $j < $fb['rating']; $j++) echo '<i class="bi bi-star-fill text-warning"></i>'; ?>
                  </td>
                  <td>
                    <?php echo (!isset($fb['status']) || !$fb['status']) ? 'Pending' : htmlspecialchars($fb['status']); ?>
                  </td>
                  <td>
                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#feedbackModal" data-feedback='<?php echo json_encode($fb); ?>'>View</button>
                    <?php if (!isset($fb['status']) || $fb['status'] !== 'Resolved'): ?>
                    <form method="post" style="display:inline">
                      <input type="hidden" name="solve_id" value="<?php echo $fb['id']; ?>">
                      <input type="hidden" name="office" value="<?php echo htmlspecialchars($selected_office); ?>">
                      <button type="submit" class="btn btn-success btn-sm">Resolved</button>
                    </form>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="alert alert-warning text-center">No feedback found for this selection.</div>
      <?php endif; ?>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="feedbackModalLabel">Feedback Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <ul class="list-group">
              <li class="list-group-item"><strong>Student Name:</strong> <span id="modalStudentName"></span></li>
              <li class="list-group-item"><strong>Student ID:</strong> <span id="modalStudentId"></span></li>
              <li class="list-group-item"><strong>Email:</strong> <span id="modalEmail"></span></li>
              <li class="list-group-item"><strong>Service:</strong> <span id="modalService"></span></li>
              <li class="list-group-item"><strong>Rating:</strong> <span id="modalRating"></span></li>
              <li class="list-group-item"><strong>Feedback:</strong> <span id="modalFeedback"></span></li>
            </ul>
          </div>
        </div>
      </div>
    </div>

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

  <script>
    // Update service dropdown based on office
    const officeServices = <?php echo json_encode(array_map(function($o){return $o['services'];}, $offices)); ?>;
    document.getElementById('office').addEventListener('change', function() {
      const office = this.value;
      const serviceSelect = document.getElementById('service');
      serviceSelect.innerHTML = '<option value="">All Services</option>';
      officeServices[office].forEach(function(service) {
        const opt = document.createElement('option');
        opt.value = service;
        opt.textContent = service;
        serviceSelect.appendChild(opt);
      });
    });

    // Modal population
    var feedbackModal = document.getElementById('feedbackModal');
    feedbackModal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      var feedback = JSON.parse(button.getAttribute('data-feedback'));
      document.getElementById('modalStudentName').textContent = feedback.studentName;
      document.getElementById('modalStudentId').textContent = feedback.studentId;
      document.getElementById('modalEmail').textContent = feedback.email;
      document.getElementById('modalService').textContent = feedback.service;
      document.getElementById('modalRating').innerHTML = '';
      for (let i = 0; i < feedback.rating; i++) {
        document.getElementById('modalRating').innerHTML += '<i class="bi bi-star-fill text-warning"></i>';
      }
      document.getElementById('modalFeedback').textContent = feedback.feedback;
    });
  </script>

</body>

</html>