<?php
// products.php
session_start();
include 'db.php';

// Fetch products
$sql = "SELECT * FROM products ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Our Products & Services - Vinefresh</title>
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
      position: sticky;
      top: 0;
      z-index: 1000;
    }
    header a {
      display: inline-block;
      margin-top: 10px;
      background: #28a745;
      color: white;
      padding: 8px 15px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: bold;
      transition: background 0.3s;
    }
    header a:hover {
      background: #218838;
    }
    .container {
      max-width: 1200px;
      margin: 20px auto;
      padding: 10px;
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #0077cc;
    }
    .products-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
    }
    .card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      padding: 15px;
      text-align: center;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .card:hover {
      transform: scale(1.03);
      box-shadow: 0 6px 15px rgba(0,0,0,0.15);
    }
    .card img {
      max-width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 10px;
      margin-bottom: 10px;
    }
    .card h3 {
      margin: 10px 0;
      color: #333;
    }
    .card p {
      font-size: 14px;
      color: #555;
    }
    .price {
      font-size: 18px;
      font-weight: bold;
      color: #28a745;
      margin: 10px 0;
    }
    .btn {
      display: inline-block;
      background: #0077cc;
      color: white;
      padding: 8px 15px;
      border-radius: 6px;
      text-decoration: none;
      transition: background 0.3s;
      cursor: pointer;
    }
    .btn:hover {
      background: #005fa3;
    }
    footer {
      text-align: center;
      padding: 15px;
      margin-top: 30px;
      background: #f1f1f1;
      color: #555;
    }
    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.6);
      justify-content: center;
      align-items: center;
      z-index: 2000;
    }
    .modal-content {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      max-width: 600px;
      width: 90%;
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
      text-align: left;
    }
    .modal-content h3 {
      margin-top: 0;
      color: #0077cc;
    }
    .modal-content img {
      width: 100%;
      height: auto;
      border-radius: 8px;
      margin-bottom: 15px;
    }
    .close {
      float: right;
      font-size: 22px;
      font-weight: bold;
      cursor: pointer;
      color: #333;
    }
    .close:hover {
      color: red;
    }
  </style>
</head>
<body>
  <header>
    <h1>Vinefresh Products & Services</h1>
    <a href="index.php">🏠 Home</a>
  </header>

  <div class="container">
    <h2>Explore Our Offerings</h2>
    <div class="products-grid">
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
          <?php
          // Handle image path smartly
          $imagePath = $row['image'];
          if (empty($imagePath)) {
              $imagePath = "uploads/default.png";
          } elseif (filter_var($imagePath, FILTER_VALIDATE_URL)) {
              $imagePath = $row['image'];
          } elseif (!preg_match('/\//', $imagePath)) {
              $imagePath = "uploads/" . $imagePath;
          }
          ?>
          <div class="card">
            <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
            <h3><?php echo htmlspecialchars($row['name']); ?></h3>
            <p><?php echo nl2br(htmlspecialchars(substr($row['description'], 0, 120))); ?>...</p>
            <div class="price">₦<?php echo number_format($row['price'], 2); ?></div>
            <button class="btn view-details" data-id="<?php echo $row['id']; ?>">View Details</button>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p style="text-align:center; color:red;">❌ No products available at the moment.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal" id="productModal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <div id="modal-body">
        <!-- AJAX-loaded content here -->
      </div>
    </div>
  </div>

  <footer>
    &copy; <?php echo date("Y"); ?> Vinefresh. All Rights Reserved.
  </footer>

  <script>
    // Handle modal
    const modal = document.getElementById("productModal");
    const closeBtn = document.querySelector(".close");
    const modalBody = document.getElementById("modal-body");

    document.querySelectorAll(".view-details").forEach(btn => {
      btn.addEventListener("click", function() {
        const productId = this.getAttribute("data-id");

        // AJAX request
        fetch("product_details.php?id=" + productId)
          .then(res => res.text())
          .then(data => {
            modalBody.innerHTML = data;
            modal.style.display = "flex";
          })
          .catch(err => {
            modalBody.innerHTML = "<p style='color:red;'>⚠️ Error loading product details.</p>";
            modal.style.display = "flex";
          });
      });
    });

    closeBtn.addEventListener("click", () => modal.style.display = "none");
    window.addEventListener("click", (e) => {
      if (e.target === modal) {
        modal.style.display = "none";
      }
    });
  </script>
</body>
</html>
