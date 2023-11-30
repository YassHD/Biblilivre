<?php

session_start();

if (!isset($_SESSION["Id_admin"]) && empty($_SESSION["Id_admin"])) {
    header("Location: /loginadmin");
    exit();
}

if (!isset($_SESSION["modifAuteur"])) {
    $_SESSION["modifAuteur"] = 0;
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

function romanToNumber($roman)
{
    $roman = strtoupper($roman);
    $romans = [
        'M' => 1000,
        'CM' => 900,
        'D' => 500,
        'CD' => 400,
        'C' => 100,
        'XC' => 90,
        'L' => 50,
        'XL' => 40,
        'X' => 10,
        'IX' => 9,
        'V' => 5,
        'IV' => 4,
        'I' => 1
    ];
    $result = 0;
    foreach ($romans as $key => $value) {
        while (strpos($roman, $key) === 0) {
            $result += $value;
            $roman = substr($roman, strlen($key));
        }
    }
    return $result;
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

if (isset($_POST) && !empty($_POST) && isset($_FILES)) {

    $_SESSION["modifAuteur"] += 1;

    if ($_FILES["file"]["error"] != 4) {

        $sql = "UPDATE Auteur 
            SET Nom = :Nom, profil = :profil, Nationalite = :Nationalite, Mort = :Mort, Epoque = :Epoque, Courant = :Courant
            WHERE Id_Auteur = :Id_Auteur";

        $stmt = $conn->prepare($sql);

        $stmt->bindValue(":Nom", htmlspecialchars($_POST["nom"]), PDO::PARAM_STR);
        $stmt->bindValue(":profil", file_get_contents($_FILES['file']['tmp_name']), PDO::PARAM_LOB);
        $stmt->bindValue(":Nationalite", htmlspecialchars($_POST["nationalite"]), PDO::PARAM_STR);
        $stmt->bindValue(":Mort", isDead($_POST["mort"]), PDO::PARAM_INT);
        $stmt->bindValue(":Epoque", htmlspecialchars(numberToRoman($_POST["epoque"])), PDO::PARAM_STR);
        $stmt->bindValue(":Courant", htmlspecialchars($_POST["courant"]), PDO::PARAM_STR);
        $stmt->bindValue(":Id_Auteur", htmlspecialchars($parts[2]), PDO::PARAM_INT);
    } else {
        $sql = "UPDATE Auteur 
            SET Nom = :Nom, Nationalite = :Nationalite, Mort = :Mort, Epoque = :Epoque, Courant = :Courant
            WHERE Id_Auteur = :Id_Auteur";

        $stmt = $conn->prepare($sql);

        $stmt->bindValue(":Nom", htmlspecialchars($_POST["nom"]), PDO::PARAM_STR);
        $stmt->bindValue(":Nationalite", htmlspecialchars($_POST["nationalite"]), PDO::PARAM_STR);
        $stmt->bindValue(":Mort", isDead($_POST["mort"]), PDO::PARAM_INT);
        $stmt->bindValue(":Epoque", htmlspecialchars(numberToRoman($_POST["epoque"])), PDO::PARAM_STR);
        $stmt->bindValue(":Courant", htmlspecialchars($_POST["courant"]), PDO::PARAM_STR);
        $stmt->bindValue(":Id_Auteur", htmlspecialchars($parts[2]), PDO::PARAM_INT);
    }

    if ($_SESSION["modifAuteur"] == 1) {
        $stmt->execute();
        header("Location: /adminauteur");
    } else {
        echo "Already send";
    }
} else {

    $sql = "SELECT * 
            FROM Auteur
            WHERE Id_Auteur = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(":id", $parts[2], PDO::PARAM_INT);
    $stmt->execute();

    $data = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }

    $data = $data[0];

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
        <link rel="stylesheet" href="/style/home.css">
        <title>New Auteur</title>
    </head>

    <body>
        <a href="/adminauteur"><img src="/assets/left_arrow.svg" alt="" class="return"></a>

        <h1 class="edit_author">Ajouter un auteur</h1>
        <div class="div_edit_author">
            <form method='post' action='<?php echo $_SERVER["REQUEST_URI"]; ?>' enctype='multipart/form-data'
                class="form_edit_author">
                <div>
                    Nom de l'auteur
                    <input type="text" name="nom" placeholder="Nom de l'auteur" required value="<?php echo $data["Nom"] ?>">
                </div>
                <div>
                    Photo de profil
                    <input type='file' name='file' accept="image/*" id="imgInp">
                    <img id="blah" src="data:image/png;base64, <?php echo base64_encode($data["profil"]) ?>" alt=""
                        style="width: 100px;" alt=" " style="width:100px;height:100px;" />
                </div>
                <div>
                    Nationalité
                    <input type="text" name="nationalite" placeholder="Nationalité" required
                        value="<?php echo $data["Nationalite"] ?>">
                </div>
                <div>
                    Mort
                    <input type="checkbox" name="mort" <?php if ($data["Mort"]) {
                        echo "checked";
                    } ?>>
                </div>
                <div>
                    Epoque
                    <input type="number" name="epoque" id="romanin" value="<?php echo romanToNumber($data["Epoque"]) ?>" />
                    <p id="romanout">
                        <?php echo $data["Epoque"] ?>
                    </p>
                </div>
                <div>
                    Courant
                    <select name="courant" required>
                        <?php
                        echo "<option value='$data[Courant]' selected>$data[Courant]</option>";
                        foreach ($vals as $key => $value) {
                            if ($value == $data["Courant"]) {
                                continue;
                            }
                            echo "<option value='$value'>$value</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    Add new Auteur
                    <input type='submit' value='Upload' class="submit">
                </div>
        </div>
        </form>


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

    $_SESSION["modifAuteur"] = 0;
}

if ($_SESSION["modifAuteur"] != 1) {
    empty($_POST);
    empty($_FILES);
    unset($_POST);
    unset($_FILES);
}

?>