<?php

session_start();

$database = new Database($_ENV["DB_HOST"], $_ENV["DB_PORT"], $_ENV["DB_DATABASE"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);

$conn = $database->getConnection();

$parts = explode("/", $_SERVER["REQUEST_URI"]);

if (isset($parts[2]) && $parts[2] == "genre" && isset($parts[3]) && !empty($parts[3])) {

    $sql = "SELECT * 
            FROM Livres
            JOIN Auteur ON Livres.Id_Auteur = Auteur.Id_Auteur
            JOIN Langue ON Livres.Id_Langue = Langue.Id_Langue
            JOIN Genre ON Livres.Id_Genre = Genre.Id_Genre
            WHERE Genre.Id_Genre = :id_genre
            ORDER BY Titre_Livre";

    $stmt = $conn->prepare($sql);

    $stmt->bindValue(":id_genre", $parts[3], PDO::PARAM_INT);

    $stmt->execute();

    $data = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }

    $sql = "SELECT * FROM Genre WHERE Id_Genre = :id_genre";

    $stmt = $conn->prepare($sql);

    $stmt->bindValue(":id_genre", $parts[3], PDO::PARAM_INT);

    $stmt->execute();

    $data_genre = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data_genre[] = $row;
    }


    $data_genre = $data_genre[0]["Titre_Genre"];
} else if (isset($parts[2]) && $parts[2] == "auteur" && isset($parts[3]) && !empty($parts[3])) {
    $sql = "SELECT * 
            FROM Livres
            JOIN Auteur ON Livres.Id_Auteur = Auteur.Id_Auteur
            JOIN Langue ON Livres.Id_Langue = Langue.Id_Langue
            JOIN Genre ON Livres.Id_Genre = Genre.Id_Genre
            WHERE Livres.Id_Auteur = :id_auteur
            ORDER BY Titre_Livre";

    $stmt = $conn->prepare($sql);

    $stmt->bindValue(":id_auteur", $parts[3], PDO::PARAM_INT);

    $stmt->execute();

    $data = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }

    $sql = "SELECT * FROM Auteur WHERE Id_Auteur = :id_auteur";

    $stmt = $conn->prepare($sql);

    $stmt->bindValue(":id_auteur", $parts[3], PDO::PARAM_INT);

    $stmt->execute();

    $data_auteur = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data_auteur[] = $row;
    }

    $data_auteur = $data_auteur[0]["Nom"];
} else if (isset($parts[2]) && $parts[2] == "langue" && isset($parts[3]) && !empty($parts[3])) {
    $sql = "SELECT * 
            FROM Livres
            JOIN Auteur ON Livres.Id_Auteur = Auteur.Id_Auteur
            JOIN Langue ON Livres.Id_Langue = Langue.Id_Langue
            JOIN Genre ON Livres.Id_Genre = Genre.Id_Genre
            WHERE Langue.Id_Langue = :id_langue
            ORDER BY Titre_Livre";

    $stmt = $conn->prepare($sql);

    $stmt->bindValue(":id_langue", $parts[3], PDO::PARAM_INT);

    $stmt->execute();

    $data = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }

    $sql = "SELECT * FROM Langue WHERE Id_Langue = :id_langue";

    $stmt = $conn->prepare($sql);

    $stmt->bindValue(":id_langue", $parts[3], PDO::PARAM_INT);

    $stmt->execute();

    $data_langue = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data_langue[] = $row;
    }

    $data_langue = $data_langue[0]["Language"];
} else if (isset($parts[2]) && $parts[2] == "type" && isset($parts[3]) && !empty($parts[3])) {
    $sql = "SELECT * 
            FROM Livres
            JOIN Auteur ON Livres.Id_Auteur = Auteur.Id_Auteur
            JOIN Langue ON Livres.Id_Langue = Langue.Id_Langue
            JOIN Genre ON Livres.Id_Genre = Genre.Id_Genre
            WHERE Livres.Id_Types = :id_type
            ORDER BY Titre_Livre";

    $stmt = $conn->prepare($sql);

    $stmt->bindValue(":id_type", $parts[3], PDO::PARAM_INT);

    $stmt->execute();

    $data = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }

    $sql = "SELECT * FROM Types WHERE Id_Types = :id_type";

    $stmt = $conn->prepare($sql);

    $stmt->bindValue(":id_type", $parts[3], PDO::PARAM_INT);

    $stmt->execute();

    $data_type = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data_type[] = $row;
    }

    $data_type = $data_type[0]["Types"];
} else {

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
}

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
    <link rel="stylesheet" href="/style/all_livres.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700&display=swap" rel="stylesheet">
    <title>all livres page</title>
