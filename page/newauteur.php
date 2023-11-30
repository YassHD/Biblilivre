<?php

session_start();

if (!isset($_SESSION["Id_admin"]) && empty($_SESSION["Id_admin"])) {
    header("Location: /adminlogin");
    exit();
}

if (!isset($_SESSION["sendAuteur"])) {
    $_SESSION["sendAuteur"] = 0;
}

// Connexion à la base de données
$database = new Database($_ENV["DB_HOST"], $_ENV["DB_PORT"], $_ENV["DB_DATABASE"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);

$conn = $database->getConnection();

function numberToRoman($number)
{
    $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
    $returnValue = '';
    while ($number > 0) {
        foreach ($map as $roman => $int) {
            if ($number >= $int) {
                $number -= $int;
                $returnValue .= $roman;
                break;
            }
        }
    }
    return $returnValue;
}

function isDead($mort)
{
    if (isset($mort)) {
        return 1;
    } else {
        return 0;
    }
}

// get all Courant for select
$sql = "SHOW COLUMNS FROM Auteur LIKE 'Courant'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$type = $row['Type'];
preg_match('/enum\((.*)\)$/', $type, $matches);
$vals = explode(',', $matches[1]);

foreach ($vals as $key => $value) {
    $vals[$key] = trim($value, "'");
}

if (isset($_POST) && !empty($_POST) && isset($_FILES) && !empty($_FILES)) {

    $_SESSION["sendAuteur"] += 1;

    $sql = "INSERT INTO Auteur (Nom, profil, Nationalite, Mort, Epoque, Courant)
                VALUES (:Nom, :profil, :Nationalite, :Mort, :Epoque, :Courant)";

    $stmt = $conn->prepare($sql);

    $stmt->bindValue(":Nom", htmlspecialchars($_POST["nom"]), PDO::PARAM_STR);
    $stmt->bindValue(":profil", file_get_contents($_FILES['file']['tmp_name']), PDO::PARAM_LOB);
    $stmt->bindValue(":Nationalite", htmlspecialchars($_POST["nationalite"]), PDO::PARAM_STR);
    $stmt->bindValue(":Mort", isDead($_POST["mort"]), PDO::PARAM_INT);
    $stmt->bindValue(":Epoque", htmlspecialchars(numberToRoman($_POST["epoque"])), PDO::PARAM_STR);
    $stmt->bindValue(":Courant", ($_POST["courant"]), PDO::PARAM_STR);

    if ($_SESSION["sendAuteur"] == 1) {
        $stmt->execute();
        header("Location: /adminauteur");
    } else {
        echo "Already send";
    }
} else {

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
        <link rel="stylesheet" href="/style/newauteur.css">
        <title>New Auteur</title>
    </head>

    <body>
        <a href="/adminauteur"><img src="/assets/left_arrow.svg" alt="" class="return"></a>

        <h1>Ajouter un auteur</h1>
        <div class="add_auteur_div">
            <form method='post' action='<?php echo $_SERVER["REQUEST_URI"]; ?>' enctype='multipart/form-data'>
                <div>
                    Nom de l'auteur
                    <input type="text" name="nom" placeholder="Nom de l'auteur" required>
                </div>
                <div>
                    Photo de profil
                    <input type='file' name='file' accept="image/*" id="imgInp" required>
                    <img id="blah" src="#" alt=" " style="width:100px;height:100px;" />
                </div>
                <div>
                    Nationalité
                    <input type="text" name="nationalite" placeholder="Nationalité" required>
                </div>
                <div>
                    Mort
                    <input type="checkbox" name="mort">
                </div>
                <div>
                    Epoque
                    <input type="number" name="epoque" id="romanin" />
                    <p id="romanout"></p>
                </div>
                <div>
                    Courant
                    <select name="courant" required>
                        <?php
                        foreach ($vals as $key => $value) {
                            echo "<option value='$value'>$value</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    Add new Auteur
                    <input type='submit' value='Upload' class="submit">
                </div>
            </form>
        </div>
    </body>

    </html>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="module">
        const imgInp = document.getElementById('imgInp')
        const blah = document.getElementById('blah')
        const romanin = document.getElementById('romanin')
        const romanout = document.getElementById('romanout')

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

        romanin.addEventListener('change', function (e) {
            romanout.innerHTML = romanize(romanin.value)
        })

        function romanize(num) {
            var lookup = {
                M: 1000,
                CM: 900,
                D: 500,
                CD: 400,
                C: 100,
                XC: 90,
                L: 50,
                XL: 40,
                X: 10,
                IX: 9,
                V: 5,
                IV: 4,
                I: 1
            },
                roman = '',
                i;
            for (i in lookup) {
                while (num >= lookup[i]) {
                    roman += i;
                    num -= lookup[i];
                }
            }
            return roman;
        }
    </script>

    <?php

    $_SESSION["sendAuteur"] = 0;
}

if ($_SESSION["sendAuteur"] != 1) {
    empty($_POST);
    empty($_FILES);
    unset($_POST);
    unset($_FILES);
}

?>