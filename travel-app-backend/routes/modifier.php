
<?php
include "../config/db.php";

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(204);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "PUT") {
    http_response_code(405);
    echo json_encode(["message" => "Méthode non autorisée"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$id = $data["id_ville"] ?? null;
$nom = trim($data["nom"] ?? "");
$pay = trim($data["pays"] ?? "");
$des = $data["description"] ?? "";
$img = $data["image"] ?? "";

if (!$id || $nom === "" || $pay === "" || $des === ""|| $img === "") {
    http_response_code(400);
    echo json_encode(["message" => "Tous les champs sont obligatoir"]);
    exit;
}

$sql = "UPDATE villes
        SET nom = ?, pays = ?, description = ?, image = ?
        WHERE id_ville = ?";

$stmt = $cnx->prepare($sql);
$stmt->execute([$nom, $pay, $des, $img, $id]);

echo json_encode(["message" => "Ville modifiée avec succès"]);
?>
