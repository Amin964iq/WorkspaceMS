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

// Retrieve workspace information based on workspace ID
if(isset($_POST['workspace_id'])) {
    $workspace_id = $_POST['workspace_id'];
    
    // Fetch workspace details
    $sql = "SELECT * FROM WorkSpace WHERE WorkspaceID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $workspace_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $workspace = $result->fetch_assoc();
    } else {
        echo "Workspace not found.";
        exit();
    }
} else {
    echo "Workspace ID not provided.";
    exit();
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
        }

        h1, h2 {
            text-align: center;
        }

        .workspace-details {
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .workspace-image {
            width: 100%;
            max-width: 300px;
            height: auto;
            display: block;
            margin: 0 auto;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .workspace-info {
            margin-bottom: 20px;
        }

        .workspace-info p {
            margin: 10px 0;
        }

        .workspace-info p span {
            font-weight: bold;
            margin-left: 5px;
        }

        .booking-form {
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: bold;
        }

        input[type="date"],
        input[type="time"],
        input[type="text"],
        button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Booking Details</h1>
        
        <!-- Workspace Details -->
        <div class="workspace-details">
            <h2>Workspace Details</h2>
            <img class="workspace-image" src="data:image/jpeg;base64,<?php echo base64_encode($workspace['ImageData']); ?>" alt="Workspace Image">
            <div class="workspace-info">
                <p><span>Description:</span> <?php echo $workspace['Description']; ?></p>
                <p><span>Location:</span> <?php echo $workspace['Location']; ?></p>
                <p><span>Capacity:</span> <?php echo $workspace['Capacity']; ?></p>
                <p><span>Price per Hour:</span> $<?php echo $workspace['Price']; ?></p>
            </div>
        </div>
        
        <!-- Booking Form -->
        <div class="booking-form">
            <form action="confirm_booking.php" method="post">
                <h2>Booking Form</h2>
                <input type="hidden" name="workspace_id" value="<?php echo $workspace_id; ?>">
                <label for="reservation_date">Date of Reservation:</label>
                <input type="date" id="reservation_date" name="reservation_date" required min="<?php echo date('Y-m-d'); ?>"><br>
                <label for="start_time">Start Time:</label>
                <input type="time" id="start_time" name="start_time" required min="08:00" max="23:59"><br>
                <label for="end_time">End Time:</label>
                <input type="time" id="end_time" name="end_time" required min="08:00" max="23:59"><br>
                <label for="additional_services">Additional Services:</label>
                <input type="text" id="additional_services" name="additional_services"><br>
                <button type="submit">Confirm Booking</button>
            </form>
        </div>
    </div>
</body>
</html>
