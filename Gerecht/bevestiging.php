<?php
session_start(); // Start de sessie om toegang te krijgen tot sessievariabelen
require_once 'functies.php'; // Importeer de functiebibliotheek met herbruikbare functies

// Controleer of bestellen mogelijk is en bereken ophaaltijd
$bestel_status = controleerBestellingMogelijk(); // Haal status op of bestellen mogelijk is
$ophaaltijd_info = berekenOphaaltijd(); // Bereken wanneer de bestelling kan worden opgehaald
$ophaaltijd = sprintf("%02d:%02d", $ophaaltijd_info['ophaaltijd_uur'], $ophaaltijd_info['ophaaltijd_minuut']); // Formatteer de ophaaltijd als "uu:mm"

// Controleer of er bestellingen en klantgegevens zijn
if (!isset($_SESSION['bestellingen']) || empty($_SESSION['bestellingen']) || 
    !isset($_SESSION['klant']) || empty($_SESSION['klant'])) { // Controleer of bestellingen en klantgegevens bestaan en niet leeg zijn
    // Stuur terug naar menu als er geen bestellingen of klantgegevens zijn
    header("Location: gerecht.php"); // Redirect naar de menukaart pagina
    exit; // Stop de uitvoering van het script
}

// Genereer een bestelnummer als die nog niet bestaat
if (!isset($_SESSION['bestelnummer'])) { // Controleer of er nog geen bestelnummer is
    $_SESSION['bestelnummer'] = 'FB-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8)); // Genereer uniek bestelnummer met prefix FB
    $_SESSION['besteltijd'] = date('H:i'); // Sla huidige tijd op als besteltijd
    $_SESSION['verlooptijd'] = date('H:i', strtotime('+30 minutes')); // Bereken verlooptijd (30 minuten na nu)
}

// Haal bestellingen en klantgegevens op
$bestellingen = $_SESSION['bestellingen']; // Haal bestellingen uit de sessie
$klant = $_SESSION['klant']; // Haal klantgegevens uit de sessie
$bestelnummer = $_SESSION['bestelnummer']; // Haal bestelnummer uit de sessie
$besteltijd = $_SESSION['besteltijd']; // Haal besteltijd uit de sessie
$verlooptijd = $_SESSION['verlooptijd']; // Haal verlooptijd uit de sessie

// Bereken het totaalbedrag
$totaal = 0; // Initialiseer totaal op 0
foreach ($bestellingen as $bestelling) { // Loop door alle bestellingen
    $totaal += $bestelling['prijs'] * $bestelling['aantal']; // Bereken prijs * aantal en tel op bij totaal
}

