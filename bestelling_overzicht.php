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
    <title>Bestel overzicht</title>
</head>
<body>
<?php
include "includes/navbar.php";
?>
<div class="container">
    <div class="row">
        <div class="col-12 text-center">
            <?php
            // Initieer variabel met de id van de reservering waar we op geklikt hebben
            $id = $_POST['id_reservering'];
            // Stop de id in een sessie variabel
            $_SESSION['id_reservering'] = $_POST['id_reservering'];

            // Maak een query naar de database voor de naam onder wie de reservering staat
            $reserveringNaamQuery = mysqli_query($conn, "SELECT naam from reservering WHERE id_reservering = '$id'");
            // Haal de data op die als resultaat wordt gegeven en maak daar een rij van zodat we kunnen pakken wat we precies willen
            $reserveringNaamRow = mysqli_fetch_assoc($reserveringNaamQuery);

            // Toon de de naam die we opgehaald hebben en bij de reservering hoort
            echo "<h3>Reservering: " . $reserveringNaamRow['naam'] . "</h3>";

            // Als de gebruiker 'keuken' is die de website ziet, kan die alleen maar de eetgerechten zien die besteld zijn
            // Als het een andere gebruiker is kan die alle gerechten zien
            if ($_SESSION['gebruikersnaam'] == "keuken") {
                $bestellingQuery = mysqli_query($conn, "SELECT bestelling_per_reservering.aantal AS aantal, menu_item.naam AS itemNaam, menu_item.prijs AS prijs 
FROM `bestelling_per_reservering` 
INNER JOIN menu_item ON menu_item.id_item = bestelling_per_reservering.menu_item_id_item 
INNER JOIN menu_categorieen ON menu_categorieen.id_menu_categorieen = menu_item.menu_categorieen_id_menu_categorieen 
INNER JOIN reservering ON reservering.id_reservering = bestelling_per_reservering.reservering_id_reservering 
WHERE reservering_id_reservering = '$id' AND (menu_categorieen.id_menu_categorieen = '5' OR menu_categorieen.id_menu_categorieen = '6')");
            } else {
                $bestellingQuery = mysqli_query($conn, "SELECT bestelling_per_reservering.aantal AS aantal, menu_item.naam AS itemNaam, menu_item.prijs AS prijs 
FROM `bestelling_per_reservering` 
INNER JOIN menu_item ON menu_item.id_item = bestelling_per_reservering.menu_item_id_item 
INNER JOIN menu_categorieen ON menu_categorieen.id_menu_categorieen = menu_item.menu_categorieen_id_menu_categorieen 
INNER JOIN reservering ON reservering.id_reservering = bestelling_per_reservering.reservering_id_reservering 
WHERE reservering_id_reservering = '$id'");
            }

            // Query voor de totaalprijs van alle bestellingen zodat de klant weet hoeveel er betaald moet worden zonder zelf te hoeven rekenen. Dit resultaat wordt in een variabel gestopt.
            $totaalPrijsQuery = "SELECT SUM(menu_item.prijs) AS totaalprijs
FROM `bestelling_per_reservering` 
INNER JOIN menu_item ON menu_item.id_item = bestelling_per_reservering.menu_item_id_item 
INNER JOIN menu_categorieen ON menu_categorieen.id_menu_categorieen = menu_item.menu_categorieen_id_menu_categorieen 
INNER JOIN reservering ON reservering.id_reservering = bestelling_per_reservering.reservering_id_reservering 
WHERE reservering_id_reservering = '$id'";
            $totaalPrijsResult = mysqli_query($conn, $totaalPrijsQuery);

            // Als de query werkt
            if ($bestellingQuery) {
                // We tellen het aantal rijen dat we terugkrijgen
                $bestelAmount = mysqli_num_rows($bestellingQuery);
                // Als het 1 of meer is gaan de door de resultaten heen loopen en alle data weergeven
                // Als er geen data is zijn er geen bestellingen voor die reservering en wordt dat gemeld
                if ($bestelAmount > 0) {
                    // Bereken de prijzen, afgaande op de prijs van 1 product en het aantal keer dat het product besteld is
                    // Initialiseer een variabel met een cijfer zodat daar gerekend mee kan worden.
                    // Waarde is 0 omdat de klant alleen hoeft te betalen wat hij besteld heeft.
                    $totaalBestellingenPrijs = 0;
                    echo "<table>";
                    echo "<tr>";
                    echo "<th>Gerecht</th><th>Aantal</th><th>Prijs per item</th><th>Totaal</th>";
                    echo "</tr>";
                    for ($count = 1; $count <= $bestelAmount; $count++) {
                        $bestellingRow = mysqli_fetch_assoc($bestellingQuery);
//                        echo "<tr>";
//                        echo "<td>" . $bestellingRow['itemNaam'] . "</td><td>" . $bestellingRow['aantal'] . "</td><td>€" . $bestellingRow['prijs'] . "</td>";
//                        echo "</tr>";

                        $subtotaal = ($bestellingRow['prijs'] * $bestellingRow['aantal']);
                        // Number_float wordt hier en later gebruikt om te zorgen dat er 2 decimalen worden laten zien, ook al is het "6.60", normaal wordt dit dan "6.6"
                        $subtotaal = number_format((float)$subtotaal, 2);
                        echo "<tr>";
                        echo "<td>" . $bestellingRow['itemNaam'] . "</td><td>€" . $bestellingRow['prijs'] . "</td><td>" . $bestellingRow['aantal'] . "</td><td>€" . $subtotaal . "</td>";
                        echo "</tr>";
                        $totaalBestellingenPrijs = ($bestellingRow['prijs'] * $bestellingRow['aantal']) + $totaalBestellingenPrijs;
                        $totaalBestellingenPrijs = number_format((float)$totaalBestellingenPrijs, 2);
                    }
                    echo "<tr>";
                    echo "<td></td><td></td><td></td><td>€" . $totaalBestellingenPrijs . "</td>";
                    echo "</tr>";
                    echo "</table>";
                } else {
                    echo "<p>Sorry, er zijn nog geen bestellingen geplaatst</p>";
                }
            }
            // Als het niet de keuken of bar is die ingelogd zijn, is het waarschijnlijk iemand die een bestelling moet kunnen plaatsen.
            // Dus als het geen keuken of bar, worden er producten laten zien voor een bestelling en kan de prijs van een product gewijzigd worden
            if ($_SESSION['gebruikersnaam'] !== "keuken" && $_SESSION['gebruikersnaam'] !== "bar") {
                echo "
            <hr>
        </div>
    </div>
    <div class='row'>
        <div class='col-12 text-center'>
            <h3>Bestelling plaatsen</h3>
        </div>
    </div>
    <div class='row'>
        <div class='col-3'>
            <button type='button' onclick='buttonWarmeDranken()'>Warme dranken</button>
            <br>
            <button type='button' onclick='buttonBieren()'>Bieren</button>
            <br>
            <button type='button' onclick='buttonHuiswijnen()'>Huiswijnen</button>
            <br>
            <button type='button' onclick='buttonFrisdranken()'>Frisdranken</button>
            <br>
            <button type='button' onclick='buttonWarmeHapjes()'>Warme hapjes</button>
            <br>
            <button type='button' onclick='buttonKoudeHapjes()'>Koude hapjes</button>
            <br>
        </div>
        <div class='col-9 text-center' id='ajax-items-container'>
        </div>
    </div>
    <hr>
    <div class='row'>
        <div class='col-12 text-center' id='ajax-bon-container'>
            <form method='post' action='bon.php'>
                <label>Hoe is er betaald?</label><br>
                <select name='betaalOptie'>
                    <option value='pin'>PIN / Creditcard</option>
                    <option value='contant'>Contant</option>
                </select>
                <br><br>
                <label>Hoeveel is er betaald?</label><br>
                <input type='number' name='hoeveelheidBetaald' step='any'>
                <br>
                <input type='submit' name='printBon' value='Print bon'>
            </form>
        </div>
    </div>";
            }
            ?>
        </div>
    </div>
</div>
<!-- Javascript -->
<script>
    // Hier gaan we met AJAX aan de slag zodat we tussen de verschillende categorieen van gerechten kunnen wisselen zonder elke keer de pagina hoeven te herladen
    // Verschillende knoppen laden de verschillende functies terwijl de herhalende code in 1 functie is gestopt die opnieuw wordt aangeroepen
    var xhttp = new XMLHttpRequest();

    function ajaxCallMenuItems() {
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("ajax-items-container").innerHTML = this.responseText;
            }
        };
    }

    function buttonWarmeDranken() {
        ajaxCallMenuItems();
        xhttp.open("GET", "ajax/warmedranken.php", true);
        xhttp.send();
    }

    function buttonBieren() {
        ajaxCallMenuItems();
        xhttp.open("GET", "ajax/bieren.php", true);
        xhttp.send();
    }

    function buttonHuiswijnen() {
        ajaxCallMenuItems();
        xhttp.open("GET", "ajax/huiswijnen.php", true);
        xhttp.send();
    }

    function buttonFrisdranken() {
        ajaxCallMenuItems();
        xhttp.open("GET", "ajax/frisdranken.php", true);
        xhttp.send();
    }

    function buttonWarmeHapjes() {
        ajaxCallMenuItems();
        xhttp.open("GET", "ajax/warmehapjes.php", true);
        xhttp.send();
    }

    function buttonKoudeHapjes() {
        ajaxCallMenuItems();
        xhttp.open("GET", "ajax/koudehapjes.php", true);
        xhttp.send();
    }

</script>
<?php
include "includes/footer.php";
?>
</body>
</html>