<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>

<?php
include 'admin/db_connect.php';

$job_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($job_id <= 0) {
  echo "Invalid Job ID.";
  exit();
}

$sql = "SELECT j.*, c.name as category_name FROM jobs j 
        LEFT JOIN categories c ON j.category_id = c.id 
        WHERE j.id = $job_id AND j.is_active = 1";

$result = $conn->query($sql);
if ($result->num_rows === 0) {
  echo "Job not found.";
  exit();
}

$job = $result->fetch_assoc();

// Check if job is saved by current user
$is_saved = false;
if (isset($_SESSION['user_id'])) {
  $saved_check_sql = "SELECT id FROM saved_jobs WHERE user_id = " . $_SESSION['user_id'] . " AND job_id = $job_id";
  $saved_check_result = $conn->query($saved_check_sql);
  $is_saved = $saved_check_result->num_rows > 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars($job['title']) ?> - Job Details | MzansiGraduates</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
    rel="stylesheet">
  <link href="job-details-style.css" rel="stylesheet" />

</head>


<body>
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
  <div class="main-content">

    <!-- Job Header -->
    <div class="job-header">


      <h1 class="job-title"><?= htmlspecialchars($job['title']) ?></h1>

      <div class="job-meta">
        <?php if (!empty($job['category_name'])): ?>
          <div class="meta-item">
            <i class="fas fa-tag"></i>
            <?= htmlspecialchars($job['category_name']) ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($job['location'])): ?>
          <div class="meta-item">
            <i class="fas fa-map-marker-alt"></i>
            <?= htmlspecialchars($job['location']) ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($job['closing_date']) && $job['closing_date'] !== '0000-00-00'): ?>
          <div class="meta-item">
            <i class="fas fa-calendar-alt"></i>
            Closes: <?= date('d M Y', strtotime($job['closing_date'])) ?>
          </div>
        <?php endif; ?>

        <div class="meta-item">
          <i class="fas fa-clock"></i>
          Posted: <?= date('d M Y', strtotime($job['date_posted'])) ?>
        </div>
      </div>
    </div>

    <!-- Job Details -->
    <div class="content-section">
      <!-- Key Information Grid -->
      <div class="info-grid">
        <?php if (!empty($job['company'])): ?>
          <div class="info-card">
            <h6><i class="fas fa-building me-2"></i>Company</h6>
            <p><?= htmlspecialchars($job['company']) ?></p>
          </div>
        <?php endif; ?>




        <?php if (!empty($job['salary'])): ?>
          <div class="info-card">
            <h6><i class="fas fa-money-bill-wave me-2"></i>Salary</h6>
            <p><?= htmlspecialchars($job['salary']) ?></p>
          </div>
        <?php endif; ?>

        <?php if (!empty($job['bursary_amount'])): ?>
          <div class="info-card">
            <h6><i class="fas fa-hand-holding-usd me-2"></i>Bursary Amount</h6>
            <p><?= htmlspecialchars($job['bursary_amount']) ?></p>
          </div>
        <?php endif; ?>
        <?php if (!empty($job['price'])): ?>
          <div class="info-card">
            <h6><i class="fas fa-hand-holding-usd me-2"></i>Price</h6>
            <p><?= htmlspecialchars($job['price']) ?></p>
          </div>
        <?php endif; ?>
      </div>

    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
      <?php if (!empty($job['link'])): ?>
        <a href="<?= htmlspecialchars($job['link']) ?>" target="_blank" class="btn-apply">
          <i class="fas fa-external-link-alt"></i>
          Apply Now
        </a>
      <?php endif; ?>

      <?php if (isset($_SESSION['user_id'])): ?>
        <button type="button" class="btn-save <?= $is_saved ? 'btn-saved' : '' ?>" id="saveJobBtn"
          data-job-id="<?= $job['id'] ?>" data-saved="<?= $is_saved ? 'true' : 'false' ?>">
          <i class="<?= $is_saved ? 'fas fa-check' : 'fas fa-bookmark' ?>"></i>
          <?= $is_saved ? 'Saved' : 'Save Job' ?>
        </button>
      <?php endif; ?>
    </div>

    <?php
 


