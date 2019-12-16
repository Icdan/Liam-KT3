<?php
// Start the sessie om met PHP sessies te starten
session_start();
// Maak connectie met de database;
include "db/db_connection.php";
// Als de bezoeker niet ingelogd is, wordt de bezoeker verwezen naar de log-in pagina
if (!$_SESSION['loggedin']) {
    header("Location: login.php");
}

// Als we aankomen op deze pagina vanaf de form waar de knop in zit wordt de prijsverandering doorgezet naar de database
// Als we vanaf de andere form komen wordt de bestelling overzicht die aan de reservering zit waar we net vandaan komen ge-update met een nieuwe bestelling
if(isset($_POST['opslaanPrijsWijziging']))
{

    $prijsWijzigingDatabase = $_POST['prijsWijzigingDatabase'];
    $prijsDieVeranderdWord = $_POST['prijsDieVeranderdWord'];
    mysqli_query($conn, "UPDATE menu_item SET prijs = '$prijsDieVeranderdWord' WHERE id_item = '$prijsWijzigingDatabase'");

} elseif (isset($_POST['opslaanInBestelling'])) {
    $reservationOrderid = $_POST['reservationOrderid'];
    $menuItemid = $_POST['reservationOrderItemid'];
    $menuItemCategoryid = $_POST['reservationOrderCategoryid'];
    $menuItemAmount = $_POST['reservationOrderAmount'];

    $newOrder = mysqli_query($conn, "INSERT INTO `bestelling_per_reservering` (`reservering_id_reservering`, `menu_item_id_item`, `menu_item_menu_categorieen_id_menu_categorieen`, `aantal`) VALUES ('$reservationOrderid', '$menuItemid', '$menuItemCategoryid', '$menuItemAmount') ");

    if (!$newOrder) {
        mysqli_query($conn, "UPDATE bestelling_per_reservering SET aantal = (aantal + '$menuItemAmount') WHERE menu_item_id_item = '$menuItemid'");
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
    <title>Reserveringen overzicht</title>
</head>
<body>
<?php
include "includes/navbar.php";
?>
<div class="container">
    <div class="row">
        <div class="col text-center">
            <table>
                <tr>
                    <th>Datum</th>
                    <th>Tijd</th>
                    <th>Tafel</th>
                    <th>Naam</th>
                    <th>Telefoon</th>
                    <th>Aantal personen</th>
                    <th>Commentaar</th>
                    <th>Allergieen</th>
                    <th>Reservering gebruikt</th>
                </tr>
                <?php
                // We halen een overzicht van alle reserveringen op, data van de reserveringen en of de gasten aangekomen zijn
                $reserveringQuery = mysqli_query($conn, "SELECT *, IF(reservering_gebruikt, 'Ja', 'Nee') AS gebruikt from reservering ");

                if ($reserveringQuery) {
                    $reserveringAmount = mysqli_num_rows($reserveringQuery);
                    // We loopen door de opgehaalde data heen zodat we alle reserveringen kunnen neerzetten i.p.v alleen de laatste in de database
                    for ($count = 1; $count <= $reserveringAmount; $count++) {
                        $row = mysqli_fetch_assoc($reserveringQuery);
                        // We halen de data die we opgehaald hebben apart en zetten het overzichtelijk neer.
                        // Ook geven we de mogelijkheid om op een reservering te klikken en naar de bestellingen daarvan te kijken
                        echo "<tr>";
                        echo "<td>" . $row['datum'] . "</td><td>" . $row['tijd'] . "</td><td>" . $row['tafel'] . "</td><td>" . $row['naam'] . "</td><td>" . $row['telefoon'] . "</td><td>" . $row['aantal_personen'] . "</td><td>" . $row['commentaar'] . "</td><td>" . $row['allergieen'] . "</td><td>" . $row['gebruikt'] . "</td>";
                        echo "<td><form method='post' action='bestelling_overzicht.php'><input type='hidden' value=" . $row['id_reservering'] . " name='id_reservering'><input type='submit' value='Bestellingen'></form></td>";
                        echo "</tr>";
                    }
                }
                ?>
            </table>
        </div>
    </div>
</div>
<!-- Javascript files -->
<?php
include "includes/footer.php";
?>
</body>
</html>