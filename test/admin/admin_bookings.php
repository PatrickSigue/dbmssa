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
    $id = $_POST['id'];

    $stmt = $pdo->prepare("UPDATE bookings SET status = 'Complete' WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

// Handle form submission for pending an order
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Pending'])) {
    $id = $_POST['id'];

    $stmt = $pdo->prepare("UPDATE bookings SET status = 'Pending' WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

// Handle form submission for cancelling an order
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Cancelled'])) {
    $id = $_POST['id'];

    $stmt = $pdo->prepare("UPDATE bookings SET status = 'Cancelled' WHERE id = :id");
    $stmt->execute(['id' => $id]);
}



$stmt = $pdo->query("SELECT bookings.*, users.username, service.SNAME FROM bookings JOIN users ON bookings.user_id = users.UID JOIN service ON bookings.S_ID = service.S_ID WHERE bookings.status = 'pending'");
$pendingAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT bookings.*, users.username, service.SNAME FROM bookings JOIN users ON bookings.user_id = users.UID JOIN service ON bookings.S_ID = service.S_ID WHERE bookings.status = 'cancelled'");
$cancelledAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT bookings.*, users.username, service.SNAME FROM bookings JOIN users ON bookings.user_id = users.UID JOIN service ON bookings.S_ID = service.S_ID WHERE bookings.status = 'complete'");
$completedAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/test/css/styles2.css">
    <title>Sir Chief's Shop - Appointment</title>
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
                <h1>Appointments</h1>
                <div class="user">
                    <?php if (isset($_SESSION['username'])): ?>
                    <li><h3>Hello, Admin!</h3></li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <li><a href="/test/php/logout_admin.php" id="nav">LOG OUT</a></li>
            <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="content">
            <h1>Sir Chief's Shop - Appointments</h1>
    <h2>Pending Appointments</h2>
    &nbsp;
    <table class="table-fixed" border="1">
        <tr>
            <th>User</th>
            <th>Appointment Date</th>
            <th>Service</th>
            <th>Make/Model</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach ($pendingAppointments as $bookings): ?>
        <tr>
            <td><?php echo $bookings['username']; ?></td>
            <td><?php echo htmlspecialchars($bookings['date']); ?></td>
            <td><?php echo htmlspecialchars($bookings['SNAME']); ?></td>
            <td><?php echo htmlspecialchars($bookings['make_model']); ?></td>
            <td><?php echo htmlspecialchars($bookings['status']); ?></td>
            <td>
                <form method="POST" action="" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $bookings['id']; ?>">
                    <input type="submit" name="Cancelled" value="Cancel" onclick="return confirm('Cancel appointment?');">
                </form>
                <form method="POST" action="" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $bookings['id']; ?>">
                    <input type="submit" name="Complete" value="Complete" onclick="return confirm('Complete appointment?');">
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h2>Completed Appointments</h2>
    &nbsp;
    <table class="table-fixed" border="1">
        <tr>
            <th>User</th>
            <th>Appointment Date</th>
            <th>Service</th>
            <th>Make/Model</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach ($completedAppointments as $bookings): ?>
        <tr>
            <td><?php echo $bookings['username']; ?></td>
            <td><?php echo htmlspecialchars($bookings['date']); ?></td>
            <td><?php echo htmlspecialchars($bookings['SNAME']); ?></td>
            <td><?php echo htmlspecialchars($bookings['make_model']); ?></td>
            <td><?php echo htmlspecialchars($bookings['status']); ?></td>
            <td>
                <form method="POST" action="" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $bookings['id']; ?>">
                    <input type="submit" name="Cancelled" value="Cancel" onclick="return confirm('Cancel appointment?');">
                </form>
                <form method="POST" action="" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $bookings['id']; ?>">
                    <input type="submit" name="Pending" value="Set as pending" onclick="return confirm('Set as pending?');">
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h2>Cancelled Appointments</h2>
    &nbsp;
    <table class="table-fixed" border="1">
        <tr>
            <th>User</th>
            <th>Appointment Date</th>
            <th>Service</th>
            <th>Make/Model</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach ($cancelledAppointments as $bookings): ?>
        <tr>
            <td><?php echo $bookings['username']; ?></td>
            <td><?php echo htmlspecialchars($bookings['date']); ?></td>
            <td><?php echo htmlspecialchars($bookings['SNAME']); ?></td>
            <td><?php echo htmlspecialchars($bookings['make_model']); ?></td>
            <td><?php echo htmlspecialchars($bookings['status']); ?></td>
            <td>
                <form method="POST" action="" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $bookings['id']; ?>">
                    <input type="submit" name="Complete" value="Complete" onclick="return confirm('Complete appointment?');">
                </form>
                <form method="POST" action="" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $bookings['id']; ?>">
                    <input type="submit" name="Pending" value="Set as pending" onclick="return confirm('Set as pending?');">
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
</body>
</html>
