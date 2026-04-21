<?php
session_name('ADMIN_SESSION');
session_start();
if (isset($_SESSION['admin_logged_in'])) {
    header('Location: admin-dashboard.php');
    exit;
}

require_once 'connection.php';
$error = '';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($db, trim($_POST['username']));
    $password = mysqli_real_escape_string($db, trim($_POST['password']));

    $res = mysqli_query($db, "SELECT * FROM adminlogin WHERE username = '$username'");
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        if ($row['password'] === $password) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $row['username'];
            header('Location: admin-dashboard.php');
            exit;
        }
    }
    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login - WasteWise</title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: 'Segoe UI', sans-serif;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #1a6b3c 0%, #2d9e5f 50%, #4caf7d 100%);
  }
  .card {
    background: #fff;
    border-radius: 16px;
    padding: 48px 40px;
    width: 100%;
    max-width: 420px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
  }
  .logo {
    text-align: center;
    margin-bottom: 32px;
  }
  .logo-icon {
    width: 64px; height: 64px;
    background: linear-gradient(135deg, #1a6b3c, #2d9e5f);
    border-radius: 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    margin-bottom: 12px;
  }
  .logo h1 { font-size: 22px; color: #1a6b3c; font-weight: 700; }
  .logo p  { font-size: 13px; color: #666; margin-top: 4px; }
  .form-group { margin-bottom: 20px; }
  label { display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px; }
  input[type=text], input[type=password] {
    width: 100%; padding: 12px 16px;
    border: 2px solid #e0e0e0; border-radius: 8px;
    font-size: 15px; transition: border-color .2s;
    outline: none;
  }
  input:focus { border-color: #2d9e5f; }
  .btn {
    width: 100%; padding: 14px;
    background: linear-gradient(135deg, #1a6b3c, #2d9e5f);
    color: #fff; border: none; border-radius: 8px;
    font-size: 16px; font-weight: 600; cursor: pointer;
    transition: opacity .2s;
  }
  .btn:hover { opacity: .9; }
  .error {
    background: #fff0f0; border: 1px solid #f5c6cb;
    color: #c0392b; border-radius: 8px;
    padding: 12px 16px; margin-bottom: 20px;
    font-size: 14px;
  }
  .hint {
    margin-top: 20px; text-align: center;
    font-size: 12px; color: #999;
  }
  .hint a { color: #2d9e5f; text-decoration: none; }
</style>
</head>
<body>
<div class="card">
  <div class="logo">
    <div class="logo-icon">♻️</div>
    <h1>WasteWise Admin</h1>
    <p>Sign in to manage the system</p>
  </div>
  <?php if ($error): ?>
    <div class="error">⚠️ <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="POST">
    <div class="form-group">
      <label>Username</label>
      <input type="text" name="username" placeholder="Enter admin username" required
             value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" placeholder="Enter password" required>
    </div>
    <button class="btn" type="submit" name="login">Sign In</button>
  </form>
  <p class="hint">Default credentials: <strong>admin</strong> / <strong>admintest</strong><br>
    <a href="index.html">← Back to Homepage</a>
  </p>
</div>
</body>
</html>
