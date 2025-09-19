<?php
// edit_student.php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

include 'db.php';

// Ensure ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("❌ Invalid request.");
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM registrations WHERE id=?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("❌ Student not found.");
}
$student = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Student - Vinefresh</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f6f9;
      padding: 20px;
    }
    .container {
      max-width: 600px;
      margin: auto;
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #0077cc;
    }
    form {
      margin-top: 20px;
    }
    label {
      font-weight: bold;
      display: block;
      margin-top: 10px;
    }
    input, select {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 16px;
    }
    .btn {
      margin-top: 20px;
      padding: 10px 15px;
      border: none;
      border-radius: 6px;
      color: white;
      cursor: pointer;
    }
    .btn-save { background: #28a745; }
    .btn-back { background: #0077cc; text-decoration: none; display: inline-block; margin-top: 15px; }
    .msg {
      text-align: center;
      margin-top: 15px;
      font-weight: bold;
    }
    .success { color: green; }
    .error { color: red; }
  </style>
</head>
<body>
  <div class="container">
    <a href="view_student.php" class="btn btn-back">⬅ Back to Students</a>
    <h2>Edit Student</h2>
    <form id="editForm">
      <input type="hidden" name="id" value="<?php echo $student['id']; ?>">

      <label>First Name</label>
      <input type="text" name="firstname" value="<?php echo htmlspecialchars($student['firstname']); ?>" required>

      <label>Middle Name</label>
      <input type="text" name="middlename" value="<?php echo htmlspecialchars($student['middlename']); ?>">

      <label>Last Name</label>
      <input type="text" name="lastname" value="<?php echo htmlspecialchars($student['lastname']); ?>" required>

      <label>Email</label>
      <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>

      <label>Phone</label>
      <input type="text" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>" required>

      <label>Package 1</label>
      <input type="text" name="pack1" value="<?php echo htmlspecialchars($student['pack1']); ?>">

      <label>Package 2</label>
      <input type="text" name="pack2" value="<?php echo htmlspecialchars($student['pack2']); ?>">

      <label>Package 3</label>
      <input type="text" name="pack3" value="<?php echo htmlspecialchars($student['pack3']); ?>">

      <label>Package 4</label>
      <input type="text" name="pack4" value="<?php echo htmlspecialchars($student['pack4']); ?>">

      <label>Package 5</label>
      <input type="text" name="pack5" value="<?php echo htmlspecialchars($student['pack5']); ?>">

      <label>Package 6</label>
      <input type="text" name="pack6" value="<?php echo htmlspecialchars($student['pack6']); ?>">

      <label>Package 7</label>
      <input type="text" name="pack7" value="<?php echo htmlspecialchars($student['pack7']); ?>">

      <label>Package 8</label>
      <input type="text" name="pack8" value="<?php echo htmlspecialchars($student['pack8']); ?>">

      <label>Package 9</label>
      <input type="text" name="pack9" value="<?php echo htmlspecialchars($student['pack9']); ?>">


      <button type="submit" class="btn btn-save">💾 Save Changes</button>
    </form>
    <p id="message" class="msg"></p>
  </div>

<script>
$(document).ready(function(){
  $("#editForm").on("submit", function(e){
    e.preventDefault();
    $.ajax({
      url: "update_student.php",
      type: "POST",
      data: $(this).serialize(),
      success: function(response){
        if(response.trim() == "success"){
          $("#message").html("✅ Student updated successfully!").addClass("success").removeClass("error");
        } else {
          $("#message").html("❌ Error updating student: " + response).addClass("error").removeClass("success");
        }
      }
    });
  });
});
</script>
</body>
</html>
