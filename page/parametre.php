<?php

session_start();

if (!isset($_SESSION["Id_client"]) || empty($_SESSION["Id_client"])) {
    header("Location: /home");
}

$database = new Database($_ENV["DB_HOST"], $_ENV["DB_PORT"], $_ENV["DB_DATABASE"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);

$conn = $database->getConnection();

if (isset($_POST) && !empty($_POST)) {
    if ($_POST["Gategorie"] == "Informations") {
        if ($_SESSION["Email"] != $_POST["email"]) {


            $sql = "SELECT * FROM Clients WHERE Email = :email";

            $stmt = $conn->prepare($sql);

            $stmt->bindValue(":email", htmlspecialchars($_POST["email"]), PDO::PARAM_STR);

            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $message = "Email déjà utilisé";
                $mailUsed = true;
            } else {
                $mailUsed = false;
            }
        } else {
            $mailUsed = false;
        }

        if (!$mailUsed) {
            $sql = "UPDATE Clients
                        SET Nom = :Nom,
                            Prenom = :Prenom,
                            Email = :Email,
                            Num_tel = :Num_tel
                        WHERE Id_client = :id";

            $stmt = $conn->prepare($sql);

            $stmt->bindValue(":Nom", htmlspecialchars($_POST["nom"]), PDO::PARAM_STR);
            $stmt->bindValue(":Prenom", htmlspecialchars($_POST["Prenom"]), PDO::PARAM_STR);
            $stmt->bindValue(":Email", htmlspecialchars($_POST["email"]), PDO::PARAM_STR);
            $stmt->bindValue(":Num_tel", htmlspecialchars($_POST["tel"]), PDO::PARAM_STR);
            $stmt->bindValue(":id", $_SESSION["Id_client"], PDO::PARAM_INT);

            $_SESSION["Nom"] = htmlspecialchars($_POST["nom"]);
            $_SESSION["Prenom"] = htmlspecialchars($_POST["Prenom"]);
            $_SESSION["Email"] = htmlspecialchars($_POST["email"]);
            $_SESSION["Num_tel"] = htmlspecialchars($_POST["tel"]);

            $stmt->execute();

            $message = "Informations modifiées";
        }
    } else if ($_POST["Gategorie"] == "Password") {
        $sql = "SELECT * FROM Clients WHERE Id_client = :id";

        $stmt = $conn->prepare($sql);

        $stmt->bindValue(":id", $_SESSION["Id_client"], PDO::PARAM_INT);

        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify(htmlspecialchars($_POST["old_password"]), $result["Password"])) {
            if ($_POST["password"] == $_POST["confirm_password"]) {
                $sql = "UPDATE Clients
                            SET Password = :Password
                            WHERE Id_client = :id";

                $stmt = $conn->prepare($sql);

                $options = [
                    'cost' => 14,
                ];
                $passwd = password_hash(htmlspecialchars($_POST["password"]), PASSWORD_DEFAULT, $options);
                $stmt->bindValue(":Password", $passwd, PDO::PARAM_STR);
                $stmt->bindValue(":id", htmlspecialchars($_SESSION["Id_client"]), PDO::PARAM_INT);

                $_SESSION["Password"] = $passwd;

                $stmt->execute();

                $message = "Mot de passe modifié";
            } else {
                $message = "Les mots de passe ne correspondent pas";
            }
        } else {
            $message = "Ancien mot de passe incorrect";
        }
    }
    unset($_POST);
    empty($_POST);
} else {
    $message = "";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style/parametre.css">
    <title>Parametre</title>
</head>

<body>
    <header>
        <a href="/home"><img src="/assets/left_arrow.svg" class="come_back" alt=""></a>
        <div class=boutton_log_out>
            <a href="/logout">Log Out</a>
        </div>
    </header>

    <h1>Paramètres</h1>
    <div class="icon_profil">
        <div class="div_icon_profil">
            <p>
                <?php echo strtoupper($_SESSION["Prenom"][0]) ?>
            </p>
        </div>
    </div>

    <?php

    if (isset($message) && !empty($message)) {
        echo "<div class='message'>" . $message . "</div>";
    }

    ?>

    <div class="container">

        <div class="form">

            <div class="title">
                <h1>Informations personnelles</h1>
            </div>

            <div id="EditInfo">Edit Mes Informations</div>

            <br>
            <hr>

            <form id="ChangeInformations" method="post" action='<?php echo $_SERVER["REQUEST_URI"]; ?>'>

                <input type="text" id="Prenom" name="Prenom" required class="input" value=<?php echo ($_SESSION["Prenom"]) ?>>

                <input type="text" id="nom" name="nom" required class="input" value=<?php echo ($_SESSION["Nom"]) ?>>

                <input type="email" id="email" name="email" required class="input" value=<?php echo ($_SESSION["Email"]) ?>>

                <input type="tel" id="tel" name="tel" required class="input" value=<?php echo ($_SESSION["Num_tel"]) ?>>

                <input type="hidden" id="Gategorie" name="Gategorie" value="Informations">

                <hr>

                <input class=boutton_centrage type="submit" value="Save Infos" id="submitInfo">

            </form>

        </div>

        <div class="form">

            <div class="title">
                <h1>Mot de passe</h1>
            </div>

            <div id="EditPass">Edit mon mot de passe</div>

            <br>
            <hr>

            <form id="ChangePassword" method="post" action='<?php echo $_SERVER["REQUEST_URI"]; ?>'>

                <input type="password" id="old_password" name="old_password" class="input" placeholder="Ancien mot de passe">

                <input type="password" id="password" name="password" class="input" placeholder="Nouveau mot de passe">

                <input type="password" id="confirm_password" name="confirm_password" class="input" placeholder="Confirmation mot de passe">

                <input type="hidden" id="GategoriePass" name="Gategorie" value="Password">

                <hr>

                <input class=boutton_centrage type="submit" value="Save Pass" id="submitPass">

            </form>

        </div>

    </div>



</body>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="/script/settings.js"></script>

</html>
