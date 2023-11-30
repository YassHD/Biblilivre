<?php

session_start();

if (!isset($_SESSION["Id_admin"]) && empty($_SESSION["Id_admin"])) {
    header("Location: /loginadmin");
    exit();
}

if (!isset($_SESSION["sendLivre"])) {
    $_SESSION["sendLivre"] = 0;
}

$database = new Database($_ENV["DB_HOST"], $_ENV["DB_PORT"], $_ENV["DB_DATABASE"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);

$conn = $database->getConnection();

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
    <link rel="stylesheet" href="/style/newlivre.css">
    <title>New Livre</title>
</head>

<style id="style_mod">

</style>

<body>
    <a href="/adminlivre"><img src="/assets/left_arrow.svg" alt="" class="return"></a>

    <div class="content">
        <?php

        if (!isset($_POST) || empty($_POST) || !isset($_FILES) || empty($_FILES)) {

            // get all langue for select
            $sql = "SELECT * 
                    FROM Langue
                    ORDER BY Language ASC";

            $stmt = $conn->prepare($sql);

            $stmt->execute();

            $dataLangue = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $dataLangue[] = $row;
            }

            // get all auteur for select
            $sql = "SELECT * 
                    FROM Auteur
                    ORDER BY Nom ASC";

            $stmt = $conn->prepare($sql);

            $stmt->execute();

            $dataAuteur = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $dataAuteur[] = $row;
            }

            // get all genre for select
            $sql = "SELECT * 
                    FROM Genre
                    ORDER BY Titre_Genre ASC";

            $stmt = $conn->prepare($sql);

            $stmt->execute();

            $dataGenre = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $dataGenre[] = $row;
            }

            // get all type for select
            $sql = "SELECT * 
                    FROM Types
                    ORDER BY Types ASC";

            $stmt = $conn->prepare($sql);

            $stmt->execute();

            $dataType = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $dataType[] = $row;
            }

            ?>
            <div class="container">
                <h1>New Livre</h1>
                <form method='post' action='<?php echo $_SERVER["REQUEST_URI"]; ?>' enctype='multipart/form-data'>
                    <div class="intern">
                        Titre du livre
                        <input type="text" name="titre" placeholder="Titre du livre" required class="input-style">
                    </div>
                    <div class="intern imginline">
                        Miniature
                        <input type='file' name='file' accept="image/*" id="imgInp" required class="input-style">
                        <img id="blah" src="#" alt=" " />
                    </div>
                    <div class="intern">
                        Intrigue
                        <textarea name="intrigue" placeholder="Intrigue du livre" required></textarea>
                    </div>
                    <div class="intern">
                        Langue
                        <select name="langue" required class="input-style">
                            <?php
                            foreach ($dataLangue as $key => $value) {
                                echo "<option value='$value[Id_Langue]'>$value[Language]</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="intern">
                        Date de publication
                        <input type="date" name="date" placeholder="Date de publication" required class="input-style">
                    </div>
                    <div class="intern">
                        Auteur
                        <select name="auteur" required class="input-style">
                            <?php
                            foreach ($dataAuteur as $key => $value) {
                                echo "<option value='$value[Id_Auteur]'>$value[Nom]</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="intern">
                        Genre
                        <select name="genre" required class="input-style">
                            <?php
                            foreach ($dataGenre as $key => $value) {
                                echo "<option value='$value[Id_Genre]'>$value[Titre_Genre]</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="intern">
                        Type
                        <select name="type" required class="input-style">
                            <?php
                            foreach ($dataType as $key => $value) {
                                echo "<option value='$value[Id_Types]'>$value[Types]</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="intern">
                        Prix
                        <input type="number" pattern="^\d*(\.\d{0,2})?$" step="0.01" name="prix" placeholder="Prix" required
                            class="input-style">
                        <label for="prix">€</label>
                    </div>
                    <div class="intern">
                        Nombre de page
                        <input type="number" pattern="^(?:\d*\.)?\d+$" step="1" name="page" placeholder="Pages" required
                            class="input-style">
                    </div>
                    <div class="intern">
                        Editeur
                        <input type="text" name="editeur" placeholder="Editeur" required class="input-style">
                    </div>
                    <div class="intern">
                        Quantity
                        <input type="number" pattern="^(?:\d*\.)?\d+$" step="1" name="quantity" placeholder="Quantity"
                            required class="input-style">
                    </div>
                    <div class="intern">
                        Add new Livre
                        <input type='submit' value='Upload' class="submit">
                    </div>
                </form>
            </div>
            <?php

            $_SESSION["sendLivre"] = 0;
        } else {

            $_SESSION["sendLivre"] += 1;

            $sql = "INSERT INTO Livres (Titre_Livre, Miniature, Intrigue, Id_Langue, Date_Publi, Id_Auteur, Id_Genre, Id_Types, Prix, Nb_Pages, Editeur, Quantity)
                VALUES (:Titre_Livre, :Miniature, :Intrigue, :Id_Langue, :Date_Publi, :Id_Auteur, :Id_Genre, :Id_Types, :Prix, :Nb_Pages, :Editeur, :Quantity)";

            $stmt = $conn->prepare($sql);

            $stmt->bindValue(":Titre_Livre", htmlspecialchars($_POST["titre"]), PDO::PARAM_STR);
            $stmt->bindValue(":Miniature", file_get_contents($_FILES['file']['tmp_name']), PDO::PARAM_LOB);
            $stmt->bindValue(":Intrigue", htmlspecialchars($_POST["intrigue"]), PDO::PARAM_STR);
            $stmt->bindValue(":Id_Langue", ($_POST["langue"]), PDO::PARAM_INT);
            $stmt->bindValue(":Date_Publi", htmlspecialchars($_POST["date"]), PDO::PARAM_STR);
            $stmt->bindValue(":Id_Auteur", ($_POST["auteur"]), PDO::PARAM_INT);
            $stmt->bindValue(":Id_Genre", ($_POST["genre"]), PDO::PARAM_INT);
            $stmt->bindValue(":Id_Types", ($_POST["type"]), PDO::PARAM_INT);
            $stmt->bindValue(":Prix", ($_POST["prix"]), PDO::PARAM_STR);
            $stmt->bindValue(":Nb_Pages", ($_POST["page"]), PDO::PARAM_INT);
            $stmt->bindValue(":Editeur", htmlspecialchars($_POST["editeur"]), PDO::PARAM_STR);
            $stmt->bindValue(":Quantity", ($_POST["quantity"]), PDO::PARAM_INT);

            if ($_SESSION["sendLivre"] == 1) {
                $stmt->execute();
                include("UpdateSpecificAuteur.php");
                UpdateSpecificAuteur($_POST["auteur"], $database);
                header("Location: /adminlivre");
            } else {
                echo "Already send";
            }
        }
        ?>

</body>

</html>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- <script type="module" src="/site/JS/script.js"></script>
<script type="module" src="/site/JS/home.js"></script> -->
<script type="module">
    const imgInp = document.getElementById('imgInp')
    const blah = document.getElementById('blah')

    imgInp.onchange = evt => {
        const [file] = imgInp.files
        if (file) {
            blah.src = URL.createObjectURL(file)
        }
    }

    $(document).on('keydown', 'input[pattern]', function (e) {
        var input = $(this);
        var oldVal = input.val();
        var regex = new RegExp(input.attr('pattern'), 'g');

        setTimeout(function () {
            var newVal = input.val();
            if (!regex.test(newVal)) {
                input.val(oldVal);
            }
        }, 1);
    })
</script>

<?php
if ($_SESSION["sendLivre"] != 1) {
    empty($_POST);
    empty($_FILES);
    unset($_POST);
    unset($_FILES);
}
?>