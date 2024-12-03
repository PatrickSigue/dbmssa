<?php

session_start();

// Check if the user is logged in and has the right UID
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    // Redirect to login page or main page if user is not authorized
    header("Location: login.php"); // Change this to your login page
    exit();
}

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

// Fetch pending order count
$stmt = $pdo->query("SELECT COUNT(*) AS pendingcount FROM orders WHERE status = 'pending'");
$pendingorder = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch pending bookings count
$stmt = $pdo->query("SELECT COUNT(*) AS pendingcount FROM bookings WHERE status = 'pending'");
$bookingscount = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch orders with usernames
$stmt = $pdo->query("SELECT orders.*, users.username, SUM(order_items.price) AS totalprice 
                      FROM orders 
                      JOIN users ON orders.user_id = users.UID 
                      JOIN order_items ON order_items.order_id = orders.order_id 
                      GROUP BY orders.order_id, orders.user_id, orders.order_date, users.username 
                      ORDER BY orders.order_id;");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch appointments
$stmt = $pdo->query("SELECT bookings.id, users.username, service.SNAME, bookings.date, bookings.status FROM bookings JOIN users ON bookings.user_id = users.UID JOIN service ON bookings.S_ID = service.S_ID;");
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/test/css/styles.css">
    <title>Admin Panel</title>
</head>
<body>
    <div class="side-menu">
        <div class="brand-name">
            <h1 style="color: white;">Sir Chiefs Shop</h1>
        </div>
        <ul>
            <li><span><a href="dashboard_admin.php#"> Dashboard </a></span></li>
            <li><span><a href="admin_inventory.php#">Inventory</a></span></li>
            <li><span><a href="admin_bookings.php#">Appointments</a></span></li>
            <li><span><a href="admin_orders.php#">Orders</a></span></li>    
        </ul>
    </div>
    <div class="container">
        <div class="header">
            <div class="nav">
                <div class="user">
                    <?php if (isset($_SESSION['username'])): ?>
                    <li><a href="profile.php"><?php echo htmlspecialchars($_SESSION['username']); ?></a></li>
                    <li><a href="/test/php/logout_admin.php" id="nav">LOG OUT</a></li>
                <?php else: ?>
                    <li><a href="php/login.php" id="openModalBtn" id="nav"><img src="assets/loginicon.png" class="login-icon" alt="Login Icon">&nbsp;LOG IN</a></li>
            <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="cards">
                <div class="card">
                    <div class="box">
                        <h1 align="center"><?php echo $pendingorder['pendingcount']; ?></h1>
                        <h3>Pending Orders</h3>
                    </div>
                </div>
                <div class="card">
                    <div class="box">
                        <h1 align="center"><?php echo $bookingscount['pendingcount']; ?></h1>
                        <h3>Pending Appointments</h3>
                    </div>
                </div>
            </div>
            <div class="content-2">
                <div class="recent-orders">
                    <div class="title">
                        <h2>Recent Orders</h2>
                    </div>
                    <table>
                        <tr>
                            <th> Order ID</th>
                            <th>Name</th>
                            <th>Order Date</th>
                            <th>Address</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Option</th>
                        </tr>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                            <td><?php echo htmlspecialchars($order['username']); ?></td>
                            <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                            <td><?php echo htmlspecialchars($order['address']); ?></td>
                            <td>₱<?php echo htmlspecialchars($order['totalprice']); ?></td>
                            <td><?php echo htmlspecialchars($order['status']); ?></td>
                            <td><button class="orderBtn" data-order-id="<?php echo htmlspecialchars($order['order_id']); ?>">View</button></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
            <div class="content-2">
              <div class="recent-orders">
                    <div class="title">
                        <h2>Recent Appointments</h2>
                    </div>
                    <table>
                        <tr>
                            <th>Appointment ID</th>
                            <th>Username</th>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Option</th>
                        </tr>
                        <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['id']); ?></td>
                            <td><?php echo htmlspecialchars($booking['username']); ?></td>
                            <td><?php echo htmlspecialchars($booking['SNAME']); ?></td>
                            <td><?php echo htmlspecialchars($booking['date']); ?></td>
                            <td><?php echo htmlspecialchars($booking['status']); ?></td>
                            <td><button class="appointmentBtn" data-appointment-id="<?php echo htmlspecialchars($booking['id']); ?>">View</button></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
        <footer>
        <p>&copy; 2024 Sir Chief's Motorshop</p>
    </footer>
    </div>

    <!-- Modal Structure for Appointment Details -->
    <div class="modal" id="appointmentModal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close" id="closeAppointmentModal">&times;</span>
                <h2>Appointment Details</h2>
            </div>
            <div class="modal-body">
                <table id="appointmentDetailsTable" width="100%">
                    <tbody>
                        <tr style="display: none;">
                            <th>Appointment ID:</th>
                            <td id="appointmentId"></td>
                        </tr>
                        <tr>
                            <th>Make/Model:</th>
                            <td id="makeModel"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Structure for Order Details -->
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
            </div>
        </div>
    </div>

    <script>
        // Get the modals
        var appointmentModal = document.getElementById('appointmentModal');
        var orderModal = document.getElementById('orderModal');
        var closeAppointmentModal = document.getElementById('closeAppointmentModal');
        var closeOrderModal = document.getElementById('closeOrderModal');

        // When the user clicks on the appointment button, open the appointment modal
        document.querySelectorAll('.appointmentBtn').forEach(button => {
            button.onclick = function() {
                var appointmentId = this.getAttribute('data-appointment-id');
                fetchAppointmentDetails(appointmentId);
                appointmentModal.style.display = "block";
            }
        });

        // When the user clicks on the order button, open the order modal
        document.querySelectorAll('.orderBtn').forEach(button => {
            button.onclick = function() {
                var orderId = this.getAttribute('data-order-id');
                fetchOrderDetails(orderId);
                orderModal.style.display = "block";
            }
        });

        // Close modals
        closeAppointmentModal.onclick = function() {
            appointmentModal.style.display = "none";
        }

        closeOrderModal.onclick = function() {
            orderModal.style.display = "none";
        }

        // Close modals when clicking outside of them
        window.onclick = function(event) {
            if (event.target == appointmentModal) {
                appointmentModal.style.display = "none";
            }
            if (event.target == orderModal) {
                orderModal.style.display = "none";
            }
        }

        function fetchAppointmentDetails(appointmentId) {
            fetch('/test/php/fetch_appointment_details.php?appointment_id=' + appointmentId)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('appointmentId').innerText = data.id;
                    document.getElementById('makeModel').innerText = data.make_model;
                })
                .catch(error => console.error('Error fetching appointment details:', error));
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