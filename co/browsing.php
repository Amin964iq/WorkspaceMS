<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workspaces</title>
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

        .workspace {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .workspace img {
            width: 100%;
            height: auto;
            border-bottom: 1px solid #ccc;
        }

        .workspace-details {
            padding: 20px;
        }

        .workspace-id {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .description {
            margin-bottom: 10px;
        }

        .location {
            margin-bottom: 10px;
        }

        .capacity {
            margin-bottom: 10px;
        }

        .price {
            margin-bottom: 10px;
        }

        .availability {
            margin-bottom: 20px;
        }

        .btn-container {
            display: flex;
            justify-content: center;
        }

        .btn-container button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-container button:hover {
            background-color: #0056b3;
        }

        .btn-container button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .no-workspaces {
            text-align: center;
            font-style: italic;
        }

        /* Styles for sorting section */
        .sort-container {
            margin-bottom: 20px;
            text-align: center;
        }

        .sort-container select {
            padding: 10px;
            font-size: 16px;
        }
    </style>
</head>

<body>
    <header>
        <h1>Workspaces</h1>
    </header>

    <nav>
        <div class="nav-links">
            <img src="amin logo.png" alt="Logo">
            <a href="c_dashboard.php">Home Page</a> |
            <a href="#">Browse Workspaces</a>
        </div>
        <a href="login.php" class="logout-link">Logout</a>
    </nav>

    <!-- Sorting Section -->
    <div class="sort-container">
        <form id="sort-form" method="GET">
            <label for="sort">Sort By:</label>
            <select name="sort" id="sort">
                <option value="price_asc">Price (Low to High)</option>
                <option value="price_desc">Price (High to Low)</option>
                <option value="capacity_asc">Capacity (Low to High)</option>
                <option value="capacity_desc">Capacity (High to Low)</option>
                <option value="availability">Availability</option>
            </select>
            <button type="submit">Sort</button>
        </form>
    </div>

    <div class="container">
        <!-- Workspace List -->
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

        // Fetch workspaces from the database
        $sql = "SELECT * FROM WorkSpace";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="workspace">';
                echo '<img src="data:image/jpeg;base64,' . base64_encode($row['ImageData']) . '" alt="Workspace Image">';
                echo '<div class="workspace-details">';
                echo '<div class="workspace-id">WorkHaus co-working space ' . $row['WorkspaceID'] . '</div>';
                echo '<div class="description">Description: ' . $row['Description'] . '</div>';
                echo '<div class="location">Location: ' . $row['Location'] . '</div>';
                echo '<div class="capacity">Capacity: ' . $row['Capacity'] . '</div>';
                echo '<div class="price">Price: $' . $row['Price'] . '</div>';
                echo '<div class="availability">Availability: ' . ($row['WorkspaceAvailability'] == 'Yes' ? 'Yes' : 'No') . '</div>';
                if ($row['WorkspaceAvailability'] == 'Yes') {
                    echo '<div class="btn-container">';
                    echo '<form method="post" action="booking.php">';
                    echo '<input type="hidden" name="workspace_id" value="' . $row['WorkspaceID'] . '">';
                    echo '<button type="submit">Book Now</button>';
                    echo '</form>';
                    echo '</div>';
                } else {
                    echo '<div class="btn-container">';
                    echo '<button disabled>Not Available</button>';
                    echo '</div>';
                }
                echo '</div>'; // Close workspace-details
                echo '</div>'; // Close workspace
            }
        } else {
            echo '<p class="no-workspaces">No workspaces available.</p>';
        }

        // Close connection
        $conn->close();
        ?>
    </div>

</body>

</html>
