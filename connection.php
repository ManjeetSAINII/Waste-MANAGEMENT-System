<?php
// Get database connection details from environment or use defaults
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'root';
$db_password = getenv('DB_PASSWORD') ?: '';
$db_name = getenv('DB_NAME') ?: 'wms';

$db = new mysqli($db_host, $db_user, $db_password, $db_name);
if(!$db) {
    die('Please check Your database connection: '.mysqli_error($db));
}

