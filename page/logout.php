<?php
session_start();
if (isset($_SESSION["Id_client"]) && !empty($_SESSION["Id_client"])) {
    session_destroy();
    $_SESSION["Id_client"] = "";
    $_SESSION["Nom"] = "";
    $_SESSION["Prenom"] = "";
    $_SESSION["Email"] = "";
    $_SESSION["Password"] = "";
    $_SESSION["Num_tel"] = "";
    header("Location: /home");
} else {
    header("Location: /home");
}
