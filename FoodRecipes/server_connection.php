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

// Create users table
$createUsersTable = "CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($createUsersTable) === TRUE) {
    echo "Users table created successfully\n";
} else {
    echo "Error creating users table: " . $conn->error . "\n";
}

// Create categories table
$createCategoriesTable = "CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
)";
if ($conn->query($createCategoriesTable) === TRUE) {
    echo "Categories table created successfully\n";
} else {
    echo "Error creating categories table: " . $conn->error . "\n";
}

// Create recipes table
$createRecipesTable = "CREATE TABLE IF NOT EXISTS recipes (
    recipe_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    instructions TEXT NOT NULL,
    prep_time INT,
    cook_time INT,
    servings INT,
    category_id INT,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
)";
if ($conn->query($createRecipesTable) === TRUE) {
    echo "Recipes table created successfully\n";
} else {
    echo "Error creating recipes table: " . $conn->error . "\n";
}

// Create ingredients table
$createIngredientsTable = "CREATE TABLE IF NOT EXISTS ingredients (
    ingredient_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
)";
if ($conn->query($createIngredientsTable) === TRUE) {
    echo "Ingredients table created successfully\n";
} else {
    echo "Error creating ingredients table: " . $conn->error . "\n";
}

// Create recipe_ingredients table
$createRecipeIngredientsTable = "CREATE TABLE IF NOT EXISTS recipe_ingredients (
    recipe_id INT,
    ingredient_id INT,
    quantity VARCHAR(50),
    PRIMARY KEY (recipe_id, ingredient_id),
    FOREIGN KEY (recipe_id) REFERENCES recipes(recipe_id) ON DELETE CASCADE,
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(ingredient_id) ON DELETE CASCADE
)";
if ($conn->query($createRecipeIngredientsTable) === TRUE) {
    echo "Recipe Ingredients table created successfully\n";
} else {
    echo "Error creating recipe_ingredients table: " . $conn->error . "\n";
}

// Create comments table
$createCommentsTable = "CREATE TABLE IF NOT EXISTS comments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    recipe_id INT,
    user_id INT,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (recipe_id) REFERENCES recipes(recipe_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
)";
if ($conn->query($createCommentsTable) === TRUE) {
    echo "Comments table created successfully\n";
} else {
    echo "Error creating comments table: " . $conn->error . "\n";
}

// Create favorites table
$createFavoritesTable = "CREATE TABLE IF NOT EXISTS favorites (
    user_id INT,
    recipe_id INT,
    PRIMARY KEY (user_id, recipe_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES recipes(recipe_id) ON DELETE CASCADE
)";
if ($conn->query($createFavoritesTable) === TRUE) {
    echo "Favorites table created successfully\n";
} else {
    echo "Error creating favorites table: " . $conn->error . "\n";
}

// Close the connection
$conn->close();
?>
