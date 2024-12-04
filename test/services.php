<?php
session_start();

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "shop"; 

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error_message'] = 'You need to be logged in to book a service.';
        echo "<script>
                alert('" . addslashes($_SESSION['error_message']) . "');
                window.location.href = 'mainpage.php';
              </script>";
        exit; // Stop further execution if the user is not logged in
    }

        // Get the data from the POST request
        $service = $_POST['S_ID'] ?? '';
        $date = $_POST['date'] ?? '';
        $makeModel = $_POST['makemodel'] ?? '';
        $userId = $_SESSION['user_id']; // Get the user ID from session
        $status = 'Pending';

        // Validate inputs
        if (!empty($service) && !empty($date) && !empty($makeModel)) {
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, S_ID, date, make_model) VALUES (?, ?, ?, ?)");
        
        // Assuming $userId, $service, $date, and $makeModel are defined
        $stmt->bind_param("iiss", $userId, $service, $date, $makeModel); // 'i' for int, 's' for string

        // Execute the query
        if ($stmt->execute()) {
            echo "<script>alert('Booking added successfully.');</script>";
        } else {
            echo "<script>alert('Error: " . addslashes($stmt->error) . "');</script>";
        }


        $stmt->close();
    }
else {
        echo "<script>alert('Please fill in all fields.');</script>";
    }
}

// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sir Chief's Motorshop Services">
    <title>Services - Sir Chief's Motorshop</title>
    <link rel="icon" href="assets/SIR CHIEF’S (4).png" type="image/png">
    <link rel="stylesheet" type="text/css" href="css/genstyle.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<style>
    body {
            font-family: verdana, sans-serif;
            font-stretch: extra-expanded;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            color: #333;
        }

        header {
            font-family: verdana, sans-serif;
            background: #fff;
            color: #333;
            text-align: center;
        }

        nav ul {
            display: flex;
            align-items: center;
            justify-content: center;
            list-style-type: none;
            margin: 0;
            padding: 0;
        }

        nav li {
            margin-left: 35px;
            margin-right: 35px;
            margin-bottom: 5px;
            margin-top: 5px;
            align-items: center;
        }

        nav img.logo {
            width: 220px;
            height: auto;
        }

        nav a {
            text-decoration: none;
            color: black;
            font-size: 20px;
        }

        nav a:hover {
            color: indianred;
        }

        ul.login-nav {
        display: flex;
        align-items: center; /* Align items vertically */
        gap: 5px;
        margin: 0; /* Remove any extra margins */
        padding: 0;
        }

        nav .login-icon {
            width: 30px;
            height: 30px; /* Keep it square for uniformity */
            vertical-align: middle;
        }

        .login-nav a {
            display: flex;
            align-items: center;
            gap: 8px; /* Space between icon and text */
            text-decoration: none;
            color: black;
            font-size: 18px;
        }

        .login-nav a:hover {
            color: indianred;
        }


        section {
            padding: 20px;
            text-align: center;
        }

        .container {
            width: 80%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            margin-bottom: 10px;
            background-color: #BDBDBD;
        }


        footer {
            text-align: center;
            padding: 10px;
            background-color: #014235;
            color: white;
            font-family: Times, serif;
            font-size: 120%;
        }
        
        /* Service Styles */
        section {
            padding: 20px;
            text-align: center;
        }

        h2 {
            font-size: 2em;
            margin-bottom: 20px;
            color: #000000; /* A color that matches the theme */
        }

        .service-container {
            display: flex;
            flex-wrap: wrap; /* Allow items to wrap in smaller screens */
            justify-content: center; /* Center the items */
            gap: 20px; /* Space between service items */
        }

        .service-item {
            background-color: rgba(255, 255, 255, 0.8); /* Light background for contrast */
            border-radius: 10px;
            padding: 20px;
            width: 300px; /* Fixed width for service items */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2); /* Subtle shadow effect */
            transition: transform 0.3s; /* Smooth transition for hover effect */
            cursor: pointer; /* Change cursor to pointer */
        }

        .service-item:hover {
            transform: scale(1.05); /* Slightly enlarge on hover */
        }

        .service-item h3 {
            font-size: 1.5em;
            margin-bottom: 10px;
            color: #333; /* Dark text for headings */
        }

        .description {
            display: none; /* Initially hide the description */
            background-color: rgba(255, 255, 255, 0.9);
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            margin-top: 10px;
            transition: all 0.3s ease;
            color: #333;
        }

        /* Button Styles */
        .button {
            display: inline-block;
            background-color: #014235; /* Matching button color */
            color: white;
            padding: 10px 20px;
            border: none;s
            border-radius: 5px;
            font-size: 1.2em;
            text-decoration: none; /* Remove underline */
            transition: background-color 0.3s;
            margin-top: 20px; /* Smooth transition for hover effect */
        }

        .button:hover {
            background-color: #45a036; /* Darker shade on hover */
        }
        /* Modal Styles */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgba(0, 0, 0, 0.7); /* Black w/ opacity */
}

