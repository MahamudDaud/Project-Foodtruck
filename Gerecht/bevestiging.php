<?php
session_start();

// Genereer een uniek bestelnummer als het nog niet bestaat
if (!isset($_SESSION['uniek_bestelnummer'])) {
    // Combinatie van timestamp en random getal voor uniciteit
    $timestamp = time();
    $randomNumber = mt_rand(1000, 9999);
    $_SESSION['uniek_bestelnummer'] = $timestamp . $randomNumber;
    
    // Haal de gekozen afhaaltijd op
    $afhaaltijd = isset($_POST["tijd"]) ? $_POST["tijd"] : "";
    
    if (!empty($afhaaltijd)) {
        // Converteer de afhaaltijd naar een timestamp
        // Formaat voorbeeld: 14:30
        $vandaag = date('Y-m-d');
        $afhaaltijdTimestamp = strtotime($vandaag . ' ' . $afhaaltijd);
        
        // Bereken vervaltijd (anderhalf uur / 90 minuten vanaf afhaaltijd)
        $_SESSION['bestelnummer_vervaltijd'] = $afhaaltijdTimestamp + (90 * 60);
    } else {
        // Fallback: als er geen afhaaltijd is, gebruik huidige tijd + 90 minuten
        $_SESSION['bestelnummer_vervaltijd'] = time() + (90 * 60);
    }
}

// Haal het unieke bestelnummer en de vervaltijd op
$uniekBestelnummer = isset($_SESSION['uniek_bestelnummer']) ? $_SESSION['uniek_bestelnummer'] : 'Onbekend';
$vervaltijd = isset($_SESSION['bestelnummer_vervaltijd']) ? date('H:i', $_SESSION['bestelnummer_vervaltijd']) : 'Onbekend';

