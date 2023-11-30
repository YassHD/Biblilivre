<?php

session_start();

if (!isset($_SESSION["Id_client"]) || empty($_SESSION["Id_client"])) {
    header("Location: /home");
}

$database = new Database($_ENV["DB_HOST"], $_ENV["DB_PORT"], $_ENV["DB_DATABASE"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);

$conn = $database->getConnection();

if (isset($parts[2]) && !empty($parts[2]) && $parts[3] == "Del") {
    $sql = "DELETE FROM Adresse WHERE Id_adresse = :id_adresse AND Id_client = :id_client";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":id_adresse", htmlspecialchars($parts[2]), PDO::PARAM_INT);
    $stmt->bindValue(":id_client", htmlspecialchars($_SESSION["Id_client"]), PDO::PARAM_INT);
    $stmt->execute();
    header("Location: /Adresse");
    exit();
}

$sql = "SELECT * FROM Adresse WHERE Id_client = :id_client";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style/adresse_liv.css">
    <title>Addrese livraison</title>
</head>

<body>
    <a href="/home"><img src="/assets/left_arrow.svg" class="come_back" alt=""></a>
    <main>
        <a href="/Add_adresse">
            <article class="add_lieu">
                <img src="/assets/plus.svg" alt="">
                <h2>Ajoutez une adresse de livraison</h2>
            </article>
        </a>
        <div>
            <h2>Mes adresses de livraison</h2>
            <?php
            if (empty($data)) {
            ?>
                <p>Vous n'avez pas encore d'adresse de livraison</p>
                <?php
            } else {
                foreach ($data as $key => $value) {
                ?>
                    <article class="My_lieu">
                        <p><?php echo $value["Adresse"] ?></p>
                        <p><?php echo $value["Complement"] ?></p>
                        <p><?php echo $value["Ville"] . ", " . $value["Code_postal"] ?></p>
                        <p><?php echo $value["Pays"] ?></p>
                        <p class="edit_del"><a href="/Edit_adresse/<?php echo $value["Id_adresse"] ?>">Modifier</a> | <a class="edit_but" id="<?php echo $value["Id_adresse"] ?>">Effacer</a></p>
                    </article>
            <?php
                }
            }
            ?>
        </div>
    </main>
</body>

</html>


<script>
    let edit_but = document.querySelectorAll(".edit_but");

    edit_but.forEach(element => {
        element.addEventListener("click", function() {
            let id = element.getAttribute("id");
            let confirm = window.confirm("Voulez vous vraiment supprimer cette adresse ?");
            if (confirm) {
                window.location.href = "/Adresse/" + id + "/Del";
            }
        })
    });
</script>