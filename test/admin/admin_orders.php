<?php

session_start();

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

// Handle form submission for completing an order
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Complete'])) {
    $order_id = $_POST['order_id'];

    $stmt = $pdo->prepare("UPDATE orders SET status = 'Complete' WHERE order_id = :order_id");
    $stmt->execute(['order_id' => $order_id]);
}

// Handle form submission for pending an order
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Pending'])) {
    $order_id = $_POST['order_id'];

    $stmt = $pdo->prepare("UPDATE orders SET status = 'Pending' WHERE order_id = :order_id");
    $stmt->execute(['order_id' => $order_id]);
}

// Handle form submission for cancelling an order
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Cancelled'])) {
    $order_id = $_POST['order_id'];

    $stmt = $pdo->prepare("UPDATE orders SET status = 'Cancelled' WHERE order_id = :order_id");
    $stmt->execute(['order_id' => $order_id]);
}

// Fetch completed orders with usernames
$stmt = $pdo->query("SELECT orders.*, users.username FROM orders JOIN users ON orders.user_id = users.UID WHERE orders.status = 'complete'");
$completedOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch pending orders with usernames
$stmt = $pdo->query("SELECT orders.*, users.username FROM orders JOIN users ON orders.user_id = users.UID WHERE orders.status = 'pending'");
$pendingOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch cancelled orders with usernames
$stmt = $pdo->query("SELECT orders.*, users.username FROM orders JOIN users ON orders.user_id = users.UID WHERE orders.status = 'cancelled'");
$cancelledOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/test/css/styles2.css">
    <title>Sir Chief's Shop - Orders</title>
</head>
<body>

    <div class="side-menu">
        <div class="brand-name">
            <h1 style="color: white;">Sir Chief's Shop</h1>
        </div>
    <ul>
        <li><span><a href="dashboard_admin.php#"> Dashboard </a></span> </li>
        <li><span><a href="admin_inventory.php#">Inventory</a></span> </li>
        <li><span><a href="admin_bookings.php#">Appointments</a></span> </li>
        <li><span><a href="admin_orders.php#">Orders</a></span> </li> 
    </ul>
    </div>

    <div class="container">
        <div class="header">
            <div class="nav">
                <h1>Orders</h1>
                <div class="user">
                    <?php if (isset($_SESSION['username'])): ?>
                    <li><h3>Hello, Admin!</h3></li> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <li><a href="/test/php/logout_admin.php" id="nav">LOG OUT</a></li>
            <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="content">
            <h1>Sir Chief's Shop - Orders</h1>

    <h2>Pending Orders</h2>
    &nbsp;
    <table class="table-fixed" border="1">
        <tr>
            <th>User</th>
            <th>Contact Number</th>
            <th>Address</th>
            <th>Amount</th>
            <th>Order Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php foreach ($pendingOrders as $order): ?>

        <tr>
            <td><?php echo htmlspecialchars($order['username']); ?></td>
            <td><?php echo htmlspecialchars($order['contact_number']); ?></td>
            <td><?php echo htmlspecialchars($order['address']); ?></td>
            <td>₱<?php echo htmlspecialchars($order['total_amount']); ?></td>
            <td><?php echo htmlspecialchars($order['order_date']); ?></td>
            <td><?php echo htmlspecialchars($order['status']); ?></td>
            <td>
                <button class="orderBtn" data-order-id="<?php echo htmlspecialchars($order['order_id']); ?>">View</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h2>Completed Orders</h2>
    &nbsp;
    <table class="table-fixed" border="1">
        <tr>
            <th>User</th>
            <th>Contact Number</th>
            <th>Address</th>
            <th>Amount</th>
            <th>Order Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php foreach ($completedOrders as $completedOrder): ?>

        <tr>
            <td><?php echo htmlspecialchars($completedOrder['username']); ?></td>
            <td><?php echo htmlspecialchars($completedOrder['contact_number']); ?></td>
            <td><?php echo htmlspecialchars($completedOrder['address']); ?></td>
            <td>₱<?php echo htmlspecialchars($completedOrder['total_amount']); ?></td>
            <td><?php echo htmlspecialchars($completedOrder['order_date']); ?></td>
            <td><?php echo htmlspecialchars($completedOrder['status']); ?></td>
            <td>
                <form method="POST" action="" style="display:inline;">
                    <input type="hidden" name="order_id" value="<?php echo $completedOrder['order_id']; ?>">
                    <input type="submit" name="Pending" value="Set Order to Pending" onclick="return confirm('Set order to Pending?');">
                </form>
                <form method="POST" action="" style="display:inline;">
                    <input type="hidden" name="order_id" value="<?php echo $completedOrder['order_id']; ?>">
                    <input type="submit" name="Cancelled" value="Cancel Order" onclick="return confirm('Cancel this order?');">
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h2>Cancelled Orders</h2>
    &nbsp;
    <table class="table-fixed" border="1">
        <tr>
            <th>User</th>
            <th>Contact Number</th>
            <th>Address</th>
            <th>Amount</th>
            <th>Order Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php foreach ($cancelledOrders as $order): ?>

        <tr>
            <td><?php echo htmlspecialchars($order['username']); ?></td>
            <td><?php echo htmlspecialchars($order['contact_number']); ?></td>
            <td><?php echo htmlspecialchars($order['address']); ?></td>
            <td>₱<?php echo htmlspecialchars($order['total_amount']); ?></td>
            <td><?php echo htmlspecialchars($order['order_date']); ?></td>
            <td><?php echo htmlspecialchars($order['status']); ?></td>
            <td>
                <form method="POST" action="" style="display:inline;">
                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                    <input type="submit" name="Complete" value="Complete Order" onclick="return confirm('Are you sure you want to complete this order?');">
                </form>
                <form method="POST" action="" style="display:inline;">
                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                    <input type="submit" name="Pending" value="Set Order to Pending" onclick="return confirm('Set order to Pending?');">
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>    
<footer>
        <p>&copy; 2024 Sir Chief's Motorshop</p>
    </footer>
