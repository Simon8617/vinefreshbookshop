<?php
// product_details.php
include 'db.php';

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("❌ Invalid product ID.");
}

$id = intval($_GET['id']);

// Fetch product by ID
$stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    die("❌ Product not found.");
}

// Handle image path smartly
$imagePath = $product['image'];
if (empty($imagePath)) {
    $imagePath = "uploads/default.png";
} elseif (filter_var($imagePath, FILTER_VALIDATE_URL)) {
    $imagePath = $product['image'];
} elseif (!preg_match('/\//', $imagePath)) {
    $imagePath = "uploads/" . $imagePath;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($product['name']); ?> - Vinefresh</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f6f9;
      margin: 0;
      padding: 0;
    }
    header {
      background: #0077cc;
      padding: 15px;
      text-align: center;
      color: white;
    }
    .container {
      max-width: 1000px;
      margin: 30px auto;
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .product-wrapper {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      align-items: flex-start;
    }
    .product-image {
      flex: 1 1 40%;
    }
    .product-image img {
      width: 100%;
      border-radius: 10px;
      object-fit: cover;
    }
    .product-info {
      flex: 1 1 55%;
    }
    .product-info h2 {
      color: #333;
      margin-bottom: 15px;
    }
    .product-info p {
      color: #555;
      line-height: 1.6;
      margin-bottom: 15px;
    }
    .price {
      font-size: 22px;
      font-weight: bold;
      color: #28a745;
      margin: 20px 0;
    }
    .btn {
      display: inline-block;
      background: #0077cc;
      color: white;
      padding: 10px 20px;
      border-radius: 6px;
      text-decoration: none;
      transition: background 0.3s;
    }
    .btn:hover {
      background: #005fa3;
    }
    .back {
      display: inline-block;
      margin-top: 20px;
      text-decoration: none;
      color: #0077cc;
      font-weight: bold;
    }
    .back:hover {
      text-decoration: underline;
    }
    footer {
      text-align: center;
      padding: 15px;
      margin-top: 30px;
      background: #f1f1f1;
      color: #555;
    }
    /* Extra styling for stock */
    .stock {
      margin-top: 10px;
      font-weight: bold;
      color: #ff6600;
    }
  </style>
</head>
<body>
  <header>
    <h1>Product Details</h1>
  </header>

  <div class="container">
    <div class="product-wrapper">
      <div class="product-image">
        <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
      </div>
      <div class="product-info">
        <h2><?php echo htmlspecialchars($product['name']); ?></h2>
        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
        <div class="price">₦<?php echo number_format($product['price'], 2); ?></div>
        <div class="stock">Available Stock: <?php echo (int)$product['stock']; ?></div>
        <br>
        <a href="order.php?product_id=<?php echo $product['id']; ?>" class="btn">🛒 Order Now</a>
        <br><br>
        <a href="products.php" class="back">&larr; Back to Products</a>
      </div>
    </div>
  </div>

  <footer>
    &copy; <?php echo date("Y"); ?> Vinefresh. All Rights Reserved.
  </footer>
</body>
</html>
