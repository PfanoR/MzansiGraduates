<?php
session_start();
include 'admin/db_connect.php';

$error = "";

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email']);
  $password = $_POST['password'];

  if (empty($email) || empty($password)) {
    $error = "Please fill in both fields.";
  } else {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
      $user = $result->fetch_assoc();

      if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];

        header("Location: index.php"); // Redirect after successful login
        exit();
      } else {
        $error = "Incorrect password.";
      }
    } else {
      $error = "No account found with that email.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login | MzansiGraduates</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <link href="login-style.css" rel="stylesheet">


</head>
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

<body>

  <!-- Login Form -->
  <div class="login-container">
    <form method="POST" action="login.php">
      <h2 class="text-center mb-3">Welcome Back</h2>
      <p class="text-center text-muted mb-4">Login to continue to MzansiGraduates</p>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
      <?php endif; ?>

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fas fa-envelope"></i></span>
          <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" required>
        </div>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fas fa-lock"></i></span>
          <input type="password" class="form-control" name="password" id="password" placeholder="Enter your password"
            required>
        </div>
      </div>

      <div class="d-grid">
        <button type="submit" class="btn btn-primary">Login</button>
      </div>

      <div class="text-center mt-3 extra-links">
        <p>Don't have an account? <a href="signup.php">Sign up</a></p>
        <p><a href="#">Forgot password?</a></p>
      </div>
    </form>
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
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>