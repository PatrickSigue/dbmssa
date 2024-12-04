<?php
// Database connection
$host = 'localhost';
$db = 'shop';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start(); // Start the session

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password']; // Keep password as plain string (consider hashing for security)
    $address = isset($_POST['address']) ? $_POST['address'] : null;
    $contact_number = isset($_POST['contact_number']) ? $_POST['contact_number'] : null;

    // Check if email or username already exists
    $check_stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $check_stmt->bind_param("ss", $email, $username);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // Set error message
        $_SESSION['error_message'] = "Email or Username already exists.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, username, password, address, contact_number) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $first_name, $last_name, $email, $username, $password, $address, $contact_number);
        if ($stmt->execute()) {
            // Set success message
            $_SESSION['registration_success'] = "Registration successful! You can now log in.";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        } else {
            // Set error message
            $_SESSION['error_message'] = "Error: " . $stmt->error;
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }
}
$conn->close();
?>
