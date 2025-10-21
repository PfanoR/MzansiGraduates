<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

include 'admin/db_connect.php';

// Get category ID from URL
$category_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($category_id <= 0) {
  echo "Invalid category.";
  exit();
}

// Get category name
$category_sql = "SELECT name FROM categories WHERE id = $category_id";
$category_result = $conn->query($category_sql);

if ($category_result->num_rows === 0) {
  echo "Category not found.";
  exit();
}

$category = $category_result->fetch_assoc();

// Get jobs in this category
$jobs_per_page = 5;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$offset = ($page - 1) * $jobs_per_page;
$jobs_sql = "SELECT * FROM jobs 
             WHERE category_id = $category_id AND is_active = 1 
             ORDER BY id DESC 
             LIMIT $jobs_per_page OFFSET $offset";

$jobs_result = $conn->query($jobs_sql);

$count_sql = "SELECT COUNT(*) AS total FROM jobs 
              WHERE category_id = $category_id AND is_active = 1";

$count_result = $conn->query($count_sql);
$total_jobs = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_jobs / $jobs_per_page);

// Get latest jobs for sidebar
$latest_jobs_sql = "SELECT j.*, c.name as category_name FROM jobs j 
                   LEFT JOIN categories c ON j.category_id = c.id 
                   WHERE j.is_active = 1 
                   ORDER BY j.id DESC 
                   LIMIT 5";
$latest_jobs_result = $conn->query($latest_jobs_sql);

