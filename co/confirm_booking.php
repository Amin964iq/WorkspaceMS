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

// Retrieve booking details from POST data
if(isset($_POST['workspace_id'], $_POST['reservation_date'], $_POST['start_time'], $_POST['end_time'])) {
    $workspace_id = $_POST['workspace_id'];
    $reservation_date = $_POST['reservation_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $additional_services = isset($_POST['additional_services']) ? $_POST['additional_services'] : "None";
    
    // Get customer ID from session
    $customer_username = $_SESSION['username'];
    $customer_query = "SELECT CustomerID FROM Customers WHERE CustomerUsername = '$customer_username'";
    $customer_result = $conn->query($customer_query);
    
    if ($customer_result->num_rows > 0) {
        $customer_row = $customer_result->fetch_assoc();
        $customer_id = $customer_row['CustomerID'];
    } else {
        echo "Customer not found.";
        exit();
    }
    
    // Calculate total price based on duration
    $sql = "SELECT Price FROM WorkSpace WHERE WorkspaceID = $workspace_id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $price_per_hour = $row['Price'];
        
        // Calculate duration in hours
        $start_datetime = new DateTime($reservation_date . ' ' . $start_time);
        $end_datetime = new DateTime($reservation_date . ' ' . $end_time);
        $duration = $start_datetime->diff($end_datetime)->h;
        
        // Calculate total price
        $total_price = $duration * $price_per_hour;
    } else {
        echo "Workspace not found.";
        exit();
    }
    
    // Insert booking into database
    $insert_query = "INSERT INTO Booking (CustomerID, WorkSpaceID, Price, BookingDate, StartTime, EndTime, Additional_Services, Total) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("iiissssd", $customer_id, $workspace_id, $price_per_hour, $reservation_date, $start_time, $end_time, $additional_services, $total_price);
    
    if ($stmt->execute()) {
        // Update workspace availability status
        $update_query = "UPDATE WorkSpace SET WorkspaceAvailability = 'No' WHERE WorkspaceID = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("i", $workspace_id);
        $update_stmt->execute();
        
        echo "Booking confirmed successfully!";
    } else {
        echo "Error confirming booking: " . $conn->error;
    }
    
    // Close prepared statement
    $stmt->close();
} else {
    echo "Booking details not provided.";
}

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Booking</title>
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

        .confirmation-message {
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .confirmation-message p {
            margin: 10px 0;
        }

        .confirmation-message p span {
            font-weight: bold;
            margin-left: 5px;
        }

        .confirmation-message .total {
            font-size: 1.2em;
            font-weight: bold;
            color: #007bff;
        }

        .confirmation-message .success {
            color: green;
            margin-top: 20px;
            text-align: center;
        }

        .confirmation-message .error {
            color: red;
            margin-top: 20px;
            text-align: center;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
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
        <h1>Confirm Booking</h1>
        
        <!-- Confirmation Message -->
        <div class="confirmation-message">
            <?php
            // Display confirmation message
            if (isset($total_price)) {
                echo "<p>Your booking has been confirmed:</p>";
                echo "<p><span>Workspace ID:</span> $workspace_id</p>";
                echo "<p><span>Reservation Date:</span> $reservation_date</p>";
                echo "<p><span>Start Time:</span> $start_time</p>";
                echo "<p><span>End Time:</span> $end_time</p>";
                echo "<p><span>Total Price:</span> <span class='total'>$total_price</span></p>";
                echo "<p><span>Additional Services:</span> ";
                echo isset($additional_services) ? $additional_services : "None";
            } else {
                echo "<p class='error'>Error confirming booking. Please try again.</p>";
            }
            ?>
        </div>
        
        <!-- Back to Dashboard Button -->
        <button onclick="window.location.href='c_dashboard.php'">Back to Dashboard</button>
    </div>
</body>
</html>
