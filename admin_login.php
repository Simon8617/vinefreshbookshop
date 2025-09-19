<?php
session_start();
include('db.php');

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            // password stored as hash
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $admin['username'];
                header("Location: admin_dashboard.php");
                exit;
            } else {
                $error = "❌ Invalid password.";
            }
        } else {
            $error = "❌ Admin not found.";
        }
    } else {
        $error = "⚠️ Please fill all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login - Vinefresh Bookshop</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #0077cc, #00a8cc);
      margin: 0;
      padding: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .login-container {
      width: 380px;
      padding: 40px 30px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 6px 15px rgba(0,0,0,0.2);
      text-align: center;
      animation: fadeIn 0.8s ease-in-out;
    }
    h2 {
      margin-bottom: 20px;
      color: #0077cc;
    }
    .error {
      background: #ffe6e6;
      color: #cc0000;
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 6px;
      font-size: 14px;
    }
    input {
      width: 100%;
      padding: 12px;
      margin: 12px 0;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
      transition: border 0.3s;
    }
    input:focus {
      border-color: #0077cc;
      outline: none;
    }
    button {
      width: 100%;
      padding: 14px;
      margin-top: 10px;
      background: #0077cc;
      border: none;
      color: white;
      font-size: 16px;
      font-weight: bold;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s, transform 0.2s;
    }
    button:hover {
      background: #005fa3;
      transform: translateY(-2px);
    }
    .back-home {
      background: #6c757d;
    }
    .back-home:hover {
      background: #5a6268;
    }
    footer {
      margin-top: 20px;
      font-size: 13px;
      color: #777;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>🔑 Admin Login</h2>
    <?php if ($error): ?>
      <p class="error"><?= $error ?></p>
    <?php endif; ?>
    <form method="POST">
      <input type="text" name="username" placeholder="👤 Username" required>
      <input type="password" name="password" placeholder="🔒 Password" required>
      <button type="submit">Login</button>
    </form>
    <!-- Back to Home Button -->
    <form action="index.php" method="get">
      <button type="submit" class="back-home">⬅️ Back to Home</button>
    </form>
    <footer>
      &copy; <?= date("Y") ?> Vinefresh Bookshop
    </footer>
  </div>
</body>
</html>
