<?php
session_start();
if (isset($_SESSION["Id_admin"]) && !empty($_SESSION["Id_admin"])) {
    session_destroy();
    $_SESSION["Id_admin"] = "";
    $_SESSION["Email_admin"] = "";
    $_SESSION["Password_admin"] = "";
    header("Location: /loginadmin");
} else {
    header("Location: /loginadmin");
}
