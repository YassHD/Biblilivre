<?php

session_start();

$parts = explode("/", $_SERVER["REQUEST_URI"]);

if (!isset($_SESSION["Id_client"]) || empty($_SESSION["Id_client"])) {
    header("Location: /home");
}

$database = new Database($_ENV["DB_HOST"], $_ENV["DB_PORT"], $_ENV["DB_DATABASE"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);

$conn = $database->getConnection();

$sql = "SELECT * FROM Commandes
        JOIN Adresse ON Commandes.Id_adresse = Adresse.Id_adresse
        JOIN Clients ON Commandes.Id_client = Clients.Id_client
        WHERE Commandes.Id_client = :id_client
        ORDER BY Commandes.date_heure_commande DESC";
$stmt = $conn->prepare($sql);
$stmt->bindValue(":id_client", htmlspecialchars($_SESSION["Id_client"]), PDO::PARAM_INT);
$stmt->execute();
$data = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[] = $row;
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Commande</title>
    <link rel="stylesheet" href="/style/commande.css">
</head>

<body>
    <a href="/panier"><img src="/assets/left_arrow.svg" class="come_back" alt=""></a>
    <main>
        <h1>Commande</h1>
        <div class="commandes">
            <?php if (empty($data)) { ?>
                <p>Vous n'avez pas encore passé de commande.</p>
                <a href="/panier">Retour au panier</a>
            <?php } else {
                foreach ($data as $key => $value) {
                    $sql = "SELECT * FROM Article_commande
                        JOIN Livres ON Article_commande.Id_Livre = Livres.Id_Livre
                        WHERE Id_commande = :id_commande";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindValue(":id_commande", $value["Id_commande"], PDO::PARAM_INT);
                    $stmt->execute();
                    $data_article = [];
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $data_article[] = $row;
                    }

                    ?>

                    <div class="commande__info">
                        <h2>Commande n°
                            <?= $value["Id_commande"] ?>
                        </h2>
                        <p>
                            <?= $value["date_heure_commande"] ?>
                        </p>
                        <p>
                            <?= $value["Prix_total"] ?>€
                        </p>
                    </div>
                    <div class="cmd_info_nd_liv">
                        <div class="commande__adresse">
                            <h2>Adresse de livraison</h2>
                            <p>
                                <?= $value["Nom"] . " " . $value["Prenom"] ?>
                            </p>
                            <p>
                                <?= $value["Adresse"] ?>
                            </p>
                            <p>
                                <?= $value["Code_postal"] . " " . $value["Ville"] ?>
                            </p>
                            <p>
                                <?= $value["Pays"] ?>
                            </p>
                        </div>

                        <div class="commande__articles">
                            <h2>Articles</h2>
                            <div class="all_article">
                                <?php foreach ($data_article as $key1 => $value1) { ?>
                                    <div class="commande__article">
                                        <img src='data:image/png;base64, <?php echo base64_encode($value1["Miniature"]) ?>' alt=''
                                            class='mini_back'>
                                        <div class="commande__article__info">
                                            <p>
                                                <?= $value1["Titre_Livre"] ?>
                                            </p>
                                            <p>
                                                <?= $value1["Prix"] ?>€
                                            </p>
                                            <p>Quantité :
                                                <?= $value1["Quantity_buy"] ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>


            <?php } ?>
        </div>
    </main>
</body>