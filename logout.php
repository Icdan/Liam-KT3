<?php
// Vernietig de bestaande sessie waardoor de gebruiker uitgelogd wordt en verwezen naar de home pagina
session_start();
session_unset();
session_destroy();

echo "<p align='center' style='margin-top:20%'>U bent uigelogd</p>";

header("Refresh:1; url=index.php");