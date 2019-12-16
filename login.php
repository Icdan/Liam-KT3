<?php
// Start the sessie om met PHP sessies te starten
session_start();
// Maak connectie met de database;
include "db/db_connection.php";

// Als er op de login knop is gedrukt worden de ingevoerde gebruikersnaam en wachtwoord vergeleken met wat er in de database staat
// Als het klopt wordt er ingelogd en een aantal behulpzame data dat gekoppeld is aan de gebruiker meegegeven
// Bijvoorbeel de voornaam zodat we ze kunnen begroeten op de hoofdpagina
if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $loginQuery = mysqli_query($conn, "SELECT * FROM `medewerker` WHERE gebruikersnaam = '$username' && wachtwoord = '$password'");

    if ($loginQuery) {
        $loginResult = mysqli_num_rows($loginQuery);
        if ($loginResult == 1) {
            $row = mysqli_fetch_assoc($loginQuery);
            $_SESSION['voornaam'] = $row['voornaam'];
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['gebruikersnaam'] = $row['gebruikersnaam'];
            $_SESSION['loggedin'] = true;
            header("Location: index.php");
        } elseif ($_POST) {
            echo "Vul alstublieft de juiste gebruikersnaam of wachtwoord in";
        }
    }
}

?>
<!doctype html>
<html lang="en">
<head>
    <!-- Vereiste meta tags voor Bootstrap -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSS -->
    <?php
    include "includes/header.php";
    ?>
    <title>Log-in page</title>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <form action="" method="post">
                <div class="form-group">
                    <label>Gebruikersnaam</label>
                    <input type="text" name="username" placeholder="Uw gebruikersnaam" maxlength="75" required>
                </div>
                <div class="form-group">
                    <label>Wachtwoord</label>
                    <input type="password" name="password" placeholder="Uw wachtwoord" maxlength="25" required>
                </div>
                <input type="submit" name="login" value="Log in" class="btn btn-primary">
            </form>
        </div>
    </div>
</div>
<!-- Javascript -->
<?php
include "includes/footer.php"
?>
</body>
</html>
