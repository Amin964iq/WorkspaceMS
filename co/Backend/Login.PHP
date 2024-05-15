<?php
include 'Connect.php'; 

function authenticateUser($username, $password, $conn) {
    $query = "SELECT * FROM customers WHERE costumerusername = :username";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($user) {       
        if(password_verify($password, $user['password'])) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if(authenticateUser($username, $password, $conn)) {
        header("Location: dashboard.php"); 
        exit();
    } else {
        echo "Incorrect username or password. Please try again.";
    }
}

?>

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
      background-color: #fff;
      padding: 30px;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      width: 300px;
    }
    .container h1 {
      text-align: center;
      margin-bottom: 20px;
    }
    .container h2 {
      text-align: center;
    }
    .form-group {
      margin-bottom: 20px;
    }
    .form-group label {
      display: block;
      margin-bottom: 5px;
    }
    .form-group input {
      width: 96%;
      padding: 9px;
      border: 1px solid #ccc;
      border-radius: 3px;
    }
    .form-group button {
      width: 100%;
      padding: 10px;
      border: none;
      background-color: #007bff;
      color: #fff;
      border-radius: 3px;
      cursor: pointer;
    }
    .form-group button:hover {
      background-color: #0056b3;
    }
    .register-link {
      text-align: center;
    }
    .register-link a {
      color: #007bff;
      text-decoration: none;
    }
    .register-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Workhaus</h1>
    <h2>Sign In</h2>
    <form id="signInForm" action="#" method="post">
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
      <p>Don't have an account? <a href="#">Register</a></p>
    </div>
  </div>
</body>
</html>
