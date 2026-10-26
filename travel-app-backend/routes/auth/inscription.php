
<?php
session_start();

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    exit;
}

require_once __DIR__ . "/../../config/db.php";

// Récupérer les données envoyées par React
$data = json_decode(file_get_contents("php://input"), true);

$nom = trim($data["nom"] ?? "");
$email = trim($data["email"] ?? "");
$mp = $data["mp"] ?? "";

// Vérifier les champs
if ($nom === "" || $email === "" || $mp === "") {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Tous les champs sont obligatoires."
    ]);
    exit;
}

// Vérifier l'email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Adresse email invalide."
    ]);
    exit;
}

// Vérifier le mot de passe
if (strlen($mp) < 8) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Le mot de passe doit contenir au moins 8 caractères."
    ]);
    exit;
}

try {
    // Vérifier si l'email existe déjà
    $check = $cnx->prepare(
        "SELECT id_user FROM users WHERE email = ?"
    );
    $check->execute([$email]);

    if ($check->fetch()) {
        http_response_code(409);
        echo json_encode([
            "success" => false,
            "message" => "Cette adresse email est déjà utilisée."
        ]);
        exit;
    }

    // Protéger le mot de passe
    $passwordHash = password_hash($mp, PASSWORD_DEFAULT);

    // Ajouter le nouvel utilisateur
    $insert = $cnx->prepare(
        "INSERT INTO users (nom, email, mp) VALUES (?, ?, ?)"
    );
    $insert->execute([$nom, $email, $passwordHash]);

    echo json_encode([
        "success" => true,
        "message" => "Inscription réussie."
    ]);
} catch (PDOException $e) {
    error_log($e->getMessage());

    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Une erreur est survenue lors de l'inscription."
    ]);
}