function displayAsList($text)
{
  $lines = explode("\n", $text);
  $inList = false;

  foreach ($lines as $line) {
    $trimmed = trim($line);

    if ($trimmed === '') continue;

    // Bullet line
    if (strpos($trimmed, '- ') === 0 || strpos($trimmed, '-') === 0) {
      if (!$inList) {
        echo '<ul>';
        $inList = true;
      }
      echo '<li>' . htmlspecialchars(ltrim($trimmed, '- ')) . '</li>';
    } else {
      if ($inList) {
        echo '</ul>';
        $inList = false;
      }
      echo '<p><strong>' . htmlspecialchars($trimmed) . '</strong></p>';
    }
  }

  if ($inList) echo '</ul>';
}

    ?>

    <!-- Job Sections -->
    <?php if (!empty($job['summary'])): ?>
      <div class="mb-4">
        <h2 class="section-title"><i class="fas fa-info-circle me-2"></i>Summary</h2>
        <div class="section-content">
          <?= nl2br(htmlspecialchars($job['summary'])) ?>
        </div>
      </div>
    <?php endif; ?>


   




    <?php if (!empty($job['service_category'])): ?>
      <div class="mb-4">
        <h2 class="section-title"><i class="fas fa-handshake"></i> Service Offered</h2>
        <div class="section-content">
          <?= nl2br(htmlspecialchars($job['service_category'])) ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($job['interview_questions'])): ?>
      <div class="mb-4">
        <h2 class="section-title"><i class="fas fa-handshake"></i> Question</h2>
        <div class="section-content">
          <?= nl2br(htmlspecialchars($job['interview_questions'])) ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($job['interview_answers'])): ?>
      <div class="mb-4">
        <h2 class="section-title"><i class="fas fa-handshake"></i> Answers</h2>
        <div class="section-content">
          <?= nl2br(htmlspecialchars($job['interview_answers'])) ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($job['description'])): ?>
      <div class="mb-4">
        <h2 class="section-title"><i class="fas fa-file-alt me-2"></i>Description</h2>
        <div class="section-content">
          <?= nl2br(htmlspecialchars($job['description'])) ?>
        </div>
      </div>
    <?php endif; ?>

     <?php if (!empty($job['institution'])): ?>
      <div class="mb-4">
        <h2 class="section-title"><i class="fas fa-university me-2"></i>Institutions</h2>
        <div class="section-content">
          <?php displayAsList($job['institution']); ?>
        </div>
      </div>
    <?php endif; ?>


    <?php if (!empty($job['field_of_study'])): ?>
      <div class="mb-4">
        <h2 class="section-title"><i class="fas fa-graduation-cap me-2"></i>Field of Study</h2>
        <div class="section-content">
          <?php displayAsList($job['field_of_study']); ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($job['availabilty'])): ?>
      <div class="mb-4">
        <h2 class="section-title"><i class="fas fa-file-alt me-2"></i>Availability</h2>
        <div class="section-content">
          <?= nl2br(htmlspecialchars($job['availabilty'])) ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($job['contact_name'])): ?>
      <div class="mb-4">
        <h2 class="section-title"><i class="fas fa-address-card"></i> Contact Name</h2>
        <div class="section-content">
          <?= nl2br(htmlspecialchars($job['contact_name'])) ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($job['contact_email'])): ?>
      <div class="mb-4">
        <h2 class="section-title"><i class="fas fa-envelope"></i> Contact Email</h2>
        <div class="section-content">
          <?= nl2br(htmlspecialchars($job['contact_email'])) ?>
        </div>
      </div>
    <?php endif; ?>
    <?php if (!empty($job['contact_phone'])): ?>
      <div class="mb-4">
        <h2 class="section-title"><i class="fas fa-phone"></i> Contact Number</h2>
        <div class="section-content">
          <?= nl2br(htmlspecialchars($job['contact_phone'])) ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($job['coverage_details'])): ?>
      <div class="mb-4">
        <h2 class="section-title"><i class="fas fa-shield-alt me-2"></i>What the Bursary Covers</h2>
        <div class="section-content">
          <?php displayAsList($job['coverage_details']); ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($job['responsibilities'])): ?>
      <div class="mb-4">
        <h2 class="section-title"><i class="fas fa-list-check me-2"></i>Responsibilities</h2>
        <div class="section-content">
          <?php displayAsList($job['responsibilities']); ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($job['requirements'])): ?>
      <div class="mb-4">
        <h2 class="section-title"><i class="fas fa-list-check me-2"></i>Requirements</h2>
        <div class="section-content">
          <?php displayAsList($job['requirements']); ?>
        </div>
      </div>
    <?php endif; ?>

    

    <?php if (!empty($job['benefits'])): ?>
      <div class="mb-4">
        <h2 class="section-title"><i class="fas fa-gift me-2"></i>Benefits</h2>
        <div class="section-content">
          <?php displayAsList($job['benefits']); ?>
        </div>
      </div>
    <?php endif; ?>

     <?php if (!empty($job['required_documents'])): ?>
      <div class="mb-4">
        <h2 class="section-title"><i class="fas fa-folder-open me-2"></i>Required Documents</h2>
        <div class="section-content">
          <?php displayAsList($job['required_documents']); ?>
        </div>
      </div>
    <?php endif; ?>



    <?php if (!empty($job['how_to_apply'])): ?>
      <div class="mb-4">
        <h2 class="section-title"><i class="fas fa-paper-plane me-2"></i>How to Apply</h2>
        <div class="section-content">
       <?= nl2br(make_links_clickable(htmlspecialchars($job['how_to_apply'], ENT_QUOTES))) ?>

        </div>
      </div>
    <?php endif; ?>

    <?php
