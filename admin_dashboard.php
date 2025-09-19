<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}
include 'db.php'; // Database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Vinefresh</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background: #f4f6f9;
      display: flex;
    }
    .sidebar {
      width: 220px;
      background: #0077cc;
      color: white;
      height: 100vh;
      position: fixed;
      padding-top: 20px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .sidebar h2 {
      text-align: center;
      margin-bottom: 30px;
    }
    .sidebar a {
      display: block;
      padding: 12px 20px;
      color: white;
      text-decoration: none;
      transition: 0.3s;
    }
    .sidebar a:hover {
      background: #005fa3;
    }
    .sidebar .logout {
      background: red;
      text-align: center;
      margin: 20px;
      border-radius: 6px;
    }
    .main {
      margin-left: 220px;
      padding: 20px;
      flex: 1;
    }
    header {
      background: #fff;
      padding: 15px;
      border-radius: 8px;
      box-shadow: 0px 4px 6px rgba(0,0,0,0.1);
      margin-bottom: 20px;
    }
    header h1 {
      margin: 0;
      color: #333;
    }
    .stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }
    .card {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0px 4px 6px rgba(0,0,0,0.1);
    }
    .progress {
      height: 12px;
      background: #eee;
      border-radius: 6px;
      overflow: hidden;
      margin-top: 8px;
    }
    .progress-bar {
      height: 100%;
      text-align: right;
      padding-right: 5px;
      color: white;
      border-radius: 6px;
    }
    .feedback-bar { background: #28a745; }
    .subscribers-bar { background: #ffc107; }
    .packages-bar { background: #17a2b8; }
    .students-bar { background: #dc3545; }
    canvas {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0px 4px 6px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <div>
      <h2>Dashboard</h2>
      <a href="view_feedback.php">View Feedback</a>
      <a href="view_subscribers.php">Newsletter Subscribers</a>
      <a href="packages.php">Manage Packages</a>
      <a href="view_student.php">Registered Students</a>
      <a href="view_orders.php">Products Order</a>
      <a href="manage_products.php">🛒 Manage Products</a> <!-- NEW LINK -->
    </div>
    <a href="logout.php" class="logout">Logout</a>
  </div>

  <!-- Main Content -->
  <div class="main">
    <header>
      <h1>Welcome, <?php echo $_SESSION['admin_username']; ?> 🎉</h1>
    </header>

    <!-- Progress Cards -->
    <div class="stats">
      <div class="card">
        <h3>Feedback</h3>
        <div class="progress"><div id="feedbackBar" class="progress-bar feedback-bar"></div></div>
      </div>
      <div class="card">
        <h3>Subscribers</h3>
        <div class="progress"><div id="subscribersBar" class="progress-bar subscribers-bar"></div></div>
      </div>
      <div class="card">
        <h3>Packages</h3>
        <div class="progress"><div id="packagesBar" class="progress-bar packages-bar"></div></div>
      </div>
      <div class="card">
        <h3>Students</h3>
        <div class="progress"><div id="studentsBar" class="progress-bar students-bar"></div></div>
      </div>
    </div>

    <!-- Chart -->
    <canvas id="statsChart" height="120"></canvas>
  </div>

<script>
$(document).ready(function(){
    function loadStats(){
        $.ajax({
            url: "stats_api.php",
            method: "GET",
            dataType: "json",
            success: function(data){
                let total = data.feedback + data.subscribers + data.packages + data.students;
                if(total === 0) total = 1;

                $("#feedbackBar").css("width", (data.feedback/total*100)+"%").text(data.feedback);
                $("#subscribersBar").css("width", (data.subscribers/total*100)+"%").text(data.subscribers);
                $("#packagesBar").css("width", (data.packages/total*100)+"%").text(data.packages);
                $("#studentsBar").css("width", (data.students/total*100)+"%").text(data.students);

                statsChart.data.datasets[0].data = [
                  data.feedback, data.subscribers, data.packages, data.students
                ];
                statsChart.update();
            }
        });
    }

    // Chart.js
    let ctx = document.getElementById("statsChart").getContext("2d");
    let statsChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: ["Feedback", "Subscribers", "Packages", "Students"],
            datasets: [{
                label: "Count",
                data: [0,0,0,0],
                backgroundColor: ["#28a745","#ffc107","#17a2b8","#dc3545"]
            }]
        }
    });

    loadStats();
    setInterval(loadStats, 5000); // auto-refresh stats
});
</script>

</body>
</html>
