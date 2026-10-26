
<?php
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

include "../config/db.php";

$nom = trim($_POST["nom"] ?? "");
$pay = trim($_POST["pays"] ?? "");
$des = trim($_POST["description"] ?? "");

if ($nom === "" || $pay === "" || $des === "" ||
    !isset($_FILES["image"]) ||
    $_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(["message" => "Tous les champs sont obligatoires"]);
    exit;
}

$image = $_FILES["image"];

$finfo = new finfo(FILEINFO_MIME_TYPE);
$type = $finfo->file($image["tmp_name"]);

$extensions = [
    "image/jpeg" => "jpg",
    "image/png"  => "png",
    "image/webp" => "webp",
    "image/gif"  => "gif"
];

if (!isset($extensions[$type])) {
    http_response_code(400);
    echo json_encode(["message" => "Format d'image non autorisé"]);
    exit;
}

if ($image["size"] > 5 * 1024 * 1024) {
    http_response_code(400);
    echo json_encode(["message" => "L'image doit être inférieure à 5 Mo"]);
    exit;
}

$dossier = __DIR__ . "/../uploads/";

if (!is_dir($dossier) &&
    !mkdir($dossier, 0755, true)) {
    http_response_code(500);
    echo json_encode(["message" => "Impossible de créer le dossier uploads"]);
    exit;
}

$nomImage = bin2hex(random_bytes(16)) . "." . $extensions[$type];
$destination = $dossier . $nomImage;

if (!move_uploaded_file($image["tmp_name"], $destination)) {
    http_response_code(500);
    echo json_encode(["message" => "Erreur lors de la sauvegarde de l'image"]);
    exit;
}

$cheminImage = "uploads/" . $nomImage;

try {
    $sql = "INSERT INTO villes (nom, pays, description, image)
            VALUES (?, ?, ?, ?)";

    $stmt = $cnx->prepare($sql);
    $stmt->execute([$nom, $pay, $des, $cheminImage]);

    $ville = [
        "id" => $cnx->lastInsertId(),
        "nom" => $nom,
        "pays" => $pay,
        "description" => $des,
        "image" => $cheminImage
    ];

    echo json_encode([
        "message" => "Ville ajoutée avec succès",
        "ville" => $ville
    ]);
} catch (Exception $e) {
    if (file_exists($destination)) {
        unlink($destination);
    }

    http_response_code(500);
    echo json_encode(["message" => "Erreur lors de l'ajout"]);
}
?>