// Get saved jobs for current user to show saved status
$saved_jobs = [];
if (isset($_SESSION['user_id'])) {
  $saved_jobs_sql = "SELECT job_id FROM saved_jobs WHERE user_id = " . $_SESSION['user_id'];
  $saved_jobs_result = $conn->query($saved_jobs_sql);
  while ($saved_job = $saved_jobs_result->fetch_assoc()) {
    $saved_jobs[] = $saved_job['job_id'];
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($category['name']) ?> Jobs | MzansiGraduates</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
   <link href="jobs-style.css" rel="stylesheet" />
  <style>
    .page-title {
      background: linear-gradient(45deg, #667eea, #764ba2);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      font-weight: 700;
      font-size: 2.5rem;
      margin-bottom: 2rem;
      text-align: center;
    }

    .navbar-brand {
      color: white !important;
      font-weight: 700;
      /* Remove gradient text */
      background: none !important;
      -webkit-background-clip: initial !important;
      -webkit-text-fill-color: initial !important;
      text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
    }
      .summary-text {
     color: #666;
     line-height: 1.6;
     margin: 1rem 0;

     display: -webkit-box;
     -webkit-line-clamp: 3;
     -webkit-box-orient: vertical;
     overflow: hidden;
     text-overflow: ellipsis;
   }
    
  </style>
</head>

<body>
  <div class="content-wrapper">
    <!-- Header -->
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

    <!-- Success Message Container -->
    <div id="successMessageContainer"></div>

    <!-- Main Content -->
    <div class="main-container">
      <div class="row">
        <!-- Left Column - Main Content -->
        <div class="col-lg-8">
          <h1 class="page-title">
            <i class="fas fa-briefcase me-3"></i>
            <?= htmlspecialchars($category['name']) ?>
          </h1>

          <!-- Job Filter Frame -->
          <div class="job-filter-frame">
            <div class="search-container">
              <i class="fas fa-search search-icon"></i>
              <input type="text" id="jobSearch" class="form-control search-input" placeholder="Search jobs by title"
                aria-label="Search jobs">
            </div>
          </div>

          <!-- Jobs List -->
          <div id="jobsList">
            <?php if ($jobs_result->num_rows > 0): ?>
              <?php while ($job = $jobs_result->fetch_assoc()): ?>
                <div class="job-card" data-title="<?= htmlspecialchars(strtolower($job['title'])) ?>"
                  data-company="<?= htmlspecialchars(strtolower($job['company'])) ?>"
                  data-summary="<?= htmlspecialchars(strtolower($job['summary'])) ?>"
                  data-location="<?= htmlspecialchars(strtolower($job['location'])) ?>">
                  <div class="card-body p-4">
                    
                    <h5 class="mb-3">
                      <a href="job-details.php?id=<?= $job['id'] ?>" class="text-decoration-none job-title">
                        <?= htmlspecialchars($job['title']) ?>
                      </a>
                    </h5>
                    <p class="summary-text"><?= htmlspecialchars($job['description']) ?></p>

                    <div class="job-meta">
                      <div class="meta-item">
                        <i class="fas fa-building"></i>
                        <span class="company"><?= htmlspecialchars($job['company']) ?></span>
                      </div>
                      <div class="meta-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span class="job-location"><?= htmlspecialchars($job['location']) ?></span>
                      </div>
                      <div class="meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span class="job-date"><?= date('d M Y', strtotime($job['date_posted'])) ?></span>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endwhile; ?>
            <?php else: ?>
              <div class="alert alert-info text-center py-4">
                <i class="fas fa-info-circle fa-2x mb-3 text-primary"></i>
                <h5>No jobs found in this category</h5>
                <p class="mb-0">Check back later for new opportunities!</p>
              </div>
            <?php endif; ?>
          </div>

          <!-- Pagination -->
          <?php if ($total_pages > 1): ?>
            <nav aria-label="Job pagination">
              <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                  <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="?id=<?= $category_id ?>&page=<?= $i ?>"><?= $i ?></a>
                  </li>
                <?php endfor; ?>
              </ul>
            </nav>
          <?php endif; ?>
        </div>

        <!-- Right Column - Sidebar -->
        <div class="col-lg-4">
          <div class="sidebar">
            <!-- Stats Card -->
            <div class="stats-card">
              <div class="stats-number"><?= $total_jobs ?></div>
              <div class="stats-label">Total Jobs Available</div>
            </div>

            <!-- Latest Jobs -->
            <div class="sidebar-title">
              <i class="fas fa-fire"></i>
              Latest Jobs
            </div>

            <?php if ($latest_jobs_result->num_rows > 0): ?>
              <?php while ($latest_job = $latest_jobs_result->fetch_assoc()): ?>
                <div class="latest-job-item">
                  <a href="job-details.php?id=<?= $latest_job['id'] ?>" class="latest-job-title">
                    <?= htmlspecialchars($latest_job['title']) ?>
                  </a>
                  <div class="latest-job-company">
                    <i class="fas fa-building me-1"></i>
                    <?= htmlspecialchars($latest_job['company']) ?>
                  </div>
                  <div class="latest-job-date">
                    <i class="fas fa-clock me-1"></i>
                    <?= date('d M Y', strtotime($latest_job['date_posted'])) ?>
                  </div>
                </div>
              <?php endwhile; ?>
            <?php else: ?>
              <div class="text-center text-muted py-4">
                <i class="fas fa-inbox fa-2x mb-2"></i>
                <p>No recent jobs available</p>
              </div>
            <?php endif; ?>

            <!-- Quick Actions -->
            <div class="sidebar-title mt-4">
              <i class="fas fa-bolt"></i>
              Quick Actions
            </div>

            <div class="d-grid gap-2">
              <a href="post-job.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Post a Job
              </a>
              <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php" class="btn btn-outline-primary">
                  <i class="fas fa-bookmark me-2"></i>View Saved Jobs
                </a>
              <?php else: ?>
                <a href="login.php" class="btn btn-outline-primary">
                  <i class="fas fa-user me-2"></i>Login/Signup
                </a>
              <?php endif; ?>
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
            <p class="mb-3">Your go-to platform for youth employment in South Africa. Connecting talent with
              opportunity.</p>
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
  </div>

  <!-- Bootstrap JS and Custom Script -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const searchInput = document.getElementById('jobSearch');
      const jobCards = document.querySelectorAll('.job-card');

      // Search functionality with improved highlighting
      searchInput.addEventListener('input', () => {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let visibleJobs = 0;

        jobCards.forEach(card => {
          const title = card.dataset.title;
          const company = card.dataset.company;
          const summary = card.dataset.summary;
          const location = card.dataset.location;

          // Reset previous highlights
          const companyElement = card.querySelector('.company');
          const titleElement = card.querySelector('.job-title');
          const summaryElement = card.querySelector('.summary-text');
          const locationElement = card.querySelector('.job-location');

          if (companyElement) companyElement.innerHTML = companyElement.textContent;
          if (titleElement) titleElement.innerHTML = titleElement.textContent;
          if (summaryElement) summaryElement.innerHTML = summaryElement.textContent;
          if (locationElement) locationElement.innerHTML = locationElement.textContent;

          if (
            title.includes(searchTerm) ||
            company.includes(searchTerm) ||
            summary.includes(searchTerm) ||
            location.includes(searchTerm)
          ) {
            card.classList.remove('hidden');
            card.style.display = 'block';
            visibleJobs++;

            // Highlight matching terms with better regex
            if (searchTerm !== '') {
              const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');

              if (companyElement && company.includes(searchTerm)) {
                companyElement.innerHTML = companyElement.textContent.replace(regex, '<span class="highlight">$1</span>');
              }
              if (titleElement && title.includes(searchTerm)) {
                titleElement.innerHTML = titleElement.textContent.replace(regex, '<span class="highlight">$1</span>');
              }
              if (summaryElement && summary.includes(searchTerm)) {
                summaryElement.innerHTML = summaryElement.textContent.replace(regex, '<span class="highlight">$1</span>');
              }
              if (locationElement && location.includes(searchTerm)) {
                locationElement.innerHTML = locationElement.textContent.replace(regex, '<span class="highlight">$1</span>');
              }
            }
          } else {
            card.classList.add('hidden');
            card.style.display = 'none';
          }
        });

        // Update job count in stats if element exists
        const statsNumber = document.querySelector('.stats-number');
        if (statsNumber && searchTerm) {
          statsNumber.textContent = visibleJobs;
        } else if (statsNumber) {
          statsNumber.textContent = <?= $total_jobs ?>;
        }
      });

      // Add smooth scrolling to job cards
      const jobLinks = document.querySelectorAll('.job-title');
      jobLinks.forEach(link => {
        link.addEventListener('click', (e) => {
          // Add a subtle animation before navigation
          const card = link.closest('.job-card');
          card.style.transform = 'scale(0.98)';
          setTimeout(() => {
            card.style.transform = 'scale(1)';
          }, 150);
        });
      });

      // Add loading animation for search
      let searchTimeout;
      searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchInput.style.background = 'linear-gradient(90deg, #f8f9fa 25%, #e9ecef 50%, #f8f9fa 75%)';
        searchInput.style.backgroundSize = '200% 100%';
        searchInput.style.animation = 'loading 1s ease-in-out';

        searchTimeout = setTimeout(() => {
          searchInput.style.background = 'white';
          searchInput.style.animation = 'none';
        }, 500);
      });
    });

    // Add CSS animation for loading effect
    const style = document.createElement('style');
    style.textContent = `
      @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
      }
      
      .job-card {
        animation: slideInUp 0.6s ease-out;
      }
      
      @keyframes slideInUp {
        from {
          opacity: 0;
          transform: translateY(30px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }
    `;
    document.head.appendChild(style);
  </script>
</body>

</html>