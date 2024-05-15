<?php
$error_message = '';
$success_message = '';

// Checking if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Function to validate email
    function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    // Function to validate phone number
    function validatePhoneNumber($phoneNumber) {
        // Remove dashes and spaces from phone number
        $phoneNumber = str_replace(['-', ' '], '', $phoneNumber);
        return (strlen($phoneNumber) == 11 && ctype_digit($phoneNumber));
    }

    // Function to validate password
    function validatePassword($password) {
        // Password must be at least 8 characters long and contain at least one number and one uppercase letter
        return (strlen($password) >= 8 && preg_match("/[0-9]/", $password) && preg_match("/[A-Z]/", $password));
    }

    // Database configuration
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "workhausdb";

    // Creating connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Checking connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Validating inputs
    $username = $_POST['username'];
    $password = $_POST['password'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $phoneNumber = $_POST['phoneNumber'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    if (!validateEmail($email)) {
        $error_message = "Invalid email format.";
    } elseif (!validatePhoneNumber($phoneNumber)) {
        $error_message = "Invalid phone number format. It must be 11 digits.";
    } elseif (!validatePassword($password)) {
        $error_message = "Password must be at least 8 characters long and contain at least one number and one uppercase letter.";
    } else {
        // Preparing and bind parameters
        $stmt = $conn->prepare("INSERT INTO Customers (CustomerUsername, CustomerFirstName, CustomerLastName, CustomerPassword, CustomerPhone, CustomerEmail, CustomerAddress) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $username, $firstName, $lastName, $password, $phoneNumber, $email, $address);

        // Executing query
        if ($stmt->execute()) {
            // Registration successful, set success message
            $success_message = "Registration successful!";
        } else {
            $error_message = "Error: " . $stmt->error;
        }

        // Closing statement
        $stmt->close();
    }

    // Closing connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
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
            color: #666666;
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

        .form-group small {
            color: #666666;
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

        .register-link {
            margin-top: 20px;
            color: #666666;
        }

        .register-link a {
            color: #007bff;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: red;
            margin-top: 10px;
        }

        .success-message {
            color: green;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Registration</h2>
        <?php if (!empty($error_message)) { ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php } ?>
        <?php if (!empty($success_message)) { ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php } ?>
        <form id="registrationForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" pattern="(?=.*\d)(?=.*[A-Z]).{8,}" title="Password must be at least 8 characters long and contain at least one number and one uppercase letter" required>
                <small>Password must be at least 8 characters long and contain at least one number and one uppercase letter</small>
            </div>
            <div class="form-group">
                <label for="firstName">First Name:</label>
                <input type="text" id="firstName" name="firstName" required>
            </div>
            <div class="form-group">
                <label for="lastName">Last Name:</label>
                <input type="text" id="lastName" name="lastName" required>
            </div>
            <div class="form-group">
                <label for="phoneNumber">Phone Number:</label>
                <input type="tel" id="phoneNumber" name="phoneNumber" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" required>
            </div>
            <div class="form-group">
                <button type="submit">Register</button>
            </div>
        </form>
        <div class="register-link">
            <p>Already have an account? <a href="login.php">Sign In</a></p>
        </div>
        <?php if (!empty($success_message)) { ?>
            <div class="form-group">
                <button onclick="window.location.href='login.php'">Okay</button>
            </div>
        <?php } ?>
    </div>
</body>

</html>
