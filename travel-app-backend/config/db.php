
<?php

$host = "localhost";
$dbname = "voyage";
$user = "root";
$pw = "";

try {
    $cnx = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pw
    );

    $cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $cnx->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur de connexion : " . $e->getMessage());

    http_response_code(500);
    exit("Erreur de connexion à la base de données.");
}
?>
