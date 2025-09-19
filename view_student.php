<?php
// view_student.php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

include 'db.php';

// Fetch students
$sql = "SELECT * FROM registrations ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Students - Vinefresh</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f6f9;
      margin: 0;
      padding: 20px;
    }
    .container {
      max-width: 1100px;
      margin: auto;
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #0077cc;
    }
    .search-box {
      margin-bottom: 20px;
      text-align: center;
    }
    .search-box input {
      width: 50%;
      padding: 10px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    th, td {
      border: 1px solid #ddd;
      padding: 12px;
      text-align: center;
    }
    th {
      background: #0077cc;
      color: white;
    }
    tr:nth-child(even) { background: #f9f9f9; }
    tr:hover { background: #f1f1f1; }
    .btn {
      padding: 6px 12px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      color: white;
    }
    .btn-edit { background: #28a745; }
    .btn-delete { background: #dc3545; }
    .btn-back {
      background: #0077cc;
      display: inline-block;
      margin-bottom: 15px;
      text-decoration: none;
      padding: 8px 15px;
      border-radius: 6px;
      color: white;
    }
    .no-data {
      text-align: center;
      color: red;
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <div class="container">
    <a href="admin_dashboard.php" class="btn-back">⬅ Back to Dashboard</a>
    <h2>Registered Students</h2>

    <div class="search-box">
      <input type="text" id="search" placeholder="🔍 Search students...">
    </div>

    <?php if ($result && $result->num_rows > 0): ?>
      <table id="studentsTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Firstname</th>
            <th>Middlename</th>
            <th>Lastname</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Course</th>
            <th>Reg Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
<tr id="row-<?php echo $row['id']; ?>">
  <td><?php echo $row['id']; ?></td>
  <td><?php echo htmlspecialchars($row['firstname']); ?></td>
  <td><?php echo htmlspecialchars($row['middlename']); ?></td>
  <td><?php echo htmlspecialchars($row['lastname']); ?></td>
  <td><?php echo htmlspecialchars($row['email']); ?></td>
  <td><?php echo htmlspecialchars($row['phone']); ?></td>
  <td>
    <?php
      // Combine non-empty package columns into a clean list
      $packages = [];
      for ($i=1; $i<=9; $i++) {
        if (!empty($row["pack$i"])) {
          $packages[] = htmlspecialchars($row["pack$i"]);
        }
      }
      echo implode(", ", $packages);
    ?>
  </td>
  <td><?php echo htmlspecialchars($row['created_at']); ?></td>
  <td>
    <button class="btn btn-edit" onclick="editStudent(<?php echo $row['id']; ?>)">✏ Edit</button>
    <button class="btn btn-delete" onclick="deleteStudent(<?php echo $row['id']; ?>)">🗑 Delete</button>
  </td>
</tr>
<?php endwhile; ?>


        </tbody>
      </table>
    <?php else: ?>
      <p class="no-data">❌ No students found.</p>
    <?php endif; ?>
  </div>

<script>
$(document).ready(function(){
  // Search filter
  $("#search").on("keyup", function(){
    var value = $(this).val().toLowerCase();
    $("#studentsTable tbody tr").filter(function(){
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});

// Delete student via AJAX
function deleteStudent(id){
  if(confirm("Are you sure you want to delete this student?")){
    $.ajax({
      url: "delete_student.php",
      type: "POST",
      data: {id: id},
      success: function(response){
        if(response == "success"){
          $("#row-" + id).fadeOut();
        } else {
          alert("❌ Error deleting student.");
        }
      }
    });
  }
}

// Edit student (redirect to form page)
function editStudent(id){
  window.location.href = "edit_student.php?id=" + id;
}
</script>
</body>
</html>
