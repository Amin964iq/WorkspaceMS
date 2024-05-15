<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            text-align: center;
            max-width: 800px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .headline {
            margin-top: 0;
            margin-bottom: 30px;
            color: #333333;
            font-size: 28px;
            font-weight: bold;
        }

        .section {
            background-color: #f9f9f9;
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        .section:hover {
            transform: translateY(-5px);
        }

        .section h2 {
            color: #333333;
            margin-bottom: 20px;
        }

        .logout-btn {
            margin-top: 20px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            padding: 10px 28px0px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #0056b3;
        }

        .logo {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <img src="amin logo.png" alt="Logo" class="logo">
        <h1 class="headline">Admin Dashboard</h1>
        <?php
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

        session_start();

        // Check if user is logged in
        if (!isset($_SESSION["username"])) {
            header("Location: login.php");
            exit();
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

            // Display appropriate link based on user role
            if ($userRole === "manager") {
                echo "<div class='section' onclick=\"location.href='m_staff.php';\">";
                echo "<h2>Modify Staff</h2>";
                echo "</div>";
            } elseif ($userRole === "admin") {
                echo "<div class='section' onclick=\"location.href='a_staff.php';\">";
                echo "<h2>Modify Staff</h2>";
                echo "</div>";
            } else {
                echo "<div class='section' onclick=\"location.href='m_staff.php';\">";
                echo "<h2>Modify Staff</h2>";
                echo "</div>";
            }
        }
        ?>
        <div class="section" onclick="location.href='m_workspaces.php';">
            <h2>Modify Workspaces</h2>
        </div>
        <form action="login.php" method="post">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</body>

</html>