</head>


<body>
    <header>
        <?php
        if (isset($_SESSION["Id_client"]) && !empty($_SESSION["Id_client"])) {
            ?>
            <div class="div_icon_profil">
                <p>
                    <?php echo strtoupper($_SESSION["Prenom"][0]) ?>
                </p>
                <a class="logout" href="/logout">Se deconnecter</a>
            </div>
            <?php
        } else {
            ?>
            <div class="logs">
                <div class="div_MeConnecter">
                    <h4 id="MeConnecter" onclick="log()">Me connecter</h4>
                    <p id="chevron">></p>
                </div>
                <a class="signUp" id="SignUp" href="/signUp">S'inscrire</a>
                <a class="login" id="LogIn" href="/login">Se connecter</a>
            </div>
            <?php
        }
        ?>
        <div class="name_page">
            <h2>livres</h2>
        </div>
        <?php
        if (isset($_SESSION["Id_client"]) && !empty($_SESSION["Id_client"])) {
            ?>
            <div class="icon_settings">
                <img src="/assets\settings.svg" alt="settings" onclick="setting()">
                <a id="I_compte" href="/parametre">Info compte</a>
                <a id="addr_liv" href="/Adresse">Info livraison</a>
                <a id="command" href="/commande">Mes commandes</a>
            </div>
            <?php
        } else {
            ?>
            <div class="icon_settings" onclick="alert('Connectez vous pour accéder au paramètre'),show_logs()">
                <img src="/assets\settings.svg" alt="settings">
            </div>
            <?php
        }
        ?>
    </header>
    <nav>
        <ul>
            <li><a href="/home" id='current_Page'>Accueil</a></li>
            <!-- <div class="underline"></div> -->
            <li><a href="/genres">Genres</a></li>
            <li><a href="/livres">Livres</a></li>
            <li><a href="/auteur">Auteur</a></li>
            <li><a href="/types">Types</a></li>
            <li><a href="/langues">Langues</a></li>
        </ul>
    </nav>

    <div class="all_books">
        <?php
        if (isset($parts[2]) && $parts[2] == "genre" && isset($parts[3]) && !empty($parts[3])) {
            echo "<h1>Tous les livres du genre : " . $data_genre . "</h1>";
        } else if (isset($parts[2]) && $parts[2] == "auteur" && isset($parts[3]) && !empty($parts[3])) {
            echo "<h1>Auteur : $data_auteur</h1>";
        } else if (isset($parts[2]) && $parts[2] == "langue" && isset($parts[3]) && !empty($parts[3])) {
            echo "<h1>Langue : $data_langue</h1>";
        } else if (isset($parts[2]) && $parts[2] == "type" && isset($parts[3]) && !empty($parts[3])) {
            echo "<h1>Type : $data_type</h1>";
        } else {

        }
        if (empty($data)) {
            echo "<h2>Aucun livre trouvé</h2>";
        } else {
            foreach ($data as $key => $value) {
                echo "
        <a href='/livre/$value[Id_Livre]'>
        <div class='book'>

            <img src='data:image/png;base64," . base64_encode($value["Miniature"]) . "' alt=''>
            
        </div>
        </a>";
            }
        }

        ?>
    </div>
</body>
<script src="/script/menu_log.js"></script>

</html>