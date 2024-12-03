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

if (isset($_GET['appointment_id'])) {
    $appointmentId = $_GET['appointment_id'];
    
    // Fetch appointment details including make_model
    $stmt = $pdo->prepare("SELECT `id`, `make_model` FROM `bookings` WHERE `id` = :appointment_id;");
    $stmt->bindParam(':appointment_id', $appointmentId);
    $stmt->execute();
    
    $appointmentDetails = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode($appointmentDetails);
}
?>