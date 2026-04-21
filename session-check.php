<?php
header('Content-Type: application/json');
session_start();
if (!empty($_SESSION['user_email'])) {
    echo json_encode([
        'logged_in' => true,
        'name' => $_SESSION['user_name'] ?? 'User'
    ]);
} else {
    echo json_encode(['logged_in' => false]);
}
