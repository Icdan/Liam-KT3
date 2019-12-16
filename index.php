<?php
// Start the sessie om met PHP sessies te starten
session_start();
// Maak connectie met de database;
include "db/db_connection.php";
// Als de bezoeker niet ingelogd is, wordt de bezoeker verwezen naar de log-in pagina
if (!$_SESSION['loggedin']) {
    header("Location: login.php");
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
    <title>Home pagina</title>
</head>
<body>
<?php
include "includes/navbar.php";
?>
<div class="container">
    <div class="row">
        <div class="col text-center">
            <?php
            // Begroeten de ingelogde gebruiker
            echo "<p class='text-center'>Hallo " . $_SESSION['voornaam'];
            ?>
        </div>
    </div>
</div>
<!-- Javascript -->
<?php
include "includes/footer.php";
?>
</body>
</html>