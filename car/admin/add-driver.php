<?php
  include '../partials/aheader.php';
  session_start();
if(!isset($_SESSION['admin_ID'])){
  header('location: '. ROOT_URL .'admin/login.php');
  exit();
}

// Function to sanitize input data
function sanitizeInput($data) {
  $data = trim($data); 
  $data = stripslashes($data); 
  $data = htmlspecialchars($data); 
  return $data;
}

define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB max file size
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Validate and sanitize inputs
  $dFname = sanitizeInput($_POST['_dFname']);
  $dLname = sanitizeInput($_POST['_dLname']);
  $dPhoneNo = sanitizeInput($_POST['_dPhoneNo']);
  $dEmail = filter_var($_POST['_dEmail'], FILTER_SANITIZE_EMAIL); // Sanitize email
  $dPassword = password_hash($_POST['_dPassword'], PASSWORD_DEFAULT); // Hash password

  if (isset($_FILES['_dLicensePhoto'])) {
    $dLicensePhoto = $_FILES['_dLicensePhoto'];

    // Check for errors
    if ($dLicensePhoto['error'] !== UPLOAD_ERR_OK) {
        // Handle file upload errors
        die("File upload failed with error code " . $dLicensePhoto['error']);
    }

    // Validate file size
    if ($dLicensePhoto['size'] > MAX_FILE_SIZE) {
        // Handle file size exceeded error
        die("File size exceeds the limit.");
    }

    // Validate file type
    $fileInfo = pathinfo($dLicensePhoto['name']);
    $fileExtension = strtolower($fileInfo['extension']);
    if (!in_array($fileExtension, ALLOWED_EXTENSIONS)) {
        // Handle invalid file type error
        die("Invalid file type.");
    }

    // Move uploaded file to destination
    $uploadDir = '/path/to/upload/directory/';
    $uploadFile = $uploadDir . basename($dLicensePhoto['name']);
    if (!move_uploaded_file($dLicensePhoto['tmp_name'], $uploadFile)) {
        // Handle file upload failure
        die("Failed to move uploaded file.");
    }
}
}
?>

<section class="form_section">
  <div class="container form_section-container">
    <h2>Add Driverr</h2>
    <form action="driver-functions.php" method="POST" enctype="multipart/form-data">  
            <input type="text" name="_dFname" placeholder="First Name"    class="form-control" required>
            <input type="text" name="_dLname" placeholder="Last Name" class="form-control" required>
            <input type="number" name="_dPhoneNo" placeholder="Phone Number" class="form-control" required>
            <input type="email" name="_dEmail" placeholder="Email" class="form-control" required>
            <input type="password" name="_dPassword" placeholder="Password" class="form-control" required>
            <input type="file" name="_dLicensePhoto" placeholder="License" accept="image/*" class="form-control">
            <button type="submit" name="submit" class="btn">Add Driver</button>
            <a href="drivers-info.php" class="btn sm danger">Back</a>
        </form>
  </div>
</section>

<?php
  include '../partials/footer.php';
?>