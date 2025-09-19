<?php
include("db.php"); // database connection

// Handle AJAX submission
if (isset($_POST['ajax']) && $_POST['ajax'] == "1") {
    $firstname   = mysqli_real_escape_string($conn, $_POST['firstname']);
    $middlename  = mysqli_real_escape_string($conn, $_POST['middlename']);
    $lastname    = mysqli_real_escape_string($conn, $_POST['lastname']);
    $address     = mysqli_real_escape_string($conn, $_POST['address']);
    $phone       = mysqli_real_escape_string($conn, $_POST['phone']);
    $gender      = mysqli_real_escape_string($conn, $_POST['gender']);
    $dob         = $_POST['day'] . "-" . $_POST['month'] . "-" . $_POST['year'];
    $email       = mysqli_real_escape_string($conn, $_POST['email']);
    $state       = mysqli_real_escape_string($conn, $_POST['state']);

    // packages
    $packs = [];
    for($i=1; $i<=9; $i++){
        $packs[$i] = isset($_POST["pack$i"]) ? $_POST["pack$i"] : '';
    }

    // Picture upload
    $file_name = "";
    if (!empty($_FILES["pix"]["name"])) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_name = time() . "_" . basename($_FILES["pix"]["name"]);
        $target_file = $target_dir . $file_name;
        move_uploaded_file($_FILES["pix"]["tmp_name"], $target_file);
    }

    // Save to DB
    $sql = "INSERT INTO registrations 
        (firstname, middlename, lastname, address, phone, gender, dob, email, state, picture,
         pack1, pack2, pack3, pack4, pack5, pack6, pack7, pack8, pack9) 
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssssssssss",
        $firstname, $middlename, $lastname, $address, $phone, $gender, $dob, $email, $state, $file_name,
        $packs[1], $packs[2], $packs[3], $packs[4], $packs[5], $packs[6], $packs[7], $packs[8], $packs[9]
    );

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "✅ Registration successful!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "❌ Database error: " . $conn->error]);
    }
    exit;
}

// Fetch packages from database
$packageOptions = "";
$pkgQuery = $conn->query("SELECT id, package_name FROM packages ORDER BY package_name ASC");
if ($pkgQuery && $pkgQuery->num_rows > 0) {
    while ($row = $pkgQuery->fetch_assoc()) {
        $packageOptions .= "<option value='{$row['package_name']}'>{$row['package_name']}</option>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Register - Vinefresh ICT</title>
  <link rel="stylesheet" href="assets/css/register.css">
  <link rel="stylesheet" type="text/css" href="assets/css/style.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    td { padding: 8px; vertical-align: top; }
    .passport { text-align: center; padding: 10px; }
    .passport img { width: 120px; height: 140px; border: 1px solid #999; margin-bottom: 5px; }
    input, select { width: 95%; padding: 6px; }
    h3 { background: #0077cc; color: #fff; padding: 5px; }
    .form-actions { text-align: center; margin-top: 15px; }
    button { padding: 8px 20px; margin: 5px; cursor: pointer; }
    #response { margin-top: 15px; text-align: center; }
  </style>
</head>
<body>
<header>
  <a href="index.php">
    <img src="assets/images/vine2.png" alt="Vinefresh Logo" class="logo">
  </a>
  <nav>
    <ul>
      <li><a href="index.php">Home</a></li>
      <li class="current"><a href="services.php">Services</a></li>
      <li><a href="instructors.php">Our Instructors</a></li>
      <li><a href="register.php">Register</a></li>
      <li><a href="contact.php">Contact Us</a></li>
    </ul>
  </nav>
</header>

<div class="container">
  <h2>VINEFRESHBOOKSHOP ICT CENTER ACADEMY</h2>
  <p class="motto">MOTTO: The Lord is our Strength</p>

  <form id="regForm" enctype="multipart/form-data">
    <table border="0">
      <tr>
        <td class="passport" rowspan="4">
          <img src="img/pic.jpg" class="preview" id="previewImg">
          <br>
          <input type="file" name="pix" id="pix" accept="image/*" required>
        </td>
        <td><input type="text" name="firstname" placeholder="First Name" required></td>
        <td><input type="text" name="middlename" placeholder="Middle Name"></td>
        <td><input type="text" name="lastname" placeholder="Last Name" required></td>
      </tr>
      <tr>
        <td colspan="3"><input type="text" name="address" placeholder="Address" required></td>
      </tr>
      <tr>
        <td><input type="text" name="phone" placeholder="Phone (11 digits)" maxlength="11" required></td>
        <td>
          <select name="gender" required>
            <option value="">--Select Gender--</option>
            <option>MALE</option>
            <option>FEMALE</option>
          </select>
        </td>
        <td>
          <label>DOB:</label><br>
          <select name="day"><?php for($i=1;$i<=31;$i++) echo "<option>$i</option>"; ?></select>
          <select name="month"><?php for($i=1;$i<=12;$i++) echo "<option>$i</option>"; ?></select>
          <select name="year"><?php for($i=1980;$i<=date("Y");$i++) echo "<option>$i</option>"; ?></select>
        </td>
      </tr>
      <tr>
        <td colspan="2"><input type="email" name="email" placeholder="Email" required></td>
        <td>
          <select name="state" required>
            <option value="">--Select State of Origin--</option>
            <option>Non - Nigerian</option>
            <?php
            $states = ["Abia","Abuja","Adamawa","Akwa Ibom","Anambra","Bauchi","Bayelsa","Benue","Borno","Cross River",
                       "Delta","Ebonyi","Edo","Ekiti","Enugu","Gombe","Imo","Jigawa","Kaduna","Kano","Katsina",
                       "Kebbi","Kogi","Kwara","Lagos","Nassarawa","Niger","Ogun","Ondo","Osun","Oyo","Plateau",
                       "Rivers","Sokoto","Taraba","Yobe","Zamfara"];
            foreach($states as $s) echo "<option>$s</option>";
            ?>
          </select>
        </td>
      </tr>
    </table>

    <h3>Programs (Packages)</h3>
    <table border="0">
      <?php for ($i=1; $i<=9; $i++): ?>
        <tr>
          <td>Package <?= $i ?>:</td>
          <td>
            <select name="pack<?= $i ?>">
              <option value="">-- Select Package --</option>
              <?= $packageOptions ?>
            </select>
          </td>
        </tr>
      <?php endfor; ?>
    </table>

    <input type="hidden" name="ajax" value="1">

    <div class="form-actions">
      <button type="submit">Submit</button>
      <button type="reset">Reset</button>
    </div>
  </form>

  <div id="response"></div>
</div>

<script>
// Preview uploaded image
$("#pix").on("change", function(){
  let reader = new FileReader();
  reader.onload = function(e){ $("#previewImg").attr("src", e.target.result); }
  reader.readAsDataURL(this.files[0]);
});

// AJAX Form Submission
$("#regForm").on("submit", function(e){
  e.preventDefault();
  let formData = new FormData(this);

  $.ajax({
    url: "register.php",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    beforeSend: function(){
      $("#response").html("<p style='color:blue;'>Submitting...</p>");
    },
    success: function(data){
      let res = JSON.parse(data);
      if(res.status == "success"){
        $("#response").html("<p style='color:green; font-weight:bold;'>" + res.message + "</p>");
        $("#regForm")[0].reset();
        $("#previewImg").attr("src","img/pic.jpg");
      } else {
        $("#response").html("<p style='color:red; font-weight:bold;'>" + res.message + "</p>");
      }
    }
  });
});
</script>

</body>
</html>
