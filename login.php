<?php
include('connection.php');
session_start();

// Function to sanitize input data
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$username = sanitize($_POST['username']);
$password = $_POST['password'];

// Validate input fields
if (empty($username) || empty($password)) {
    // Input fields are empty
    $_SESSION['error'] = 'Please enter your username and password.';
    header('location: index.php');
    exit;
}

$login = $connection->prepare("SELECT * FROM member WHERE username = ?");
$login->bind_param("s", $username);
$login->execute();
$result = $login->get_result();
$fetch = $result->fetch_assoc();

if ($result->num_rows == 1) {
    $hashedPassword = $fetch['password'];

    if (password_verify($password, $hashedPassword)) {
        // Passwords match
        if ($fetch['usertype'] == 'admin') {
            $_SESSION['member_id'] = $fetch['member_id'];
            $_SESSION['username'] = $fetch['username'];
            header('location: admin_dasboard/admin_dashboard.php');
            exit();
        } elseif ($fetch['usertype'] == 'user') {
            $_SESSION['userid'] = $fetch['member_id'];
            $_SESSION['membername'] = $fetch['username'];
            header('location: user_dashboard/user_dashboard.php');
            exit();
        }
    } else {
        $_SESSION['error'] = 'Invalid username or password.';
        header('location: index.php');
        exit();
    }
} else {
    $_SESSION['error'] = 'Invalid username or password.';
    header('location: index.php');
    exit();
}

$login->close();
$connection->close();
?>
