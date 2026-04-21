<?php
header('Content-Type: application/json');
require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$fname   = trim(mysqli_real_escape_string($db, $_POST['fname'] ?? ''));
$lname   = trim(mysqli_real_escape_string($db, $_POST['lname'] ?? ''));
$email   = trim(mysqli_real_escape_string($db, $_POST['email'] ?? ''));
$phone   = trim(mysqli_real_escape_string($db, $_POST['phone'] ?? ''));
$comment = trim(mysqli_real_escape_string($db, $_POST['message'] ?? ''));

if (!$fname || !$email || !$comment) {
    echo json_encode(['success' => false, 'message' => 'Name, email, and message are required.']);
    exit;
}

$sql = "INSERT INTO contact (fname, lname, contactEmail, contactPhone, comment)
        VALUES ('$fname', '$lname', '$email', '$phone', '$comment')";

if (mysqli_query($db, $sql)) {
    echo json_encode(['success' => true, 'message' => 'Your message has been sent successfully! We will get back to you soon.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($db)]);
}
