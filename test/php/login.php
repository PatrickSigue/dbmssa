<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

// Database connection
$host = 'localhost';
$db = 'shop';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute the query to find the user
    $stmt = $conn->prepare("SELECT UID, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password
        if ($password === $user['password']) {
            // Success: Set session variables
            $_SESSION['user_id'] = $user['UID'];
            $_SESSION['username'] = $username;

            if ($user['UID'] === 1){
                header("Location: /test/admin/dashboard_admin.php");
                exit();
            }
            else{
                // Redirect after login
                $redirect_to = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : 'mainpage.php';
                header("Location: $redirect_to");
                exit();
            }
        } else {
            // Invalid password
            $_SESSION['error_message'] = "Invalid username or password.";
            $redirect_to = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : 'mainpage.php';
            header("Location: $redirect_to");
        }
    } else {
        // User not found
        $_SESSION['error_message'] = "Invalid username or password.";
        $redirect_to = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : 'mainpage.php';
            header("Location: $redirect_to");
    }

    $stmt->close();
}

$conn->close();
?>
