<?php
// packages.php
$host = "localhost"; 
$user = "root"; 
$pass = ""; 
$db   = "vinefresh"; // change to your DB name

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

// Handle AJAX CRUD requests
if (isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == "add") {
        $name = $conn->real_escape_string($_POST['name']);
        $conn->query("INSERT INTO packages (name) VALUES ('$name')");
        echo "success";
        exit;
    }
    if ($action == "edit") {
        $id = intval($_POST['id']);
        $name = $conn->real_escape_string($_POST['name']);
        $conn->query("UPDATE packages SET name='$name' WHERE id=$id");
        echo "success";
        exit;
    }
    if ($action == "delete") {
        $id = intval($_POST['id']);
        $conn->query("DELETE FROM packages WHERE id=$id");
        echo "success";
        exit;
    }
    if ($action == "bulk_delete") {
        $ids = implode(",", array_map('intval', $_POST['ids']));
        $conn->query("DELETE FROM packages WHERE id IN ($ids)");
        echo "success";
        exit;
    }
}

// Fetch all packages
$result = $conn->query("SELECT * FROM packages ORDER BY id DESC");
$packages = [];
while ($row = $result->fetch_assoc()) {
    $packages[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Our Packages</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f6f9;
      margin: 0;
      padding: 20px;
    }
    .container {
      max-width: 900px;
      margin: auto;
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }
    .top-bar {
      display: flex;
      justify-content: space-between;
      margin-bottom: 15px;
    }
    .top-bar input {
      width: 50%;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    .btn {
      padding: 8px 15px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      margin-left: 5px;
    }
    .btn-add { background: #28a745; color: #fff; }
    .btn-edit { background: #007bff; color: #fff; }
    .btn-delete { background: #dc3545; color: #fff; }
    .btn-bulk { background: #6c757d; color: #fff; }
    .btn-back { background: #ff9800; color: #fff; float:right;}
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }
    table, th, td {
      border: 1px solid #ddd;
    }
    th, td {
      padding: 10px;
      text-align: center;
    }
    th { background: #f8f9fa; }
    .no-result { text-align:center; color:red; display:none; margin-top:10px; }
  </style>
</head>
<body>
<div class="container">
  <h2>Available Training Packages</h2>
  
  <div class="top-bar">
    <input type="text" id="search" placeholder="Search package...">
    <div>
      <button class="btn btn-add" onclick="addPackage()">+ Add Package</button>
      <button class="btn btn-bulk" onclick="bulkDelete()">Delete Selected</button>
      <a href="admin_dashboard.php" class="btn btn-back">⬅ Back to Dashboard</a>
    </div>
  </div>

  <table id="packageTable">
    <thead>
      <tr>
        <th><input type="checkbox" id="selectAll"></th>
        <th>ID</th>
        <th>Package Name</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($packages as $p): ?>
        <tr data-id="<?= $p['id'] ?>">
          <td><input type="checkbox" class="selectRow"></td>
          <td><?= $p['id'] ?></td>
          <td class="pkg-name"><?= htmlspecialchars($p['package_name']) ?></td>
          <td>
            <button class="btn btn-edit" onclick="editPackage(<?= $p['id'] ?>)">Edit</button>
            <button class="btn btn-delete" onclick="deletePackage(<?= $p['id'] ?>)">Delete</button>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="no-result" id="noResult">❌ No package found</div>
</div>

<script>
$(document).ready(function(){
  // search filter
  $("#search").on("keyup", function(){
    var value = $(this).val().toLowerCase();
    var found = false;
    $("#packageTable tbody tr").each(function(){
      if ($(this).text().toLowerCase().indexOf(value) > -1) {
        $(this).show();
        found = true;
      } else {
        $(this).hide();
      }
    });
    $("#noResult").toggle(!found);
  });

  // select all
  $("#selectAll").click(function(){
    $(".selectRow").prop('checked', this.checked);
  });
});

// CRUD functions
function addPackage() {
  var name = prompt("Enter new package name:");
  if (name) {
    $.post("packages.php", {action:"add", name:name}, function(res){
      location.reload();
    });
  }
}

function editPackage(id) {
  var row = $("tr[data-id='"+id+"']");
  var current = row.find(".pkg-name").text();
  var name = prompt("Edit package name:", current);
  if (name) {
    $.post("packages.php", {action:"edit", id:id, name:name}, function(res){
      if(res=="success") row.find(".pkg-name").text(name);
    });
  }
}

function deletePackage(id) {
  if (confirm("Delete this package?")) {
    $.post("packages.php", {action:"delete", id:id}, function(res){
      if(res=="success") $("tr[data-id='"+id+"']").remove();
    });
  }
}

function bulkDelete() {
  var ids = [];
  $(".selectRow:checked").each(function(){
    ids.push($(this).closest("tr").data("id"));
  });
  if (ids.length < 1) { alert("No packages selected."); return; }
  if (confirm("Delete selected packages?")) {
    $.post("packages.php", {action:"bulk_delete", ids:ids}, function(res){
      if(res=="success") {
        ids.forEach(id => $("tr[data-id='"+id+"']").remove());
      }
    });
  }
}
</script>
</body>
</html>
