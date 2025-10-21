<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to save jobs']);
    exit();
}

include 'admin/db_connect.php';

$user_id = $_SESSION['user_id'];
$job_id = isset($_POST['job_id']) ? (int)$_POST['job_id'] : 0;
$action = isset($_POST['action']) ? $_POST['action'] : 'save';

if ($job_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid job ID']);
    exit();
}

// Check if job exists
$job_check_sql = "SELECT id FROM jobs WHERE id = $job_id AND is_active = 1";
$job_check_result = $conn->query($job_check_sql);

if ($job_check_result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Job not found']);
    exit();
}

if ($action === 'save') {
    // Check if job is already saved
    $check_sql = "SELECT id FROM saved_jobs WHERE user_id = $user_id AND job_id = $job_id";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Job already saved']);
        exit();
    }
    
    // Save the job
    $save_sql = "INSERT INTO saved_jobs (user_id, job_id, saved_at) VALUES ($user_id, $job_id, NOW())";
    
    if ($conn->query($save_sql)) {
        echo json_encode(['success' => true, 'message' => 'Job saved successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save job']);
    }
    
} elseif ($action === 'unsave') {
    // Remove the job from saved jobs
    $unsave_sql = "DELETE FROM saved_jobs WHERE user_id = $user_id AND job_id = $job_id";
    
    if ($conn->query($unsave_sql)) {
        echo json_encode(['success' => true, 'message' => 'Job removed from saved jobs']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to remove job']);
    }
    
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

$conn->close();
?>