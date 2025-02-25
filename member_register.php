<?php
include('connection.php');
session_start();

require 'vendor/autoload.php'; // Include the Composer autoloader

use PragmaRX\Google2FAQRCode\Google2FA;
//use Google2FAQRCode; 
use BaconQrCode\Renderer\Image\Png;
use BaconQrCode\Writer;

//use PragmaRX\Google2FA\Google2FA;

$google2fa = new Google2FA();

// Function to sanitize input data
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to check password strength
function checkPasswordStrength($password) {
    // Minimum password length
    $minLength = 8;

    // Check if password meets the minimum length requirement
    if (strlen($password) < $minLength) {
        return false;
    }

    // Check if password contains at least one uppercase letter, one lowercase letter, and one digit
    if (!preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password) || !preg_match("/\d/", $password)) {
        return false;
    }

    return true;
}

$fullname = sanitize($_POST['fullname']);
$username = sanitize($_POST['username']);
$email = sanitize($_POST['email']);
$password = $_POST['password'];

// Validate input fields
if (empty($fullname) || empty($username) || empty($email) || empty($password)) {
    // Input fields are empty
    ?>
    <div class="alert alert-danger">
        <strong>Error!</strong> Please fill in all the required fields.
    </div>
    <?php
    exit; // Stop further execution
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // Invalid email format
    ?>
    <div class="alert alert-danger">
        <strong>Error!</strong> Invalid email format.
    </div>
    <?php
    exit; // Stop further execution
}

// Check password strength
if (!checkPasswordStrength($password)) {
    // Weak password
    ?>
    <div class="alert alert-danger">
        <strong>Error!</strong> Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one digit.
    </div>
    <?php
    exit; // Stop further execution
}

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Google 2FA
$two_factor_code = $google2fa->generateSecretKey();

// Generate the inline URL for the QR code
$inlineUrl = $google2fa->getQRCodeUrl(
    $fullname,
    $email,
    $two_factor_code
);

// Output the QR code image
header('Content-Type: image/png');
echo $qrCodeImage;

// Prepare the statement
$insert = $connection->prepare("INSERT INTO member(name, username, password, email, two_factor_code, usertype) VALUES(?, ?, ?, ?, ?, 'user')");

// Bind the parameters
$insert->bind_param("sssss", $fullname, $username, $hashedPassword, $email, $two_factor_code);
//print('Problem');

if ($insert->execute()) {
     // Redirect to confirm.php and pass necessary data as query parameters
    $confirmUrl = "confirm.php?fullname=" . urlencode($fullname) . "&email=" . urlencode($email) . "&twofactorcode=" . urlencode($two_factor_code) . "&inlineurl=" . urlencode($inlineUrl);
    header("Location: $confirmUrl");
    exit;

} else {
    // Registration failed
    ?>
    <div class="alert alert-danger">
        <strong>Error!</strong> Registration failed. Please try again.
    </div>
    <?php
}

$insert->close();
$connection->close();
?>
