<?php

session_start();

$parts = explode("/", $_SERVER["REQUEST_URI"]);

if (!isset($_SESSION["Id_client"]) || empty($_SESSION["Id_client"])) {
    header("Location: /home");
}

$database = new Database($_ENV["DB_HOST"], $_ENV["DB_PORT"], $_ENV["DB_DATABASE"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);

$conn = $database->getConnection();

if (isset($parts[2]) && !empty($parts[2])) {
    $id_adresse = htmlspecialchars($parts[2]);

    $sql = "SELECT * FROM Adresse WHERE Id_adresse = :id_adresse AND Id_client = :id_client";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":id_adresse", $id_adresse, PDO::PARAM_INT);
    $stmt->bindValue(":id_client", htmlspecialchars($_SESSION["Id_client"]), PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    if (empty($data)) {
        header("Location: /Adresse");
        exit();
    } else {
        $sql = "SELECT * FROM Article_panier 
        JOIN Livres ON Article_panier.Id_Livre = Livres.Id_Livre
        WHERE Id_client = :id_client";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_client", htmlspecialchars($_SESSION["Id_client"]), PDO::PARAM_INT);
        $stmt->execute();
        $data_panier = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data_panier[] = $row;
        }

        $total_prix = 0;
        foreach ($data_panier as $key => $value) {
            $total_prix += $value["quantity"] * $value["Prix"];
        }

        $sql = "INSERT INTO Commandes (Id_client, Id_adresse, date_heure_commande, Prix_total) VALUES (:id_client, :id_adresse, NOW(), :prix_total)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_client", htmlspecialchars($_SESSION["Id_client"]), PDO::PARAM_INT);
        $stmt->bindValue(":id_adresse", $id_adresse, PDO::PARAM_INT);
        $stmt->bindValue(":prix_total", $total_prix, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $conn->prepare("SELECT LAST_INSERT_ID()");
        $stmt->execute();
        $id_commande = $stmt->fetch(PDO::FETCH_ASSOC)["LAST_INSERT_ID()"];

        foreach ($data_panier as $key => $value) {
            $sql = "INSERT INTO Article_commande (Id_commande, Id_Livre, Quantity_buy) VALUES (:id_commande, :id_livre, :quantite)";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(":id_commande", $id_commande, PDO::PARAM_INT);
            $stmt->bindValue(":id_livre", $value["Id_livre"], PDO::PARAM_INT);
            $stmt->bindValue(":quantite", $value["quantity"], PDO::PARAM_INT);
            $stmt->execute();
        }

        $sql = "DELETE FROM Article_panier WHERE Id_client = :id_client";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_client", htmlspecialchars($_SESSION["Id_client"]), PDO::PARAM_INT);
        $stmt->execute();

        header("Location: /commande");
        exit();
    }
}



$sql = "SELECT * FROM Adresse WHERE Id_client = :id_client";
$stmt = $conn->prepare($sql);
$stmt->bindValue(":id_client", htmlspecialchars($_SESSION["Id_client"]), PDO::PARAM_INT);
$stmt->execute();

$data = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data[] = $row;
}

$sql = "SELECT * 
        FROM Article_panier
        JOIN Livres ON Article_panier.Id_Livre = Livres.Id_Livre
        JOIN Auteur ON Livres.Id_Auteur = Auteur.Id_Auteur
        JOIN Langue ON Livres.Id_Langue = Langue.Id_Langue            
        WHERE Id_client = :id_client";
$stmt = $conn->prepare($sql);
$stmt->bindValue(":id_client", htmlspecialchars($_SESSION["Id_client"]), PDO::PARAM_INT);
$stmt->execute();
$data_panier = [];
$total_article = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $total_article += $row["quantity"];
    $data_panier[] = $row;
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/style/checkout.css">
    <title>Checkout</title>
</head>

<body>
    <header>
        <a href="/panier"><img src="/assets/left_arrow.svg" class="come_back" alt=""></a>
        <h2>Checkout</h2>
    </header>
    <article>
        <div class="info">
            <h3>Informations personnelles</h3>
            <p>Nom :
                <?= $_SESSION["Nom"] ?>
            </p>
            <p>Prénom :
                <?= $_SESSION["Prenom"] ?>
            </p>
            <p>Email :
                <?= $_SESSION["Email"] ?>
            </p>
            <p>Numéro de téléphone :
                <?= $_SESSION["Num_tel"] ?>
            </p>
            <a href="/parametre">Modifier mes informations</a>
        </div>
        <div class="address_liv">
            <h3>Adresse de livraison</h3>
            <?php
            if (empty($data)) {
                ?>
                <p>Vous n'avez pas encore d'adresse de livraison</p>
                <a href="/Adresse">Ajouter une adresses</a>
                <?php
            } else {
                ?>
                <select name="adresse" id="adresse">
                    <?php

                    foreach ($data as $key => $value) {
                        ?>
                        <option value="<?php echo $value["Id_adresse"] ?>">
                            <?php echo $value["Adresse"] . " " . $value["Complement"] . " " . $value["Ville"] . " " . $value["Code_postal"] . " " . $value["Pays"] ?>
                        </option>
                        <?php
                    }

                    ?>

                </select>
                <a href="/Adresse">Modifier mes adresses</a>

                <?php
            }
            ?>
        </div>
    </article>
    <main>


        <div>
            <h3>Résumer de la commande</h3>
            <div class="all_books">
                <?php
                foreach ($data_panier as $key => $value) {
                    ?>
                    <div class="book">
                        <img src="data:image/png;base64,<?= base64_encode($value["Miniature"]) ?>" alt="">
                        <div class="info_book">
                            <strong>
                                <p>
                                    <?= $value["Titre_Livre"] ?>
                                </p>
                            </strong>
                            <p>
                                <?= $value["Nom"] ?>
                            </p>
                            <p>
                                <?= $value["Language"] ?>
                            </p>
                            <strong>
                                <p>
                                    <?= $value["Prix"] ?>€
                                </p>
                            </strong>
                            <p>Quantité :
                                <?= $value["quantity"] ?>
                            </p>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <button id="commander">Valider la commande</button>

    </main>
</body>

</html>

<script>
    document.getElementById("commander").addEventListener("click", function () {
        let adresse = document.getElementById("adresse").value;
        window.location.href = "/checkout/" + adresse;
    })
</script>