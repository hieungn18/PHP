<?php
session_start();

// Database connection details
$host = "localhost";
$user = "hnguyen182";
$pass = "hnguyen182";
$dbname = "hnguyen182";

// Connect to the database
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize user input
    $username = $conn->real_escape_string($_POST['username']);
    $password = md5($_POST['password']); // Hash the password

    // Query to check user credentials
    $sql = "SELECT * FROM users WHERE username='$username' AND password_hash='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // User authenticated, set session variables
        $_SESSION['authenticated'] = true;
        $_SESSION['username'] = $username;
        header("Location: main.php"); // Redirect to the homepage
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Food Recipes</title>
    <style>
        /* Reset styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        header {
            background-color: #333;
            color: white;
            padding: 20px 30px;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }

        #home-button {
            position: relative;
        }

        #home-button a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border: 1px solid white;
            border-radius: 5px;
            background-color: #555;
            transition: background-color 0.3s;
        }

        #home-button a:hover {
            background-color: white;
            color: #333;
        }

        .content {
            flex: 1; /* Push footer to the bottom */
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 80px; /* Offset for the fixed header */
        }

        .login-container {
            background-color: #fff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        label {
            display: block;
            text-align: left;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        p {
            margin-top: 10px;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px 20px;
            margin-top: auto; /* Push footer to the bottom */
        }
    </style>
</head>
<body>
    <header>
        <div id="home-button">
            <a href="main.php">Home</a>
        </div>
    </header>

    <div class="content">
        <div class="login-container">
            <h2>Login</h2>
            <form method="post" action="login.php">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <input type="submit" value="Log In">
            </form>
            <p>Don't have an account? <a href="signup.php">Sign up here</a>.</p>
        </div>
    </div>

    <footer>&copy; 2024 Food Recipes. All rights reserved.</footer>
</body>
</html>
