<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome, Boss!</title>
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

        .edit-btn,
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

        .edit-btn:hover,
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
        <h1>Modify Staff</h1>

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
            if ($userRole === "employee"|| $userRole === "admin") {
                // Redirect employee to a different page or display an error message
                echo "Access Denied. You are not authorized to access this page.";
                exit();
            } elseif ($userRole === "manager") {
                // Allow admin and manager to access the page
                // Continue with the rest of the code for modifying staff
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
                <th>Role</th>
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
                // Update staff role
                if (isset($_POST['edit'])) {
                    $staffID = $_POST['edit'];
                    $newRole = $_POST['role'][$staffID];
                    $sql = "UPDATE Staff SET StaffRole='$newRole' WHERE StaffID=$staffID";
                    $result = $conn->query($sql);
                }

                // Delete staff
                if (isset($_POST['delete'])) {
                    $staffID = $_POST['delete'];
                    $sql = "DELETE FROM Staff WHERE StaffID=$staffID";
                    $result = $conn->query($sql);
                }

                // Add new staff
                if (isset($_POST['add'])) {
                    $newUsername = $_POST['new-username'];
                    $newPassword = $_POST['new-password'];
                    $newRole = $_POST['new-role'];

                    // Get the maximum StaffID
                    $sql = "SELECT MAX(StaffID) AS MaxID FROM Staff";
                    $result = $conn->query($sql);
                    $row = $result->fetch_assoc();
                    $maxID = $row["MaxID"];

                    // Assign the next available StaffID
                    $newID = $maxID + 1;

                    // Insert new staff with the next available StaffID
                    $sql = "INSERT INTO Staff (StaffID, Username, StaffPassword, StaffRole)
                     VALUES ('$newID', '$newUsername', '$newPassword', '$newRole')";
                    $result = $conn->query($sql);
                }
            }

            $sql = "SELECT * FROM Staff";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["Username"] . "</td>";
                    echo "<td>
                            <form method='post'>
                                <select name='role[" . $row["StaffID"] . "]'>
                                    <option value='manager'" . ($row["StaffRole"] == "manager" ? " selected" : "") . ">Manager</option>
                                    <option value='admin'" . ($row["StaffRole"] == "admin" ? " selected" : "") . ">Admin</option>
                                    <option value='employee'" . ($row["StaffRole"] == "employee" ? " selected" : "") . ">Employee</option>
                                </select>
                                <button type='submit' name='edit' value='" . $row["StaffID"] . "' class='edit-btn'>Edit</button>
                            </form>
                          </td>";
                    echo "<td>
                            <form method='post'>
                                <button type='submit' name='delete' value='" . $row["StaffID"] . "' class='delete-btn'>Delete</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No staff found</td></tr>";
            }

            $conn->close();
            ?>
        </table>

        <div class="add-section">
            <h2>Add New Staff</h2>
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
                    <label for="new-role">Role:</label>
                    <select id="new-role" name="new-role">
                        <option value="manager">Manager</option>
                        <option value="admin">Admin</option>
                        <option value="employee">Employee</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" name="add" class="add-btn">Add</button>
                </div>
            </form>
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
                echo "<p class='success-message'>Staff added successfully.</p>";
            }
            ?>
        </div>
        <div>
            <a href="m_dashboard.php" class="done-btn">Done</a>
        </div>
    </div>
</body>

</html>
