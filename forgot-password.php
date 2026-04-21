<?php
session_start();
require_once "connection.php";

$error = '';

if (isset($_POST['send_otp'])) {
    $email = trim(mysqli_real_escape_string($db, $_POST['email'] ?? ''));
    if (!$email) {
        $error = "Please enter your email address.";
    } else {
        $res = mysqli_query($db, "SELECT id FROM usertable WHERE email = '$email' AND status = 'verified'");
        if (mysqli_num_rows($res) === 0) {
            $error = "No verified account found with that email.";
        } else {
            $otp = rand(100000, 999999);
            mysqli_query($db, "UPDATE usertable SET code = $otp WHERE email = '$email'");
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_otp']   = $otp;
            header('Location: reset-password.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
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
            max-width: 420px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            text-align: center;
        }
        .card-icon { font-size: 52px; margin-bottom: 10px; }
        .card h2 { font-weight: 700; color: #333; margin-bottom: 6px; }
        .card p.subtitle { color: #666; font-size: 14px; margin-bottom: 24px; }
        .form-control:focus {
            border-color: #4CAF50;
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
        <div class="card-icon">&#128273;</div>
        <h2>Forgot Password</h2>
        <p class="subtitle">Enter your registered email and we'll show you a reset code.</p>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2" style="font-size:14px"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="forgot-password.php">
            <div class="form-group text-left">
                <label style="font-size:14px;font-weight:600;">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
            </div>
            <button type="submit" name="send_otp" class="btn-green">Send Reset Code</button>
        </form>

        <a href="login-user.php" class="back-link">&#8592; Back to Login</a>
    </div>
</body>
</html>
