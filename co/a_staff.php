<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome, Admin!</title>
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
            width: 800px;
            text-align: center;
        }

        .staff-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .staff-table th,
        .staff-table td {
            border: 1px solid #ddd;
            padding: 12px;
        }

        .staff-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .delete-btn,
        .add-btn,
        .done-btn {
            padding: 10px 20px;
            border: none;
            background-color: #007bff;
            color: #ffffff;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin: 5px;
        }

        .delete-btn:hover,
        .add-btn:hover,
        .done-btn:hover {
            background-color: #0056b3;
        }

        .success-message {
            color: green;
            margin-top: 10px;
        }

        .error-message {
            color: red;
            margin-top: 10px;
        }

        .add-section {
            margin-bottom: 30px;
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

        .form-group input,
        .form-group select {
            width: calc(100% - 12px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #007bff;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Welcome, Admin!</h1>

        <?php
        session_start();

        // Check if user is logged in
        if (!isset($_SESSION["username"])) {
            header("Location: login.php");
            exit();
        }

        // Check user role
        $conn = new mysqli("localhost", "root", "", "workhausdb");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $username = $_SESSION["username"];

        $sql = "SELECT StaffRole FROM Staff WHERE Username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $userRole = $row["StaffRole"];

            // Access control based on user role
            if ($userRole !== "admin") {
                // Redirect to unauthorized page or display an error message
                echo "Access Denied. You are not authorized to access this page.";
                exit();
            }
        } else {
            // Redirect if user not found in database
            header("Location: login.php");
            exit();
        }

        // Continue with the rest of the code for modifying staff
        ?>

        <table class="staff-table">
            <tr>
                <th>Username</th>
                <th>Action</th>
            </tr>
            <?php
            // Create connection
            $conn = new mysqli("localhost", "root", "", "workhausdb");

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Delete staff
                if (isset($_POST['delete'])) {
                    $staffID = $_POST['delete'];
                    $sql = "DELETE FROM Staff WHERE StaffID=$staffID";
                    $result = $conn->query($sql);
                }

                // Add new employee
                if (isset($_POST['add'])) {
                    $newUsername = $_POST['new-username'];
                    $newPassword = $_POST['new-password'];
                    $newRole = "employee"; // Assigning only the employee role for admins

                    // Insert new employee
                    $sql = "INSERT INTO Staff (Username, StaffPassword, StaffRole) VALUES ('$newUsername', '$newPassword', '$newRole')";
                    $result = $conn->query($sql);
                }
            }

            // Select only employees
            $sql = "SELECT * FROM Staff WHERE StaffRole = 'employee'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["Username"] . "</td>";
                    echo "<td>
                            <form method='post'>
                                <button type='submit' name='delete' value='" . $row["StaffID"] . "' class='delete-btn'>Delete</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='2'>No employees found</td></tr>";
            }

            $conn->close();
            ?>
        </table>

        <div class="add-section">
            <h2>Add New Employee</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="new-username">Username:</label>
                    <input type="text" id="new-username" name="new-username" required>
                </div>
                <div class="form-group">
                    <label for="new-password">Password:</label>
                    <input type="password" id="new-password" name="new-password" required>
                </div>
                <div class="form-group">
                    <button type="submit" name="add" class="add-btn">Add Employee</button>
                </div>
            </form>
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
                echo "<p class='success-message'>Employee added successfully.</p>";
            }
            ?>
        </div>
        <div>
            <a href="m_dashboard.php" class="done-btn">Done</a>
        </div>
    </div>
</body>

</html>
