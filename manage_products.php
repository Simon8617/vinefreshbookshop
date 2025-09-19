<?php
// manage_products.php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}
include 'db.php';

// Handle add product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);

    $imagePath = "";
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/products/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $imagePath = $targetDir . time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    }

    $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, image) VALUES (?,?,?,?,?)");
    $stmt->bind_param("ssdis", $name, $description, $price, $stock, $imagePath);

    if ($stmt->execute()) {
        exit("success");
    } else {
        exit("error");
    }
}



// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete') {
    $id = intval($_POST['id']);
    $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    exit("deleted");
}

// Handle edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit') {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);

    $imagePath = $_POST['old_image']; // keep old image if not updated
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/products/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $imagePath = $targetDir . time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    }

    $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, stock=?, image=? WHERE id=?");
    $stmt->bind_param("ssdisi", $name, $description, $price, $stock, $imagePath, $id);
    $stmt->execute();
    exit("updated");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Products</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f6f9; padding: 20px; }
    h1 { text-align: center; color: #333; }
    .form-container, .edit-modal {
      background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 30px;
      box-shadow: 0px 4px 8px rgba(0,0,0,0.1); max-width: 600px; margin: auto;
    }
    input, textarea { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ddd; border-radius: 6px; }
    button { padding: 8px 16px; border-radius: 6px; border: none; cursor: pointer; }
    .btn-primary { background: #0077cc; color: white; }
    .btn-primary:hover { background: #005fa3; }
    .btn-danger { background: red; color: white; }
    .btn-danger:hover { background: darkred; }
    .btn-edit { background: orange; color: white; }
    .btn-edit:hover { background: darkorange; }
    table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; box-shadow: 0px 4px 8px rgba(0,0,0,0.1); }
    th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: center; }
    th { background: #0077cc; color: white; }
    img { max-width: 80px; border-radius: 6px; }
    #editModal { display: none; position: fixed; top: 0; left: 0; width:100%; height:100%; background: rgba(0,0,0,0.6); }
    .edit-modal { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); }
  </style>
</head>
<body>
  <h1>Manage Products</h1>

  <!-- Upload Form -->
  <div class="form-container">
    <h2>Add New Product</h2>
    <form id="productForm" enctype="multipart/form-data">
      <input type="hidden" name="action" value="add">
      <input type="text" name="name" placeholder="Product Name" required>
      <textarea name="description" placeholder="Product Description" required></textarea>
      <input type="number" name="price" step="0.01" placeholder="Price" required>
      <input type="number" name="stock" placeholder="Stock Quantity" required>
      <input type="file" name="image" accept="image/*" required>
      <button type="submit" class="btn-primary">Upload Product</button> <a href="admin_dashboard.php"><strong>Home</strong></a>
    </form>
    <p id="message"></p>
  </div>

  <!-- Product List -->
  <table>
    <thead>
      <tr>
        <th>Image</th><th>Name</th><th>Description</th><th>Price (₦)</th><th>Stock</th><th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $result = $conn->query("SELECT * FROM products ORDER BY id DESC");
      while ($row = $result->fetch_assoc()) {
        echo "<tr id='row{$row['id']}'>
                <td><img src='{$row['image']}'></td>
                <td>{$row['name']}</td>
                <td>{$row['description']}</td>
                <td>{$row['price']}</td>
                <td>{$row['stock']}</td>
                <td>
                  <button class='btn-edit' 
                    data-id='{$row['id']}' 
                    data-name='{$row['name']}'
                    data-description='{$row['description']}'
                    data-price='{$row['price']}'
                    data-stock='{$row['stock']}'
                    data-image='{$row['image']}'>Edit</button>
                  <button class='btn-danger delete-btn' data-id='{$row['id']}'>Delete</button>
                </td>
              </tr>";
      }
      ?>
    </tbody>
  </table>

  <!-- Edit Modal -->
  <div id="editModal">
    <div class="edit-modal">
      <h2>Edit Product</h2>
      <form id="editForm" enctype="multipart/form-data">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" id="edit_id">
        <input type="hidden" name="old_image" id="edit_old_image">
        <input type="text" name="name" id="edit_name" required>
        <textarea name="description" id="edit_description" required></textarea>
        <input type="number" step="0.01" name="price" id="edit_price" required>
        <input type="number" name="stock" id="edit_stock" required>
        <input type="file" name="image" accept="image/*">
        <button type="submit" class="btn-primary">Update Product</button>
        <button type="button" class="btn-danger" onclick="$('#editModal').hide()">Cancel</button>
      </form>
      <p id="editMessage"></p>
    </div>
  </div>

<script>
$(document).ready(function(){
  // Add product
  $("#productForm").on("submit", function(e){
    e.preventDefault();
    $.ajax({
      url: "manage_products.php",
      type: "POST",
      data: new FormData(this),
      contentType: false,
      processData: false,
      success: function(res){
        if(res==="success"){ alert("✅ Product added"); location.reload(); }
        else alert("❌ Error adding product");
      }
    });
  });

  // Delete product
  $(".delete-btn").click(function(){
    if(!confirm("Delete this product?")) return;
    let id=$(this).data("id");
    $.post("manage_products.php",{action:"delete",id:id},function(res){
      if(res==="deleted"){ $("#row"+id).fadeOut(); }
      else alert("❌ Error deleting");
    });
  });

  // Open edit modal
  $(".btn-edit").click(function(){
    $("#edit_id").val($(this).data("id"));
    $("#edit_name").val($(this).data("name"));
    $("#edit_description").val($(this).data("description"));
    $("#edit_price").val($(this).data("price"));
    $("#edit_stock").val($(this).data("stock"));
    $("#edit_old_image").val($(this).data("image"));
    $("#editModal").show();
  });

  // Update product
  $("#editForm").submit(function(e){
    e.preventDefault();
    $.ajax({
      url:"manage_products.php",
      type:"POST",
      data:new FormData(this),
      contentType:false,
      processData:false,
      success:function(res){
        if(res==="updated"){ alert("✅ Product updated"); location.reload(); }
        else alert("❌ Error updating");
      }
    });
  });
});
</script>
</body>
</html>
