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

// Retrieve customer's bookings
$sql = "SELECT * FROM Booking WHERE CustomerID = $customer_id";
$result = $conn->query($sql);

// Cancel booking if requested
if(isset($_POST['cancel_booking_id'])) {
    $cancel_booking_id = $_POST['cancel_booking_id'];
    
    // Delete booking from database
    $cancel_sql = "DELETE FROM Booking WHERE BookingID = $cancel_booking_id";
    if ($conn->query($cancel_sql) === TRUE) {
        // Update workspace availability status
        $update_query = "UPDATE WorkSpace SET WorkspaceAvailability = 'Yes' WHERE WorkspaceID IN (SELECT WorkSpaceID FROM Booking WHERE BookingID = $cancel_booking_id)";
        if ($conn->query($update_query) === TRUE) {
            echo '<script>alert("Booking cancelled successfully!");</script>';
        } else {
            echo '<script>alert("Error updating workspace availability: ' . $conn->error . '");</script>';
        }
    } else {
        echo '<script>alert("Error cancelling booking: ' . $conn->error . '");</script>';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
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

        .booking-details {
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .booking-details p {
            margin: 10px 0;
        }

        .booking-details p span {
            font-weight: bold;
            margin-left: 5px;
        }

        .no-bookings {
            text-align: center;
            font-style: italic;
        }

        .done-button {
            text-align: center;
            margin-top: 20px;
        }

        .done-button button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .done-button button:hover {
            background-color: #0056b3;
        }

        .cancel-button {
            text-align: center;
            margin-top: 10px;
        }

        .cancel-button button {
            padding: 10px 20px;
            background-color: #dc3545;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .cancel-button button:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>My Bookings</h1>

    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="booking-details">';
            echo '<p><span>Booking ID:</span> ' . $row['BookingID'] . '</p>';
            echo '<p><span>Workspace ID:</span> ' . $row['WorkSpaceID'] . '</p>';
            echo '<p><span>Price:</span> $' . $row['Price'] . '</p>';
            echo '<p><span>Booking Date:</span> ' . $row['BookingDate'] . '</p>';
            echo '<p><span>Start Time:</span> ' . $row['StartTime'] . '</p>';
            echo '<p><span>End Time:</span> ' . $row['EndTime'] . '</p>';
            echo '<<p><span>Additional Services:</span> ' . $row['Additional_Services'] . '</p>';
            echo '</div>';
            // Cancel booking form
            echo '<form class="cancel-button" method="post">';
            echo '<input type="hidden" name="cancel_booking_id" value="' . $row['BookingID'] . '">';
            echo '<button type="submit">Cancel Booking</button>';
            echo '</form>';
        }
    } else {
        echo '<p class="no-bookings">You have no bookings yet.</p>';
    }
    ?>

    <div class="done-button">
        <button onclick="window.location.href='c_dashboard.php'">Done</button>
    </div>

</div>
</body>
</html>

<?php
// Close connection
$conn->close();
?>