.modal-content {
    background-color: #ffffff;  
    margin: 10% auto; /* Center the modal */
    padding: 20px;
    border: none; /* No border */
    border-radius: 10px; /* Rounded corners */
    width: 80%; /* Could be more or less, depending on screen size */
    max-width: 400px; /* Max width for larger screens */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow effect */
    transition: all 0.3s ease; /* Smooth transition */
}

.close {
    color: #aaa; /* Gray color */
    float: right; /* Right align */
    font-size: 28px; /* Large font size */
    font-weight: bold; /* Bold */
}

.close:hover,
.close:focus {
    color: black; /* Change color on hover */
    text-decoration: none; /* No underline */
    cursor: pointer; /* Pointer cursor */
}

#bookingModal {
    z-index: 1001;  /* Ensure this is higher than any background or overlay */
}

/* Form Styles */
form {
    display: flex;
    flex-direction: column; /* Stack form elements vertically */
}

label {
    margin: 10px 0 5px; /* Spacing for labels */
}

input, select {
    padding: 10px; /* Padding for inputs */
    margin-bottom: 15px; /* Space between inputs */
    border: 1px solid #ccc; /* Border for inputs */
    border-radius: 5px; /* Rounded corners for inputs */
    font-size: 1em; /* Font size */
}

/* Button Styles */
button {
    background-color: #57b846; /* Button color */
    color: white; /* Text color */
    padding: 10px 20px; /* Padding */
    border: none; /* No border */
    border-radius: 5px; /* Rounded corners */
    font-size: 1em; /* Font size */
    cursor: pointer; /* Pointer cursor */
    transition: background-color 0.3s; /* Smooth transition */
}

button:hover {
    background-color: #45a036; /* Darker shade on hover */
}
label {
    margin: 10px 0 5px; /* Spacing for labels */
    color: #333; /* Dark color for better visibility */
}
    </style>
<body>
<header>
   <nav>
        <ul>
            <ul class="main-nav">
            <li><a href="mainpage.php"><img src="assets/SIR CHIEF’S (5).png" class="logo" alt="Logo"></a></li> <!-- Updated link -->
            <li><a href="aboutsir.php">ABOUT US</a></li>
            <li><a href="services.php">SERVICES</a></li>
            <li><a href="contact.php">CONTACT</a></li>
            <li><a href="incredbs.php">SHOP</a></li>
        </ul>
        <ul class="login-nav">
            <ul class="login-nav">
                <?php if (isset($_SESSION['username'])): ?>
                    <li><a href="myacc.php" style="text-transform: uppercase;"><?php echo "HI, " . htmlspecialchars($_SESSION['username']); ?></a></li>
                    <li><a href = "php/logout.php" style = "text-decoration: underline;">LOG OUT</a></li>
                <?php else: ?>
                    <li><a href="#" id="openLoginBtn"><img src="assets/loginicon.png" class="login-icon" alt="Login Icon">&nbsp;LOG IN</a></li>
                <?php endif; ?>
            </ul>
        </ul>
    </nav>
</header>

<section>
    <h2>Our Services</h2>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="error-message" style="color: red;">
            <?php
            echo htmlspecialchars($_SESSION['error_message']);
            unset($_SESSION['error_message']); // Clear the message after displaying
            ?>
        </div>
    <?php endif; ?>
    <div class="service-container">
        <div class="service-item" onclick="toggleDescription(this)">
	<img src= "assets/service/servicepreventivepic.png">
            <h3>Preventive Maintenance Service</h3>
            <p class="description">This service includes a comprehensive check of your vehicle's systems to ensure everything is functioning properly.</p>
        </div>
        <div class="service-item" onclick="toggleDescription(this)">
	<img src= "assets/service/servicecheckuppic.png">
            <h3>General Checkup</h3>
            <p class="description">This service includes a thorough inspection of your vehicle to identify any potential issues.</p>
        </div>
        <div class="service-item" onclick="toggleDescription(this)">
	<img src= "assets/service/serviceenginepic.png">
            <h3>Engine Overhaul</h3>
            <p class="description">This service involves a complete disassembly and inspection of the engine components.</p>
        </div>
        <div class="service-item" onclick="toggleDescription(this)">
	<img src= "assets/service/servicetirepic.png">
            <h3>Wheel Build/Tire Replacement</h3>
            <p class="description">This service includes replacing old tires and ensuring proper wheel alignment.</p>
        </div>
    </div>

    <a class="button" id="bookNowBtn">Book Now!</a>
</section>

<div id="bookingModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeBookingModal">&times;</span>
        <h2>Book Your Service</h2>
        <form id="bookingForm" method="POST" action="services.php">
            <label for="serviceSelect">Select Service:</label>
            <select id="serviceSelect" name="S_ID">
                <option value="1">Preventive Maintenance Service</option>
                <option value="2">General Checkup</option>
                <option value="3">Engine Overhaul</option>
                <option value="4">Wheel Build/Tire Replacement</option>
		<option value="5">Others</option>
            </select>
            <br><br>
            <label for="dateInput">Select Date:</label>
            <input type="date" id="dateInput" name="date" required>
            <br><br>
            <input type="text" id="MakeModelInput" name="makemodel" placeholder="Make and Model info here..." required> 
            <br><br>
            <button type="submit">Submit Booking</button>
        </form>
    </div>
