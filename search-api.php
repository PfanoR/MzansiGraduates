<?php
include 'admin/db_connect.php';

$keyword = isset($_GET['keyword']) ? strtolower($_GET['keyword']) : '';
$location = isset($_GET['location']) ? strtolower($_GET['location']) : '';
$category = isset($_GET['category']) ? strtolower($_GET['category']) : '';

$sql = "SELECT jobs.* FROM jobs 
        JOIN categories ON jobs.category_id = categories.id 
        WHERE jobs.is_active = 1";

$params = [];

if (!empty($keyword)) {
  $sql .= " AND (LOWER(jobs.title) LIKE ? OR LOWER(jobs.company) LIKE ?)";
  $kw = "%$keyword%";
  $params[] = $kw;
  $params[] = $kw;
}

if (!empty($location)) {
  $sql .= " AND LOWER(jobs.location) LIKE ?";
  $params[] = "%$location%";
}

if (!empty($category)) {
  $sql .= " AND LOWER(categories.name) LIKE ?";
  $params[] = "%$category%";
}

$sql .= " ORDER BY jobs.date_posted DESC";
$stmt = $conn->prepare($sql);

// Bind parameters
if (!empty($params)) {
  $types = str_repeat('s', count($params));
  $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows > 0):
  while ($job = $result->fetch_assoc()):
    ?>
    <a href="job-details.php?id=<?= $job['id'] ?>" class="text-decoration-none text-dark">
      <div class="card mb-3 hover-shadow">
        <div class="card-body">
          <h5 class="fw-bold"><?= htmlspecialchars($job['company']) ?>: <?= htmlspecialchars($job['title']) ?></h5>
          <p class="text-muted"><?= htmlspecialchars($job['summary']) ?></p>
          <small class="text-muted">
            <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($job['location']) ?> |
            <i class="fas fa-clock"></i> <?= date('d M Y', strtotime($job['date_posted'])) ?>
          </small>
        </div>
      </div>
    </a>

    <?php
  endwhile;
else:
  ?>
  <div class="alert alert-info">No jobs found matching your search.</div>
  <?php
endif;
?>