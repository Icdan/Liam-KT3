<?php
//Start the sessie om met PHP sessies te starten
session_start();
//Maak connectie met de database;
include "../db/db_connection.php";

//Pak de ID van de reservering waar we mee aan de slag willen. Deze wordt geinitieerd wanneer we naar de bestellingen van een reservering kijken
$id = $_SESSION['id_reservering'];

//Query naar de database voor de info die we willen
$categoryItemQuery = "SELECT menu_item.naam AS itemNaam, menu_item.id_item, menu_categorieen.id_menu_categorieen AS categorie_id, menu_item.prijs AS prijs
FROM menu_item
INNER JOIN menu_categorieen ON menu_categorieen.id_menu_categorieen = menu_item.menu_categorieen_id_menu_categorieen
WHERE menu_categorieen.id_menu_categorieen = 4";
//Stoppen de resultaten van de query in een variabel
$categoryResult = mysqli_query($conn, $categoryItemQuery);

// Initieer variabel tellenForms naar 0
$tellenForms = 0;

echo "<table>";
// Als 1 of meer resultaten zijn van de database query wordt er gelooped zodat we alle gerechten neer kunnen zetten. Ook worden er een aantal hidden inputs neergezet zodat die niet zichtbaar zijn op het normale scherm
// maar we wel de data kunnen gebruiken voor toevoegen van bestellingen
// Er zijn 2 forms. De eerste is voor data voor het toevoegen van bestellingen, de tweede is voor het wijzigen van de prijs van een gerecht
if (mysqli_num_rows($categoryResult) > 0) {
    while ($categoryItemRow = mysqli_fetch_assoc($categoryResult)) {
        $tellenForms++;
        echo "<tr>";
        echo "<td><form name='addToOrderForm' method='post' action='reserveringen-overzicht.php'>
<input type='submit' name='opslaanInBestelling' value='" . $categoryItemRow['itemNaam'] . "'>
<input type='hidden' value='" . $categoryItemRow['id_item'] . "' name='reservationOrderItemid'>
<input type='hidden' value='" . $categoryItemRow['categorie_id'] . "' name='reservationOrderCategoryid'>
<input type='input' name='reservationOrderAmount'>
<input type='hidden' value='$id' name='reservationOrderid'>
</form>
</td>
<td>€
<form method='post' action='reserveringen-overzicht.php' id='editDrinksForm" . $tellenForms . "'></form>
<input type='numbers' id='menuItemPrijs' name='prijsDieVeranderdWord' value='". $categoryItemRow['prijs'] . "' form='editDrinksForm" . $tellenForms . "'/>
</td>
<td>
<input type='hidden' name='prijsWijzigingDatabase' value='" . $categoryItemRow['id_item'] ."' form='editDrinksForm" . $tellenForms . "'/>
<input type='submit' value='Prijswijziging opslaan' form='editDrinksForm" . $tellenForms . "' name='opslaanPrijsWijziging'/>
</td>";
        echo "</tr>";
    }
}
echo "<table>";