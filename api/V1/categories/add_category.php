<?php
 
require_once __DIR__ . '/../../database/config.php';
require_once __DIR__ . '/../../cors/cors.php';
 
$db = new Database();
$conn = $db->getConnection();

 
if ($conn === null) {
    echo json_encode(["error" => "Failed to connect to the database."]);
    die();
}

 
$categoryName = isset($_POST['name']) ? $_POST['name'] : '';
$parentId = isset($_POST['parent_id']) ? $_POST['parent_id'] : null;
 

if (empty($categoryName)   ) {
    echo json_encode(["error" => "Invalid input. Please provide category name "]);
    die();
}

// here i insured that the parent level < 4 and i made it in only one query
$query = "
INSERT INTO categories (name, parent_id, level)
SELECT 
    :name, 
    :parentId, 
    CASE WHEN :parentId IS NULL THEN 1 
        ELSE COALESCE((SELECT level + 1 FROM categories WHERE id = :parentId LIMIT 1), 1)
    END
WHERE 
    :parentId IS NULL OR (SELECT level FROM categories WHERE id = :parentId) < 4;

";

$stmt = $conn->prepare($query);
 
$stmt->bindParam(':name', $categoryName, PDO::PARAM_STR);
$stmt->bindParam(':parentId', $parentId, PDO::PARAM_INT);
 

 
if ($stmt->execute()) {
    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => "Category inserted successfully."]);
    } else {
        echo json_encode(["error" => "the depth can't be (>= 4)."]);
    }
} else {
    echo json_encode(["error" => "Failed to insert the category."]);
}
?>
