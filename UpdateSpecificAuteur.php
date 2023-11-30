<?php

function UpdateSpecificAuteur($id_auteur, $database)
{
    $conn = $database->getConnection();

    $sql = "UPDATE Auteur
            SET Nombres_Oeuvres = (SELECT COUNT(*) FROM Livres WHERE Livres.Id_Auteur = $id_auteur),
            Moyenne_Prix = (SELECT IFNULL(ROUND(AVG(Livres.Prix),2),0) FROM Livres WHERE Livres.Id_Auteur = $id_auteur)
            WHERE Id_Auteur = $id_auteur";

    $stmt = $conn->prepare($sql);

    $stmt->execute();

}