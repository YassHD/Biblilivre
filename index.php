<?php

declare(strict_types=1);

require_once realpath(__DIR__ . '/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

spl_autoload_register(function ($class) {
    require __DIR__ . "/src/$class.php";
});

$parts = explode("/", $_SERVER["REQUEST_URI"]);

// gestion des redirections

if ($parts[1] == "home") {
    include("page/home.php");
} elseif ($parts[1] == "livre") {
    include("page/livre.php");
} elseif ($parts[1] == "UpdateDB") {
    include("UpdateDB.php");
} elseif ($parts[1] == "newlivre") {
    include("page/newlivre.php");
} elseif ($parts[1] == "newauteur") {
    include("page/newauteur.php");
} elseif ($parts[1] == "adminlivre") {
    include("page/adminlivre.php");
} elseif ($parts[1] == "adminauteur") {
    include("page/adminauteur.php");
} elseif ($parts[1] == "modiflivre") {
    include("page/modiflivre.php");
} elseif ($parts[1] == "modifauteur") {
    include("page/modifauteur.php");
} elseif ($parts[1] == "signUp") {
    include("page/signUp.php");
} elseif ($parts[1] == "login") {
    include("page/login.php");
} elseif ($parts[1] == "logout") {
    include("page/logout.php");
} elseif ($parts[1] == "loginadmin") {
    include("page/loginadmin.php");
} elseif ($parts[1] == "logoutadmin") {
    include("page/logoutadmin.php");
} elseif ($parts[1] == "genres") {
    include("page/genres.php");
} elseif ($parts[1] == "livres") {
    include("page/all_livre.php");
} elseif ($parts[1] == "auteur") {
    include("page/auteur.php");
} elseif ($parts[1] == "types") {
    include("page/types.php");
} elseif ($parts[1] == "parametre") {
    include("page/parametre.php");
} elseif ($parts[1] == "panier") {
    include("page/panier.php");
} elseif ($parts[1] == "langues") {
    include("page/langues.php");
} elseif ($parts[1] == "Adresse") {
    include("page/Adresse_liv.php");
} elseif ($parts[1] == "Add_adresse") {
    include("page/Add_adresse.php");
} elseif ($parts[1] == "Edit_adresse") {
    include("page/Edit_adresse.php");
} elseif ($parts[1] == "checkout") {
    include("page/checkout.php");
} elseif ($parts[1] == "commande") {
    include("page/commande.php");
} else {
    header("Location: /home");
}
