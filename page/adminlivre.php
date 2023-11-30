<?php

session_start();

if (!isset($_SESSION["Id_admin"]) && empty($_SESSION["Id_admin"])) {
    header("Location: /loginadmin");
    exit();
}

$database = new Database($_ENV["DB_HOST"], $_ENV["DB_PORT"], $_ENV["DB_DATABASE"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);

$conn = $database->getConnection();

if (isset($parts[2]) && !empty($parts[2])) {

    $sql = "SELECT * 
            FROM Livres
            WHERE Id_Livre = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":id", $parts[2], PDO::PARAM_INT);
    $stmt->execute();

    $data = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }

    $sql = "DELETE FROM Livres WHERE Id_Livre = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":id", $parts[2], PDO::PARAM_INT);
    $stmt->execute();

    include("UpdateSpecificAuteur.php");
    UpdateSpecificAuteur($data[0]["Id_Auteur"], $database);
    header("Location: /adminlivre");
}

$sql = "SELECT * 
            FROM Livres
            JOIN Auteur ON Livres.Id_Auteur = Auteur.Id_Auteur
            JOIN Langue ON Livres.Id_Langue = Langue.Id_Langue
            ORDER BY Titre_Livre";

$stmt = $conn->prepare($sql);

$stmt->execute();

$data = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[] = $row;
}

// display_data($data);


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;1,100&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/style/home.css">
    <title>Admin livre page</title>
</head>

<style id="style_mod">

</style>

<body>
    <header>
        <div>
            <a class="signUp" id="log_out_admin" href="/logoutadmin">Se deconnecter</a>
        </div>
        <h1>Admin Livre</h1>
        <a href="/adminauteur">Modifier les auteurs</a>
    </header>



    <div class="all_livres">
        <a href="/newlivre">
            <h2>Ajouter un livre</h2>
            <img src="/assets/plus.svg" alt="">
        </a>
    </div>

    <div class="content">
        <?php
        foreach ($data as $key => $value) {
            echo "
        <a href='/modiflivre/$value[Id_Livre]'>
        <div class='book'>
            <h2>$value[Titre_Livre]</h2>
            <p class='lang'>$value[Acronyme]</p>
            <img src='data:image/png;base64," . base64_encode($value["Miniature"]) . "' alt=''>
            <p>de $value[Nom] pour $value[Prix]€</p>
            <button class='supprimer' data-id='$value[Id_Livre]'>Supprimer</button>
        </div>
        </a>";
        }

        ?>
    </div>

</body>

</html>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- <script type="module" src="/site/JS/script.js"></script>
<script type="module" src="/site/JS/home.js"></script> -->
<script type="module">
    // Sélectionnez tous les boutons "Supprimer" par leur classe
    const boutonsSupprimer = document.querySelectorAll('.supprimer');

    // Ajoutez un gestionnaire d'événements pour chaque bouton
    boutonsSupprimer.forEach(bouton => {
        bouton.addEventListener('click', (event) => {
            // Empêchez le comportement par défaut du bouton (pour empêcher la navigation vers le lien)
            event.preventDefault();

            // Récupérez l'ID du livre à supprimer à partir de l'attribut data-id
            const idLivre = bouton.getAttribute('data-id');

            // Affichez une boîte de dialogue de confirmation
            const confirmation = window.confirm("Êtes-vous sûr de vouloir supprimer ce livre ?");

            // Si l'utilisateur clique sur "OK" dans la boîte de dialogue, supprime 
            if (confirmation) {
                window.location.href = `/adminlivre/${idLivre}`;
            }
        });
    });
</script>