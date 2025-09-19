<?php
// Start session and include DB connection
session_start();
include('db.php');

// Newsletter subscription handling
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subscribe'])) {
    $email = trim($_POST['email']);

    if (!empty($email)) {
        // Check if email already exists
        $check = $conn->prepare("SELECT * FROM customers WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $message = "⚠️ This email is already subscribed!";
        } else {
            // Insert new subscriber
            $insert = $conn->prepare("INSERT INTO customers (email) VALUES (?)");
            $insert->bind_param("s", $email);
            if ($insert->execute()) {
                $message = "✅ Thanks for subscribing to our Newsletter!";
            } else {
                $message = "❌ Error: " . $conn->error;
            }
        }
    } else {
        $message = "⚠️ Please enter a valid email.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width">
	<meta name="description" content="Vinefresh Bookshop">
	<meta name="keywords" content="vinefresh, books, services">
	<meta name="author" content="Simon Ogbodo">
	<title>Vinefresh Media | Services</title>
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">

	<!-- Font Awesome for icons -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

	<style>
		.dark {
			background: #f4f4f4;
			padding: 15px;
			border-radius: 8px;
		}
		.button_1 {
			display: inline-block;
			background: #0077cc;
			color: #fff;
			padding: 8px 16px;
			text-decoration: none;
			border-radius: 5px;
			cursor: pointer;
			border: none;
		}
		.button_1:hover {
			background: #005fa3;
		}
		#services {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
			gap: 20px;
		}
		#services li {
			list-style: none;
			background: #fff;
			padding: 20px;
			border-radius: 10px;
			box-shadow: 0 4px 6px rgba(0,0,0,0.1);
			transition: transform 0.2s;
			text-align: center;
		}
		#services li:hover {
			transform: scale(1.05);
		}
		#services i, #services img {
			font-size: 50px;
			color: #0077cc;
			margin-bottom: 10px;
		}
		#services h3 {
			margin: 10px 0;
		}
	</style>
</head>
<body>
	<div class="all">
	<header><a href="index.php">
    <img src="assets/images/vine2.png" alt="Vinefresh Logo" class="logo" width=""></a>
    <nav>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li class="current"><a href="services.php">Services</a></li>
        <li><a href="instructors.php">Our Instructors</a></li>
        <li><a href="register.php">Register</a></li>
        <li><a href="contact.php">Contact Us</a></li>
      </ul>
    </nav>
  </header>

	<!-- Main Section -->
	<section id="main">
		<div class="container">
			<article id="main-col">
				<h1 class="page-title" align="center">Our Services</h1>
				<ul id="services">
					<li>
						<i class="fas fa-book"></i>
						<h3>📚 BOOKS</h3>
						<p>We stock a wide range of textbooks and literature at affordable prices.</p>
						<p><b>Pricing:</b> ₦2,000 - ₦3,000</p>
					</li>
					<li>
						<i class="fas fa-pencil-alt"></i>
						<h3>✍ WRITING MATERIALS</h3>
						<p>We supply pens, pencils, markers, rulers, crayons, refills, correction pens and more.</p>
						<p><b>Pricing:</b> ₦100 - ₦1,500</p>
					</li>
					<li>
						<i class="fas fa-laptop-code"></i>
						<h3>💻 COMPUTER SERVICES</h3>
						<p>We offer OS installation, formatting, drivers, updates, antivirus, professional training, uploading & downloading.</p>
						<p><b>Pricing:</b> ₦2,000 - ₦15,000</p>
					</li>
				</ul>
			</article>
			<?php if (isset($_SESSION['feedback_status'])): ?>
    <p style="color:green; font-weight:bold;">
        <?= $_SESSION['feedback_status']; ?>
    </p>
    <?php unset($_SESSION['feedback_status']); ?>
<?php endif; ?>


			<aside id="sidebar">
				<div class="dark">
					<h3>Get Feedback</h3>
					<form method="POST" action="feedback.php">
						<div>
							<label>Name</label><br>
							<input type="text" placeholder="Name" name="fullname" required>
						</div>
						<div>
							<label>Email</label><br>
							<input type="email" placeholder="Email Address" name="email" required>
						</div>
						<div>
							<label>Message</label><br>
							<textarea placeholder="Message" name="message" required></textarea>
						</div>
						<br>
						<button class="button_1" type="submit" name="send_feedback">Send</button>
					</form>
				</div>
			</aside>
		</div>
	</section>

	<!-- Footer -->
<footer>
    <div class="social-links" style="margin-bottom: 10px; text-align: center;">
        <?php
        $query = $conn->query("SELECT * FROM social_links");
        if ($query->num_rows > 0) {
            while ($row = $query->fetch_assoc()) {
                echo '<a href="' . htmlspecialchars($row['url']) . '" target="_blank" style="margin: 0 10px; color: #0077cc; font-size: 24px;">
                        <i class="' . htmlspecialchars($row['icon']) . '"></i>
                      </a>';
            }
        } else {
            echo "<p>No social links available</p>";
        }
        ?>
    </div>
    <p>&copy; 2025 Vinefresh Bookshop | All Rights Reserved</p>
</footer>

	</div>
</body>
</html>