</div>
    <!-- Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div class="form-modal">
                <div class="form-toggle">
                    <button id="login-toggle" onclick="toggleLogin()">Log In</button>
                    <button id="signup-toggle" onclick="toggleSignup()">Sign Up</button>
                </div>

                <div id="login-form">
                    <!-- Login Form -->
                    <form action="php/login.php" method="POST">
                        <input type="text" name="username" placeholder="Enter email or username" required />
                        <input type="password" name="password" placeholder="Enter password" required />
                        <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" />
                        <button type="submit">Login</button>
                    </form>
                </div>

                <div id="signup-form" style="display: none;">
                    <!-- Sign-Up Form -->
                    <form action="php/registration.php" method="POST">
                        <input type="text" name="first_name" placeholder="Enter your first name" required />
                        <input type="text" name="last_name" placeholder="Enter your last name" required />
                        <input type="email" name="email" placeholder="Enter your email" required />
                        <input type="text" name="username" placeholder="Choose a username" required />
                        <input type="password" name="password" placeholder="Create password" required />
                        <input type="text" name="address" placeholder="Enter your address (optional)" />
                        <input type="text" name="contact_number" placeholder="Enter your contact number (optional)" />
                        <button type="submit" class="btn signup">Create Account</button>
                        <p>Clicking <strong>create account</strong> means that you agree to our <a href="javascript:void(0)">terms of services</a>.</p>
                        <hr />
                    </form>
                </div>
            </div>
        </div>
    </div>
    
<script>
    // Function to show the booking modal
    function showBookingModal() {
        document.getElementById("bookingModal").style.display = "block";
    }

    // Function to close the booking modal
    function closeBookingModal() {
        document.getElementById("bookingModal").style.display = "none";
    }

    // Function to close the login modal
    function closeLoginModal() {
        document.getElementById("loginModal").style.display = "none";
    }

    // Function to toggle between login and signup forms
    function toggleSignup() {
        document.getElementById("login-toggle").style.backgroundColor = "#fff";
        document.getElementById("login-toggle").style.color = "#222";
        document.getElementById("signup-toggle").style.backgroundColor = "#57b846";
        document.getElementById("signup-toggle").style.color = "#fff";
        document.getElementById("login-form").style.display = "none";
        document.getElementById("signup-form").style.display = "block";
    }

    function toggleLogin() {
        document.getElementById("login-toggle").style.backgroundColor = "#57B846";
        document.getElementById("login-toggle").style.color = "#fff";
        document.getElementById("signup-toggle").style.backgroundColor = "#fff";
        document.getElementById("signup-toggle").style.color = "#222";
        document.getElementById("signup-form").style.display = "none";
        document.getElementById("login-form").style.display = "block";
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
        var bookingModal = document.getElementById("bookingModal");
        var loginModal = document.getElementById("loginModal");
        if (event.target == bookingModal) {
            closeBookingModal();
        } else if (event.target == loginModal) {
            closeLoginModal();
        }
    };

    // Document ready function
    $(document).ready(function() {
        // Show booking modal when 'Book Now' button is clicked
        $('#bookNowBtn').on('click', showBookingModal);

        // Close booking modal when 'X' is clicked
        $('#closeBookingModal').on('click', closeBookingModal);

        // Show login modal
        $('#openLoginBtn').on('click', function() {
            $('#loginModal').show(); // Show the login modal
        });

        // Close login modal
        $('.close').on('click', closeLoginModal);

        // Validate booking form date
        $('#bookingForm').on('submit', function(e) {
            const date = $('#dateInput').val();
            if (!date || new Date(date) < new Date()) {
                e.preventDefault();
                alert("Please select a valid future date.");
            }
        });

        // Display session-based alerts
        <?php if (isset($_SESSION['error_message'])): ?>
            alert("<?php echo $_SESSION['error_message']; ?>");
            <?php unset($_SESSION['error_message']); // Clear the error message after displaying ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['registration_success'])): ?>
            alert("<?php echo $_SESSION['registration_success']; ?>");
            <?php unset($_SESSION['registration_success']); // Clear the success message after displaying ?>
        <?php endif; ?>
    });
function toggleDescription(element) {
    const description = element.querySelector('.description');
    const computedStyle = window.getComputedStyle(description);
    
    // Toggle the display property based on the computed style
    if (computedStyle.display === "none") {
        description.style.display = "block"; // Show the description
    } else {
        description.style.display = "none"; // Hide the description
    }
}
</script>

<footer>
    <p>&copy; 2024 Sir Chief's Motorshop</p>
</footer>
</body>
</html>
