
<?php
session_start();

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    exit;
}

if (!isset($_SESSION["id"], $_SESSION["role"])) {
    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "Vous devez vous connecter."
    ]);
    exit;
}

echo json_encode([
    "success" => true,
    "user" => [
        "id" => $_SESSION["id"],
        "nom" => $_SESSION["nom"],
        "email" => $_SESSION["email"],
        "role" => $_SESSION["role"]
    ]
]);
