<?php
session_start();
$host = 'localhost'; // Your database host
$db = 'shop'; // Your database name
$user = 'root'; // Your database username
$pass = ''; // Your database password

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Get form data
    $contact = $_POST['contact'] ?? null;
    $address = $_POST['address'] ?? null;

    // Update query
    $sql = "UPDATE users SET contact_number = ?, address = ? WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $contact, $address, $username);

    if ($stmt->execute()) {
        header("Location: myacc.php?update=success");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "You must be logged in to update your details.";
}

$conn->close();
?>
