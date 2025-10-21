<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include 'admin/db_connect.php';


if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['submit_job'])) {
  $job_title = $conn->real_escape_string(trim($_POST['job_title']));
  $job_description = $conn->real_escape_string(trim($_POST['job_description']));
  $service_category = $conn->real_escape_string(trim($_POST['service_category']));
  $location = $conn->real_escape_string(trim($_POST['location']));
  $availability = $conn->real_escape_string(trim($_POST['availability']));
  $contact_name = $conn->real_escape_string(trim($_POST['contact_name']));
  $contact_email = $conn->real_escape_string(trim($_POST['contact_email']));
  $contact_phone = $conn->real_escape_string(trim($_POST['contact_phone']));
  $budget = $conn->real_escape_string(trim($_POST['budget']));

  if (empty($job_title) || empty($job_description) || empty($location) || empty($contact_name)) {
    echo "<div class='alert alert-danger'>Please fill all required fields.</div>";
  } else {
    $user_id = $_SESSION['user_id'];
    $sql = "INSERT INTO home_services_jobs (user_id, job_title, job_description, service_category, location, availability, contact_name, contact_email, contact_phone, budget)
            VALUES ('$user_id', '$job_title', '$job_description', '$service_category', '$location', '$availability', '$contact_name', '$contact_email', '$contact_phone', '$budget')";

    if ($conn->query($sql) === TRUE) {
      header("Location: post-job.php?success=1");
    } else {
      echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
  }
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Post Personal Services </title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <link href="post-job-style.css" rel="stylesheet" />

  <script>
    const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
  </script>
</head>

<body>
  <!-- Navigation -->
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

  <!-- Form Section -->
  <div class="container mt-5">
    <h2>Post Personal Services</h2>
    <form action="" method="POST">
      <div class="mb-3">
        <label for="job_title" class="form-label">Job Title *</label>
        <input type="text" name="job_title" id="job_title" class="form-control" required />
      </div>

      <div class="mb-3">
        <label for="job_description" class="form-label">Job Description *</label>
        <textarea name="job_description" id="job_description" class="form-control" rows="5" required></textarea>
      </div>

      <div class="mb-3">
        <label for="service_category" class="form-label">Service Category</label>
        <input type="text" name="service_category" id="service_category" class="form-control"
          placeholder="e.g. Plumber, Babysitter" />
      </div>

      <div class="mb-3">
        <label for="location" class="form-label">Location *</label>
        <input type="text" name="location" id="location" class="form-control" required />
      </div>

      <div class="mb-3">
        <label for="availability" class="form-label">Availability</label>
        <input type="text" name="availability" id="availability" class="form-control"
          placeholder="e.g. Weekdays only, Weekends, Flexible" />
      </div>

      <div class="mb-3">
        <label for="contact_name" class="form-label">Contact Name *</label>
        <input type="text" name="contact_name" id="contact_name" class="form-control" required />
      </div>

      <div class="mb-3">
        <label for="contact_email" class="form-label">Contact Email</label>
        <input type="email" name="contact_email" id="contact_email" class="form-control" />
      </div>

      <div class="mb-3">
        <label for="contact_phone" class="form-label">Contact Phone</label>
        <input type="text" name="contact_phone" id="contact_phone" class="form-control" />
      </div>

      <div class="mb-3">
        <label for="budget" class="form-label">Price</label>
        <input type="text" name="budget" id="budget" class="form-control" placeholder="e.g. Negotiable, R300 per day" />
      </div>

      <button type="submit" name="submit_job" class="btn btn-primary">Post Job</button>
    </form>
  </div>

  <!-- Footer -->
  <footer class="footer py-5">
    <div class="container">
      <div class="row gy-4">
        <div class="col-lg-4">
          <h3 class="fw-bold mb-3">
            <i class="fa-solid fa-user-graduate"></i> MzansiGraduates
          </h3>
          <p>Your go-to platform for youth employment in South Africa.</p>
          <div class="social-links">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-linkedin-in"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
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
          <p><i class="fas fa-envelope me-2"></i> info@mzansigraduates.co.za</p>
          <p><i class="fas fa-map-marker-alt me-2"></i> South Africa</p>
        </div>
      </div>

      <hr class="my-4" />

      <div class="row align-items-center">
        <div class="col-md-6">
          <small>&copy; 2025 MzansiGraduates. All Rights Reserved.</small>
        </div>
        <div class="col-md-6 text-md-end">
          <small>Powered by <strong>Ramph Technologies</strong></small>
        </div>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const postJobBtn = document.querySelector('button[name="submit_job"]');
      const form = postJobBtn.closest('form');

      postJobBtn.addEventListener('click', function (event) {
        if (!isLoggedIn) {
          event.preventDefault(); // Stop form submit
          alert('Please log in to post a job.');
          window.location.href = 'login.php?redirect=post-job';
        }
      });
    });
    // Check if URL has ?success=1
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('success') === '1') {
      alert("Job submitted successfully! Our team will review it and post it in the Home & Personal Service category.");

      // Optionally, remove the ?success=1 from URL so alert doesn't show again on refresh
      if (history.replaceState) {
        const newUrl = window.location.href.split('?')[0];
        history.replaceState(null, '', newUrl);
      }
    }
  </script>


</body>


</html>