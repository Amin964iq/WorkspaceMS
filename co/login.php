<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
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

        .container h1 {
            margin-bottom: 20px;
            color: #333333;
        }

        .container h2 {
            margin-bottom: 20px;
            color: #666666;
        }

        .form-group {
            margin-bottom: 20px;
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

        .register-link {
            color: #333333;
        }

        .register-link a {
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .register-link a:hover {
            color: #0056b3;
        }

        .error-message {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Workhaus</h1>
        <h2>Sign In</h2>
        <?php
        session_start();
        $error_message = "";
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST["username"];
            $password = $_POST["password"];
            $conn = new mysqli("localhost", "root", "", "workhausdb");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            // Check if the user is an admin
            $sql = "SELECT * FROM Staff WHERE Username = ? AND StaffPassword = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                // Redirect to admin dashboard
                $_SESSION["username"] = $username;
                header("Location: m_dashboard.php");
                exit();
            }

            // Check if the user is a customer
            $sql = "SELECT * FROM Customers WHERE CustomerUsername = ? AND CustomerPassword = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                // Redirect to browsing page
                $_SESSION["username"] = $username;
                header("Location: c_dashboard.php");
                exit();
            } else {
                $error_message = "Invalid username or password. Please try again.";
            }
            $stmt->close();
            $conn->close();
        }
        ?>
        <?php if (!empty($error_message)) { ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php } ?>
        <form id="signInForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <button type="submit">Sign In</button>
            </div>
        </form>
        <div class="register-link">
            <p>Don't have an account? <a href="Signup.php">Register</a></p>
        </div>
    </div>
</body>

</html>