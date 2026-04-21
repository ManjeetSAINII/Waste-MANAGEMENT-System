<?php
session_name('ADMIN_SESSION');
session_start();
session_destroy();
header('Location: adminlogin.php');
exit;
