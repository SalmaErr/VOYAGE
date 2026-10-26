<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");


if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(204);
    exit;
}

include "../config/db.php";

$sql = "SELECT * FROM villes";

$stmt = $cnx->query($sql);
$stmt = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($stmt);
?>

