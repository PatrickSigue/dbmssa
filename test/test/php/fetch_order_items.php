<?php
$host = 'localhost'; // or your host
$db = 'shop'; // your database name
$user = 'root'; // your database username
$pass = ''; // your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

$order_id = $_GET['order_id'];

// Update the SQL query to filter by order_id
$stmt = $pdo->prepare("SELECT order_items.order_id, products.name AS product_name, product_sizes.size, order_items.quantity, order_items.price 
                        FROM order_items 
                        JOIN products ON order_items.product_id = products.PID 
                        JOIN product_sizes ON order_items.size_id = product_sizes.id 
                        WHERE order_items.order_id = :order_id 
                        ORDER BY order_items.order_item_id;");
$stmt->execute(['order_id' => $order_id]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Wrap the order items in an object with an 'items' property
$response = ['items' => $order_items];

echo json_encode($response);
?>