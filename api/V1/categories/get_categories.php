<?php
 
require_once __DIR__ . '/../../database/config.php';
require_once __DIR__ . '/../../cors/cors.php';
 
$db = new Database();
$conn = $db->getConnection();

if ($conn === null) {
    echo json_encode(["error" => "Failed to connect to the database."]);
    die();
}
$parentId = isset($_GET['parentId']) ? $_GET['parentId'] : null;

 
$query = "
   SELECT 
        c.* , (select count(id) from courses where category_id =c.id) count
    FROM 
        categories c
    WHERE 
        (parent_id IS NULL AND :parentId IS NULL) OR (parent_id = :parentId)
";

$stmt = $conn->prepare($query);
 
$stmt->bindParam(':parentId', $parentId, PDO::PARAM_INT);

 
$stmt->execute();

 
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

 
if ($courses) {
    echo json_encode($courses);
} else {
   echo json_encode([]);
}