
<?php
include "../config/db.php";

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(204);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "DELETE") {
    http_response_code(405);
    echo json_encode(["message" => "Méthode non autorisée"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$id = $data["id_ville"] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(["message" => "ID de la ville obligatoire"]);
    exit;
}

$sql = "DELETE FROM villes WHERE id_ville = ?";
$stmt = $cnx->prepare($sql);
$stmt->execute([$id]);

echo json_encode(["message" => "Ville supprimée avec succès"]);
?>
