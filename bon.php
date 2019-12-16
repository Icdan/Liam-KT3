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
    <title>Bon overzicht</title>
</head>
<body>
<?php
include "includes/navbar.php";
?>
<div class="container">
    <div class="row">
        <div class="col-12 text-center">
            <?php
            // We zorgen dat we bezig blijven met dezelfde reservering_id als waar we net waren voor de bestelling overzicht
            $id = $_SESSION['id_reservering'];

            // Maak een query naar de database voor de naam onder wie de reservering staat
            $reserveringNaamQuery = mysqli_query($conn, "SELECT naam from reservering WHERE id_reservering = '$id'");
            // Haal de data op die als resultaat wordt gegeven en maak daar een rij van zodat we kunnen pakken wat we precies willen
            $reserveringNaamRow = mysqli_fetch_assoc($reserveringNaamQuery);

            // Toon de de naam die we opgehaald hebben en bij de reservering hoort
            echo "<h3>Bon: " . $reserveringNaamRow['naam'] . "</h3>";

            // Query om alle data van bestellingen voor de reservering van de klant zodat alle data voor de klant kan worden getoond. Dit resultaat wordt in een variabel gestopt.
            $bonQuery = "SELECT bestelling_per_reservering.aantal AS aantal, menu_item.naam AS itemNaam, menu_item.prijs AS prijs, reservering.tafel as tafel, reservering.tijd as tijd, DATE_FORMAT(reservering.datum, \"%d-%M-%Y\") as datum
FROM `bestelling_per_reservering` 
INNER JOIN menu_item ON menu_item.id_item = bestelling_per_reservering.menu_item_id_item 
INNER JOIN menu_categorieen ON menu_categorieen.id_menu_categorieen = menu_item.menu_categorieen_id_menu_categorieen 
INNER JOIN reservering ON reservering.id_reservering = bestelling_per_reservering.reservering_id_reservering 
WHERE reservering_id_reservering = '$id'";
            $bonResult = mysqli_query($conn, $bonQuery);

            $bonDataResult = mysqli_query($conn, $bonQuery);
            $bonDataRow = mysqli_fetch_assoc($bonDataResult);
            echo "Tafel: " . $bonDataRow['tafel'] . "<br>";
            echo "Datum: " . $bonDataRow['datum'] . "<br>";
            echo "Tijd: " . $bonDataRow['tijd'] . "<br>";

            // Als beide queries succesvol zijn
            if ($bonQuery) {
                $bonAmount = mysqli_num_rows($bonResult);
                if ($bonAmount > 0) {
                    // Als er een resultaat is van de queries
                    // Display alle data
                    // Bereken de prijzen, afgaande op de prijs van 1 product en het aantal keer dat het product besteld is
                    // Initialiseer een variabel met een cijfer zodat daar gerekend mee kan worden.
                    // Waarde is 0 omdat de klant alleen hoeft te betalen wat hij besteld heeft.
                    $bonPrijs = 0;
                    echo "<table>";
                    echo "<tr>";
                    echo "<th>Item item</th><th>Prijs per item</th><th>Aantal</th><th>Totaal</th>";
                    echo "</tr>";
                    for ($count = 1; $count <= $bonAmount; $count++) {
                        $bonRow = mysqli_fetch_assoc($bonResult);
                        $subtotaal = ($bonRow['prijs'] * $bonRow['aantal']);
                        // Number_float wordt hier en later gebruikt om te zorgen dat er 2 decimalen worden laten zien, ook al is het "6.60", normaal wordt dit dan "6.6"
                        $subtotaal = number_format((float)$subtotaal, 2);

                        echo "<tr>";
                        echo "<td>" . $bonRow['itemNaam'] . "</td><td>€" . $bonRow['prijs'] . "</td><td>" . $bonRow['aantal'] . "</td><td>€" . $subtotaal . "</td>";
                        echo "</tr>";
                        $bonPrijs = ($bonRow['prijs'] * $bonRow['aantal']) + $bonPrijs;
                        $bonPrijs = number_format((float)$bonPrijs, 2);
                    }
                    echo "<tr>";
                    echo "<td></td><td></td><td></td><td>€" . $bonPrijs . "</td>";
                    echo "</tr>";

                    echo "<tr>";
                    echo "<td></td><td>Betaald</td><td></td><td>€" . number_format((float)$_POST['hoeveelheidBetaald'], 2) . "</td>";
                    echo "</tr>";
                    echo "<tr>";
                    echo "<td></td><td>Terug</td><td></td><td>€" . number_format((float)($_POST['hoeveelheidBetaald'] - $bonPrijs),  2) . "</td>";
                    echo "</tr>";

                    echo "</table>";
                }
            }
            echo "<input type='button' value='Print this page' onclick='printPage()' id='printButton' />";

            include "includes/footer.php";
            ?>
        </div>
    </div>
</div>
<script>
    // Print de bon als pdf voor de klant
    function printPage() {
        document.getElementById("printButton").style.visibility = "hidden";
        window.print();
    }
</script>
</body>
</html>
