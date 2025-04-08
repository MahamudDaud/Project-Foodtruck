<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Overzicht Bestellingen</title>
    <link rel="stylesheet" href="bestel.css?v=<?php echo time(); ?>">
    <style>
        img.bestelling-afbeelding {
            max-width: 80px !important;
            max-height: 80px !important;
            width: 80px !important;
            height: 80px !important;
            object-fit: cover;
        }
        
        .terug-link {
            display: inline-block;
            background-color: #456990;
            color: white;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 30px;
            margin-top: 15px;
            text-align: center;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        
        .terug-link:hover {
            background-color: #2a4258;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Overzicht van je bestellingen</h1>
        
        <div class="bestel-overzicht">
            <?php
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
                echo '<div class="totaal"><span>Totaal:</span><span>€' . number_format($totaal, 2, ',', '.') . '</span></div>';
            } else {
                echo "<p>Je hebt nog geen bestellingen geplaatst.</p>";
            }
            ?>
        </div>

        <div class="formulier-sectie">
            <h2>Jouw gegevens</h2>
            <form action="bevestiging.php" method="post">
                <div class="form-group">
                    <label for="naam">Naam:</label>
                    <input type="text" name="Naam" id="naam" required>
                </div>
                
                <div class="form-group">
                    <label for="achternaam">Achternaam:</label>
                    <input type="text" name="Achternaam" id="achternaam" required>
                </div>
                
                <div class="form-group">
                    <label for="tijd">Afhaaltijd:</label>
                    <select name="tijd" id="tijd" required>
                        <option value="">-- Selecteer afhaaltijd --</option>
                        <!-- Tijdsopties worden met JavaScript gevuld -->
                    </select>
                </div>
                
                <h2>Betaalmethode</h2>
                <div class="betaalmethode-opties">
                    <div class="radio-option">
                        <input type="radio" name="betaal" id="creditcard" value="Creditcard of bankpas" required>
                        <label for="creditcard">Creditcard of bankpas</label>
                    </div>
                    
                    <div class="radio-option">
                        <input type="radio" name="betaal" id="paypal" value="Paypal">
                        <label for="paypal">Paypal</label>
                    </div>
                    
                    <div class="radio-option">
                        <input type="radio" name="betaal" id="klarna" value="Klarna">
                        <label for="klarna">Klarna</label>
                    </div>
                    
                    <div class="radio-option">
                        <input type="radio" name="betaal" id="ideal" value="iDeal">
                        <label for="ideal">iDeal</label>
                        
                        <select name="bank" id="bank">
                            <option value="">-- Selecteer bank --</option>
                            <option value="ING">ING</option>
                            <option value="Rabobank">Rabobank</option>
                            <option value="ABN AMRO">ABN AMRO</option>
                            <option value="SNS Bank">SNS Bank</option>
                        </select>
                    </div>
                </div>
                
                <input type="submit" value="Bevestig bestelling" class="submit-btn" id="bevestigBtn">
                <a href="gerecht.html" class="terug-link">Terug naar menu</a>
            </form>
        </div>
    </div>

    <script>
        // Toon de bankselectie alleen wanneer iDeal is geselecteerd
        document.addEventListener('DOMContentLoaded', function() {
            const idealRadio = document.getElementById('ideal');
            const bankSelect = document.getElementById('bank');
            const bevestigBtn = document.getElementById('bevestigBtn');
            const hasItems = <?php echo (isset($_SESSION['bestellingen']) && count($_SESSION['bestellingen']) > 0) ? 'true' : 'false'; ?>;
            
            // Check of er items in het winkelmandje zitten
            if (!hasItems) {
                bevestigBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    alert('U heeft nog geen gerechten geselecteerd. Ga terug naar het menu om gerechten te kiezen.');
                });
            }
            
            // Verberg de bankselectie initieel
            bankSelect.style.display = idealRadio.checked ? 'inline-block' : 'none';
            
            // Voeg event listeners toe aan alle radio buttons
            document.querySelectorAll('input[name="betaal"]').forEach(function(radio) {
                radio.addEventListener('change', function() {
                    bankSelect.style.display = idealRadio.checked ? 'inline-block' : 'none';
                    // Maak bank veld verplicht alleen als iDeal is geselecteerd
                    bankSelect.required = idealRadio.checked;
                });
            });

            // Afhaaltijden genereren
            function generatePickupTimes() {
                const tijdSelect = document.getElementById('tijd');
                const nu = new Date();
                const huidigeUur = nu.getHours();
                const huidigeMinuut = nu.getMinutes();
                
                // Bereken hoeveel minuten er nog zijn tot het volgende kwartier
                let minuten = huidigeMinuut % 15;
                minuten = 15 - minuten;
                
                // Maak een nieuwe datum voor het eerstvolgende kwartier
                const eersteKwartier = new Date(nu);
                eersteKwartier.setMinutes(huidigeMinuut + minuten);
                eersteKwartier.setSeconds(0);
                
                // Als het minder dan 20 minuten tot het volgende kwartier is, sla het over
                // en begin bij het kwartier daarna
                const minimaleVoorbereidingsTijd = 20; // in minuten
                let startTijd;
                
                if (minuten < minimaleVoorbereidingsTijd) {
                    // Ga naar het kwartier na het eerstvolgende kwartier
                    startTijd = new Date(eersteKwartier);
                    startTijd.setMinutes(eersteKwartier.getMinutes() + 15);
                } else {
                    startTijd = eersteKwartier;
                }
                
                // Genereer tijdsopties voor de komende 6 uur met intervallen van 15 minuten
                const eindTijd = new Date(nu);
                eindTijd.setHours(eindTijd.getHours() + 6);
                
                let huidigeOptie = new Date(startTijd);
                while (huidigeOptie <= eindTijd) {
                    const uur = huidigeOptie.getHours().toString().padStart(2, '0');
                    const minuut = huidigeOptie.getMinutes().toString().padStart(2, '0');
                    const tijdString = `${uur}:${minuut}`;
                    
                    const option = document.createElement('option');
                    option.value = tijdString;
                    option.textContent = tijdString;
                    tijdSelect.appendChild(option);
                    
                    // Ga naar het volgende kwartier
                    huidigeOptie.setMinutes(huidigeOptie.getMinutes() + 15);
                }
            }
            
            // Roep de functie aan om de tijden te genereren
            generatePickupTimes();
        });
    </script>
</body>
</html>