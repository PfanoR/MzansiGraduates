<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

include 'admin/db_connect.php'; // Update the path if needed

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $full_name = trim($_POST['fullname']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $confirm_password = $_POST['confirmPassword'];

  // Validate inputs
  if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
    $errors[] = "All fields are required.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
  } elseif ($password !== $confirm_password) {
    $errors[] = "Passwords do not match.";
  }

  // Check for existing user and insert
  if (empty($errors)) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
      $errors[] = "Email is already registered.";
    } else {
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);
      $role = 'job_seeker';

      $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
      $stmt->bind_param("ssss", $full_name, $email, $hashed_password, $role);

      if ($stmt->execute()) {
        header("Location: signup.php?success=1");
        exit();
      } else {
        $errors[] = "Something went wrong. Please try again.";
      }
    }

    $stmt->close();
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Signup | MzansiGraduates</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="signup-style.css" rel="stylesheet">
  <style>
   
  </style>

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
</head>

<body>


  <div class="signup-box">
    <h2 class="text-center mb-3">Create an Account</h2>
    <p class="text-center text-muted mb-4">Join MzansiGraduates and start your journey today!</p>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?>
          <div><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="mb-3">
        <label for="fullname" class="form-label">Full Name</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fas fa-user"></i></span>
          <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Enter your full name"
            required>
        </div>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fas fa-envelope"></i></span>
          <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
        </div>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fas fa-lock"></i></span>
          <input type="password" class="form-control" id="password" name="password" placeholder="Create a password"
            required>
        </div>
      </div>

      <div class="mb-4">
        <label for="confirmPassword" class="form-label">Confirm Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fas fa-lock"></i></span>
          <input type="password" class="form-control" id="confirmPassword" name="confirmPassword"
            placeholder="Confirm your password" required>
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-100">Sign Up</button>

      <div class="text-center mt-3 extra-links">
        <p>Already have an account? <a href="login.php">Login here</a></p>
      </div>
    </form>
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
      <script>
        alert("Account created successfully! Redirecting to login...");
        window.location.href = "login.php";
      </script>
    <?php endif; ?>

  </div>
  <!-- Footer -->


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>