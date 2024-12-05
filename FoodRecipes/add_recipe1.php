<?php
session_start();
if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $host = "localhost";
    $user = "hnguyen182";
    $pass = "hnguyen182";
    $dbname = "hnguyen182";

    $conn = new mysqli($host, $user, $pass, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $instructions = $conn->real_escape_string($_POST['instructions']);
    $category = "Entree";

    $sql = "INSERT INTO recipes (title, description, instructions, category) VALUES ('$title', '$description', '$instructions', '$category')";
    if ($conn->query($sql) === TRUE) {
        header("Location: entrees.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add New Recipe</title>
	<style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        #container {
            width: 90%;
			
			max-width: 1100px;
            margin: 0 auto;
            padding: 10px;
			font-family: Arial, sans-serif;
            font-size: 18px;
        }

        header {
            padding: 30px;
            text-align: center;
            background-color: #333;
            color: white;
            position: relative;
        }

        #home-button {
            position: absolute;
            top: 20px;
            left: 20px;
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

        #login-section {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 16px;
        }

        #login-section a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border: 1px solid white;
            border-radius: 5px;
            background-color: #555;
            transition: background-color 0.3s;
        }

        #login-section a:hover {
            background-color: white;
            color: #333;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .recipe-details {
            border: 1px solid #ccc;
            border-radius: 5px;
            margin: 20px 0;
            padding: 15px;
            background-color: #f9f9f9;
			line-height: 1.6;
        }
		
		textarea {
			margin: 30px;
            width: 95%;
            max-width: 100%; /* Ensures text areas fit within the container */
            box-sizing: border-box;
            padding: 10px;
            font-family: Arial, sans-serif;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            resize: vertical; /* Allow vertical resizing */
        }
		
		textarea[name="title"] {
            height: 80px; /* Larger height for title */
        }
        textarea[name="description"] {
            height: 150px; /* Larger height for description */
        }

        textarea[name="instructions"] {
            height: 200px; /* Larger height for instructions */
        }
		
        footer {
            text-align: center;
            padding: 10px;
            background-color: #333;
            color: white;
            margin-top: 20px;
        }
    </style>
</head>
<body>
	<header>
        <div id="home-button">
            <a href="entrees.php">Back to Entrees</a>
        </div>
		
		<!-- Login Section -->
        <div id="login-section">
            <?php
            if (isset($_SESSION['authenticated']) && $_SESSION['authenticated']) {
                echo "<span>Welcome, " . htmlspecialchars($_SESSION['username']) . "</span> | <a href='logout.php'>Log Out</a>";
            } else {
                echo "<a href='login.php'>Log In</a>";
            }
            ?>
        </div>
    </header>
	
    <h1>Add a New Recipe</h1>
    <?php if (isset($_SESSION['authenticated']) && $_SESSION['authenticated']) : ?>
            <div class="actions">
                <!-- Edit Form -->
                <form method="post" action="" style="display: inline;">
                    <label>Title:</label><br>
                    <textarea name="title" required><?= htmlspecialchars($recipe['title']) ?></textarea><br><br>
                    <label>Description:</label><br>
                    <textarea name="description" required><?= htmlspecialchars($recipe['description']) ?></textarea><br><br>
                    <label>Instructions:</label><br>
                    <textarea name="instructions" required><?= htmlspecialchars($recipe['instructions']) ?></textarea><br><br>
					<button type="submit">Add Recipe</button>
				</form>
    <p><a href="entrees.php">Back to Entrees</a></p>
	</div>
        <?php endif; ?>
</body>
</html>
