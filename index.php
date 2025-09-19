<?php
// Start session and include DB connection
session_start();
include('db.php');

// Newsletter subscription handling (for AJAX response)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subscribe'])) {
    $email = trim($_POST['email']);
    $response = "";

    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $check = $conn->prepare("SELECT * FROM subscribers WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $response = "⚠️ This email is already subscribed!";
        } else {
            $insert = $conn->prepare("INSERT INTO subscribers (email) VALUES (?)");
            $insert->bind_param("s", $email);
            if ($insert->execute()) {
                $response = "✅ Thanks for subscribing to our Newsletter!";
            } else {
                $response = "❌ Error: " . $conn->error;
            }
        }
    } else {
        $response = "⚠️ Please enter a valid email.";
    }

    echo $response;
    exit; // stop further execution for AJAX
}

// Fetch social links from DB
$socialLinks = [];
$result = $conn->query("SELECT * FROM social_links LIMIT 1");
if ($result && $result->num_rows > 0) {
    $socialLinks = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Vinefresh Bookshop</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    body { margin: 0; font-family: Arial, sans-serif; color: #333; }

    /* Header */
    header {
      background: #fff;
      padding: 15px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 1000;
    }
    header img.logo { height: 60px; }
    nav ul { list-style: none; margin: 0; padding: 0; display: flex; gap: 20px; }
    nav ul li a {
      text-decoration: none;
      color: #0077cc;
      font-weight: bold;
      transition: color 0.3s;
    }
    nav ul li a:hover, nav ul li.current a { color: #005fa3; }

    /* Hero section */
    .hero {
      background: url('assets/images/pen3.jpg') no-repeat center center/cover;
      color: white;
      text-align: center;
      padding: 120px 20px;
      position: relative;
    }
    .hero::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.5);
    }
    .hero h1, .hero p, .hero .hero-buttons {
      position: relative;
      z-index: 1;
    }
    .hero h1 { font-size: 3rem; margin-bottom: 15px; }
    .hero p { font-size: 1.2rem; margin-bottom: 20px; }

    /* Hero Buttons */
    .hero-buttons {
      display: flex;
      justify-content: center;
      gap: 20px;
    }
    .hero-buttons button {
      padding: 14px 28px;
      font-size: 1.1rem;
      border: none;
      border-radius: 50px;
      background: linear-gradient(45deg, #0077cc, #00aaff);
      color: #fff;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
    }
    .hero-buttons button:hover {
      background: linear-gradient(45deg, #005fa3, #0088cc);
      transform: translateY(-4px) scale(1.05);
    }

    /* Marquee */
    .marquee-container {
      width: 100%;
      overflow: hidden;
      background: #f8f9fa;
      border-top: 3px solid #0077cc;
      border-bottom: 3px solid #0077cc;
      padding: 15px 0;
    }
    .marquee {
      display: flex;
      animation: scroll-left 25s linear infinite;
    }
    .marquee img {
      height: 120px;
      margin: 0 40px;
    }
    @keyframes scroll-left {
      0% { transform: translateX(100%); }
      100% { transform: translateX(-100%); }
    }

    /* Newsletter */
    .newsletter {
      text-align: center;
      padding: 50px 20px;
      background: #f4f4f4;
    }
    .newsletter h2 { margin-bottom: 20px; }
    .newsletter form { display: inline-flex; gap: 10px; }
    .newsletter input {
      padding: 12px;
      width: 280px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
    }
    .newsletter button {
      padding: 12px 24px;
      background: #0077cc;
      border: none;
      border-radius: 8px;
      color: white;
      font-weight: bold;
      cursor: pointer;
    }
    .newsletter button:hover { background: #005fa3; }
    #message { margin-top: 12px; font-weight: bold; }

    /* Footer */
    footer {
      background: #222;
      color: #fff;
      padding: 30px 20px;
      text-align: center;
    }
    footer .social-icons { margin: 15px 0; }
    footer .social-icons a {
      color: #fff;
      margin: 0 10px;
      font-size: 1.4rem;
      transition: 0.3s;
    }
    footer .social-icons a:hover { color: #0077cc; }
  </style>
</head>
<body>
  <header>
    <img src="assets/images/vine2.png" alt="Vinefresh Logo" class="logo">
    <nav>
      <ul>
        <li class="current"><a href="index.php">Home</a></li>
        <li><a href="services.php">Services</a></li>
        <li><a href="instructors.php">Our Instructors</a></li>
        <li><a href="register.php">Register</a></li>
        <li><a href="contact.php">Contact Us</a></li>
      </ul>
    </nav>
  </header>

  <section class="hero">
    <h1>Welcome to Vinefresh Bookshop</h1>
    <p>Your hub for books, stationeries & professional computer training</p>
    <div class="hero-buttons">
      <button onclick="window.location.href='register.php'">Register for Training</button>
      <button onclick="window.location.href='products.php'">View Our Products</button>
    </div>
  </section>

  <!-- Marquee Section -->
  <div class="marquee-container">
    <div class="marquee">
      <img src="assets/images/yel.png" alt="Banner 1">
      <!--<img src="assets/images/vine2.png" alt="Banner 2">-->
    </div>
  </div>

  <!-- Newsletter Section -->
  <section class="newsletter">
    <h2>Subscribe to our Newsletter</h2>
    <form id="newsletterForm">
      <input type="email" name="email" id="email" placeholder="Enter your email..." required>
      <button type="submit" name="subscribe">Subscribe</button>
    </form>
    <p id="message"></p>
  </section>

  <!-- Footer -->
  <footer>
    <p>&copy; <?= date("Y") ?> Vinefresh Bookshop | All Rights Reserved</p>
    <div class="social-icons">
      <?php if (!empty($socialLinks['facebook'])): ?>
        <a href="<?= $socialLinks['facebook'] ?>" target="_blank"><i class="fab fa-facebook"></i></a>
      <?php endif; ?>
      <?php if (!empty($socialLinks['twitter'])): ?>
        <a href="<?= $socialLinks['twitter'] ?>" target="_blank"><i class="fab fa-twitter"></i></a>
      <?php endif; ?>
      <?php if (!empty($socialLinks['instagram'])): ?>
        <a href="<?= $socialLinks['instagram'] ?>" target="_blank"><i class="fab fa-instagram"></i></a>
      <?php endif; ?>
      <?php if (!empty($socialLinks['linkedin'])): ?>
        <a href="<?= $socialLinks['linkedin'] ?>" target="_blank"><i class="fab fa-linkedin"></i></a>
      <?php endif; ?>
    </div>
    <p><a href="admin_login.php" style="color: #00aaff; font-weight: bold;">Admin Login</a></p>
  </footer>

  <script>
    // AJAX for newsletter
    $(document).ready(function(){
      $("#newsletterForm").submit(function(e){
        e.preventDefault();
        $.ajax({
          type: "POST",
          url: "index.php",
          data: $(this).serialize() + "&subscribe=1",
          success: function(response){
            $("#message").html(response);
            $("#email").val('');
          }
        });
      });
    });
  </script>
</body>
</html>
