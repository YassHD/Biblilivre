<?php

session_start();

if (!isset($_SESSION["Id_client"]) || empty($_SESSION["Id_client"])) {
    header("Location: /home");
}

$database = new Database($_ENV["DB_HOST"], $_ENV["DB_PORT"], $_ENV["DB_DATABASE"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);

$conn = $database->getConnection();

$parts = explode("/", $_SERVER["REQUEST_URI"]);

if ($parts[1] == "panier" && isset($parts[2]) && !empty($parts[2])  && $parts[2] == "movetokart" && isset($parts[3]) && !empty($parts[3])) {

    $sql = "SELECT * FROM Article_souhait WHERE Id_livre = :id_livre && Id_client = :id_client";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":id_livre", htmlspecialchars($parts[3]), PDO::PARAM_INT);
    $stmt->bindValue(":id_client", htmlspecialchars($_SESSION["Id_client"]), PDO::PARAM_INT);
    $stmt->execute();

    $data = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }

    if (!empty($data)) {
        $sql = "DELETE FROM Article_souhait WHERE Id_livre = :id_livre && Id_client = :id_client";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_livre", htmlspecialchars($parts[3]), PDO::PARAM_INT);
        $stmt->bindValue(":id_client", htmlspecialchars($_SESSION["Id_client"]), PDO::PARAM_INT);
        $stmt->execute();
    }

    $sql = "SELECT * FROM Article_panier WHERE Id_livre = :id_livre && Id_client = :id_client";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":id_livre", htmlspecialchars($parts[3]), PDO::PARAM_INT);
    $stmt->bindValue(":id_client", htmlspecialchars($_SESSION["Id_client"]), PDO::PARAM_INT);
    $stmt->execute();

    $data = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }

    if (empty($data)) {
        $sql = "INSERT INTO Article_panier (Id_livre, Id_client, quantity) VALUES (:id_livre, :id_client, 1)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_livre", htmlspecialchars($parts[3]), PDO::PARAM_INT);
        $stmt->bindValue(":id_client", htmlspecialchars($_SESSION["Id_client"]), PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $sql = "UPDATE Article_panier SET quantity = quantity + 1 WHERE Id_livre = :id_livre && Id_client = :id_client";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_livre", htmlspecialchars($parts[3]), PDO::PARAM_INT);
        $stmt->bindValue(":id_client", htmlspecialchars($_SESSION["Id_client"]), PDO::PARAM_INT);
        $stmt->execute();
    }

    header("Location: /panier");
} else if ($parts[1] == "panier" && isset($parts[2]) && !empty($parts[2]) && $parts[2] == "movetosouhait" && isset($parts[3]) && !empty($parts[3])) {
    $sql = "SELECT * FROM Article_panier WHERE Id_livre = :id_livre && Id_client = :id_client";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":id_livre", htmlspecialchars($parts[3]), PDO::PARAM_INT);
    $stmt->bindValue(":id_client", htmlspecialchars($_SESSION["Id_client"]), PDO::PARAM_INT);
    $stmt->execute();

    $data = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }

    if (!empty($data)) {
        $sql = "DELETE FROM Article_panier WHERE Id_livre = :id_livre && Id_client = :id_client";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_livre", htmlspecialchars($parts[3]), PDO::PARAM_INT);
        $stmt->bindValue(":id_client", htmlspecialchars($_SESSION["Id_client"]), PDO::PARAM_INT);
        $stmt->execute();
    }

    $sql = "SELECT * FROM Article_souhait WHERE Id_livre = :id_livre && Id_client = :id_client";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":id_livre", htmlspecialchars($parts[3]), PDO::PARAM_INT);
    $stmt->bindValue(":id_client", htmlspecialchars($_SESSION["Id_client"]), PDO::PARAM_INT);
    $stmt->execute();

    $data = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }

    if (empty($data)) {
        $sql = "INSERT INTO Article_souhait (Id_livre, Id_client) VALUES (:id_livre, :id_client)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_livre", htmlspecialchars($parts[3]), PDO::PARAM_INT);
        $stmt->bindValue(":id_client", htmlspecialchars($_SESSION["Id_client"]), PDO::PARAM_INT);
        $stmt->execute();
    }

    header("Location: /panier");
} else if ($parts[1] == "panier" && isset($parts[2]) && !empty($parts[2]) && $parts[2] == "removefromcart" && isset($parts[3]) && !empty($parts[3])) {
    $sql = "DELETE FROM Article_panier WHERE Id_livre = :id_livre && Id_client = :id_client";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":id_livre", htmlspecialchars($parts[3]), PDO::PARAM_INT);
    $stmt->bindValue(":id_client", htmlspecialchars($_SESSION["Id_client"]), PDO::PARAM_INT);
    $stmt->execute();
    header("Location: /panier");
} else if ($parts[1] == "panier" && isset($parts[2]) && !empty($parts[2]) && $parts[2] == "removeformsouhait" && isset($parts[3]) && !empty($parts[3])) {
    $sql = "DELETE FROM Article_souhait WHERE Id_livre = :id_livre && Id_client = :id_client";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":id_livre", htmlspecialchars($parts[3]), PDO::PARAM_INT);
    $stmt->bindValue(":id_client", htmlspecialchars($_SESSION["Id_client"]), PDO::PARAM_INT);
    $stmt->execute();
    header("Location: /panier");
} else if ($parts[1] == "panier" && isset($parts[2]) && !empty($parts[2]) && $parts[2] == "addtocart" && isset($parts[3]) && !empty($parts[3])) {
    $quantity = 1;
    if (isset($parts[4]) && !empty($parts[4])) {
        $quantity = htmlspecialchars($parts[4]);
    }

    $sql = "SELECT * FROM Article_panier WHERE Id_livre = :id_livre && Id_client = :id_client";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":id_livre", htmlspecialchars($parts[3]), PDO::PARAM_INT);
    $stmt->bindValue(":id_client", htmlspecialchars($_SESSION["Id_client"]), PDO::PARAM_INT);
    $stmt->execute();

    $data = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }

    if (!empty($data)) {
        if ($data[0]["quantity"] + $quantity <= 0) {
            $sql = "DELETE FROM Article_panier WHERE Id_livre = :id_livre && Id_client = :id_client";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(":id_livre", htmlspecialchars($parts[3]), PDO::PARAM_INT);
            $stmt->bindValue(":id_client", htmlspecialchars($_SESSION["Id_client"]), PDO::PARAM_INT);
            $stmt->execute();
            header("Location: /panier");
            exit();
        }
        $sql = "UPDATE Article_panier SET quantity = quantity + :quantity WHERE Id_livre = :id_livre && Id_client = :id_client";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_livre", htmlspecialchars($parts[3]), PDO::PARAM_INT);
        $stmt->bindValue(":id_client", htmlspecialchars($_SESSION["Id_client"]), PDO::PARAM_INT);
        $stmt->bindValue(":quantity", $quantity, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $sql = "INSERT INTO Article_panier (Id_livre, Id_client, quantity) VALUES (:id_livre, :id_client, :quantity)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_livre", htmlspecialchars($parts[3]), PDO::PARAM_INT);
        $stmt->bindValue(":id_client", htmlspecialchars($_SESSION["Id_client"]), PDO::PARAM_INT);
        $stmt->bindValue(":quantity", $quantity, PDO::PARAM_INT);
        $stmt->execute();
    }
    header("Location: /panier");
} else if ($parts[1] == "panier" && isset($parts[2]) && !empty($parts[2]) && $parts[2] == "addtosouhait" && isset($parts[3]) && !empty($parts[3])) {
    $sql = "SELECT * FROM Article_souhait WHERE Id_livre = :id_livre && Id_client = :id_client";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":id_livre", htmlspecialchars($parts[3]), PDO::PARAM_INT);
    $stmt->bindValue(":id_client", htmlspecialchars($_SESSION["Id_client"]), PDO::PARAM_INT);
    $stmt->execute();

    $data = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }

    if (empty($data)) {
        $sql = "INSERT INTO Article_souhait (Id_livre, Id_client) VALUES (:id_livre, :id_client)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_livre", htmlspecialchars($parts[3]), PDO::PARAM_INT);
        $stmt->bindValue(":id_client", htmlspecialchars($_SESSION["Id_client"]), PDO::PARAM_INT);
        $stmt->execute();
    }
    header("Location: /panier");
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
    <link rel="stylesheet" href="../style/panier.css">
