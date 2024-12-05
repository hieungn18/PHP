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

// Handle adding a new recipe
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_recipe'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $instructions = $conn->real_escape_string($_POST['instructions']);
    $category = "Appetizer";

    $sql = "INSERT INTO recipes (title, description, instructions, category) VALUES ('$title', '$description', '$instructions', '$category')";
    $conn->query($sql);
}

// Handle deleting a recipe
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_recipe'])) {
    $recipe_id = intval($_POST['recipe_id']); // Fetch the recipe ID
    $sql = "DELETE FROM recipes WHERE recipe_id = $recipe_id"; // Use the correct column name
    $conn->query($sql); // Execute the query
}

// Handle editing a recipe
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_recipe'])) {
    $recipe_id = intval($_POST['recipe_id']);
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $instructions = $conn->real_escape_string($_POST['instructions']);

    $sql = "UPDATE recipes SET title = '$title', description = '$description', instructions = '$instructions' WHERE recipe_id = $recipe_id";
    $conn->query($sql);
}

// Fetch all appetizer recipes
$sql = "SELECT * FROM recipes WHERE category = 'Appetizer' ORDER BY created_at DESC";
$result = $conn->query($sql);

// Pagination setup
$limit = 5; // Number of recipes per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1; // Current page
$offset = ($page - 1) * $limit; // Calculate offset

// Fetch recipes for the current page
$sql = "SELECT * FROM recipes WHERE category = 'Appetizer' ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Count total recipes for pagination
$total_sql = "SELECT COUNT(*) AS total FROM recipes WHERE category = 'Appetizer'";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_recipes = $total_row['total'];
$total_pages = ceil($total_recipes / $limit);

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Appetizer Recipes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        #container {
            width: 90%;
            margin: 0 auto;
            padding: 20px;
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

        .recipe {
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
        }

        .actions {
            margin-top: 10px;
        }

        .actions form {
            display: inline;
        }

        .actions button {
            padding: 5px 10px;
            margin-right: 5px;
            background-color: red;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .actions button.edit {
            background-color: green;
        }

        .actions button:hover {
            opacity: 0.8;
        }
		
		.pagination {
            
            bottom: 20px;
            right: 20px;
            text-align: right;
        }

        .pagination a {
            color: #333;
            text-decoration: none;
            padding: 8px 15px;
            border: 1px solid #ccc;
            margin: 0 5px;
            border-radius: 5px;
            background-color: #f9f9f9;
            transition: background-color 0.3s;
        }

        .pagination a:hover {
            background-color: #ddd;
        }

        .pagination .current-page {
            font-weight: bold;
            background-color: #333;
            color: white;
        }
		
		#add-recipe-button {
            display: inline-block;
            margin-bottom: 20px;
        }

        #add-recipe-button a {
            color: #333;
            text-decoration: none;
            padding: 8px 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
            transition: background-color 0.3s;
        }

        #add-recipe-button a:hover {
            background-color: #ddd;
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
            <a href="main.php">Home</a>
        </div>
        Appetizer Recipes
        <div id="login-section">
            <?php
            if (isset($_SESSION['authenticated']) && $_SESSION['authenticated']) {
                echo "<span>Welcome, " . htmlspecialchars($_SESSION['username']) . " </span> | <a href='logout.php'>Log Out</a>";
            } else {
                echo "<a href='login.php'>Log In</a>";
            }
            ?>
        </div>
    </header>

    <div id="container">
        <h1>Delicious Appetizer Recipes</h1>
		
		<div id="add-recipe-button">
            <a href="add_recipe.php">Add New Recipe</a>
        </div><br><br>
		
        <!-- Display Recipes -->
		<?php while ($row = $result->fetch_assoc()) : ?>
			<div class="recipe">
				<!-- Link the title to recipe_details.php -->
				<h2><a href="recipe_details.php?recipe_id=<?= $row['recipe_id'] ?>"><?= htmlspecialchars($row['title']) ?></a></h2>
				<p><strong>Description:</strong><p><?= htmlspecialchars($row['description']) ?></p></p>
				
				
			</div>
		<?php endwhile; ?>
		
		<!-- Pagination Links -->
        <div class="pagination">
            <!-- Previous Button -->
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>">Previous</a>
            <?php endif; ?>

            <!-- Page Numbers -->
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="current-page"><?= $i ?></span>
                <?php else: ?>
                    <a href="?page=<?= $i ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <!-- Next Button -->
            <?php if ($page < $total_pages): ?>
                <a href="?page=<?= $page + 1 ?>">Next</a>
            <?php endif; ?>
        </div>
		
       

    <footer>&copy; 2024 Food Recipes. All rights reserved.</footer>
</body>
</html>
