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

// Fetch user information
$username = $_SESSION['username'];
$sql = "SELECT * FROM Customers WHERE CustomerUsername = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
}

// Update user information
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newFirstName = $_POST["firstname"];
    $newLastName = $_POST["lastname"];
    $newEmail = $_POST["email"];
    $newPhone = $_POST["phone"];
    $newAddress = $_POST["address"];
    $newPassword = $_POST["password"];

    $updateSql = "UPDATE Customers SET CustomerFirstName=?, CustomerLastName=?, CustomerEmail=?, CustomerPhone=?, CustomerAddress=?, CustomerPassword=? WHERE CustomerUsername=?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("sssssss", $newFirstName, $newLastName, $newEmail, $newPhone, $newAddress, $newPassword, $username);
    if ($updateStmt->execute()) {
        // Data updated successfully
        $success_message = "Profile updated successfully.";
        // Update session variables
        $_SESSION['firstname'] = $newFirstName;
        $_SESSION['lastname'] = $newLastName;
        $_SESSION['email'] = $newEmail;
        $_SESSION['phone'] = $newPhone;
        $_SESSION['address'] = $newAddress;
    } else {
        $error_message = "Error updating profile: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="styles.css"> 
    <style>
       
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 320px;
            text-align: center;
        }

        .container h2 {
            margin-bottom: 20px;
            color: #333333;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333333;
            font-weight: bold;
        }

        .form-group input {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #cccccc;
            border-radius: 5px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            border-color: #007bff;
            outline: none;
        }

        .form-group button {
            width: calc(100% - 20px);
            padding: 10px;
            border: none;
            background-color: #007bff;
            color: #ffffff;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-group button:hover {
            background-color: #0056b3;
        }

        .message {
            margin-top: 10px;
            color: #333333;
        }

        .error-message {
            color: red;
        }

        .success-message {
            color: green;
        }

        .toggle-password {
            cursor: pointer;
            color: #007bff;
            margin-left: 10px;
            font-size: 0.9em;
        }

        .toggle-password:hover {
            color: #0056b3;
        }
    </style>
    <script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            var toggleText = document.getElementById("toggleText");
            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleText.innerText = "Hide";
            } else {
                passwordField.type = "password";
                toggleText.innerText = "Show";
            }
        }
    </script>
</head>

<body>
    <div class="container">
        <h2>Edit Profile</h2>
        <?php if (!empty($error_message)) { ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php } ?>
        <?php if (!empty($success_message)) { ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php } ?>
        <form id="profileForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo isset($row['CustomerUsername']) ? $row['CustomerUsername'] : ''; ?>" readonly>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" value="<?php echo isset($row['CustomerPassword']) ? $row['CustomerPassword'] : ''; ?>" required>
                <span id="toggleText" class="toggle-password" onclick="togglePassword()">Show</span>
            </div>
            <div class="form-group">
                <label for="firstname">First Name:</label>
                <input type="text" id="firstname" name="firstname" value="<?php echo isset($row['CustomerFirstName']) ? $row['CustomerFirstName'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="lastname">Last Name:</label>
                <input type="text" id="lastname" name="lastname" value="<?php echo isset($row['CustomerLastName']) ? $row['CustomerLastName'] : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo isset($row['CustomerEmail']) ? $row['CustomerEmail'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="phone" value="<?php echo isset($row['CustomerPhone']) ? $row['CustomerPhone'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?php echo isset($row['CustomerAddress']) ? $row['CustomerAddress'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <button type="submit">Save Changes</button>
            </div>
            <div class="form-group">
                <button type="button" onclick="window.location.href='c_dashboard.php'">Done</button>
            </div>
        </form>
    </div>
</body>

</html>

<?php
// Close connection
$conn->close();
?>
