<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include 'admin/db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>MzansiGraduates | Search Jobs</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- FontAwesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  <!-- Custom CSS -->
  <link href="browse-job-style.css" rel="stylesheet" />
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
            <a class="nav-link text-white fs-5 me-3" href="index.php">
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

  <!-- Search Hero Section -->
  <section class="search-hero py-5">
    <div class="container">
      <div class="search-hero-content text-center">
        <h1 class="display-5 fw-bold mb-3">Find Your Dream Job</h1>
        <p class="lead mb-4">Search thousands of opportunities across South Africa</p>
        <div class="d-flex justify-content-center gap-4">
          <div class="text-center">
            <h4 class="fw-bold mb-1">500+</h4>
            <small>Active Jobs</small>
          </div>
          <div class="text-center">
            <h4 class="fw-bold mb-1">50+</h4>
            <small>Companies</small>
          </div>
          <div class="text-center">
            <h4 class="fw-bold mb-1">12</h4>
            <small>Categories</small>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Main Content -->
  <div class="main-content">
    <div class="container">
      <!-- Search Form -->
      <div class="search-form-container">
        <div class="row align-items-center mb-4">
          <div class="col">
            <h3 class="fw-bold mb-0">
              <i class="fas fa-search text-primary me-2"></i>Search Jobs
            </h3>
          </div>
          <div class="col-auto">
            <span class="badge bg-primary fs-6">Live Search</span>
          </div>
        </div>

        <form id="jobSearchForm" class="row g-4">
          <div class="col-md-4">
            <label for="keyword" class="form-label">
              <i class="fas fa-keyboard"></i>Job Title or Keywords
            </label>
            <input type="text" class="form-control" id="keyword" name="keyword"
              placeholder="e.g. Software Developer, Marketing" />
          </div>

          <div class="col-md-4">
            <label for="location" class="form-label">
              <i class="fas fa-map-marker-alt"></i>Location
            </label>
            <input type="text" class="form-control" id="location" name="location"
              placeholder="e.g. Johannesburg, Cape Town" />
          </div>

          <div class="col-md-4">
            <label for="category" class="form-label">
              <i class="fas fa-tags"></i>Category
            </label>
            <select id="category" name="category" class="form-select">
              <option value="">All Categories</option>
              <?php
          
              $categories = $conn->query("SELECT * FROM categories ORDER BY name ASC");
              while ($row = $categories->fetch_assoc()):
                ?>
                <option value="<?= htmlspecialchars($row['name']) ?>"><?= htmlspecialchars($row['name']) ?></option>
              <?php endwhile; ?>
            </select>

          </div>

          <div class="col-12">
            <div class="d-flex gap-3 justify-content-center">
              <button type="button" class="btn search-btn" onclick="performSearch()">
                <i class="fas fa-search me-2"></i>Search Jobs
              </button>
              <button type="button" class="btn clear-btn" onclick="clearSearch()">
                <i class="fas fa-times me-2"></i>Clear
              </button>
            </div>
          </div>
        </form>
      </div>

      <!-- Search Results -->
      <div id="searchResults" class="search-results" style="display: none;">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="fw-bold mb-0">Search Results</h4>
          <span id="resultsCount" class="badge bg-success fs-6"></span>
        </div>
        <div id="resultsContainer"></div>
      </div>

      <!-- Loading Spinner -->
      <div id="loadingSpinner" class="loading-spinner" style="display: none;">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3">Searching for jobs...</p>
      </div>

      <!-- Search Tips -->
      <div class="search-tips">
        <h5 class="fw-bold mb-3">
          <i class="fas fa-lightbulb text-warning me-2"></i>Search Tips
        </h5>
        <div class="row">
          <div class="col-md-6">
            <div class="tip-item">
              <div class="tip-icon">
                <i class="fas fa-star"></i>
              </div>
              <div>
                <strong>Use specific keywords</strong><br>
                <small>Try "Junior Developer" instead of just "Developer"</small>
              </div>
            </div>
            <div class="tip-item">
              <div class="tip-icon">
                <i class="fas fa-map"></i>
              </div>
              <div>
                <strong>Include nearby areas</strong><br>
                <small>Search broader locations for more opportunities</small>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="tip-item">
              <div class="tip-icon">
                <i class="fas fa-filter"></i>
              </div>
              <div>
                <strong>Use category filters</strong><br>
                <small>Narrow down results by selecting a category</small>
              </div>
            </div>
            <div class="tip-item">
              <div class="tip-icon">
                <i class="fas fa-clock"></i>
              </div>
              <div>
                <strong>Check regularly</strong><br>
                <small>New jobs are posted daily</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
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
            <li><a href="index.php" class="text-decoration-none">Home</a></li>
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
    
          <p><i class="fas fa-map-marker-alt me-2"></i>South Africa</p>
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

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const form = document.getElementById('jobSearchForm');
    const keywordInput = document.getElementById('keyword');
    const locationInput = document.getElementById('location');
    const categorySelect = document.getElementById('category');
    const resultsDiv = document.getElementById('searchResults');
    const loadingSpinner = document.getElementById('loadingSpinner');

    [keywordInput, locationInput, categorySelect].forEach(input => {
      input.addEventListener('input', () => {
        performSearch();
      });
    });

    function performSearch() {
      const keyword = keywordInput.value.trim();
      const location = locationInput.value.trim();
      const category = categorySelect.value;

      // Show loading spinner
      loadingSpinner.style.display = 'block';
      resultsDiv.style.display = 'none';

      // Skip search if all inputs are empty
      if (!keyword && !location && !category) {
        loadingSpinner.style.display = 'none';
        resultsDiv.style.display = 'none';
        return;
      }

      fetch(`search-api.php?keyword=${encodeURIComponent(keyword)}&location=${encodeURIComponent(location)}&category=${encodeURIComponent(category)}`)
        .then(response => response.text())
        .then(html => {
          resultsDiv.innerHTML = html;
          loadingSpinner.style.display = 'none';
          resultsDiv.style.display = 'block';
        })
        .catch(error => {
          resultsDiv.innerHTML = `<div class="alert alert-danger">Error loading jobs</div>`;
          loadingSpinner.style.display = 'none';
          resultsDiv.style.display = 'block';
          console.error('Error fetching jobs:', error);
        });
    }

    function clearSearch() {
      keywordInput.value = '';
      locationInput.value = '';
      categorySelect.value = '';
      resultsDiv.innerHTML = '';
      resultsDiv.style.display = 'none';
      loadingSpinner.style.display = 'none';
    }

    function saveJob(jobId) {
      alert('Job saved! (Login required)');
    }

    document.addEventListener('DOMContentLoaded', function () {
      const formContainer = document.querySelector('.search-form-container');
      formContainer.style.opacity = '0';
      formContainer.style.transform = 'translateY(20px)';

      setTimeout(() => {
        formContainer.style.transition = 'all 0.6s ease';
        formContainer.style.opacity = '1';
        formContainer.style.transform = 'translateY(0)';
      }, 300);
    });
  </script>

</body>

</html>