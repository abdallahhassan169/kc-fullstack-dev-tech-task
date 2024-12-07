<?php
 
require_once __DIR__ . '/../../database/config.php';
require_once __DIR__ . '/../../cors/cors.php';
 
$db = new Database();
$conn = $db->getConnection();

if ($conn === null) {
    echo json_encode(["error" => "Failed to connect to the database."]);
    die();
}
$categoryId = isset($_GET['categoryId']) ? $_GET['categoryId'] : null;

 
$query = "
    SELECT 
  c.* , cat.name as category
    FROM 
        courses c
    JOIN 
        categories cat ON c.category_id = cat.id
    WHERE 
        (:categoryId IS NULL OR c.category_id = :categoryId)
";

$stmt = $conn->prepare($query);
 
$stmt->bindParam(':categoryId', $categoryId, PDO::PARAM_INT);

 
$stmt->execute();

 
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

 
if ($courses) {
    echo json_encode($courses);
} else {
    echo json_encode([]);
}