</head>

<body>
    <a href="/home"><img src="/assets/left_arrow.svg" alt="" class="return"></a>
    <section class="panier">
        <?php

        // Récupérer les articles du panier
        $sql = "SELECT * FROM Article_panier
                JOIN Livres ON Article_panier.Id_livre = Livres.Id_livre
                JOIN Clients ON Article_panier.Id_client = Clients.Id_client
                WHERE Article_panier.Id_client = :id_client";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_client", htmlspecialchars($_SESSION["Id_client"]), PDO::PARAM_INT);
        $stmt->execute();

        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        if (!empty($data)) {
            echo "Votre panier";

            $total = 0;
            $articles = 0;

            foreach ($data as $key => $value) {
                $total += $value["Prix"] * $value["quantity"];
                $articles += $value["quantity"];

                echo "
                <article class='article_panier'>
                <a href='/livre/$value[Id_livre]'>
                <img src='data:image/png;base64," . base64_encode($value["Miniature"]) . "' alt='' class='mini_back'>
                </a>
                    <div class='info_article'>
                        <p>" . $value["Titre_Livre"] . "</p>
                        <p>" . $value["Prix"] . " €</p>
                        <div class='quantity'>
                        <p>Quantité : </p>
                            <a href='/panier/addtocart/$value[Id_livre]'><button class='add'>+</button></a>
                            <p>$value[quantity]</p>
                            <a href='/panier/addtocart/$value[Id_livre]/-1'><button class='remove'>-</button></a>
                        </div>
                    </div>
                <a href='/panier/movetosouhait/$value[Id_livre]'><button class='add_to_souhait'>Déplacer dans la liste de souhait</button></a>
                <a href='/panier/removefromcart/$value[Id_livre]'><button class='remove_from_cart'>Supprimer</button></a>
                </article>
                ";
            }

            echo "<p>Sous-total ($articles articles) : $total €</p>
            <a href='/checkout'><button class='checkout'>Passer la commande</button></a>";
        } else {
            echo "Votre panier est vide.";
        }

        ?>

    </section>

    <section class="souhait">
        <?php

        // Récupérer les articles du panier
        $sql = "SELECT * FROM Article_souhait
                JOIN Livres ON Article_souhait.Id_livre = Livres.Id_livre
                JOIN Clients ON Article_souhait.Id_client = Clients.Id_client
                WHERE Article_souhait.Id_client = :id_client";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_client", htmlspecialchars($_SESSION["Id_client"]), PDO::PARAM_INT);
        $stmt->execute();

        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        if (!empty($data)) {
            echo "Votre liste de souhait";

            foreach ($data as $key => $value) {

                echo "
                <article class='article_panier'>
                <a href='/livre/$value[Id_livre]'>
                <img src='data:image/png;base64," . base64_encode($value["Miniature"]) . "' alt='' class='mini_back'>
                </a>
                    <div class='info_article'>
                        <p>" . $value["Titre_Livre"] . "</p>
                        <p>" . $value["Prix"] . " €</p>
                    </div>
                <a href='/panier/movetokart/$value[Id_livre]'><button class='add_to_cart'>Ajouter au panier</button></a>
                <a href='/panier/removeformsouhait/$value[Id_livre]'><button class='remove_from_souhait'>Supprimer</button></a>
                </article>
                ";
            }
        } else {
            echo "Votre liste de souhait est vide.";
        }

        ?>

    </section>

</body>

</html>