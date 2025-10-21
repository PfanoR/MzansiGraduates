<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>

<?php
include 'admin/db_connect.php';

// Get all categories
$category_sql = "SELECT c.*, 
    (SELECT COUNT(*) FROM jobs j WHERE j.category_id = c.id AND j.is_active = 1) AS job_count 
    FROM categories c ORDER BY name";
$categories = $conn->query($category_sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>MzansiGraduates | Mzansi's Gateway to Youth Employment</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  
  <!-- Custom CSS -->
    <link href="index-style.css" rel="stylesheet" />
 
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

  <!-- Hero Section -->
  <section class="hero-section py-5">
    <div class="container">
      <div class="row align-items-center min-vh-50">
        <div class="col-lg-8 mx-auto text-center hero-content">
          <h1 class="display-4 fw-bold mb-4">Mzansi's Gateway to Youth Employment</h1>
          <p class="lead mb-4 fs-4">Find internships, learnerships, graduate programmes & entry-level jobs across South Africa.</p>
          
          <div class="row justify-content-center mb-4">
            <div class="col-md-4">
              <div class="stats-counter">
                <h3 class="fw-bold mb-1">500+</h3>
                <p class="mb-0">Active Jobs</p>
              </div>
            </div>
            <div class="col-md-4">
              <div class="stats-counter">
                <h3 class="fw-bold mb-1">10K+</h3>
                <p class="mb-0">Job Seekers</p>
              </div>
            </div>
            <div class="col-md-4">
              <div class="stats-counter">
                <h3 class="fw-bold mb-1">200+</h3>
                <p class="mb-0">Companies</p>
              </div>
            </div>
          </div>
          
          <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="browse-job.php" class="btn btn-light btn-lg btn-custom">
              <i class="fas fa-search me-2"></i>Browse Jobs
            </a>
            <a href="post-job.php" class="btn btn-outline-light btn-lg btn-outline-custom">
              <i class="fas fa-briefcase me-2"></i>Post a Job
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Dynamic Job Categories -->
  <section class="py-5 bg-light">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="section-title fw-bold">Explore Job Categories</h2>
        <p class="lead text-muted">Discover opportunities in your field of interest</p>
      </div>
      
      <div class="row g-4">
        <?php while ($cat = $categories->fetch_assoc()): ?>
          <div class="col-6 col-md-4 col-lg-3">
            <div class="card category-card text-center h-100">
              <div class="card-body">
                <i class="<?= htmlspecialchars($cat['icon_class']) ?> fa-3x mb-3"></i>
                <h5 class="card-title fw-bold"><?= htmlspecialchars($cat['name']) ?></h5>
                <p class="card-text text-muted mb-3"><?= $cat['job_count'] ?> job<?= $cat['job_count'] !== 1 ? 's' : '' ?> available</p>
                <div class="d-flex justify-content-center">
                  <span class="badge bg-primary rounded-pill">View Jobs</span>
                </div>
                <a href="jobs.php?id=<?= $cat['id'] ?>" class="stretched-link"></a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    </div>
  </section>

  <!-- Why Use Section -->
  <section class="py-5">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="section-title fw-bold">Why Choose MzansiGraduates?</h2>
        <p class="lead text-muted">Your success is our priority</p>
      </div>
      
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card feature-card text-center p-4">
            <div class="feature-icon">
              <i class="fas fa-map-marker-alt"></i>
            </div>
            <h5 class="fw-bold mb-3">For South African Youth</h5>
            <p class="text-muted">Jobs made for locals, by locals. We understand the South African job market and connect you with opportunities that matter.</p>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="card feature-card text-center p-4">
            <div class="feature-icon">
              <i class="fas fa-seedling"></i>
            </div>
            <h5 class="fw-bold mb-3">No Experience Needed</h5>
            <p class="text-muted">We focus on entry-level and junior roles perfect for graduates and young professionals starting their careers.</p>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="card feature-card text-center p-4">
            <div class="feature-icon">
              <i class="fas fa-bell"></i>
            </div>
            <h5 class="fw-bold mb-3">Free Job Alerts</h5>
            <p class="text-muted">Get notified when new jobs match your field. Never miss an opportunity that could change your life.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

<!-- 
  Newsletter Section (Temporarily Disabled)

  <section class="newsletter-section py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="newsletter-form text-center">
            <h2 class="fw-bold mb-3">Stay Updated</h2>
            <p class="lead mb-4">Join our mailing list and never miss a job post again. Get weekly updates on new opportunities.</p>
            
            <form class="row g-3 justify-content-center">
              <div class="col-md-6">
                <input type="email" class="form-control form-control-lg" placeholder="Enter your email address" required />
              </div>
              <div class="col-md-4">
                <button type="submit" class="btn btn-light btn-lg w-100 fw-bold">
                  <i class="fas fa-paper-plane me-2"></i>Subscribe
                </button>
              </div>
            </form>
            
            <small class="text-white-50 mt-3 d-block">
              <i class="fas fa-lock me-1"></i>We respect your privacy. Unsubscribe anytime.
            </small>
          </div>
        </div>
      </div>
    </div>
  </section>
-->

  <!-- Footer -->
  <footer class="footer py-5">
    <div class="container">
      <div class="row gy-4">
        <div class="col-lg-4">
          <h3 class="fw-bold mb-3">
            <i class="fa-solid fa-user-graduate"></i>MzansiGraduates
          </h3>
          <p class="mb-3">Your go-to platform for youth employment in South Africa. Connecting talent with opportunity.</p>
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Custom JS -->
  <script>
    // Add smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
          behavior: 'smooth'
        });
      });
    });

    // Add loading animation for category cards
    document.addEventListener('DOMContentLoaded', function() {
      const cards = document.querySelectorAll('.category-card');
      cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
          card.style.transition = 'all 0.6s ease';
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }, index * 100);
      });
    });
  </script>
</body>

</html>