// Helper functie voor het tonen van klantgegevens
function toonKlantgegeven($label, $waarde) { // Functie om klantgegevens geformatteerd weer te geven
    echo '<div class="klantgegevens-rij">'; // Open div voor de rij
    echo '<span class="klantgegevens-label">' . $label . ':</span>'; // Toon label met dubbele punt
    echo '<span>' . htmlspecialchars($waarde) . '</span>'; // Toon veilig geëscapede waarde
    echo '</div>'; // Sluit div voor de rij
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestelling Bevestigd - FoodTruck</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="bestel.css">
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="images/Logo.jpg" alt="Foodtruck Logo" class="logo">
            <h1>Food Truck</h1>
        </div>
        <nav>
            <ul>
                <li><a href="hoofdpagina/index.php">Home</a></li>
                <li><a href="gerecht.php">Menu</a></li>
                <li><a href="#">Over ons</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="container">
            <div class="bevestiging-sectie">
                <h1 class="bevestiging-titel">Bedankt voor je bestelling!</h1>
                <p>Je bestelling is succesvol ontvangen en wordt klaargemaakt.</p>
                
                <!-- Bestelnummer sectie -->
                <div class="bestelnummer-info">
                    <p class="bestelnummer-instructie">Jouw bestelnummer:</p>
                    <span class="bestelnummer"><?php echo $bestelnummer; ?></span> <!-- Toon het bestelnummer -->
                    <p>Geef dit nummer bij het afhalen van je bestelling.</p>
                    <p class="bestelnummer-vervaltijd">Bestelling geplaatst om <?php echo $besteltijd; ?><br> <!-- Toon de besteltijd -->
                    Geldig tot <?php echo $verlooptijd; ?></p> <!-- Toon de verlooptijd -->
                </div>
                
                <!-- Afhaal informatie -->
                <div class="afhalen-info">
                    <h3>Afhaalinformatie</h3>
                    <p>Je kunt je bestelling ophalen om: <strong><?php echo $ophaaltijd; ?></strong></p> <!-- Toon de ophaaltijd -->
                    <p>Vergeet niet je bestelnummer mee te nemen!</p>
                </div>
                
                <!-- Klantgegevens -->
                <div class="klantgegevens">
                    <h3>Klantgegevens</h3>
                    <?php
                    toonKlantgegeven('Naam', $klant['voornaam'] . ' ' . $klant['achternaam']); // Toon voornaam en achternaam
                    toonKlantgegeven('E-mail', $klant['email']); // Toon email
                    toonKlantgegeven('Telefoonnummer', $klant['telefoonnummer']); // Toon telefoonnummer
                    toonKlantgegeven('Betaalmethode', $klant['betaalmethode']); // Toon gekozen betaalmethode
                    ?>
                </div>
                
                <!-- Bestel overzicht -->
                <section class="bestel-overzicht">
                    <h2>Bestelde items</h2>
                    
                    <ul class="bestellingen-lijst">
                        <?php foreach ($bestellingen as $item): ?> <!-- Loop door alle bestelde items -->
                            <li class="bestelling-item">
                                <img src="<?php echo $item['afbeelding']; ?>" alt="<?php echo $item['naam']; ?>" class="bestelling-afbeelding"> <!-- Toon afbeelding van gerecht -->
                                <div class="bestelling-info">
                                    <div class="bestelling-naam"><?php echo $item['naam']; ?></div> <!-- Toon naam van gerecht -->
                                    <div class="bestelling-details">Aantal: <?php echo $item['aantal']; ?></div> <!-- Toon aantal besteld -->
                                </div>
                                <div class="bestelling-prijs">€<?php echo number_format($item['prijs'] * $item['aantal'], 2, ',', '.'); ?></div> <!-- Toon totaalprijs met Nederlandse notatie -->
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <div class="totaal">
                        <span>Totaal:</span>
                        <span>€<?php echo number_format($totaal, 2, ',', '.'); ?></span> <!-- Toon totaalbedrag met Nederlandse notatie -->
                    </div>
                </section>
                
                <p>Een bevestiging is verstuurd naar je e-mail.</p>
                <a href="gerecht.php" class="terug-link">Terug naar menu</a> <!-- Link om terug te gaan naar het menu -->
            </div>
        </div>
    </main>
    
    <footer>
        <div class="footer-content">
            <div class="footer-logo">
                <img src="images/Logo.jpg" alt="Foodtruck Logo" class="logo">
                <h3>Food Truck</h3>
            </div>
            <div class="footer-info">
                <h4>Openingstijden</h4>
                <p>Maandag - Vrijdag: 09:00 - 17:00</p>
                <p>Zaterdag - Zondag: 07:00 - 19:00</p>
            </div>
            <div class="footer-contact">
                <h4>Contact</h4>
                <p>Email: info@foodtruck.nl</p>
                <p>Telefoon: 06-12345678</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Food Truck - Alle rechten voorbehouden</p> <!-- Toon het huidige jaartal dynamisch -->
        </div>
    </footer>
    
    <?php
    // Verwijder de bestellingen uit de sessie (winkelwagen leegmaken)
    unset($_SESSION['bestellingen']); // Maak de winkelwagen leeg na bevestiging
    ?>
</body>
</html>