function make_links_clickable($text) {
    $text = preg_replace(
        '~(https?://[^\s]+)~',
        '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>',
        $text
    );
    return $text;
}
?>



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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Save job functionality
    document.addEventListener('DOMContentLoaded', function () {
      const saveBtn = document.getElementById('saveJobBtn');

      if (saveBtn) {
        saveBtn.addEventListener('click', function () {
          const jobId = this.dataset.jobId;
          const isSaved = this.dataset.saved === 'true';

          this.disabled = true;

          fetch('save-job.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `job_id=${jobId}&action=${isSaved ? 'unsave' : 'save'}`
          })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                const newSavedState = !isSaved;
                this.dataset.saved = newSavedState.toString();

                if (newSavedState) {
                  this.className = 'btn-save btn-saved';
                  this.innerHTML = '<i class="fas fa-check"></i> Saved';
                  showSuccessMessage('Job saved successfully!');
                } else {
                  this.className = 'btn-save';
                  this.innerHTML = '<i class="fas fa-bookmark"></i> Save Job';
                  showSuccessMessage('Job removed from saved jobs!');
                }
              } else {
                showErrorMessage(data.message || 'Failed to save job. Please try again.');
              }
            })
            .catch(error => {
              console.error('Error:', error);
              showErrorMessage('An error occurred. Please try again.');
            })
            .finally(() => {
              this.disabled = false;
            });
        });
      }
    });

    function showSuccessMessage(message) {
      const container = document.getElementById('successMessageContainer');
      const messageDiv = document.createElement('div');
      messageDiv.className = 'alert alert-success alert-dismissible fade show success-message';
      messageDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      `;

      container.appendChild(messageDiv);

      setTimeout(() => {
        if (messageDiv.parentNode) {
          messageDiv.remove();
        }
      }, 3000);
    }

    function showErrorMessage(message) {
      const container = document.getElementById('successMessageContainer');
      const messageDiv = document.createElement('div');
      messageDiv.className = 'alert alert-danger alert-dismissible fade show success-message';
      messageDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      `;

      container.appendChild(messageDiv);

      setTimeout(() => {
        if (messageDiv.parentNode) {
          messageDiv.remove();
        }
      }, 5000);
    }
  </script>
</body>

</html>