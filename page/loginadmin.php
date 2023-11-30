<?php

session_start();

$database = new Database($_ENV["DB_HOST"], $_ENV["DB_PORT"], $_ENV["DB_DATABASE"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);

$conn = $database->getConnection();

if (isset($_SESSION["Id_admin"]) && !empty($_SESSION["Id_admin"])) {
    header("Location: /adminlivre");
    exit();
}

if (!isset($_SESSION["logAdmin"])) {
    $_SESSION["logAdmin"] = 0;
}

if (isset($_POST) && !empty($_POST)) {
    if (empty($_POST["email"]) || empty($_POST["password"])) {
        showinput("Please fill all the fields");
    } else {
        $sql = "SELECT * FROM Admin WHERE email = :Email";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":Email", htmlspecialchars($_POST["email"]), PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            if (password_verify(htmlspecialchars($_POST["password"]), $result["Password"])) {
                $_SESSION["logAdmin"] += 1;
                $_SESSION["Id_admin"] = $result["Id_admin"];
                $_SESSION["Email_admin"] = $result["email"];
                $_SESSION["Password_admin"] = $result["Password"];
                header("Location: /adminlivre");
            } else {
                showinput("Wrong password");
            }
        } else {
            showinput("Email not found");
        }
    }
} else {
    showinput("");
}

function showinput($message)
{

?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" href="/style/login.css">

        <title>Login Admin</title>
    </head>

    <body>
        <a href="/home"><img src="/assets/left_arrow.svg" alt="" class="return"></a>
        <h1>Login Admin</h1>
        <div class=contain>

            <form method="post" action='<?php echo $_SERVER["REQUEST_URI"]; ?>'>
                <?php
                if (isset($message)) {
                    echo "<p class='msg'>$message</p>";
                }
                ?>

                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required class="input" placeholder="Email"><br><br>

                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required class="input" placeholder="password"><br><br>

                <div class="btn">
                    <input type="submit" value="Done" class="button-60" id="button">
                </div>

            </form>

        </div>
    </body>

    </html>
    <script src="/style/script.js"></script>

<?php
    $_SESSION["logAdmin"] = 0;
}

if ($_SESSION["logAdmin"] != 1) {
    empty($_POST);
    unset($_POST);
}

?>