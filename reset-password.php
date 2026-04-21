<?php
session_start();
require_once "connection.php";

if (empty($_SESSION['reset_email'])) {
    header('Location: forgot-password.php');
    exit;
}

$reset_email = $_SESSION['reset_email'];
$otp_stored  = $_SESSION['reset_otp'];
$error       = '';

if (isset($_POST['reset'])) {
    $entered  = trim($_POST['otp'] ?? '');
    $newpass  = $_POST['new_password'] ?? '';
    $cpass    = $_POST['confirm_password'] ?? '';

    if (!$entered || !$newpass || !$cpass) {
        $error = "All fields are required.";
    } elseif ($entered != $otp_stored) {
        $error = "Incorrect reset code. Please try again.";
    } elseif ($newpass !== $cpass) {
        $error = "Passwords do not match.";
    } elseif (strlen($newpass) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $hashed     = password_hash($newpass, PASSWORD_BCRYPT);
        $safe_email = mysqli_real_escape_string($db, $reset_email);
        mysqli_query($db, "UPDATE usertable SET password='$hashed', code=0 WHERE email='$safe_email'");
        unset($_SESSION['reset_email'], $_SESSION['reset_otp']);
        header('Location: login-user.php?reset=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        body {
            min-height: 100vh;
            background: linear-gradient(60deg, rgba(56,142,60,1) 0%, rgba(76,175,80,1) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: #fff;
            border-radius: 10px;
            padding: 40px 35px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            text-align: center;
        }
        .card-icon { font-size: 52px; margin-bottom: 10px; }
        .card h2 { font-weight: 700; color: #333; margin-bottom: 6px; }
        .card p.subtitle { color: #666; font-size: 14px; margin-bottom: 20px; }
        .otp-demo-box {
            background: #f1f8e9;
            border: 1px dashed #4CAF50;
            border-radius: 8px;
            padding: 12px 20px;
            margin-bottom: 24px;
        }
        .otp-demo-box p { margin: 0; font-size: 13px; color: #555; }
        .otp-demo-box .otp-code {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 8px;
            color: #2e7d32;
            margin-top: 4px;
        }
        .form-control:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76,175,80,0.2);
        }
        .otp-input {
            text-align: center;
            font-size: 22px;
            font-weight: 600;
            letter-spacing: 6px;
            height: 52px;
            border: 2px solid #ddd;
            border-radius: 8px;
            width: 100%;
            margin-bottom: 12px;
        }
        .otp-input:focus {
            border-color: #4CAF50;
            outline: none;
            box-shadow: 0 0 0 3px rgba(76,175,80,0.2);
        }
        .btn-green {
            background: #4CAF50;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            padding: 12px;
            width: 100%;
            margin-top: 8px;
            transition: background 0.3s;
        }
        .btn-green:hover { background: #388E3C; color: #fff; }
        .back-link { display: block; margin-top: 16px; font-size: 13px; color: #4CAF50; }
        .back-link:hover { color: #388E3C; }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-icon">&#128274;</div>
        <h2>Reset Password</h2>
        <p class="subtitle">Use the code below and set a new password for<br><strong><?php echo htmlspecialchars($reset_email); ?></strong></p>

        <div class="otp-demo-box">
            <p>Your reset code</p>
            <div class="otp-code"><?php echo $otp_stored; ?></div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2" style="font-size:14px"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="reset-password.php">
            <input type="text" name="otp" class="otp-input" maxlength="6"
                   placeholder="_ _ _ _ _ _" autocomplete="off" required>
            <div class="form-group text-left">
                <label style="font-size:14px;font-weight:600;">New Password</label>
                <input type="password" name="new_password" class="form-control" placeholder="Min. 6 characters" required>
            </div>
            <div class="form-group text-left">
                <label style="font-size:14px;font-weight:600;">Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="Repeat new password" required>
            </div>
            <button type="submit" name="reset" class="btn-green">Reset Password</button>
        </form>

        <a href="forgot-password.php" class="back-link">&#8592; Back</a>
    </div>
</body>
</html>
