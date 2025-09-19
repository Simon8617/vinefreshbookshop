<?php
include('db.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Subscribers</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f8f9fa;
      margin: 20px;
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }
    .subscribers-container {
      max-width: 900px;
      margin: auto;
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }
    th, td {
      border: 1px solid #ddd;
      padding: 12px;
      text-align: left;
    }
    th {
      background: #28a745;
      color: white;
    }
    tr:nth-child(even) {
      background: #f2f2f2;
    }
    button {
      padding: 6px 12px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
    }
    .btn-delete {
      background: #dc3545;
      color: white;
    }
    .btn-delete:hover {
      background: #a71d2a;
    }
    .btn-bulk {
      background: #ff9800;
      color: white;
      margin-top: 10px;
    }
    .btn-bulk:hover {
      background: #e68900;
    }
    .btn-dashboard {
      background: #007bff;
      color: white;
      margin-bottom: 15px;
    }
    .btn-dashboard:hover {
      background: #0056b3;
    }
    .search-box {
      margin-bottom: 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .search-box input {
      padding: 8px;
      width: 250px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    .search-box button {
      padding: 8px 14px;
      background: #28a745;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .search-box button:hover {
      background: #218838;
    }
    .message {
      text-align: center;
      margin-bottom: 15px;
      font-size: 14px;
      color: green;
    }
  </style>
</head>
<body>

<div class="subscribers-container">
  <h2>Subscribers List</h2>
  <a href="admin_dashboard.php"><button class="btn-dashboard">⬅ Back to Dashboard</button></a>
  
  <div id="message" class="message"></div>

  <!-- 🔍 Search -->
  <div class="search-box">
    <input type="text" id="searchInput" placeholder="Search by email...">
    <button onclick="searchSubscribers()">Search</button>
  </div>

  <form id="subscribersForm">
    <table>
      <tr>
        <th><input type="checkbox" id="selectAll"></th>
        <th>ID</th>
        <th>Email</th>
        <th>Subscribed At</th>
        <th>Action</th>
      </tr>

      <tbody id="subscribersData">
        <?php
        $sql = "SELECT * FROM subscribers ORDER BY subscribed_at DESC";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr id='subscriber-{$row['id']}'>
                        <td><input type='checkbox' name='subscriber_ids[]' value='{$row['id']}'></td>
                        <td>{$row['id']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['subscribed_at']}</td>
                        <td><button type='button' class='btn-delete' onclick='deleteSubscriber({$row['id']})'>Delete</button></td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5' style='text-align:center;'>No subscribers yet</td></tr>";
        }
        ?>
      </tbody>
    </table>

    <button type="button" class="btn-bulk" onclick="bulkDelete()">Delete Selected</button>
  </form>
</div>

<script>
  // ✅ Delete single subscriber
  function deleteSubscriber(id) {
    if (confirm("Are you sure you want to delete this subscriber?")) {
      $.ajax({
        url: "delete_subscriber.php",
        type: "POST",
        data: { subscriber_id: id },
        success: function(response) {
          if (response.includes("success")) {
            $("#subscriber-" + id).fadeOut();
            $("#message").html("Subscriber deleted successfully!");
          } else {
            $("#message").html(response);
          }
        }
      });
    }
  }

  // ✅ Bulk delete subscribers
  function bulkDelete() {
    var ids = [];
    $("input[name='subscriber_ids[]']:checked").each(function(){
      ids.push($(this).val());
    });

    if (ids.length === 0) {
      alert("Please select at least one subscriber.");
      return;
    }

    if (confirm("Are you sure you want to delete selected subscribers?")) {
      $.ajax({
        url: "delete_subscriber.php",
        type: "POST",
        data: { bulk_ids: ids },
        success: function(response) {
          if (response.includes("success")) {
            ids.forEach(id => {
              $("#subscriber-" + id).fadeOut();
            });
            $("#message").html("Selected subscribers deleted successfully!");
          } else {
            $("#message").html(response);
          }
        }
      });
    }
  }

  // ✅ Select All Checkboxes
  $("#selectAll").click(function() {
    $("input[name='subscriber_ids[]']").prop('checked', this.checked);
  });

  // ✅ Search subscribers
  function searchSubscribers() {
    var keyword = $("#searchInput").val();
    $.ajax({
      url: "search_subscribers.php",
      type: "GET",
      data: { keyword: keyword },
      success: function(data) {
        $("#subscribersData").html(data);
      }
    });
  }
</script>

</body>
</html>
