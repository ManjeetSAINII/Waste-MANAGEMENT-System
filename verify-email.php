<?php
session_start();
require_once "connection.php";

if (empty($_SESSION['verify_email'])) {
    header('Location: signup-user.php');
    exit;
}

$verify_email = $_SESSION['verify_email'];
$otp_stored   = $_SESSION['verify_otp'];
$error        = '';
$success      = '';

if (isset($_POST['verify'])) {
    $entered = trim($_POST['otp'] ?? '');

    if (!$entered) {
        $error = "Please enter the verification code.";
    } elseif ($entered != $otp_stored) {
        $error = "Incorrect code. Please try again.";
    } else {
        $safe_email = mysqli_real_escape_string($db, $verify_email);
        mysqli_query($db, "UPDATE usertable SET status='verified', code=0 WHERE email='$safe_email'");

        $row = mysqli_fetch_assoc(mysqli_query($db, "SELECT name FROM usertable WHERE email='$safe_email'"));
        $_SESSION['user_email'] = $verify_email;
        $_SESSION['user_name']  = $row['name'];
        unset($_SESSION['verify_email'], $_SESSION['verify_otp']);
        header('Location: index.html');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Email</title>
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
        .verify-card {
            background: #fff;
            border-radius: 10px;
            padding: 40px 35px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            text-align: center;
        }
        .otp-icon {
            font-size: 52px;
            margin-bottom: 10px;
        }
        .verify-card h2 {
            font-weight: 700;
            color: #333;
            margin-bottom: 6px;
        }
        .verify-card p.subtitle {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .otp-demo-box {
            background: #f1f8e9;
            border: 1px dashed #4CAF50;
            border-radius: 8px;
            padding: 12px 20px;
            margin-bottom: 24px;
        }
        .otp-demo-box p {
            margin: 0;
            font-size: 13px;
            color: #555;
        }
        .otp-demo-box .otp-code {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 8px;
            color: #2e7d32;
            margin-top: 4px;
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
        }
        .otp-input:focus {
            border-color: #4CAF50;
            outline: none;
            box-shadow: 0 0 0 3px rgba(76,175,80,0.2);
        }
        .btn-verify {
            background: #4CAF50;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            padding: 12px;
            width: 100%;
            margin-top: 14px;
            transition: background 0.3s;
        }
        .btn-verify:hover { background: #388E3C; }
        .back-link {
            display: block;
            margin-top: 16px;
            font-size: 13px;
            color: #4CAF50;
        }
        .back-link:hover { color: #388E3C; }
    </style>
</head>
<body>
    <div class="verify-card">
        <div class="otp-icon">&#128274;</div>
        <h2>Verify Your Email</h2>
        <p class="subtitle">Enter the 6-digit code for<br><strong><?php echo htmlspecialchars($verify_email); ?></strong></p>

        <div class="otp-demo-box">
            <p>Your verification code</p>
            <div class="otp-code"><?php echo $otp_stored; ?></div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2" style="font-size:14px"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="verify-email.php">
            <input type="text" name="otp" class="otp-input" maxlength="6"
                   placeholder="_ _ _ _ _ _" autocomplete="off" required>
            <button type="submit" name="verify" class="btn-verify">Verify &amp; Continue</button>
        </form>

        <a href="signup-user.php" class="back-link">&#8592; Back to Signup</a>
    </div>
</body>
</html>
