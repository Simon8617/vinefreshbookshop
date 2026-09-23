<?php
// contact.php
include("db.php");

// Handle AJAX request
if (isset($_POST['ajax']) && $_POST['ajax'] == "1") {
    $name    = mysqli_real_escape_string($conn, $_POST['name']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        echo json_encode(["status"=>"error","message"=>"All fields are required!"]);
        exit;
    }

    $sql = "INSERT INTO contact_messages (name, email, subject, message) 
            VALUES ('$name','$email','$subject','$message')";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(["status"=>"success","message"=>"Message sent successfully!"]);
    } else {
        echo json_encode(["status"=>"error","message"=>"Database error: " . mysqli_error($conn)]);
    }
    exit;
}

// Fetch dynamic social links
$social_links = [];
$result = mysqli_query($conn, "SELECT * FROM social_links");
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $social_links[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Contact Us - Vinefresh ICT</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
body {
  font-family: Arial, sans-serif;
  background: #f4f7fb;
  margin: 0;
  padding: 0;
}
.container {
  max-width: 700px;
  margin: 50px auto;
  background: #fff;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
h2 {
  text-align: center;
  margin-bottom: 20px;
  color: #0066cc;
}
.form-group {
  margin-bottom: 15px;
}
label {
  display: block;
  margin-bottom: 6px;
  font-weight: bold;
  color: #444;
}
input, textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 15px;
}
textarea {
  resize: vertical;
  height: 120px;
}
button {
  background: #0066cc;
  color: white;
  padding: 12px 20px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 16px;
  transition: 0.3s;
}
button:hover {
  background: #004c99;
}
#response {
  margin-top: 15px;
  text-align: center;
  font-weight: bold;
}
.social, .contact-info {
  text-align: center;
  margin-top: 25px;
}
.social a {
  margin: 0 8px;
  font-size: 28px;
  color: #555;
  transition: 0.3s;
}
.social a:hover {
  color: #0066cc;
}
.contact-info p {
  margin: 8px 0;
  font-size: 16px;
  color: #333;
}
.contact-info i {
  margin-right: 8px;
  color: #0066cc;
}
.home-link {
  display: inline-block;
  margin-top: 15px;
  text-decoration: none;
  color: #0066cc;
  font-weight: bold;
  transition: 0.3s;
}
.home-link:hover {
  text-decoration: underline;
}
</style>
</head>
<body>

<div class="container">
  <h2>Contact Us</h2>
  <form id="contactForm">
    <div class="form-group">
      <label><i class="fa fa-user"></i> Full Name</label>
      <input type="text" name="name" required>
    </div>
    <div class="form-group">
      <label><i class="fa fa-envelope"></i> Email</label>
      <input type="email" name="email" required>
    </div>
    <div class="form-group">
      <label><i class="fa fa-tag"></i> Subject</label>
      <input type="text" name="subject" required>
    </div>
    <div class="form-group">
      <label><i class="fa fa-comment"></i> Message</label>
      <textarea name="message" required></textarea>
    </div>
    <input type="hidden" name="ajax" value="1">
    <button type="submit"><i class="fa fa-paper-plane"></i> Send Message</button>
  </form>
  <div id="response"></div>

  <!-- Static Contact Info -->
  <div class="contact-info">
    <p><i class="fa fa-envelope"></i> chimeifeanyi180@gmail.com</p>
    <p><i class="fa fa-phone"></i> 07066078281</p>
    <a class="home-link" href="index.php"><i class="fa fa-home"></i> Back to Home</a>
  </div>

  <!-- Dynamic Social Links -->
  <div class="social">
    <?php foreach ($social_links as $link): ?>
      <a href="<?= htmlspecialchars($link['url']) ?>" target="_blank">
        <i class="<?= htmlspecialchars($link['icon']) ?>"></i>
      </a>
    <?php endforeach; ?>
  </div>
</div>

<script>
$("#contactForm").on("submit", function(e){
  e.preventDefault();
  let formData = $(this).serialize();

  $.ajax({
    url: "contact.php",
    type: "POST",
    data: formData,
    beforeSend: function(){
      $("#response").html("<p style='color:blue;'>Sending...</p>");
    },
    success: function(data){
      let res = JSON.parse(data);
      if(res.status === "success"){
        $("#response").html("<p style='color:green;'>" + res.message + "</p>");
        $("#contactForm")[0].reset();
      } else {
        $("#response").html("<p style='color:red;'>" + res.message + "</p>");
      }
    },
    error: function(){
      $("#response").html("<p style='color:red;'>AJAX error occurred!</p>");
    }
  });
});
</script>

</body>
</html>
