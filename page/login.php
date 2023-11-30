<?php

session_start();

$database = new Database($_ENV["DB_HOST"], $_ENV["DB_PORT"], $_ENV["DB_DATABASE"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);

$conn = $database->getConnection();

if (isset($_SESSION["Id_client"]) && !empty($_SESSION["Id_client"])) {
    header("Location: /home");
    exit();
}

if (!isset($_SESSION["logUser"])) {
    $_SESSION["logUser"] = 0;
}

if (isset($_POST) && !empty($_POST)) {
    if (empty($_POST["email"]) || empty($_POST["password"])) {
        showinput("Please fill all the fields");
    } else {
        $sql = "SELECT * FROM Clients WHERE Email = :Email";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":Email", htmlspecialchars($_POST["email"]), PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            if (password_verify(htmlspecialchars($_POST["password"]), $result["Password"])) {
                $_SESSION["logUser"] += 1;
                $_SESSION["Id_client"] = $result["Id_client"];
                $_SESSION["Nom"] = $result["Nom"];
                $_SESSION["Prenom"] = $result["Prenom"];
                $_SESSION["Email"] = $result["Email"];
                $_SESSION["Password"] = $result["Password"];
                $_SESSION["Num_tel"] = $result["Num_tel"];
                header("Location: /home");
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

        <title>Login Page</title>
    </head>

    <body>
        <a href="/home"><img src="/assets/left_arrow.svg" class="return" alt=""></a>
        <h1>Login</h1>
        <div class=contain>

            <form method="post" action='<?php echo $_SERVER["REQUEST_URI"]; ?>'>
                <?php
                if (isset($message)) {
                    echo "<p class='msg'>$message</p>";
                }
                ?>
                <div class="form-group">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" required class="input" placeholder="Email"><br><br>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe :</label>
                    <input type="password" id="password" name="password" required class="input" placeholder="password"><br><br>
                </div>
                <div class="btn">
                    <input type="submit" value="Login" class="button-60" id="button">
                </div>

                <p class="ligne_connection">By clicking on <a href="#">"Login"</a>, you accept the</p>
                <p>pour s'inscrire cliquez <a href="./signUp">ici</a></p>
                <p class="deco">Terms and Conditions of Use.</p>
            </form>


        </div>
    </body>

    </html>
    <script src="/script/script.js"></script>

<?php
    $_SESSION["logUser"] = 0;
}

if ($_SESSION["logUser"] != 1) {
    empty($_POST);
    unset($_POST);
}

?>