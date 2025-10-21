<?php
session_start();
include 'admin/db_connect.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

// Handle "Clear All Saved Jobs"
if (isset($_POST['clear_all']) && $_POST['clear_all'] == 1) {
  $clear_sql = "DELETE FROM saved_jobs WHERE user_id = ?";
  $clear_stmt = $conn->prepare($clear_sql);
  $clear_stmt->bind_param("i", $user_id);

  if ($clear_stmt->execute()) {
    $success_message = "All saved jobs cleared successfully!";
  } else {
    $error_message = "Failed to clear saved jobs.";
  }
}


// Handle delete request
if (isset($_POST['delete_job']) && isset($_POST['job_id'])) {
  $job_id = $_POST['job_id'];

  $delete_sql = "DELETE FROM saved_jobs WHERE user_id = ? AND job_id = ?";
  $delete_stmt = $conn->prepare($delete_sql);
  $delete_stmt->bind_param("ii", $user_id, $job_id);

  if ($delete_stmt->execute()) {
    $success_message = "Job removed successfully!";
  } else {
    $error_message = "Error removing job from saved jobs.";
  }
}


// Fetch saved jobs
$sql = "SELECT j.*, s.saved_at
        FROM saved_jobs s
        JOIN jobs j ON j.id = s.job_id
        WHERE s.user_id = ? ORDER BY s.saved_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Dashboard | MzansiGraduates</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
   <link href="dashboard-style.css" rel="stylesheet" />


  <style>
  
  </style>
</head>

