<?php
session_name('ADMIN_SESSION');
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    exit('Unauthorized');
}

require_once 'connection.php';

$action = $_GET['action'] ?? '';
$id     = (int)($_GET['id'] ?? 0);

switch ($action) {
    case 'complete':
        mysqli_query($db, "UPDATE garbageinfo SET status='Completed' WHERE Id=$id");
        header('Location: admin-dashboard.php?tab=reports&msg=marked');
        break;

    case 'pending':
        mysqli_query($db, "UPDATE garbageinfo SET status='Pending' WHERE Id=$id");
        header('Location: admin-dashboard.php?tab=reports&msg=reverted');
        break;

    case 'delete_report':
        $row = mysqli_fetch_assoc(mysqli_query($db, "SELECT file FROM garbageinfo WHERE Id=$id"));
        if ($row && $row['file'] && file_exists($row['file'])) {
            unlink($row['file']);
        }
        mysqli_query($db, "DELETE FROM garbageinfo WHERE Id=$id");
        header('Location: admin-dashboard.php?tab=reports&msg=deleted');
        break;

    case 'delete_message':
        mysqli_query($db, "DELETE FROM contact WHERE id=$id");
        header('Location: admin-dashboard.php?tab=messages&msg=deleted');
        break;

    case 'delete_user':
        mysqli_query($db, "DELETE FROM usertable WHERE id=$id");
        header('Location: admin-dashboard.php?tab=users&msg=deleted');
        break;

    default:
        header('Location: admin-dashboard.php');
}
exit;
