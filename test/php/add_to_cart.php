<?php
// Database connection setup
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

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Get the POST data
    $product_id = $_POST['product_id'];
    $size_id = $_POST['size_id'];
    $quantity = intval($_POST['quantity']);

    // Validate inputs
    if ($product_id > 0 && $size_id > 0 && $quantity > 0) {
        
        // Fetch the size details (price and available quantity)
        $size_stmt = $conn->prepare("SELECT price, quantity FROM Product_Sizes WHERE id = ?");
        $size_stmt->bind_param("i", $size_id);
        $size_stmt->execute();
        $size_result = $size_stmt->get_result();
        
        if ($size_result->num_rows > 0) {
            $size_row = $size_result->fetch_assoc();
            $price = $size_row['price'];
            $available_stock = $size_row['quantity'];

            // Check if the requested quantity is available
            if ($quantity <= $available_stock) {

                // Get the user_id from session
                $user_id = $_SESSION['user_id'];
                
                // Check if the product already exists in the cart for this user
                $cart_stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ? AND size_id = ?");
                $cart_stmt->bind_param("iii", $user_id, $product_id, $size_id);
                $cart_stmt->execute();
                $cart_result = $cart_stmt->get_result();
                
                if ($cart_result->num_rows > 0) {
                    // Product already in cart, update the quantity
                    $cart_row = $cart_result->fetch_assoc();
                    $new_quantity = $cart_row['quantity'] + $quantity;

                    // Update cart with new quantity
                    $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ? AND size_id = ?");
                    $update_stmt->bind_param("iiii", $new_quantity, $user_id, $product_id, $size_id);
                    if ($update_stmt->execute()) {
                        echo "Product quantity updated in cart!";
                    } else {
                        echo "Error updating cart: " . $update_stmt->error;
                    }
                } else {
                    // Product not in cart, insert into cart
                    $insert_stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, size_id, quantity, price) VALUES (?, ?, ?, ?, ?)");
                    $insert_stmt->bind_param("iiidi", $user_id, $product_id, $size_id, $quantity, $price);
                    if ($insert_stmt->execute()) {
                        echo "Product added to cart!";
                    } else {
                        echo "Error adding product to cart: " . $insert_stmt->error;
                    }
                }
            } else {
                echo "The requested quantity exceeds the available stock. Only $available_stock left.";
            }
        } else {
            echo "Selected size not available.";
        }
    } else {
        echo "Please provide valid input data.";
    }
} else {
    echo "You need to be logged in to add items to your cart.";
}

$conn->close();
?>