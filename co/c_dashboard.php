<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$database = "workhausdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 20px 0;
            text-align: center;
        }

        nav {
            background-color: #fff;
            padding: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        nav .nav-links {
            display: flex;
            align-items: center;
        }

        nav img {
            height: 40px;
            margin-right: 20px;
        }

        nav a {
            color: #333;
            text-decoration: none;
            margin: 0 10px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            display: grid;
            gap: 20px;
        }

        .dashboard-section {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: transform 0.3s ease;
        }

        .dashboard-section:hover {
            transform: translateY(-5px);
        }

        .dashboard-section h2 {
            margin-bottom: 10px;
        }

        .dashboard-section a {
            display: block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .dashboard-section a:hover {
            background-color: #0056b3;
        }

        .logout-link {
            color: #333;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <header>
        <h1>Customer Dashboard</h1>
    </header>

    <nav>
        <div class="nav-links">
            <img src="amin logo.png" alt="Logo">
            <a href="#">Home Page</a> |
            <a href="browsing.php">Browse Workspaces</a>
        </div>
        <a href="login.php" class="logout-link">Logout</a>
    </nav>

    <div class="container">
        <div class="dashboard-section">
            <h2>My Profile</h2>
            <a href="c_profile.php">View Profile</a>
        </div>
        <div class="dashboard-section">
            <h2>My Bookings</h2>
            <a href="c_bookings.php">View Bookings</a>
        </div>
        <div class="dashboard-section">
            <h2>Browse Workspaces</h2>
            <a href="browsing.php">Browse Workspaces</a>
        </div>
    </div>

</body>

</html>

<?php
// Close connection
$conn->close();
?>