// Maak een korter, gebruiksvriendelijker bestelnummer voor de klant
$kortBestelnummer = substr($uniekBestelnummer, -6);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bevestiging Bestelling</title>
    <link rel="stylesheet" href="bestel.css?v=<?php echo time(); ?>">
    <style>
        img.bestelling-afbeelding {
            max-width: 80px !important;
            max-height: 80px !important;
            width: 80px !important;
            height: 80px !important;
            object-fit: cover;
        }
        
        .bestelnummer {
            background-color: #f8f8f8;
            border: 2px solid #333;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }
        
        .bestelnummer .nummer {
            font-size: 24px;
            font-weight: bold;
            color: #fff;
            letter-spacing: 3px;
            margin: 10px auto;
            font-family: monospace;
            background-color: #140F2D;
            display: inline-block;
            padding: 8px 15px;
            border-radius: 6px;
            width: auto;
            max-width: 200px;
        }
        
        .bestelnummer .info {
            color: #e74c3c;
            font-weight: bold;
        }
        
        .bedankt-bericht {
            font-size: 0.9rem;
            max-width: 50%;
            margin: 0 auto;
            line-height: 1;
        }
        
        .bedankt-bericht p {
            margin-bottom: 12px;
        }
        
        .bedankt-bericht h3 {
            font-size: 1.1rem;
            margin: 10px 0 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bestelling Bevestigd</h1>
        
        <div class="bevestiging-sectie">
            <h2>Klantgegevens</h2>
            <div class="klantgegevens">
                <?php
                // Controleer of het formulier is verzonden
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    // Haal de klantgegevens op uit het formulier
                    $naam = isset($_POST["Naam"]) ? htmlspecialchars($_POST["Naam"], ENT_QUOTES, 'UTF-8') : "";
                    $achternaam = isset($_POST["Achternaam"]) ? htmlspecialchars($_POST["Achternaam"], ENT_QUOTES, 'UTF-8') : "";
                    $tijd = isset($_POST["tijd"]) ? htmlspecialchars($_POST["tijd"], ENT_QUOTES, 'UTF-8') : "";
                    $betaalmethode = isset($_POST["betaal"]) ? htmlspecialchars($_POST["betaal"], ENT_QUOTES, 'UTF-8') : "";
                    $bank = isset($_POST["bank"]) ? htmlspecialchars($_POST["bank"], ENT_QUOTES, 'UTF-8') : "";
                    
                    // Toon de klantgegevens
                    echo "<p><strong>Naam:</strong> $naam $achternaam</p>";
                    echo "<p><strong>Afhaaltijd:</strong> $tijd uur</p>";
                    echo "<p><strong>Betaalmethode:</strong> $betaalmethode";
                    if ($betaalmethode == "iDeal" && !empty($bank)) {
                        echo " ($bank)";
                    }
                    echo "</p>";
                } else {
                    echo "<p>Geen gegevens ontvangen. Ga terug naar de <a href='bestellingpagine.php' class='terug-link'>bestellingspagina</a>.</p>";
                }
                ?>
            </div>
        </div>

        <div class="bevestiging-sectie">
            <h2>Bestelgegevens</h2>
            <?php
            // Toon de bestellingen uit de sessie
            if (isset($_SESSION['bestellingen']) && count($_SESSION['bestellingen']) > 0) {
                $totaal = 0;
                echo '<ul class="bestellingen-lijst">';
                foreach ($_SESSION['bestellingen'] as $bestelling) {
                    $gerecht = htmlspecialchars($bestelling['gerecht'], ENT_QUOTES, 'UTF-8');
                    $aantal = $bestelling['aantal'];
                    $prijs = number_format($bestelling['prijs'], 2, ',', '.');
                    $subtotaal = $bestelling['aantal'] * $bestelling['prijs'];
                    $totaal += $subtotaal;
                    $subtotaalFormatted = number_format($subtotaal, 2, ',', '.');
                    
                    // Gebruik de opgeslagen afbeeldingspad of een standaard afbeelding
                    $afbeelding = isset($bestelling['afbeelding']) ? $bestelling['afbeelding'] : 'images/default-gerecht.jpg';
                    
                    // Check of het bestand bestaat, anders gebruik een fallback
                    if (!file_exists($afbeelding)) {
                        // Probeer de afbeelding te vinden gebaseerd op de naam van het gerecht
                        $mogelijkeAfbeeldingen = [
                            "images/" . $gerecht . ".jpg",
                            "images/" . $gerecht . "'s.jpg",
                            "images/" . str_replace("'", "", $gerecht) . ".jpg"
                        ];
                        
                        foreach ($mogelijkeAfbeeldingen as $mogelijkeAfbeelding) {
                            if (file_exists($mogelijkeAfbeelding)) {
                                $afbeelding = $mogelijkeAfbeelding;
                                break;
                            }
                        }
                    }
                    
                    echo '<li class="bestelling-item">';
                    echo '<img src="' . htmlspecialchars($afbeelding, ENT_QUOTES, 'UTF-8') . '" alt="' . $gerecht . '" class="bestelling-afbeelding" loading="lazy" width="80" height="80" style="width:80px; height:80px; object-fit:cover;">';
                    echo '<div class="bestelling-info">';
                    echo '<div class="bestelling-naam">' . $gerecht . '</div>';
                    echo '<div class="bestelling-details">Aantal: ' . $aantal . '</div>';
                    echo '<div class="bestelling-details">Prijs per stuk: €' . $prijs . '</div>';
                    echo '</div>';
                    echo '<div class="bestelling-prijs">€' . $subtotaalFormatted . '</div>';
                    echo '</li>';
                }
                echo '</ul>';
                
                // Samenvatting sectie
                echo '<div class="samenvatting">';
                echo '<span class="samenvatting-label">Subtotaal</span>';
                echo '<span class="samenvatting-waarde">€' . number_format($totaal, 2, ',', '.') . '</span>';
                echo '</div>';
                
                echo '<div class="samenvatting">';
                echo '<span class="samenvatting-label">Verzend- en verwerkingskosten</span>';
                echo '<span class="samenvatting-waarde">Gratis</span>';
                echo '</div>';
                
                echo '<div class="totaal-betalen">';
                echo '<span>Totaal te betalen</span>';
                echo '<span>€' . number_format($totaal, 2, ',', '.') . '</span>';
                echo '</div>';
            } else {
                echo "<p>Je hebt nog geen bestellingen geplaatst.</p>";
            }
            ?>
            
            <div class="bedankt-bericht">
                <p>Bedankt voor je bestelling! Je kunt je bestelling afhalen op de aangegeven tijd.</p>
                
                <div class="bestelnummer">
                    <h3>Jouw unieke bestelnummer:</h3>
                    <div class="nummer"><?php echo $kortBestelnummer; ?></div>
                    <p class="info">Bewaar dit nummer! U heeft het nodig bij het afhalen van uw bestelling.</p>
                    <p>Dit bestelnummer is geldig tot <?php echo $vervaltijd; ?> uur</p>
                    <p>Na deze tijd vervalt het bestelnummer en moet de bestelling opnieuw geplaatst worden.</p>
                </div>
            </div>
            
            <a href="destroy.php" class="terug-link">Terug naar hoofdpagina</a>
        </div>
    </div>
</body>
</html>