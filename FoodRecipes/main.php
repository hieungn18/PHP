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

// Function to resize and save an image
function resizeImage($file, $target_file, $max_width = 3000, $max_height = 3000) {
    list($orig_width, $orig_height) = getimagesize($file);
    $resize_width = $orig_width;
    $resize_height = $orig_height;

    // Calculate scaling
    if ($orig_width > $max_width || $orig_height > $max_height) {
        $scale = min($max_width / $orig_width, $max_height / $orig_height);
        $resize_width = (int)($orig_width * $scale);
        $resize_height = (int)($orig_height * $scale);
    }

    // Create a blank image
    $image_resized = imagecreatetruecolor($resize_width, $resize_height);

    // Load original image
    $image_info = pathinfo($file);
    $image_extension = strtolower($image_info['extension']);
    if ($image_extension == 'jpeg' || $image_extension == 'jpg') {
        $image = imagecreatefromjpeg($file);
    } elseif ($image_extension == 'png') {
        $image = imagecreatefrompng($file);
    } else {
        return false; // Unsupported format
    }

    // Copy and resize original image into blank image
    imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $resize_width, $resize_height, $orig_width, $orig_height);

    // Save resized image
    if ($image_extension == 'jpeg' || $image_extension == 'jpg') {
        imagejpeg($image_resized, $target_file, 85); // Save with quality 85%
    } elseif ($image_extension == 'png') {
        imagepng($image_resized, $target_file, 8);
    }

    // Free memory
    imagedestroy($image);
    imagedestroy($image_resized);

    return true;
}

// Function to fetch a random recipe with an image
function getRandomRecipe($conn) {
    $categories = ['Appetizer', 'Entree', 'Dessert'];
    $randomCategory = $categories[array_rand($categories)]; // Pick a random category
    $sql = "SELECT recipe_id, image_path FROM recipes WHERE category = '$randomCategory' AND image_path IS NOT NULL ORDER BY RAND() LIMIT 1";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
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
        if (in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
            // Resize and save the image
            if (resizeImage($_FILES['recipe_image']['tmp_name'], $target_file)) {
                // Save resized image path to the database
                $recipe_id = intval($_POST['recipe_id']);
                $sql = "UPDATE recipes SET image_path = '$target_file' WHERE recipe_id = $recipe_id";
                if ($conn->query($sql)) {
                    header("Location: index.php");
                    exit();
                } else {
                    $image_error = "Error saving image to database.";
                }
            } else {
                $image_error = "Failed to resize the image.";
            }
        } else {
            $image_error = "Invalid file type. Only JPG, JPEG, and PNG are allowed.";
        }
    } else {
        $image_error = "File upload error.";
    }
}

// Fetch a random recipe
$randomRecipe = getRandomRecipe($conn);

$conn->close();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Food Recipes Homepage</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        #container {
            display: grid;
            grid-template-areas:
                "header header"
                "aside1 main"
                "aside2 main"
                "aside3 main"
				"footer footer";
            grid-template-columns: 1fr 3fr;
            grid-template-rows: auto auto auto auto;
            gap: 10px;
            width: 90%;
            margin: 0 auto;
        }

        #header {
            grid-area: header;
            padding: 20px;
            text-align: center;
            background-color: #333;
            color: white;
            position: relative;
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

        #main {
            grid-area: main;
            min-height: 500px;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #85A5F5;
            color: black;
            border: 1px solid black;
            position: relative;
        }

        .recipe-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .recipe-image:hover {
            transform: scale(1.05);
        }

        .aside {
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
            background-color: #85A5F5;
            color: black;
            border: 1px solid black;
            cursor: pointer;
        }

        .aside:hover {
            background-color: #6e93ff;
        }

        #aside1 {
            grid-area: aside1;
        }

        #aside2 {
            grid-area: aside2;
        }

        #aside3 {
            grid-area: aside3;
        }

        footer {
            grid-area: footer;
            padding: 20px;
            text-align: center;
            background-color: #333;
            color: white;
            border: 1px solid black;
        }

        @media (max-width: 768px) {
            #container {
                grid-template-areas:
                    "header"
                    "main"
                    "aside1"
                    "aside2"
                    "aside3"
                    "footer";
                grid-template-columns: 1fr;
            }

            .recipe-image {
                width: 80%;
            }
        }
    </style>
</head>

<body>
    <div id="container">
        <!-- Header with Login Section -->
        <div id="header">
            Welcome to Food Recipes
            <div id="login-section">
                <?php
                if (isset($_SESSION['authenticated']) && $_SESSION['authenticated']) {
                    echo "<span>Welcome, " . htmlspecialchars($_SESSION['username']) . "</span> | <a href='logout.php'>Log Out</a>";
                } else {
                    echo "<a href='login.php'>Log In</a>";
                }
                ?>
            </div>
        </div>

        <!-- Main Content with a Random Image -->
        <div id="main">
            <?php if ($randomRecipe): ?>
                <img src="<?= htmlspecialchars($randomRecipe['image_path']) ?>" class="recipe-image" alt="Recipe Image" onclick="location.href='recipe_details.php?recipe_id=<?= $randomRecipe['recipe_id'] ?>';">
            <?php else: ?>
                <p>No images available for display.</p>
            <?php endif; ?>
        </div>

        <!-- Asides with Links to Categories -->
        <div id="aside1" class="aside" onclick="location.href='appetizers.php';"><strong>Appetizers</strong></div>
        <div id="aside2" class="aside" onclick="location.href='entrees.php';"><strong>Entrees</strong></div>
        <div id="aside3" class="aside" onclick="location.href='desserts.php';"><strong>Desserts</strong></div>

        <!-- Footer -->
        <footer>&copy; 2024 Food Recipes. All rights reserved.</footer>
    </div>
</body>

</html>
