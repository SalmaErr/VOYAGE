
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

$data = json_decode(file_get_contents("php://input"), true);

$email = trim($data["email"] ?? "");
$mp = $data["mp"] ?? "";

if ($email === "" || $mp === "") {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Email et mot de passe obligatoires."
    ]);
    exit;
}

try {
    // Chercher d'abord dans la table admin
    $req = $cnx->prepare(
        "SELECT id_admin, nom, email, mp FROM admin WHERE email = ?"
    );
    $req->execute([$email]);
    $compte = $req->fetch();

    if ($compte && password_verify($mp, $compte["mp"])) {
        session_regenerate_id(true);

        $_SESSION["id"] = $compte["id_admin"];
        $_SESSION["nom"] = $compte["nom"];
        $_SESSION["email"] = $compte["email"];
        $_SESSION["role"] = "admin";

        echo json_encode([
            "success" => true,
            "message" => "Connexion réussie.",
            "role" => "admin",
            "nom" => $compte["nom"]
        ]);
        exit;
    }

    // Sinon, chercher dans la table users
    $req = $cnx->prepare(
        "SELECT id_user, nom, email, mp FROM users WHERE email = ?"
    );
    $req->execute([$email]);
    $compte = $req->fetch();

    if ($compte && password_verify($mp, $compte["mp"])) {
        session_regenerate_id(true);

        $_SESSION["id"] = $compte["id_user"];
        $_SESSION["nom"] = $compte["nom"];
        $_SESSION["email"] = $compte["email"];
        $_SESSION["role"] = "user";

        echo json_encode([
            "success" => true,
            "message" => "Connexion réussie.",
            "role" => "user",
            "nom" => $compte["nom"]
        ]);
        exit;
    }

    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Email ou mot de passe incorrect."
    ]);
} catch (PDOException $e) {
    error_log($e->getMessage());

    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Une erreur est survenue lors de la connexion."
    ]);
}
