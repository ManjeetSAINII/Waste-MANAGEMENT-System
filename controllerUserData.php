<?php
session_start();
require_once "connection.php";

$email  = "";
$name   = "";
$errors = [];

// Signup
if (isset($_POST['signup'])) {
    $name      = trim(mysqli_real_escape_string($db, $_POST['name'] ?? ''));
    $email     = trim(mysqli_real_escape_string($db, $_POST['email'] ?? ''));
    $password  = $_POST['password'] ?? '';
    $cpassword = $_POST['cpassword'] ?? '';

    if (!$name || !$email || !$password) {
        $errors['required'] = "All fields are required.";
    } elseif ($password !== $cpassword) {
        $errors['password'] = "Confirm password does not match!";
    } else {
        $res = mysqli_query($db, "SELECT id FROM usertable WHERE email = '$email'");
        if (mysqli_num_rows($res) > 0) {
            $errors['email'] = "An account with this email already exists!";
        } else {
            $otp     = rand(100000, 999999);
            $encpass = password_hash($password, PASSWORD_BCRYPT);
            $sql = "INSERT INTO usertable (name, email, password, code, status)
                    VALUES ('$name', '$email', '$encpass', $otp, 'unverified')";
            if (mysqli_query($db, $sql)) {
                $_SESSION['verify_email'] = $email;
                $_SESSION['verify_otp']   = $otp;
                header('Location: verify-email.php');
                exit;
            } else {
                $errors['db'] = "Registration failed. Please try again.";
            }
        }
    }
}

// Login
if (isset($_POST['login'])) {
    $email    = trim(mysqli_real_escape_string($db, $_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $errors['required'] = "Email and password are required.";
    } else {
        $res = mysqli_query($db, "SELECT * FROM usertable WHERE email = '$email'");
        if (mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_assoc($res);
            if (password_verify($password, $row['password'])) {
                if ($row['status'] !== 'verified') {
                    // Re-send OTP flow: store in session and redirect
                    $_SESSION['verify_email'] = $email;
                    $_SESSION['verify_otp']   = $row['code'];
                    header('Location: verify-email.php');
                    exit;
                }
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name']  = $row['name'];
                header('Location: index.html');
                exit;
            } else {
                $errors['login'] = "Incorrect email or password!";
            }
        } else {
            $errors['login'] = "No account found with that email address.";
        }
    }
}
