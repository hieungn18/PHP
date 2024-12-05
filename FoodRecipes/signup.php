<?php
$host = "localhost";
$user = "hnguyen182";
$pass = "hnguyen182";
$dbname = "hnguyen182";

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = ""; // Initialize error message
$success = ""; // Initialize success message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input and sanitize it
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']); // Get email
    $password = $_POST['password']; // Raw password
    $confirm_password = $_POST['confirm_password']; // Confirm password

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error = "Passwords do not match. Please try again.";
    } else {
        $password_hash = md5($password); // Hash the password using md5

        // Check if the username already exists
        $checkUsername = "SELECT * FROM users WHERE username = '$username'";
        $result = $conn->query($checkUsername);

        if ($result->num_rows > 0) {
            $error = "Username already exists. Please choose a different username.";
        } else {
            // If username does not exist, insert the new user
            $sql = "INSERT INTO users (username, email, password_hash) VALUES ('$username', '$email', '$password_hash')";

            if ($conn->query($sql) === TRUE) {
                $success = "User registered successfully. <a href='login.php'>Login here</a>.";
            } else {
                $error = "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sign Up - Food Recipes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background-color: #333;
            color: white;
            padding: 0;
            position: relative;
            text-align: center;
        }

        #home-button {
            position: absolute;
            top: 30px;
            left: 15px;
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

        .signup-container {
            background-color: #fff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
            margin: auto;
            margin-bottom: auto;
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

        input[type="text"], input[type="password"], input[type="email"] {
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

        .error {
            color: red;
            margin-bottom: 15px;
        }

        .success {
            color: green;
            margin-bottom: 15px;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px;
            margin-top: auto;
        }
    </style>
</head>
<body>
    <header>
        <div id="home-button">
            <a href="main.php">Home</a>
        </div>
        <h1>Sign Up</h1>
    </header>
    <div class="signup-container">
        <h2>Sign Up</h2>
        <!-- Display error and success messages -->
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <?php if (!empty($success)) echo "<p class='success'>$success</p>"; ?>
        <form method="post" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <input type="submit" value="Sign Up">
        </form>
        <p>Already have an account? <a href="login.php">Go back to Login</a></p>
    </div>
    <footer>&copy; 2024 Food Recipes. All rights reserved.</footer>
</body>
</html>
