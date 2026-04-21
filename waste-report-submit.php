<?php
header('Content-Type: application/json');
require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$name        = trim(mysqli_real_escape_string($db, $_POST['name'] ?? ''));
$email       = trim(mysqli_real_escape_string($db, $_POST['email'] ?? ''));
$mobile      = trim(mysqli_real_escape_string($db, $_POST['phone'] ?? ''));
$wastetype   = trim(mysqli_real_escape_string($db, $_POST['wastetype'] ?? ''));
$location    = trim(mysqli_real_escape_string($db, $_POST['location'] ?? ''));
$description = trim(mysqli_real_escape_string($db, $_POST['description'] ?? ''));
$date        = date('Y-m-d H:i:s');
$status      = 'Pending';
$file_path   = '';

if (!$name || !$email || !$mobile || !$wastetype || !$location || !$description) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

// Handle file upload
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    $ext       = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed   = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Only image files are allowed.']);
        exit;
    }
    $filename  = uniqid('waste_', true) . '.' . $ext;
    $file_path = $upload_dir . $filename;
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
        $file_path = '';
    }
}

$sql = "INSERT INTO garbageinfo (name, mobile, email, wastetype, location, locationdescription, file, date, status)
        VALUES ('$name', '$mobile', '$email', '$wastetype', '$location', '$description', '$file_path', '$date', '$status')";

if (mysqli_query($db, $sql)) {
    echo json_encode(['success' => true, 'message' => 'Your waste report has been submitted successfully! Admin will review it soon.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($db)]);
}
