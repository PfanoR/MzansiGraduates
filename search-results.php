<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>


<?php
include 'admin/db_connect.php';

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$location = isset($_GET['location']) ? trim($_GET['location']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

$sql = "SELECT * FROM jobs WHERE is_active = 1";
$conditions = [];

if (!empty($keyword)) {
    $conditions[] = "title LIKE '%" . $conn->real_escape_string($keyword) . "%'";
}

if (!empty($location)) {
    $conditions[] = "location LIKE '%" . $conn->real_escape_string($location) . "%'";
}

if (!empty($category)) {
    $categoryMap = [
        "it-tech" => 1,
        "retail" => 2,
        "finance-accounting" => 3,
        "admin-office" => 4,
        "call-centre" => 5,
        "sales-marketing" => 6,
        "transport-labour" => 7,
        "construction-trade" => 8,
        "government-jobs" => 9,
        "entry-level" => 10,
        "internships" => 12,
        "learnerships" => 13
    ];
    $category_id = $categoryMap[$category] ?? 0;
    if ($category_id > 0) {
        $conditions[] = "category_id = $category_id";
    }
}

if (!empty($conditions)) {
    $sql .= " AND " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY date_posted DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results | MzansiGraduates</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<div class="container py-5">
    <h2 class="mb-4">Search Results</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($job = $result->fetch_assoc()): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($job['title']) ?></h5>
                    <p class="card-text">
                        <?= htmlspecialchars($job['summary']) ?>
                    </p>
                    <a href="job-details.php?id=<?= $job['id'] ?>" class="btn btn-primary btn-sm">View Details</a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-warning">No matching jobs found.</div>
    <?php endif; ?>
</div>

</body>
</html>