<body>

  <!-- Navbar -->
  <header class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
      <a class="navbar-brand fs-2 fw-bold" href="index.php">
        <i class="fa-solid fa-user-graduate"></i>MzansiGraduates
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active text-white fs-5 me-3" href="index.php">
              <i class="fas fa-home me-1"></i>Home
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white fs-5 me-3" href="post-job.php">
              <i class="fas fa-plus-circle me-1"></i>Post a Job
            </a>
          </li>
          <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item">
              <a class="nav-link text-white fs-5 me-3" href="dashboard.php">
                <i class="fas fa-bookmark me-1"></i>Saved Jobs
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white fs-5" href="logout.php">
                <i class="fas fa-sign-out-alt me-1"></i>Logout
              </a>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link text-white fs-5" href="login.php">
                <i class="fas fa-user me-1"></i>Login/Signup
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </header>

  <!-- Saved Jobs -->
  <div class="container my-5">
    <h2 class="mb-4">
      <i class="fas fa-bookmark me-2"></i>Saved Jobs
      <span class="badge bg-primary ms-2"><?= $result->num_rows ?></span>
    </h2>

    <!-- Success/Error Messages -->
    <?php if (isset($success_message)): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= $success_message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?= $error_message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
      <?php while ($job = $result->fetch_assoc()): ?>
        <div class="card mb-4 job-card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <!-- Title -->
              <h5 class="fw-bold mb-2 flex-grow-1">
                <a href="job-details.php?id=<?= $job['id'] ?>" class="text-decoration-none job-link">
                  <?= htmlspecialchars($job['title']) ?>
                </a>
              </h5>

              <!-- Delete Button -->
              <div class="ms-3">
                <button type="button" class="btn btn-outline-danger btn-sm delete-btn" data-bs-toggle="modal"
                  data-bs-target="#deleteModal<?= $job['id'] ?>">
                  <i class="fas fa-trash-alt me-1"></i>Remove
                </button>
              </div>
            </div>

            <!-- Summary -->
            <p class="text-muted mb-3"><?= htmlspecialchars($job['summary']) ?></p>

            <hr>

            <div class="d-flex justify-content-between align-items-center small text-muted mb-2">
              <!-- Date and Location -->
              <div>
                <i class="fas fa-clock me-1"></i> Date Posted: <?= date('d F Y', strtotime($job['date_posted'])) ?>
                <span class="ms-4"><i
                    class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($job['location']) ?></span>
              </div>

              <!-- Saved Date -->
              <div>
                <i class="fas fa-bookmark me-1"></i> Saved: <?= date('d M Y', strtotime($job['saved_at'])) ?>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-3">
              <a href="job-details.php?id=<?= $job['id'] ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-eye me-1"></i>View Details
              </a>
            </div>
          </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal<?= $job['id'] ?>" tabindex="-1"
          aria-labelledby="deleteModalLabel<?= $job['id'] ?>" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel<?= $job['id'] ?>">
                  <i class="fas fa-exclamation-triangle text-warning me-2"></i>Confirm Removal
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <p>Are you sure you want to remove this job from your saved jobs?</p>
                <div class="card">
                  <div class="card-body">
                    <h6 class="card-title"><?= htmlspecialchars($job['company']) ?></h6>
                    <p class="card-text text-muted"><?= htmlspecialchars($job['title']) ?></p>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                  <i class="fas fa-times me-1"></i>Cancel
                </button>
                <form method="POST" style="display: inline;">
                  <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                  <button type="submit" name="delete_job" class="btn btn-danger">
                    <i class="fas fa-trash-alt me-1"></i>Remove Job
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      <?php endwhile; ?>

      <!-- Clear All Button -->
      <div class="text-center mt-4">
        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#clearAllModal">
          <i class="fas fa-trash me-1"></i>Clear All Saved Jobs
        </button>
      </div>

      <!-- Clear All Modal -->
      <div class="modal fade" id="clearAllModal" tabindex="-1" aria-labelledby="clearAllModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="clearAllModalLabel">
                <i class="fas fa-exclamation-triangle text-warning me-2"></i>Clear All Saved Jobs
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <p>Are you sure you want to remove <strong>all</strong> saved jobs? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="fas fa-times me-1"></i>Cancel
              </button>
              <form method="POST" style="display: inline;">
                <input type="hidden" name="clear_all" value="1">
                <button type="submit" name="delete_job" class="btn btn-danger">
                  <i class="fas fa-trash me-1"></i>Clear All
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>

    <?php else: ?>
      <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>You haven't saved any jobs yet.
        <a href="index.php" class="alert-link">Browse jobs</a> to start saving your favorites!
      </div>
    <?php endif; ?>
  </div>
  <!-- Footer -->
  <footer class="footer py-5">
    <div class="container">
      <div class="row gy-4">
        <div class="col-lg-4">
          <h3 class="fw-bold mb-3">
            <i class="fa-solid fa-user-graduate"></i>MzansiGraduates
          </h3>
          <p class="mb-3">Your go-to platform for youth employment in South Africa. Connecting talent with opportunity.
          </p>
          <div class="social-links">
            <a href="#" class="text-decoration-none">
              <i class="fab fa-facebook-f"></i>
            </a>
            <a href="#" class="text-decoration-none">
              <i class="fab fa-twitter"></i>
            </a>
            <a href="#" class="text-decoration-none">
              <i class="fab fa-linkedin-in"></i>
            </a>
            <a href="#" class="text-decoration-none">
              <i class="fab fa-instagram"></i>
            </a>
          </div>
        </div>

        <div class="col-lg-2 col-md-3">
          <h5 class="fw-bold mb-3">Quick Links</h5>
          <ul class="list-unstyled">
            <li><a href="browse-job.php" class="text-decoration-none">Browse Jobs</a></li>
            <li><a href="post-job.php" class="text-decoration-none">Post a Job</a></li>
            <li><a href="#" class="text-decoration-none">About Us</a></li>
            <li><a href="#" class="text-decoration-none">Contact</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3">
          <h5 class="fw-bold mb-3">Job Types</h5>
          <ul class="list-unstyled">
            <li><a href="jobs.php?id=12" class="text-decoration-none">Internships</a></li>
            <li><a href="jobs.php?id=13" class="text-decoration-none">Learnerships</a></li>
            <li><a href="jobs.php?id=11" class="text-decoration-none">Bursaries</a></li>
            <li><a href="jobs.php?id=10" class="text-decoration-none">Entry Level</a></li>
          </ul>
        </div>

        <div class="col-lg-4 col-md-6">
          <h5 class="fw-bold mb-3">Contact Info</h5>
          <p><i class="fas fa-envelope me-2"></i>info@mzansigraduates.co.za</p>
          <p><i class="fas fa-map-marker-alt me-2"></i> South Africa</p>
        </div>
      </div>

      <hr class="my-4" />

      <div class="row align-items-center">
        <div class="col-md-6">
          <small>&copy; 2025 MzansiGraduates. All Rights Reserved.</small>
        </div>
        <div class="col-md-6 text-md-end">
          <small>Powered By <strong>Ramph Technologies</strong></small>
        </div>
      </div>
    </div>
  </footer>


  <!-- Bootstrap JavaScript -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Auto-hide alerts -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Auto-hide success/error messages after 5 seconds
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(alert => {
        setTimeout(() => {
          const bsAlert = new bootstrap.Alert(alert);
          bsAlert.close();
        }, 5000);
      });
    });
  </script>



</body>

</html>