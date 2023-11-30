<?php

declare(strict_types=1);

require_once realpath(__DIR__ . '/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

spl_autoload_register(function ($class) {
    require __DIR__ . "/src/$class.php";
});

include("src/DisplayData.php");

$database = new Database($_ENV["DB_HOST"], $_ENV["DB_PORT"], $_ENV["DB_DATABASE"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);

updateAuteurNbLivre($database);

function updateAuteurNbLivre($database)
{
    $conn = $database->getConnection();

    $sql = "SELECT * 
            FROM Auteur";

    $stmt = $conn->prepare($sql);

    $stmt->execute();

    $data = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }

    foreach ($data as $key => $value) {
        $sql = "UPDATE Auteur
                SET Nombres_Oeuvres = (SELECT COUNT(*) FROM Livres WHERE Livres.Id_Auteur = $value[Id_Auteur]),
                Moyenne_Prix = (SELECT IFNULL(ROUND(AVG(Livres.Prix),2),0) FROM Livres WHERE Livres.Id_Auteur = $value[Id_Auteur])
                WHERE Id_Auteur = $value[Id_Auteur]";

        $stmt = $conn->prepare($sql);

        $stmt->execute();
    }

}
