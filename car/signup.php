<?php
session_start();
if(isset($_SESSION['customer_ID'])){
  header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Car Rental Website</title>

  <!--Style-->
  <link rel="stylesheet" href="css/style.css">
    <!--Icon-->
  <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
  <!--FONTS-->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;800&family=Playfair:wght@300&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

</head>
<body>

<section class="form_section">
  <div class="container form_section-container">
    <h2>Sign Up</h2>
     <!--<div class="alert_message error">
    <p>Error Message</p>
  </div>-->
  <?php
  $errors = []; // Define $errors array
  if (count($errors) > 0): // Check if $errors is not empty
  ?>
      <div class="alert_message error">
        <ul>
          <?php foreach ($errors as $error): ?>
            <li><?php echo $error; ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

  <form class="signup" action="signup.php" method="POST" enctype="multipart/form-data">
    <input type="text" name="fname" placeholder="First Name">
    <input type="text" name="lname" placeholder="Last Name">
    <label for="DOB" style="color: var(--color-white);">Birth Date</label>
    <input type="date" name="birthdate" id="DOB">
    <input type="tel" name="contactNo" placeholder="Contact Number">
    <input type="email" name="email" placeholder="Email">
    <input type="password" name="password" placeholder="Create Password">
    <input type="password" name="cpassword" placeholder="Confirm Password">

    <button type="submit" name="sign-up-btn" class="btn">Sign Up</button>
    <small>Already have an account? <a href="signin.php">Sign In</a></small>
  </form>
</div>
</section>

</body>
</html>

<?php
require_once 'config/database.php';
$errors = [];

//signup data

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $birthdate = $_POST['birthdate'];
    $contactNo = trim($_POST['contactNo']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

//Input validation

  if (empty($fname) || empty($lname) || empty($birthdate) || empty($contactNo) || empty($email) || empty($password) || empty($cpassword)) {
    $errors[] = "All fields are required";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $errors[] = "Invalid email format";
}

if (strlen($password) < 8) {
  $errors[] = "Password must be at least 8 characters long";
}

if (!preg_match("/[A-Z]/", $password)) {
  $errors[] = "Password must contain at least one uppercase letter";
}

if (!preg_match("/[0-9\W]/", $password)) {
  $errors[] = "Password must contain at least one symbol or number";
}

if ($password !== $cpassword) {
  $errors[] = "Passwords do not match";
}

$birthdate_timestamp = strtotime($birthdate);
$eighteen_years_ago = strtotime('-18 years');

if ($birthdate_timestamp > $eighteen_years_ago) {
  $errors[] = "Invalid age: You must be 18 years or older to sign up";
}

// Email already exists
$sql = "SELECT * FROM customer WHERE cEmail = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $errors[] = "Email already exists";
  }

 // Proper error handling
if (count($errors) === 0) {
  $passwordHash = password_hash($password, PASSWORD_DEFAULT); // Secure coding
  $sql = "CALL getCustomerInfo(?, ?, ?, ?, ?, ?)"; // Defensive coding to prevent SQL injection
  $stmt = $connection->prepare($sql);
  $stmt->bind_param("ssssss", $fname, $lname, $birthdate, $contactNo, $email, $passwordHash);
  
  if ($stmt->execute()) {
      header('Location: signin.php');
      exit();
  }else {
    $errors[] = "Something went wrong";
}
}
}
?>
