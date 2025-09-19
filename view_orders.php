<?php
// view_orders.php
include 'db.php';

// Fetch orders with product info
$sql = "SELECT o.*, p.name AS product_name 
        FROM orders o 
        JOIN products p ON o.product_id = p.id 
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Orders - Vinefresh</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 0; }
    header { background: #0077cc; padding: 15px; text-align: center; color: white; }
    .container { max-width: 1200px; margin: 30px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    h2 { color: #0077cc; margin-bottom: 20px; text-align: center; }
    .search-box { margin-bottom: 15px; text-align: right; }
    .search-box input { padding: 8px; border: 1px solid #ccc; border-radius: 5px; width: 250px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
    th { background: #0077cc; color: white; }
    tr:hover { background: #f9f9f9; }
    .btn { padding: 6px 12px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; }
    .edit-btn { background: #ffc107; color: black; }
    .delete-btn { background: #dc3545; color: white; }
    .save-btn { background: #28a745; color: white; display: none; }
    .cancel-btn { background: #6c757d; color: white; display: none; }
    .message { margin: 15px 0; text-align: center; font-weight: bold; }
    .success { color: #28a745; }
    .error { color: red; }
  </style>
</head>
<body>
  <header>
    <h1>Admin - Manage Orders</h1>
  </header>

  <div class="container">
    <h2>All Orders</h2>
    <div class="search-box">
      <input type="text" id="searchInput" placeholder="🔍 Search by customer, phone or product..."> <br>
     <a href="admin_dashboard.php"><strong>Home</strong></a>
    </div>
    <div class="message" id="responseMsg"></div>
    <table id="ordersTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>Customer</th>
          <th>Phone</th>
          <th>Address</th>
          <th>Product</th>
          <th>Quantity</th>
          <th>Total Price</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while($row = $result->fetch_assoc()): ?>
            <tr data-id="<?php echo $row['id']; ?>">
              <td><?php echo $row['id']; ?></td>
              <td contenteditable="false" class="editable" data-field="customer_name"><?php echo htmlspecialchars($row['customer_name']); ?></td>
              <td contenteditable="false" class="editable" data-field="customer_phone"><?php echo htmlspecialchars($row['customer_phone']); ?></td>
              <td contenteditable="false" class="editable" data-field="customer_address"><?php echo htmlspecialchars($row['customer_address']); ?></td>
              <td><?php echo htmlspecialchars($row['product_name']); ?></td>
              <td contenteditable="false" class="editable" data-field="quantity"><?php echo $row['quantity']; ?></td>
              <td>₦<?php echo number_format($row['total_price'], 2); ?></td>
              <td contenteditable="false" class="editable" data-field="status"><?php echo $row['status']; ?></td>
              <td>
                <button class="btn edit-btn">✏️ Edit</button>
                <button class="btn save-btn">💾 Save</button>
                <button class="btn cancel-btn">❌ Cancel</button>
                <button class="btn delete-btn">🗑 Delete</button>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="9" style="text-align:center; color:red;">❌ No orders found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <script>
    const table = document.getElementById("ordersTable");
    const responseMsg = document.getElementById("responseMsg");

    // Search filter
    document.getElementById("searchInput").addEventListener("keyup", function() {
      const filter = this.value.toLowerCase();
      const rows = table.querySelectorAll("tbody tr");
      rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
      });
    });

    // Handle Edit / Save / Cancel
    table.addEventListener("click", function(e) {
      if (e.target.classList.contains("edit-btn")) {
        const row = e.target.closest("tr");
        row.querySelectorAll(".editable").forEach(cell => cell.contentEditable = "true");
        row.querySelector(".edit-btn").style.display = "none";
        row.querySelector(".save-btn").style.display = "inline-block";
        row.querySelector(".cancel-btn").style.display = "inline-block";
      }

      if (e.target.classList.contains("cancel-btn")) {
        location.reload(); // Reload to reset values
      }

      if (e.target.classList.contains("save-btn")) {
        const row = e.target.closest("tr");
        const id = row.dataset.id;
        const updatedData = {};
        row.querySelectorAll(".editable").forEach(cell => {
          updatedData[cell.dataset.field] = cell.innerText.trim();
        });

        fetch("order_actions.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ action: "update", id, ...updatedData })
        })
        .then(res => res.json())
        .then(data => {
          responseMsg.textContent = data.message;
          responseMsg.className = "message " + (data.status === "success" ? "success" : "error");
          if (data.status === "success") {
            row.querySelectorAll(".editable").forEach(cell => cell.contentEditable = "false");
            row.querySelector(".edit-btn").style.display = "inline-block";
            row.querySelector(".save-btn").style.display = "none";
            row.querySelector(".cancel-btn").style.display = "none";
          }
        });
      }

      if (e.target.classList.contains("delete-btn")) {
        if (!confirm("Are you sure you want to delete this order?")) return;
        const row = e.target.closest("tr");
        const id = row.dataset.id;

        fetch("order_actions.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ action: "delete", id })
        })
        .then(res => res.json())
        .then(data => {
          responseMsg.textContent = data.message;
          responseMsg.className = "message " + (data.status === "success" ? "success" : "error");
          if (data.status === "success") {
            row.remove();
          }
        });
      }
    });
  </script>
</body>
</html>
