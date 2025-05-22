<?php
include "config.php";

// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Fetch feedback from all departments
$all_feedbacks = [];
$recent_activities = [];

$departments = ['registrar', 'library', 'finance'];
foreach ($departments as $dept) {
    $sql = "SELECT id, studentName, studentId, email, service, rating, feedback, status, date, '$dept' as department FROM `$dept` ORDER BY date DESC";
    
    if ($result = mysqli_query($link, $sql)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $all_feedbacks[] = $row;
            
            // Add to recent activities
            $recent_activities[] = [
                'studentName' => $row['studentName'],
                'action' => 'Submitted ' . ucfirst($dept) . ' Feedback',
                'department' => ucfirst($dept),
                'time' => $row['date']
            ];
        }
        mysqli_free_result($result);
    }
}

// Sort recent activities by time
usort($recent_activities, function($a, $b) {
    return strtotime($b['time']) - strtotime($a['time']);
});

// Get only the 5 most recent activities
$recent_activities = array_slice($recent_activities, 0, 5);

// Get statistics
$total_feedbacks = count($all_feedbacks);
$unread_messages = 0;

foreach ($all_feedbacks as $fb) {
    if (!isset($fb['status']) || $fb['status'] === 'Pending') {
        $unread_messages++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Admin Panel - My.IIT</title>
  
  <!-- Existing Assets -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="admin/assets/css/now-ui-dashboard.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Montserrat:700,400,200" rel="stylesheet">
  
  <style>
    body, h1, h2, h3, h4, h5, h6, .admin-sidebar, .admin-main {
      font-family: 'Montserrat', Arial, sans-serif;
      font-size: 0.9rem;
    }
    body {
      background: #f7f9fa;
    }
    
    .admin-sidebar {
      background: #800000;
      min-height: 100vh;
      padding: 32px 20px 20px 20px;
      position: fixed;
      width: 250px;
      box-shadow: 2px 0 12px rgba(0,0,0,0.04);
      border-top-right-radius: 24px;
      border-bottom-right-radius: 24px;
    }
    
    .admin-main {
      margin-left: 250px;
      padding: 32px 32px 16px 32px;
      background: linear-gradient(135deg, #f7f9fa 80%, #fff 100%);
      min-height: 100vh;
    }
    
    .admin-sidebar .nav-link {
      font-weight: 500;
      font-size: 0.95rem;
      border-radius: 8px;
      margin-bottom: 6px;
      transition: background 0.2s, color 0.2s;
    }
    .admin-sidebar .nav-link.active, .admin-sidebar .nav-link:hover {
      background: #fff;
      color: #800000 !important;
    }
    .admin-sidebar .nav-link i {
      font-size: 1rem;
      vertical-align: middle;
    }
    .admin-card {
      border: 1.5px solid #800000;
      border-radius: 16px;
      transition: all 0.3s;
      background: #fff;
    }
    
    .admin-card:hover {
      box-shadow: 0 4px 18px rgba(128,0,0,0.13);
      transform: translateY(-4px) scale(1.01);
    }
    
    .admin-table thead {
      background: #800000;
      color: #fff;
      font-size: 0.85rem;
    }
    
    .admin-table tbody {
      font-size: 0.85rem;
    }
    
    .btn-admin {
      background: #800000;
      color: #fff;
      border-radius: 8px;
      font-weight: 600;
      padding: 8px 20px;
      transition: background 0.2s, color 0.2s;
      font-size: 0.85rem;
    }
    
    .btn-admin:hover {
      background: #fff;
      color: #800000;
      border: 1.5px solid #800000;
    }
    
    .text-maroon {
      color: #800000 !important;
    }
    
    @media (max-width: 991px) {
      .admin-sidebar {
        position: static;
        width: 100%;
        min-height: auto;
        border-radius: 0;
        box-shadow: none;
      }
      .admin-main {
        margin-left: 0;
        padding: 16px;
      }
    }

    h2 {
      font-size: 1.5rem;
    }

    h4 {
      font-size: 1.2rem;
    }

    h5 {
      font-size: 1rem;
    }

    .card-title {
      font-size: 1.1rem;
    }

    .footer {
      background: #800000;
      color: #fff;
      padding: 40px 0;
      position: relative;
      margin-top: 40px;
      font-size: 0.85rem;
    }
    .footer .logo {
      font-size: 1.3rem;
      font-weight: 700;
      color: #fff;
      text-decoration: none;
    }
    .footer .logo span {
      color: #ffc107;
    }
    .footer-about p {
      margin: 0;
      opacity: 0.9;
    }
    .footer-links {
      margin-top: 20px;
    }
    .footer-links h4 {
      font-size: 1.1rem;
      margin-bottom: 15px;
      position: relative;
    }
    .footer-links h4::after {
      content: '';
      position: absolute;
      width: 50px;
      height: 3px;
      background: #ffc107;
      bottom: -5px;
      left: 0;
    }
    .footer-links ul {
      list-style: none;
      padding: 0;
    }
    .footer-links ul li {
      margin-bottom: 10px;
    }
    .footer-links ul li a {
      color: #fff;
      text-decoration: none;
      transition: color 0.3s;
    }
    .footer-links ul li a:hover {
      color: #ffc107;
    }
    .footer-newsletter {
      margin-top: 20px;
    }
    .footer-newsletter h4 {
      font-size: 1.1rem;
      margin-bottom: 15px;
      position: relative;
    }
    .footer-newsletter h4::after {
      content: '';
      position: absolute;
      width: 50px;
      height: 3px;
      background: #ffc107;
      bottom: -5px;
      left: 0;
    }
    .footer-newsletter p {
      margin: 0;
      opacity: 0.9;
    }
  </style>
</head>

<body>

  <!-- Sidebar -->
  <div class="admin-sidebar">
    <div class="text-center mb-4">
      <i class="bi bi-building-fill text-white" style="font-size: 3.5rem;"></i>
      <h3 class="text-white mt-2" style="font-weight:700;">My.IIT Admin</h3>
    </div>
    
    <nav class="nav flex-column" aria-label="Admin sidebar navigation">
      <a class="nav-link text-white active" href="admin.php">
        <i class="bi bi-speedometer2 me-2"></i>Dashboard
      </a>
      <a class="nav-link text-white" href="admin-feedback.php">
        <i class="bi bi-chat-dots me-2"></i>Feedback Reports
      </a>
      <a class="nav-link text-white mt-auto" href="logout2.php" style="margin-top: auto !important;">
        <i class="bi bi-box-arrow-right me-2"></i>Logout
      </a>
    </nav>
  </div>

  <!-- Main Content -->
  <div class="admin-main">
    <h2 class="mb-4 text-maroon">Dashboard</h2>
    <!-- Dashboard Cards -->
    <div class="row g-4 mb-4">
      <div class="col-md-6 col-lg-6">
        <div class="card admin-card">
          <div class="card-body">
            <h5><i class="bi bi-chat me-2 text-maroon"></i>New Feedback</h5>
            <h2 class="text-maroon"><?php echo $total_feedbacks; ?></h2>
            <p class="mb-0"><?php echo $unread_messages; ?> unread messages</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activity Table -->
    <div class="card admin-card">
      <div class="card-body">
        <h4 class="card-title mb-4">
          <i class="bi bi-clock-history me-2"></i>Recent Activity
        </h4>
        
        <div class="table-responsive">
          <table class="table admin-table">
            <thead>
              <tr>
                <th>User</th>
                <th>Action</th>
                <th>Department</th>
                <th>Time</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recent_activities as $activity): ?>
                <tr>
                  <td><?php echo htmlspecialchars($activity['studentName']); ?></td>
                  <td><?php echo htmlspecialchars($activity['action']); ?></td>
                  <td><?php echo htmlspecialchars($activity['department']); ?></td>
                  <td><?php echo date('M d, Y H:i', strtotime($activity['time'])); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <a href="index.php" class="btn btn-light w-100 mb-3" style="color:#800000;font-weight:600;">
      <i class="bi bi-house-door me-2"></i>Back to Site
    </a>
  </div>

  <!-- Footer -->
  <footer class="footer position-relative dark-background mt-5">
    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-4 col-md-6 footer-about">
          <a href="index.php" class="logo d-flex align-items-center">
            <span class="sitename">My.IIT</span>
          </a>
          <div class="footer-contact pt-3">
            <p>Andres Bonifacio Ave, Iligan City</p>
            <p>9200 Lanao del Norte</p>
          </div>
        </div>
        <div class="col-lg-4 col-md-6 footer-links">
          <h4>Quick Links</h4>
          <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="admin.php">Admin Panel</a></li>
          </ul>
        </div>
        <div class="col-lg-4 col-md-6 footer-newsletter">
          <h4>Contact</h4>
          <p>info@msuiit.edu.ph</p>
        </div>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="admin/assets/js/now-ui-dashboard.min.js?v=1.5.0"></script>
  <script src="assets/js/main.js"></script>
</body>
</html>