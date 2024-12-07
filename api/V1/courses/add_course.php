<?php
 
require_once __DIR__ . '/../../database/config.php';
require_once __DIR__ . '/../../cors/cors.php';
 
$db = new Database();
$conn = $db->getConnection();
 
if ($conn === null) {
    echo json_encode(["error" => "Failed to connect to the database."]);
    die();
}

 
$name = isset($_POST['name']) ? $_POST['name'] : null;
$description = isset($_POST['description']) ? $_POST['description'] : null;
$category_id = isset($_POST['category_id']) ? $_POST['category_id'] : null;
$image = isset($_FILES['image']) ? $_FILES['image'] : null;

 
if (empty($name) || empty($description) || empty($category_id)) {
    echo json_encode(["error" => "Course name, description, and category  are required."]);
    die();
}

 
if (strlen($name) < 3) {
    echo json_encode(["error" => "Course name must be at least 3 characters long."]);
    die();
}


// Image upload validation
$imagePath = null;
if ($image) {
    $allowedFileTypes = ['image/jpeg', 'image/png', 'image/gif'];  
    $maxFileSize = 5 * 1024 * 1024;  

   
    if (!in_array($image['type'], $allowedFileTypes)) {
        echo json_encode(["error" => "Only JPG, PNG, and GIF images are allowed."]);
        die();
    }

    
    if ($image['size'] > $maxFileSize) {
        echo json_encode(["error" => "Image file size should not exceed 5MB."]);
        die();
    }

    
    $imageName = time() . '-' . basename($image['name']);
    $targetDirectory = __DIR__ . '/../../uploads/';
    $targetFile = $targetDirectory . $imageName;

    if (!move_uploaded_file($image['tmp_name'], $targetFile)) {
        echo json_encode(["error" => "Failed to upload image."]);
        die();
    }

     
    $imagePath = '/uploads/' . $imageName;
}

 
$query = "INSERT INTO courses (name, description, category_id, image_path) VALUES (:name, :description, :category_id, :image_path)";

try {
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':description', $description, PDO::PARAM_STR);
    $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
    $stmt->bindParam(':image_path', $imagePath, PDO::PARAM_STR); 

 
    if ($stmt->execute()) {
        echo json_encode(["success" => "Course inserted successfully."]);
    } else {
        echo json_encode(["error" => "Failed to insert course."]);
    }
} catch (PDOException $e) {
  
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
