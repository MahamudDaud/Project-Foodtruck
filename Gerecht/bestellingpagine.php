<?php
// Start de sessie om gegevens te kunnen delen tussen pagina's
session_start();

// Laad de functies in
require_once 'functies.php';

// Controleer of bestellen mogelijk is
$bestel_status = controleerBestellingMogelijk();
$kan_bestellen = $bestel_status['kan_bestellen'];
$tijd_bericht = $bestel_status['tijd_bericht'];

// Bereken ophaaltijd
$ophaaltijd_info = berekenOphaaltijd();
$ophaaltijd_uur = $ophaaltijd_info['ophaaltijd_uur'];
$ophaaltijd_minuut = $ophaaltijd_info['ophaaltijd_minuut'];
$ophaaltijd_bericht = $ophaaltijd_info['ophaaltijd_bericht'];

// Als er geen bestellingen zijn, stuur de gebruiker terug naar de menukaart
if(!isset($_SESSION['bestellingen']) || count($_SESSION['bestellingen']) === 0) {
    header("Location: gerecht.php");
    exit;
}

// Maak een variabele voor de bestellingen en totaalbedrag
$bestellingen = $_SESSION['bestellingen'];
$totaal = 0;
foreach($bestellingen as $bestelling) {
    $totaal += $bestelling['prijs'] * $bestelling['aantal'];
}

// Validatie fouten array
$fouten = [];

// Form verwerking
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Controleer eerst of we binnen besteluren zijn
    if (!$kan_bestellen) {
        $fouten[] = $tijd_bericht;
    } else {
        // Valideer de invoervelden van het formulier
        $required_fields = ['voornaam', 'achternaam', 'email', 'telefoonnummer', 'betaalmethode'];
        
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                $fouten[] = ucfirst($field) . " is verplicht.";
            }
        }
        
        // Email validatie
        if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $fouten[] = "Email is niet geldig.";
        }
        
        // Telefoonnummer validatie
        if (!empty($_POST['telefoonnummer']) && !preg_match("/^[0-9]{10}$/", $_POST['telefoonnummer'])) {
            $fouten[] = "Telefoonnummer moet uit 10 cijfers bestaan.";
        }

        // Als er geen fouten zijn, sla de gegevens op en ga naar de bevestigingspagina
        if(empty($fouten)) {
            // Sla de klantgegevens op in de sessie
            $_SESSION['klant'] = [
                'voornaam' => $_POST['voornaam'],
                'achternaam' => $_POST['achternaam'],
                'email' => $_POST['email'],
                'telefoonnummer' => $_POST['telefoonnummer'],
                'betaalmethode' => $_POST['betaalmethode'],
                'ophaaltijd' => sprintf("%02d:%02d", $ophaaltijd_uur, $ophaaltijd_minuut)
            ];

            // Stuur door naar de bevestigingspagina
            header("Location: bevestiging.php");
            exit;
        }
    }
}

// Helper functie voor het maken van formulier velden
function generateFormGroup($label, $name, $type, $value = '', $placeholder = '') {
    echo '<div class="form-group">';
    echo '<label for="' . $name . '">' . $label . '</label>';
    echo '<input type="' . $type . '" id="' . $name . '" name="' . $name . '" value="' . htmlspecialchars($value) . '" placeholder="' . $placeholder . '">';
    echo '</div>';
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestelling Plaatsen - FoodTruck</title>
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
            <h1>Bestelling Afronden</h1>
            
            <?php if (!$kan_bestellen): ?>
                <div class="tijd-melding">
                    <p><?php echo $tijd_bericht; ?></p>
                    <p><a href="gerecht.php" class="terug-link">Terug naar menu</a></p>
                </div>
            <?php else: ?>
                <!-- Bestel overzicht -->
                <section class="bestel-overzicht">
                    <h2>Je Bestelling</h2>
                    
                    <ul class="bestellingen-lijst">
                        <?php 
                        foreach ($bestellingen as $item): 
                        ?>
                            <li class="bestelling-item">
                                <img src="<?php echo $item['afbeelding']; ?>" alt="<?php echo $item['naam']; ?>" class="bestelling-afbeelding">
                                <div class="bestelling-info">
                                    <div class="bestelling-naam"><?php echo $item['naam']; ?></div>
                                    <div class="bestelling-details">Aantal: <?php echo $item['aantal']; ?></div>
                                </div>
                                <div class="bestelling-prijs">€<?php echo number_format($item['prijs'] * $item['aantal'], 2, ',', '.'); ?></div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <div class="totaal">
                        <span>Totaal:</span>
                        <span>€<?php echo number_format($totaal, 2, ',', '.'); ?></span>
                    </div>
                </section>
                
                <!-- Afhalen informatie -->
                <div class="afhalen-informatie">
                    <h3>Afhaalgegevens</h3>
                    <p>Je kunt je bestelling ophalen vanaf: <strong><?php echo sprintf("%02d:%02d", $ophaaltijd_uur, $ophaaltijd_minuut); ?></strong></p>
                </div>
                
                <!-- Contactgegevens formulier -->
                <form method="post" action="">
                    <?php if (!empty($fouten)): ?>
                        <div class="error">
                            <ul>
                                <?php foreach ($fouten as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <section class="formulier-sectie">
                        <h2>Contactgegevens</h2>
                        
                        <?php
                        // Formulier velden genereren met helper functie
                        generateFormGroup('Voornaam', 'voornaam', 'text', $_POST['voornaam'] ?? '', 'Uw voornaam');
                        generateFormGroup('Achternaam', 'achternaam', 'text', $_POST['achternaam'] ?? '', 'Uw achternaam');
                        generateFormGroup('E-mail', 'email', 'email', $_POST['email'] ?? '', 'voorbeeld@email.com');
                        generateFormGroup('Telefoonnummer', 'telefoonnummer', 'tel', $_POST['telefoonnummer'] ?? '', '0612345678');
                        ?>
                    </section>
                    
                    <section class="formulier-sectie">
                        <h2>Betaalmethode</h2>
                        
                        <div class="betaalmethode-opties">
                            <div class="radio-option">
                                <label>
                                    <input type="radio" name="betaalmethode" value="Contant" <?php if (isset($_POST['betaalmethode']) && $_POST['betaalmethode'] == 'Contant') echo 'checked'; ?>>
                                    Contant bij afhalen
                                </label>
                            </div>
                            
                            <div class="radio-option">
                                <label>
                                    <input type="radio" name="betaalmethode" value="Pin" <?php if (isset($_POST['betaalmethode']) && $_POST['betaalmethode'] == 'Pin') echo 'checked'; ?>>
                                    Pin bij afhalen
                                </label>
                            </div>
                            
                            <div class="radio-option">
                                <label>
                                    <input type="radio" name="betaalmethode" value="iDeal" <?php if (isset($_POST['betaalmethode']) && $_POST['betaalmethode'] == 'iDeal') echo 'checked'; ?>>
                                    iDeal (direct betalen)
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" name="submit" class="submit-btn">Bestelling Afronden</button>
                    </section>
                </form>
            <?php endif; ?>
            
            <a href="gerecht.php" class="terug-link">Terug naar menu</a>
            <a href="destroy.php" class="terug-link">Winkelwagen leegmaken</a>
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
            <p>&copy; <?php echo date('Y'); ?> Food Truck - Alle rechten voorbehouden</p>
        </div>
    </footer>
</body>
</html>