</div>
    <div class="modal" id="orderModal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close" id="closeOrderModal">&times;</span>
                <h2>Order Details</h2>
            </div>
            <div class="modal-body">
                <table id="orderItemsTable" width="100%">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Size</th>
                            <th>Quantity</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody id="orderItemsBody">
                        <!-- Order items will be populated here -->
                    </tbody>
                </table>
                <form method="POST" action="" style="display:inline;">
                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                    <input type="submit" name="Complete" value="Complete Order" onclick="return confirm('Are you sure you want to complete this order?');">
                </form>
                <form method="POST" action="" style="display:inline;">
                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                    <input type="submit" name="Cancelled" value="Cancel Order" onclick="return confirm('Cancel this order?');">
                </form>
            </div>
        </div>
    </div>

<script>
    var orderModal = document.getElementById('orderModal');
    document.querySelectorAll('.orderBtn').forEach(button => {
            button.onclick = function() {
                var orderId = this.getAttribute('data-order-id');
                fetchOrderDetails(orderId);
                orderModal.style.display = "block";
            }
        });
    closeOrderModal.onclick = function() {
            orderModal.style.display = "none";
        }
    window.onclick = function(event) {
        if (event.target == orderModal) {
            orderModal.style.display = "none";
        }
    }

    function fetchOrderDetails(orderId) {
            fetch('/test/php/fetch_order_items.php?order_id=' + orderId)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('orderItemsBody').innerHTML = '';
                    data.items.forEach(item => {
                        var row = `<tr>
                            <td>${item.product_name}</td>
                            <td>${item.size}</td>
                            <td>${item.quantity}</td>
                            <td>₱${item.price}</td>
                        </tr>`;
                        document.getElementById('orderItemsBody').innerHTML += row;
                    });
                })
                .catch(error => console.error('Error fetching order details:', error));
        }
</script>
</body>
</html>
