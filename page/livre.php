<?php

session_start();

$parts = explode("/", $_SERVER["REQUEST_URI"]);

if ($parts[1] == "livre" && !isset($parts[2])) {
    header("Location: /home");
}

$database = new Database($_ENV["DB_HOST"], $_ENV["DB_PORT"], $_ENV["DB_DATABASE"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);

$conn = $database->getConnection();

if ($parts[1] == "livre" && isset($parts[2]) && !empty($parts[2]) && isset($parts[3]) && !empty($parts[3]) && $parts[3] == "add_wish") {

    $sql = "SELECT * FROM Article_souhait WHERE Id_livre = :id_livre AND Id_client = :id_client";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":id_livre", $parts[2], PDO::PARAM_INT);
    $stmt->bindValue(":id_client", $_SESSION["Id_client"], PDO::PARAM_INT);
    $stmt->execute();

    $data = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }

    if (empty($data)) {
        $sql = "INSERT INTO Article_souhait (Id_livre, Id_client) VALUES (:id_livre, :id_client)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_livre", $parts[2], PDO::PARAM_INT);
        $stmt->bindValue(":id_client", $_SESSION["Id_client"], PDO::PARAM_INT);
        $stmt->execute();
    }
    header("Location: /livre/$parts[2]");
} else if ($parts[1] == "livre" && isset($parts[2]) && !empty($parts[2]) && isset($parts[3]) && !empty($parts[3]) && $parts[3] == "remove_wish") {

    $sql = "SELECT * FROM Article_souhait WHERE Id_livre = :id_livre AND Id_client = :id_client";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":id_livre", $parts[2], PDO::PARAM_INT);
    $stmt->bindValue(":id_client", $_SESSION["Id_client"], PDO::PARAM_INT);
    $stmt->execute();

    $data = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }

    if (!empty($data)) {
        $sql = "DELETE FROM Article_souhait WHERE Id_livre = :id_livre AND Id_client = :id_client";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id_livre", $parts[2], PDO::PARAM_INT);
        $stmt->bindValue(":id_client", $_SESSION["Id_client"], PDO::PARAM_INT);
        $stmt->execute();
    }
    header("Location: /livre/$parts[2]");
}

$sql = "SELECT * 
        FROM Livres
        JOIN Auteur ON Livres.Id_Auteur = Auteur.Id_Auteur
        JOIN Langue ON Livres.Id_Langue = Langue.Id_Langue
        JOIN Genre ON Livres.Id_Genre = Genre.Id_Genre
        WHERE Id_Livre = :id";

$stmt = $conn->prepare($sql);
$stmt->bindValue(':id', $parts[2], PDO::PARAM_INT);

$stmt->execute();

$datalivre = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $datalivre[] = $row;
}

if (empty($datalivre)) {
    header("Location: /home");
}

// display_data($data);

setlocale(LC_TIME, "fr_FR");

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
    <link rel="stylesheet" href="/style/livre.css">
    <title>livre</title>
</head>


<body>
    <a href="/home"><img src="/assets/left_arrow.svg" alt="" class="return"></a>
    <main>

        <article class="main_info">
            <?php
            foreach ($datalivre as $key => $value) {
                echo "
                <img src='data:image/png;base64," . base64_encode($value["Miniature"]) . "' alt='' class='mini_back'>
                <div class='card'>
                    <div class='head_card'>
                        <p class='pages'>$value[Nb_Pages] Pages</p>
                        <p class='lang'>$value[Acronyme]</p>
                    </div>
                    <div class='main_card'>
                        <p>$value[Titre_Livre]</p>
                        <p>$value[Titre_Genre]</p>
                        <p>Édité par $value[Editeur]</p>
                        <img src='data:image/png;base64," . base64_encode($value["Miniature"]) . "' alt=''>
                        <p>Écrit par $value[Nom]</p>
                    </div>
                    <div class='foot_card'>
                    ";
                if (isset($_SESSION["Id_client"]) && !empty($_SESSION["Id_client"])) {
                    $sql = "SELECT * FROM Article_souhait WHERE Id_livre = :id_livre AND Id_client = :id_client";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindValue(":id_livre", $parts[2], PDO::PARAM_INT);
                    $stmt->bindValue(":id_client", $_SESSION["Id_client"], PDO::PARAM_INT);
                    $stmt->execute();

                    $data = [];

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $data[] = $row;
                    }

                    if (empty($data)) {
                        echo "
                        <a href='/livre/$parts[2]/add_wish'>
                            <img class='wish' src='/assets/add_wish.svg' alt=''>
                        </a>";
                    } else {
                        echo "
                        <a href='/livre/$parts[2]/remove_wish'>
                            <img id='wishremove' class='wish' src='/assets/wished.svg' alt=''>
                        </a>";
                    }
                }

                echo "<h3>$value[Prix]€</h3>";
                if (isset($_SESSION["Id_client"]) && !empty($_SESSION["Id_client"])) {
                    echo "
                        <div class='choix_qte'>
                        <label for='quantity'>Quantité :</label>
                        <select name='quantity' id='quantity'>
                            <option value='1' selected>1</option>
                            <option value='2'>2</option>
                            <option value='3'>3</option>
                            <option value='4'>4</option>
                            <option value='5'>5</option>
                            <option value='6'>6</option>
                            <option value='7'>7</option>
                            <option value='8'>8</option>
                            <option value='9'>9</option>
                            <option value='10'>10</option>
                        </select>
                        </div>
                        <img id='addtocart' src='/assets/cart.svg' alt=''>";
                }
                echo "
                    </div>
                </div>";
            }
            ?>
        </article>
        <article class="second_info">
            <?php
            foreach ($datalivre as $key => $value) {
                echo "
                <h3 id='date'>Publié le " . date('d/m/Y', strtotime($value["Date_Publi"])) . " </h3>
                <p>$value[Intrigue]</p>";
            }
            ?>
        </article>
    </main>
</body>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- <script type="module" src="/site/JS/script.js"></script>
<script type="module" src="/site/JS/home.js"></script> -->
<script type="module">

</script>
<script>
    <?php
    if (isset($_SESSION["Id_client"]) && !empty($_SESSION["Id_client"])) {

        if (!empty($data)) {
            ?>
            wishremove = document.getElementById("wishremove")
            wishremove.onmouseover = function () {
                wishremove.src = "/assets/unwish.svg"
            }
            wishremove.onmouseout = function () {
                wishremove.src = "/assets/wished.svg"
            }
            <?php
        }
        ?>
        addtocart = document.getElementById("addtocart")

        addtocart.onclick = function () {
            quantity = document.getElementById("quantity").value
            window.location.href = "/panier/addtocart/<?php echo $parts[2] ?>/" + quantity
        }

        <?php
    }
    ?>
</script>

</html>