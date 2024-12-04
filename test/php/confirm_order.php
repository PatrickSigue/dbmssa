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

// Get data from the form
$user_id = $_SESSION['user_id'];
$contact_number = $_POST['contact_number'];
$address = $_POST['address'];
$total_amount = $_POST['total_amount'];

// Insert order into the orders table
$order_stmt = $conn->prepare("INSERT INTO orders (user_id, contact_number, address, total_amount) VALUES (?, ?, ?, ?)");
$order_stmt->bind_param("issd", $user_id, $contact_number, $address, $total_amount);
$order_stmt->execute();

// Get the last inserted order ID
$order_id = $conn->insert_id;

// Fetch the cart items to add them to the order_items table
$cart_stmt = $conn->prepare("SELECT c.product_id, c.size_id, c.quantity, c.price FROM cart c WHERE c.user_id = ?");
$cart_stmt->bind_param("i", $user_id);
$cart_stmt->execute();
$cart_result = $cart_stmt->get_result();

while ($item = $cart_result->fetch_assoc()) {
    $product_id = $item['product_id'];
    $size_id = $item['size_id'];
    $quantity = $item['quantity'];
    $price = $item['price'];
    $total = $quantity * $price;

    // Insert each cart item into the order_items table
    $order_item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, size_id, quantity, price, total) VALUES (?, ?, ?, ?, ?, ?)");
    $order_item_stmt->bind_param("iiidid", $order_id, $product_id, $size_id, $quantity, $price, $total);
    $order_item_stmt->execute();

     // Update the stock in the product_sizes table by subtracting the quantity ordered
    $update_stock_stmt = $conn->prepare("UPDATE Product_Sizes SET quantity = quantity - ? WHERE product_id = ? AND id = ?");
    $update_stock_stmt->bind_param("iii", $quantity, $product_id, $size_id);
    $update_stock_stmt->execute();
}

$clear_cart_stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
$clear_cart_stmt->bind_param("i", $user_id);
$clear_cart_stmt->execute();

// Return success message
echo "Your order has been placed successfully!";
?>
