
<?php
include "../config/db.php";

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(204);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["message" => "Méthode non autorisée"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$nom = $data["nom"] ?? "";
$pay = $data["pays"] ?? "";
$des = $data["description"] ?? "";
$img = $data["image"] ?? "";

if ($nom === "" || $pay === "" || $des === "" || $img === "") {
    http_response_code(400);
    echo json_encode(["message" => "ous les champs sont obligatoires"]);
    exit;
}

$sql = "INSERT INTO villes (nom, pays, description, image)
        VALUES (?, ?, ?, ?)";

$stmt = $cnx->prepare($sql);
$success = $stmt->execute([$nom, $pay, $des, $img]);

if ($success) {
    echo json_encode(["message" => "Ville ajoutée avec succès"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Erreur lors de l'ajout"]);
   
}
?>
