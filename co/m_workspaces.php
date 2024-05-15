<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "workhausdb");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle editing availability and price
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["save"])) {
    $workspaceIDs = $_POST["workspace_id"];
    $availabilities = $_POST["availability"];
    $prices = $_POST["price"];

    foreach ($workspaceIDs as $key => $workspaceID) {
        $availability = $availabilities[$key];
        $price = floatval($prices[$key]);

        // Update availability and price
        $sql = "UPDATE WorkSpace SET WorkspaceAvailability = ?, Price = ? WHERE WorkspaceID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdi", $availability, $price, $workspaceID);
        $stmt->execute();
        $stmt->close();
    }
    echo "<script>alert('Saved successfully');</script>";
}

// Handle deletion of selected workspaces
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_selected"])) {
    if (isset($_POST["selected_workspaces"]) && !empty($_POST["selected_workspaces"])) {
        $selectedWorkspaces = implode(",", $_POST["selected_workspaces"]);
        $sql = "DELETE FROM WorkSpace WHERE WorkspaceID IN ($selectedWorkspaces)";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Deleted successfully');</script>";
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    }
}

// Handle addition of new workspace
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_workspace"])) {
    $description = htmlspecialchars($_POST["description"]);
    $location = htmlspecialchars($_POST["location"]);
    $capacity = intval($_POST["capacity"]);
    $price = floatval($_POST["price"]);
    $availability = $_POST["availability"];

    // Get the latest WorkspaceID
    $latestWorkspaceIDQuery = $conn->query("SELECT MAX(WorkspaceID) AS LatestID FROM WorkSpace");
    $latestWorkspaceIDRow = $latestWorkspaceIDQuery->fetch_assoc();
    $latestID = intval($latestWorkspaceIDRow["LatestID"]);
    $newWorkspaceID = $latestID + 1;

    // Insert new workspace
    $sql = "INSERT INTO WorkSpace (WorkspaceID, Description, Location, Capacity, Price, WorkspaceAvailability) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssds", $newWorkspaceID, $description, $location, $capacity, $price, $availability);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Added successfully');</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Workspaces</title>
    <style>
        /* Add your CSS styling here */
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
            margin-top: 30px;
        }

        h1 {
            margin-top: 0;
            margin-bottom: 30px;
            color: #333333;
        }

        h2 {
            margin-bottom: 20px;
            color: #666666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            border-bottom: 1px solid #dddddd;
        }

        th {
            background-color: #f9f9f9;
            text-align: left;
            font-weight: bold;
        }

        input[type="checkbox"] {
            margin-right: 5px;
        }

        input[type="number"] {
            width: 70px;
        }

        select {
            width: 100px;
        }

        button {
            padding: 10px 20px;
            margin: 10px;
            border: none;
            background-color: #007bff;
            color: #ffffff;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .input-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333333;
        }

        .input-group input[type="text"],
        .input-group input[type="number"],
        .input-group select {
            width: calc(100% - 10px);
            padding: 10px;
            border: 1px solid #dddddd;
            border-radius: 5px;
            outline: none;
        }

        .input-group select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="black" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>');
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 12px;
        }

        .input-group button {
            width: 100%;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Modify Workspaces</h1>

        <!-- Display Existing Workspaces -->
        <h2>Existing Workspaces</h2>
        <form method="post">
            <table>
                <tr>
                    <th></th>
                    <th>WorkspaceID</th>
                    <th>Description</th>
                    <th>Capacity</th>
                    <th>Price ($)</th>
                    <th>Availability</th>
                </tr>
                <?php
                $result = $conn->query("SELECT * FROM WorkSpace");
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td><input type='checkbox' name='selected_workspaces[]' value='" . $row["WorkspaceID"] . "'></td>
                                <td>" . $row["WorkspaceID"] . "</td>
                                <td>" . $row["Description"] . "</td>
                                <td>" . $row["Capacity"] . "</td>
                                <td><input type='number' name='price[]' value='" . $row["Price"] . "' min='0.01' step='0.01'></td>
                                <td>
                                    <select name='availability[]'>
                                        <option value='Yes'" . ($row["WorkspaceAvailability"] == 'Yes' ? " selected" : "") . ">Yes</option>
                                        <option value='No'" . ($row["WorkspaceAvailability"] == 'No' ? " selected" : "") . ">No</option>
                                    </select>
                                </td>
                                <input type='hidden' name='workspace_id[]' value='" . $row["WorkspaceID"] . "'>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No workspaces found.</td></tr>";
                }
                ?>
            </table>
            <button type="submit" name="save">Save</button>
            <button type="submit" name="delete_selected">Delete Selected Workspaces</button>
        </form>

        <!-- Add New Workspace -->
        <h2>Add New Workspace</h2>
        <form method="post">
            <input type="hidden" name="add_workspace" value="1">
            <div class="input-group">
                <label for="description">Description:</label>
                <input type="text" id="description" name="description" required>
            </div>
            <div class="input-group">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" required>
            </div>
            <div class="input-group">
                <label for="capacity">Capacity:</label>
                <input type="number" id="capacity" name="capacity" required min="1">
            </div>
            <div class="input-group">
                <label for="price">Price ($):</label>
                <input type="number" id="price" name="price" required min="0.01" step="0.01">
            </div>
            <div class="input-group">
                <label for="availability">Availability:</label>
                <select name="availability" required>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>
            <button type="submit">Add</button>
        </form>

        <!-- Done Button -->
        <form method="post" action="m_dashboard.php">
            <button type="submit">Done</button>
        </form>
    </div>
</body>
</html>
