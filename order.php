<?php
// order.php
session_start();
include 'db.php';

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle AJAX order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token'])) {
    header('Content-Type: application/json');

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        echo json_encode(["status" => "error", "message" => "⚠️ Invalid session. Refresh and try again."]);
        exit;
    }

    $product_id = intval($_POST['product_id'] ?? 0);
    $quantity   = intval($_POST['quantity'] ?? 1);
    $name       = trim($_POST['customer_name'] ?? '');
    $phone      = trim($_POST['customer_phone'] ?? '');
    $address    = trim($_POST['customer_address'] ?? '');

    // Basic validation
    if ($product_id <= 0 || $quantity <= 0 || empty($name) || empty($phone) || empty($address)) {
        echo json_encode(["status" => "error", "message" => "⚠️ Please fill all fields correctly."]);
        exit;
    }
    if (!preg_match("/^[0-9]{10,15}$/", $phone)) {
        echo json_encode(["status" => "error", "message" => "⚠️ Enter a valid phone number."]);
        exit;
    }
    if (!preg_match("/^[a-zA-Z\s'.-]+$/", $name)) {
        echo json_encode(["status" => "error", "message" => "⚠️ Name contains invalid characters."]);
        exit;
    }

    // Fetch product
    $stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product) {
        echo json_encode(["status" => "error", "message" => "❌ Product not found."]);
        exit;
    }

    if ($product['stock'] < $quantity) {
        echo json_encode(["status" => "error", "message" => "❌ Not enough stock available."]);
        exit;
    }

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (product_id, customer_name, customer_phone, customer_address, quantity, total_price) VALUES (?,?,?,?,?,?)");
    $total = $product['price'] * $quantity;
    $stmt->bind_param("isssid", $product_id, $name, $phone, $address, $quantity, $total);

    if ($stmt->execute()) {
        // Reduce stock
        $newStock = $product['stock'] - $quantity;
        $conn->query("UPDATE products SET stock=$newStock WHERE id=$product_id");

        echo json_encode(["status" => "success", "message" => "✅ Order placed successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "❌ Failed to place order. Try again."]);
    }
    exit;
}

// Display order form (GET request)
$product_id = intval($_GET['product_id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("❌ Product not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order <?php echo htmlspecialchars($product['name']); ?> - Vinefresh</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 0; }
    header { background: #0077cc; padding: 15px; text-align: center; color: white; }
    .container {
      max-width: 600px; margin: 40px auto; background: white;
      padding: 20px; border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    h2 { margin-bottom: 20px; color: #0077cc; text-align: center; }
    .product-summary { display: flex; align-items: center; margin-bottom: 20px; }
    .product-summary img {
      width: 120px; height: 120px; object-fit: cover;
      border-radius: 8px; margin-right: 15px;
    }
    .product-summary h3 { margin: 0; color: #333; }
    .price { color: #28a745; font-weight: bold; }
    label { display: block; margin: 10px 0 5px; font-weight: bold; }
    input, textarea {
      width: 100%; padding: 10px; border: 1px solid #ccc;
      border-radius: 6px; font-size: 14px;
    }
    textarea { resize: vertical; min-height: 70px; }
    .btn {
      display: block; width: 100%; background: #0077cc;
      color: white; padding: 12px; border: none;
      border-radius: 6px; font-size: 16px;
      margin-top: 20px; cursor: pointer;
      transition: background 0.3s;
    }
    .btn:hover { background: #005fa3; }
    .btn:disabled { background: #999; cursor: not-allowed; }
    .message { margin-top: 15px; text-align: center; font-weight: bold; }
    .success { color: #28a745; }
    .error { color: red; }
    .back { display: block; text-align: center; margin-top: 20px; color: #0077cc; text-decoration: none; }
    .back:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <header>
    <h1>Place Your Order</h1>
  </header>

  <div class="container">
    <h2>Order Product</h2>
    <div class="product-summary">
      <img src="uploads/<?php echo htmlspecialchars($product['image'] ?? 'default.png'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
      <div>
        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
        <div class="price">₦<?php echo number_format($product['price'], 2); ?></div>
      </div>
    </div>

    <form id="orderForm">
      <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
      
      <label for="quantity">Quantity</label>
      <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo (int)$product['stock']; ?>" required>
      
      <label for="customer_name">Full Name</label>
      <input type="text" id="customer_name" name="customer_name" required>
      
      <label for="customer_phone">Phone Number</label>
      <input type="text" id="customer_phone" name="customer_phone" required placeholder="e.g. 08012345678">
      
      <label for="customer_address">Delivery Address</label>
      <textarea id="customer_address" name="customer_address" required></textarea>
      
      <button type="submit" class="btn" id="submitBtn">🛒 Confirm Order</button>
    </form>

    <div class="message" id="responseMsg"></div>
    <a href="products.php" class="back">&larr; Back to Products</a>
  </div>

  <script>
    const form = document.getElementById("orderForm");
    const btn = document.getElementById("submitBtn");
    const msg = document.getElementById("responseMsg");

    form.addEventListener("submit", function(e) {
      e.preventDefault();
      btn.disabled = true;
      msg.textContent = "⏳ Processing your order...";
      msg.className = "message";

      const formData = new FormData(form);

      fetch("order.php", {
        method: "POST",
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        msg.textContent = data.message;
        msg.className = "message " + (data.status === "success" ? "success" : "error");
        btn.disabled = false;

        if (data.status === "success") {
          form.reset();
        }
      })
      .catch(err => {
        msg.textContent = "⚠️ Error placing order.";
        msg.className = "message error";
        btn.disabled = false;
      });
    });
  </script>
</body>
</html>
