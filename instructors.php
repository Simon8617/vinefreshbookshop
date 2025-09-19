<?php
// Start session and include DB connection
session_start();
include('db.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Vinefresh Bookshop Instructors">
  <meta name="keywords" content="vinefresh, instructors, trainers, teachers">
  <meta name="author" content="Simon Ogbodo">
  <title>Vinefresh Media | Our Instructors</title>
  <link rel="stylesheet" href="assets/css/style.css">

  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f9f9f9;
    }

    header {
      background: #0077cc;
      padding: 15px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }
    header .logo {
      height: 60px;
    }
    nav ul {
      list-style: none;
      display: flex;
      gap: 20px;
      margin: 0;
      padding: 0;
    }
    nav ul li a {
      color: #fff;
      font-weight: bold;
      text-decoration: none;
      transition: 0.3s;
    }
    nav ul li a:hover,
    nav ul li.current a {
      color: #ffdd57;
    }

    .page-title {
      text-align: center;
      margin: 40px 0 20px;
      font-size: 2rem;
      color: #0077cc;
    }

    #instructors {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 25px;
      margin: 20px;
      padding: 10px;
    }

    .card {
      background: #fff;
      padding: 25px 20px;
      text-align: center;
      border-radius: 15px;
      box-shadow: 0 6px 15px rgba(0,0,0,0.1);
      transition: all 0.3s ease-in-out;
    }
    .card:hover {
      transform: translateY(-8px);
      box-shadow: 0 12px 25px rgba(0,0,0,0.15);
    }
    .card img {
      width: 130px;
      height: 130px;
      object-fit: cover;
      border-radius: 50%;
      border: 4px solid #0077cc;
      margin-bottom: 15px;
    }
    .card h3 {
      margin: 10px 0 5px;
      font-size: 20px;
      color: #333;
    }
    .card p {
      font-size: 14px;
      color: #666;
      margin: 5px 0;
    }
    .card p.role {
      font-weight: bold;
      color: #0077cc;
    }

    .social-icons {
      margin-top: 12px;
    }
    .social-icons a {
      color: #0077cc;
      margin: 0 8px;
      font-size: 20px;
      transition: 0.3s;
    }
    .social-icons a:hover {
      color: #005fa3;
      transform: scale(1.2);
    }

    footer {
      background: #222;
      color: #fff;
      text-align: center;
      padding: 20px;
      margin-top: 40px;
    }
    footer p {
      margin: 5px;
    }
  </style>
</head>
<body>
  <header>
    <img src="assets/images/vine2.png" alt="Vinefresh Logo" class="logo">
    <nav>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="services.php">Services</a></li>
        <li class="current"><a href="instructors.php">Our Instructors</a></li>
        <li><a href="register.php">Register</a></li>
        <li><a href="contact.php">Contact Us</a></li>
      </ul>
    </nav>
  </header>

  <h1 class="page-title">Meet Our Instructors</h1>
  <section id="instructors">
    <!-- Instructor 1 -->
    <div class="card">
      <img src="assets/images/instructor1.jpg" alt="Instructor 1">
      <h3>Mr. Ifeanyi Timothy Chime</h3>
      <p class="role">MD/CEO | Physicist | Professional Teacher</p>
      <p align="justify">Founder and owner of Vinefresh Bookshop and ICT center. Holds a B.Sc in Physics & Astronomy from UNN, pursuing a Masters in Medical Physics. A teacher, mentor, and minister dedicated to ensuring student excellence.</p>
      <div class="social-icons">
        <a href="#"><i class="fab fa-facebook"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fab fa-linkedin"></i></a>
      </div>
    </div>

    <!-- Instructor 2 -->
    <div class="card">
      <img src="assets/images/instructor2.png" alt="Instructor 2">
      <h3>Mr. Ogbodo Simon Onyedikachi</h3>
      <p class="role">CBT Instructor | Database & Web Developer</p>
      <p>Database specialist, graphics designer, web developer, networking & cybersecurity expert.</p>
      <div class="social-icons">
        <a href="#"><i class="fab fa-facebook"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fab fa-linkedin"></i></a>
      </div>
    </div>

    <!-- Instructor 3 -->
    <div class="card">
      <img src="assets/images/instructor3.jpg" alt="Instructor 3">
      <h3>Mr. Nwobodo Emmanuel Tochukwu</h3>
      <p class="role">Graphics Designer | Video Editor | ICT Administrator</p>
      <p>Overall manager of Vinefresh Bookshop ICT Center, expert in photography, video editing, and ICT maintenance.</p>
      <div class="social-icons">
        <a href="#"><i class="fab fa-facebook"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fab fa-linkedin"></i></a>
      </div>
    </div>

    <!-- Instructor 4 -->
    <div class="card">
      <img src="assets/images/instructor4.jpg" alt="Instructor 4">
      <h3>Mr. Nnamani Uche</h3>
      <p class="role">Assistant CBT Instructor | Academic Coach</p>
      <p>Dedicated teacher and academic coach, assisting in CBT training and student development.</p>
      <div class="social-icons">
        <a href="#"><i class="fab fa-facebook"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fab fa-linkedin"></i></a>
      </div>
    </div>
  </section>

  <footer>
    <p>&copy; 2025 Vinefresh Bookshop | All Rights Reserved</p>
    <p><a href="admin_login.php" style="color:#0077cc; font-weight:bold;">Admin Login</a></p>
  </footer>
</body>
</html>
