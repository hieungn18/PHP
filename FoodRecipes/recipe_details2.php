<?php
session_start();
$host = "localhost";
$user = "hnguyen182";
$pass = "hnguyen182";
$dbname = "hnguyen182";

// Create database connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the recipe_id from the URL
if (!isset($_GET['recipe_id']) || !is_numeric($_GET['recipe_id'])) {
    die("Invalid recipe ID.");
}

$recipe_id = intval($_GET['recipe_id']);

// Fetch the recipe details from the database
$sql = "SELECT * FROM recipes WHERE recipe_id = $recipe_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $recipe = $result->fetch_assoc();
} else {
    die("Recipe not found.");
}

// Handle deleting the recipe
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_recipe'])) {
    $sql = "DELETE FROM recipes WHERE recipe_id = $recipe_id";
    if ($conn->query($sql)) {
        header("Location: desserts.php"); // Redirect to desserts page
        exit();
    } else {
        echo "Error deleting recipe: " . $conn->error;
    }
}

// Handle editing the recipe
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_recipe'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $instructions = $conn->real_escape_string($_POST['instructions']);

    $sql = "UPDATE recipes SET title = '$title', description = '$description', instructions = '$instructions' WHERE recipe_id = $recipe_id";
    if ($conn->query($sql)) {
        header("Location: recipe_details2.php?recipe_id=$recipe_id"); // Refresh the details page
        exit();
    } else {
        echo "Error updating recipe: " . $conn->error;
    }
}

// Handle image upload
$image_error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_image'])) {
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true); // Ensure the directory exists
    }

    if (isset($_FILES['recipe_image']) && $_FILES['recipe_image']['error'] == 0) {
        $target_file = $target_dir . basename($_FILES['recipe_image']['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate file type
        if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($_FILES['recipe_image']['tmp_name'], $target_file)) {
                // Save file path to database
                $sql = "UPDATE recipes SET image_path = '$target_file' WHERE recipe_id = $recipe_id";
                if ($conn->query($sql)) {
                    header("Location: recipe_details2.php?recipe_id=$recipe_id");
                    exit();
                } else {
                    $image_error = "Error saving image to database.";
                }
            } else {
                $image_error = "Error uploading the file.";
            }
        } else {
            $image_error = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
        }
    } else {
        $image_error = "File upload error.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($recipe['title']) ?> - Recipe Details</title>
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
            padding: 20px;
            font-family: Arial, sans-serif;
            font-size: 18px;
        }

        header {
            padding: 20px;
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
		
		.recipe-details p {
            margin: 10px 0;
        }

        .recipe-details pre {
            white-space: pre-wrap;
            line-height: 1.6;
        }
		
        textarea {
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            padding: 10px;
            font-family: Arial, sans-serif;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            resize: vertical;
        }

        textarea[name="title"] {
            height: 100px;
        }

        textarea[name="description"] {
            height: 150px;
        }

        textarea[name="instructions"] {
            height: 200px;
        }

        .recipe-image {
            text-align: center;
            margin-bottom: 20px;
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
            <a href="desserts.php">Back to Desserts</a>
        </div>

        <div id="login-section">
            <?php
            if (isset($_SESSION['authenticated']) && $_SESSION['authenticated']) {
                echo "<span>Welcome, " . htmlspecialchars($_SESSION['username']) . "</span> | <a href='logout.php'>Log Out</a>";
            } else {
                echo "<a href='login.php'>Log In</a>";
            }
            ?>
        </div>

        Recipe Details
    </header>

    <div id="container">
        <div class="recipe-details">
            <h1><?= htmlspecialchars($recipe['title']) ?></h1>
            <?php if (!empty($recipe['image_path'])): ?>
                <div class="recipe-image">
                    <img src="<?= htmlspecialchars($recipe['image_path']) ?>" alt="Recipe Image" width="100%">
                </div>
            <?php else: ?>
                <p>No image uploaded for this recipe.</p>
            <?php endif; ?>
            <p><strong>Description:</strong></p>
            <pre><?= htmlspecialchars($recipe['description']) ?></pre>
            <p><strong>Instructions:</strong></p>
            <pre><?= htmlspecialchars($recipe['instructions']) ?></pre>
        </div>

        <?php if (isset($_SESSION['authenticated']) && $_SESSION['authenticated']) : ?>
            <div class="actions">
                <form method="post" action="" style="display: inline;">
                    <label>Title:</label><br>
                    <textarea name="title" required><?= htmlspecialchars($recipe['title']) ?></textarea><br><br>
                    <label>Description:</label><br>
                    <textarea name="description" required><?= htmlspecialchars($recipe['description']) ?></textarea><br><br>
                    <label>Instructions:</label><br>
                    <textarea name="instructions" required><?= htmlspecialchars($recipe['instructions']) ?></textarea><br><br>
                    <button type="submit" name="edit_recipe" class="edit">Save Changes</button>
                </form><br><br>

                <form method="post" action="" enctype="multipart/form-data" style="display: inline;">
                    <input type="file" id="recipe_image" name="recipe_image" accept="image/*" style="display: none;" onchange="document.getElementById('upload_image_button').click();">
                    <button type="button" id="upload_image_button">Upload Image</button>
                    <button type="submit" name="upload_image" style="display: none;" id="upload_submit_button"></button>
                </form><br><br>

                <form method="post" action="" style="display: inline;">
                    <button type="submit" name="delete_recipe">Delete Recipe</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <footer>&copy; 2024 Food Recipes. All rights reserved.</footer>

    <script>
        document.getElementById('upload_image_button').addEventListener('click', function() {
            document.getElementById('recipe_image').click();
        });
        document.getElementById('recipe_image').addEventListener('change', function() {
            document.getElementById('upload_submit_button').click();
        });
    </script>
</body>
</html>
