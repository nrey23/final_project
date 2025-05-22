<?php
include "config.php";

// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Get filter parameters
$selected_office = isset($_GET['office']) ? $_GET['office'] : 'all';
$selected_status = isset($_GET['status']) ? $_GET['status'] : 'all';

// Handle status updates
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
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle mark as pending
if (isset($_POST['pending_id']) && isset($_POST['office'])) {
    $office = $_POST['office'];
    $id = intval($_POST['pending_id']);
    if (in_array($office, ['registrar', 'library', 'finance'])) {
        $sql = "UPDATE `$office` SET status='Pending' WHERE id=?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch feedback from all departments
$all_feedbacks = [];
$recent_activities = [];

$departments = ['registrar', 'library', 'finance'];
foreach ($departments as $dept) {
    // Skip if filtering by office and this isn't the selected office
    if ($selected_office !== 'all' && $dept !== $selected_office) {
        continue;
    }
    
    // Modified query to explicitly select all fields including date
    $sql = "SELECT id, studentName, studentId, email, service, rating, feedback, status, date, '$dept' as department FROM `$dept`";
    
    // Add status filter if selected
    if ($selected_status !== 'all') {
        $sql .= " WHERE status " . ($selected_status === 'Resolved' ? "= 'Resolved'" : "IS NULL OR status = 'Pending'");
    }
    
    $sql .= " ORDER BY date DESC";
    
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

// Sort all feedback by date in descending order
usort($all_feedbacks, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});

// Sort recent activities by time
usort($recent_activities, function($a, $b) {
    return strtotime($b['time']) - strtotime($a['time']);
});

// Get only the 5 most recent activities
$recent_activities = array_slice($recent_activities, 0, 5);

// Get statistics
$new_feedbacks = count($all_feedbacks);
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
      <a class="nav-link text-white" href="admin.php">
        <i class="bi bi-speedometer2 me-2"></i>Dashboard
      </a>
      <a class="nav-link text-white active" href="admin-feedback.php">
        <i class="bi bi-chat-dots me-2"></i>Feedback Reports
      </a>
      <a class="nav-link text-white" href="logout2.php">
        <i class="bi bi-box-arrow-right me-2"></i>Logout
      </a>
    </nav>
  </div>

  <!-- Main Content -->
  <div class="admin-main">
    <h2 class="mb-4 text-maroon">Feedback Reports</h2>
    <!-- Dashboard Cards -->
    <div class="row g-4 mb-4">
      <div class="col-md-6 col-lg-6">
        <div class="card admin-card">
          <div class="card-body">
            <h5><i class="bi bi-chat me-2 text-maroon"></i>New Feedback</h5>
            <h2 class="text-maroon"><?php echo $new_feedbacks; ?></h2>
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

    <!-- Student Feedback Table -->
    <div class="card admin-card mb-4">
      <div class="card-body">
        <h4 class="card-title mb-4">
          <i class="bi bi-chat-dots me-2"></i>Student Feedback
        </h4>
        
        <!-- Filter Options -->
        <div class="row mb-4">
          <div class="col-md-6">
            <form method="get" class="row g-3">
              <div class="col-md-5">
                <label for="office" class="form-label">Filter by Office</label>
                <select name="office" id="office" class="form-select" onchange="this.form.submit()">
                  <option value="all" <?php echo $selected_office === 'all' ? 'selected' : ''; ?>>All Offices</option>
                  <option value="registrar" <?php echo $selected_office === 'registrar' ? 'selected' : ''; ?>>Registrar</option>
                  <option value="library" <?php echo $selected_office === 'library' ? 'selected' : ''; ?>>Library</option>
                  <option value="finance" <?php echo $selected_office === 'finance' ? 'selected' : ''; ?>>Finance</option>
                </select>
              </div>
              <div class="col-md-5">
                <label for="status" class="form-label">Filter by Status</label>
                <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                  <option value="all" <?php echo $selected_status === 'all' ? 'selected' : ''; ?>>All Status</option>
                  <option value="Pending" <?php echo $selected_status === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                  <option value="Resolved" <?php echo $selected_status === 'Resolved' ? 'selected' : ''; ?>>Resolved</option>
                </select>
              </div>
              <div class="col-md-2 d-flex align-items-end">
                <a href="admin-feedback.php" class="btn btn-secondary w-100">Reset</a>
              </div>
            </form>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table admin-table">
            <thead>
              <tr>
                <th>Student</th>
                <th>Message</th>
                <th>Department</th>
                <th>Date</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($all_feedbacks as $fb) {
                  $status = isset($fb['status']) && $fb['status'] === 'Resolved' ? '<span class="badge bg-success">Resolved</span>' : '<span class="badge bg-warning text-dark">Pending</span>';
                  $date = isset($fb['date']) ? date('M d, Y H:i', strtotime($fb['date'])) : 'N/A';
                  echo "<tr>
                          <td>{$fb['studentName']}</td>
                          <td>{$fb['feedback']}</td>
                          <td>{$fb['department']}</td>
                          <td>{$date}</td>
                          <td>{$status}</td>
                          <td>
                            <div class='btn-group' role='group'>
                              <button type='button' class='btn btn-sm btn-info view-feedback' data-bs-toggle='modal' data-bs-target='#viewModal' 
                                data-student-name='".htmlspecialchars($fb['studentName'])."'
                                data-student-id='".htmlspecialchars($fb['studentId'])."'
                                data-email='".htmlspecialchars($fb['email'])."'
                                data-department='".htmlspecialchars($fb['department'])."'
                                data-service='".htmlspecialchars($fb['service'])."'
                                data-rating='".htmlspecialchars($fb['rating'])."'
                                data-feedback='".htmlspecialchars($fb['feedback'])."'
                                data-status='".htmlspecialchars($fb['status'] ?? 'Pending')."'
                                data-date='".htmlspecialchars($date)."'>
                                <i class='bi bi-eye'></i> View
                              </button>";
                  
                  if (!isset($fb['status']) || $fb['status'] !== 'Resolved') {
                      echo "<form method='post' style='display:inline'>
                              <input type='hidden' name='solve_id' value='{$fb['id']}'>
                              <input type='hidden' name='office' value='{$fb['department']}'>
                              <button type='submit' class='btn btn-sm btn-success'>
                                <i class='bi bi-check-circle'></i> Resolve
                              </button>
                            </form>";
                  } else {
                      echo "<form method='post' style='display:inline'>
                              <input type='hidden' name='pending_id' value='{$fb['id']}'>
                              <input type='hidden' name='office' value='{$fb['department']}'>
                              <button type='submit' class='btn btn-sm btn-warning'>
                                <i class='bi bi-clock'></i> Mark Pending
                              </button>
                            </form>";
                  }
                  
                  echo "</div>
                        </td>
                      </tr>";
              }
              ?>
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
  <script>
document.addEventListener('DOMContentLoaded', function() {
    // Add click event listener to all view buttons
    document.querySelectorAll('.view-feedback').forEach(button => {
        button.addEventListener('click', function() {
            // Get data from data attributes
            const studentName = this.getAttribute('data-student-name');
            const studentId = this.getAttribute('data-student-id');
            const email = this.getAttribute('data-email');
            const department = this.getAttribute('data-department');
            const service = this.getAttribute('data-service');
            const rating = this.getAttribute('data-rating');
            const feedback = this.getAttribute('data-feedback');
            const status = this.getAttribute('data-status');
            const date = this.getAttribute('data-date');
            
            // Update modal content
            document.getElementById('modalStudentName').textContent = studentName || 'N/A';
            document.getElementById('modalStudentId').textContent = studentId || 'N/A';
            document.getElementById('modalEmail').textContent = email || 'N/A';
            document.getElementById('modalDepartment').textContent = department || 'N/A';
            document.getElementById('modalService').textContent = service || 'N/A';
            document.getElementById('modalMessage').textContent = feedback || 'N/A';
            document.getElementById('modalStatus').textContent = status || 'Pending';
            document.getElementById('modalDate').textContent = date || 'N/A';
            
            // Display rating as stars
            const ratingContainer = document.getElementById('modalRating');
            ratingContainer.innerHTML = '';
            const ratingValue = parseInt(rating) || 0;
            for (let i = 0; i < ratingValue; i++) {
                ratingContainer.innerHTML += '<i class="bi bi-star-fill text-warning"></i>';
            }
        });
    });
});
  </script>

  <!-- Add View Modal -->
  <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewModalLabel">Feedback Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <strong>Student Name:</strong>
            <p id="modalStudentName" class="mb-0"></p>
          </div>
          <div class="mb-3">
            <strong>Student ID:</strong>
            <p id="modalStudentId" class="mb-0"></p>
          </div>
          <div class="mb-3">
            <strong>Email:</strong>
            <p id="modalEmail" class="mb-0"></p>
          </div>
          <div class="mb-3">
            <strong>Department:</strong>
            <p id="modalDepartment" class="mb-0"></p>
          </div>
          <div class="mb-3">
            <strong>Service:</strong>
            <p id="modalService" class="mb-0"></p>
          </div>
          <div class="mb-3">
            <strong>Rating:</strong>
            <p id="modalRating" class="mb-0"></p>
          </div>
          <div class="mb-3">
            <strong>Message:</strong>
            <p id="modalMessage" class="mb-0"></p>
          </div>
          <div class="mb-3">
            <strong>Status:</strong>
            <p id="modalStatus" class="mb-0"></p>
          </div>
          <div class="mb-3">
            <strong>Date:</strong>
            <p id="modalDate" class="mb-0"></p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</body>
</html>