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
$ophaaltijd_bericht = $ophaaltijd_info['ophaaltijd_bericht'];

// Controleer of er bestellingen in de winkelwagen zitten
$heeft_bestellingen = heeftBestellingen();
$aantal_items = $heeft_bestellingen ? count($_SESSION['bestellingen']) : 0;
?>

<!DOCTYPE html>
<html lang="nl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Foodtruck Menu</title>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <!-- Header met logo en winkelwagen -->
    <div class="header">
      <div class="logo">
        <img src="images/Logo.jpg" alt="Foodtruck Logo">
      </div>

      <div class="winkelwagen">
        <a href="bestellingpagine.php" class="winkelwagen-link">
          <span class="winkelwagen-icon">🛒</span>
          Winkelwagen
          <?php if($aantal_items > 0): ?>
          <span class="winkelwagen-aantal"><?php echo $aantal_items; ?></span>
          <?php endif; ?>
        </a>
      </div>
    </div>

    <!-- Hoofdinhoud van de pagina -->
    <main class="container">
      <h1>Foodtruck Menu</h1>
      
      <?php if (!$kan_bestellen): ?>
        <!-- Toon een melding als bestellen niet mogelijk is -->
        <div class="tijd-melding">
          <p><?php echo $tijd_bericht; ?></p>
          <p>Openingstijden: <br>Weekdagen: 09:00 - 17:00<br>Weekend: 07:00 - 19:00</p>
        </div>
      <?php else: ?>
        <!-- Toon informatie over wanneer de bestelling kan worden opgehaald -->
        <div class="openingstijden">
          <h3>Bestelinfo</h3>
          <p><?php echo $ophaaltijd_bericht; ?></p>
        </div>
      <?php endif; ?>
      
      <!-- Menu items -->
      <div class="menukaart">
        <?php
        // Gerechten array met naam, prijs en afbeelding
        $gerechten = [
            [
                'id' => 'loempia',
                'naam' => 'Loempia',
                'prijs' => 5.50,
                'afbeelding' => 'images/Loempia\'s.jpg'
            ],
            [
                'id' => 'burritos',
                'naam' => 'Burritos',
                'prijs' => 6.50,
                'afbeelding' => 'images/Burritos.jpg'
            ],
            [
                'id' => 'nachos',
                'naam' => 'Nachos',
                'prijs' => 10.50,
                'afbeelding' => 'images/Nachos.jpg'
            ],
            [
                'id' => 'pozole',
                'naam' => 'Pozole',
                'prijs' => 6.00,
                'afbeelding' => 'images/Pozole.jpg'
            ],
            [
                'id' => 'tacos',
                'naam' => 'Tacos',
                'prijs' => 7.00,
                'afbeelding' => 'images/Tacos.jpg'
            ],
            [
                'id' => 'burritos_xl',
                'naam' => 'Burritos XL',
                'prijs' => 8.50,
                'afbeelding' => 'images/Burritos.jpg'
            ]
        ];
        
        // Loop door alle gerechten en toon ze
        foreach ($gerechten as $gerecht) :
        ?>
        <div class="gerecht-item">
          <img src="<?php echo $gerecht['afbeelding']; ?>" alt="<?php echo $gerecht['naam']; ?>" class="gerecht-afbeelding">
          <div class="gerecht-naam"><?php echo $gerecht['naam']; ?></div>
          <div class="gerecht-prijs">€<?php echo number_format($gerecht['prijs'], 2, ',', '.'); ?></div>
          <form action="bestelling.php" method="post">
            <input type="hidden" name="gerecht" value="<?php echo $gerecht['id']; ?>">
            <input type="hidden" name="naam" value="<?php echo $gerecht['naam']; ?>">
            <input type="hidden" name="prijs" value="<?php echo $gerecht['prijs']; ?>">
            <input type="hidden" name="afbeelding" value="<?php echo $gerecht['afbeelding']; ?>">
            <input type="number" name="aantal" value="1" min="1" max="10" class="aantal-selector" <?php echo !$kan_bestellen ? 'disabled' : ''; ?>>
            <button type="submit" name="toevoegen" class="bestel-knop" <?php echo !$kan_bestellen ? 'disabled' : ''; ?>>Bestellen</button>
          </form>
        </div>
        <?php endforeach; ?>
      </div>
    </main>
    
    <!-- Footer met copyright informatie -->
    <footer>
      <p>&copy; <?php echo date('Y'); ?> Foodtruck Menu - Alle rechten voorbehouden</p>
    </footer>
  </body>
</html>
