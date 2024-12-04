<?php
$host = 'localhost';
$db = 'shop';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

// Check if the user is logged in and the product ID is set
if (isset($_SESSION['user_id'], $_POST['cid'])) {
    $user_id = $_SESSION['user_id'];
    $cid = $_POST['cid'];

    // Prepare and execute the query to delete the product from the cart
    $stmt = $conn->prepare("DELETE FROM cart WHERE cid = ? AND user_id = ?");
    $stmt->bind_param("ii", $cid, $user_id);
    
    if ($stmt->execute()) {
        echo "Item removed successfully.";
    } else {
        echo "Error removing item.";
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
