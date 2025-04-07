<?php
// Start de sessie om gegevens te kunnen delen tussen pagina's
session_start(); // Start de sessie om toegang te krijgen tot sessievariabelen

// Laad de functies in
require_once 'functies.php'; // Importeer de functiebibliotheek met herbruikbare functies

// Controleer of bestellen mogelijk is
$bestel_status = controleerBestellingMogelijk(); // Controleert of het binnen openingstijden is om te bestellen
$kan_bestellen = $bestel_status['kan_bestellen']; // Boolean of bestellen mogelijk is
$tijd_bericht = $bestel_status['tijd_bericht']; // Eventueel bericht waarom bestellen niet mogelijk is

// Als bestellen niet mogelijk is, stuur terug naar menu
if (!$kan_bestellen) { // Controleert of bestellen niet mogelijk is
    // Sla een foutmelding op in de sessie
    $_SESSION['foutmelding'] = "Bestellen is momenteel niet mogelijk: " . $tijd_bericht; // Sla foutmelding op in sessie
    header("Location: gerecht.php"); // Redirect naar menukaart
    exit(); // Stop de uitvoering van het script
}

// ===== GEGEVENS VAN DE GERECHTEN =====

// Een array met alle gerechten en hun prijzen
$gerechten = [
    "Loempia" => 5.50, // Prijs van Loempia
    "Burritos"  => 6.50, // Prijs van Burritos
    "Nachos"    => 10.50, // Prijs van Nachos
    "Pozole"    => 6.00, // Prijs van Pozole
    "Tacos"     => 7.00, // Prijs van Tacos
    "Burritos XL" => 8.50, // Prijs van Burritos XL (grotere versie)
];

// Een array met de afbeeldingspaden voor elk gerecht
$gerechtAfbeeldingen = [
    "Loempia" => "images/Loempia's.jpg", // Pad naar afbeelding van Loempia
    "Burritos" => "images/Burritos.jpg", // Pad naar afbeelding van Burritos
    "Nachos" => "images/Nachos.jpg", // Pad naar afbeelding van Nachos
    "Pozole" => "images/Pozole.jpg", // Pad naar afbeelding van Pozole
    "Tacos" => "images/Tacos.jpg", // Pad naar afbeelding van Tacos
    "Burritos XL" => "images/Burritos.jpg" // Pad naar afbeelding van Burritos XL (zelfde als normale Burritos)
];

// ===== VERWERKING BESTELLING =====

// Array voor het opslaan van eventuele fouten
$fouten = []; // Initialiseer lege array voor validatiefouten

// Controleer of het formulier is verzonden
if (isset($_POST['toevoegen'])) { // Controleert of het formulier is verzonden via de toevoegen knop
    // Valideer de formulier gegevens
    if (!isset($_POST['gerecht']) || empty($_POST['gerecht'])) { // Controleert of gerecht geselecteerd is
        $fouten[] = "Geen gerecht geselecteerd."; // Voeg foutmelding toe aan fouten array
    }
    
    if (!isset($_POST['aantal']) || !is_numeric($_POST['aantal']) || $_POST['aantal'] < 1) { // Controleert of aantal geldig is
        $fouten[] = "Selecteer een geldig aantal."; // Voeg foutmelding toe aan fouten array
    }
    
    // Als er geen fouten zijn, voeg het gerecht toe aan de winkelwagen
    if (count($fouten) == 0) { // Controleert of er geen validatiefouten zijn
        // Maak de bestellingen array aan als die nog niet bestaat
        if (!isset($_SESSION['bestellingen'])) { // Controleert of winkelwagen al bestaat
            $_SESSION['bestellingen'] = []; // Maak een lege winkelwagen aan
        }
        
        // Haal de gegevens uit het formulier
        $gerecht_id = $_POST['gerecht']; // Haal geselecteerde gerecht ID op uit formulier
        $gerecht_naam = $_POST['naam']; // Haal geselecteerde gerecht naam op uit formulier
        $gerecht_prijs = floatval($_POST['prijs']); // Haal geselecteerde gerecht prijs op en converteer naar float
        $aantal = intval($_POST['aantal']); // Haal geselecteerde aantal op en converteer naar integer
        $afbeelding = $_POST['afbeelding']; // Haal pad naar afbeelding op uit formulier
        
        // Kijk of dit gerecht al in de winkelwagen zit
        $gerecht_bestaat = false; // Initialiseer variabele op false
        
        foreach ($_SESSION['bestellingen'] as $key => $item) { // Loop door alle items in winkelwagen
            if ($item['gerecht_id'] === $gerecht_id) { // Controleert of het gerecht al in winkelwagen zit
                // Update het aantal
                $_SESSION['bestellingen'][$key]['aantal'] += $aantal; // Verhoog het aantal van het bestaande item
                $gerecht_bestaat = true; // Zet variabele op true om aan te geven dat het gerecht al bestaat
                break; // Stop de loop
            }
        }
        
        // Als het gerecht nog niet in de winkelwagen zit, voeg het toe
        if (!$gerecht_bestaat) { // Controleert of het gerecht nog niet in winkelwagen zit
            $_SESSION['bestellingen'][] = [ // Voeg nieuw item toe aan winkelwagen
                'gerecht_id' => $gerecht_id, // Sla gerecht ID op
                'naam' => $gerecht_naam, // Sla gerecht naam op
                'prijs' => $gerecht_prijs, // Sla gerecht prijs op
                'aantal' => $aantal, // Sla aantal op
                'afbeelding' => $afbeelding // Sla pad naar afbeelding op
            ];
        }
        
        // Redirect naar de bestellingenpagina om de bestelling te bekijken
        header("Location: bestellingpagine.php"); // Redirect naar de bestelling pagina
        exit(); // Stop de uitvoering van het script
    }
}

// Als er fouten zijn, redirect terug naar menu met foutmelding
if (count($fouten) > 0) { // Controleert of er validatiefouten zijn
    $_SESSION['foutmelding'] = implode("<br>", $fouten); // Sla foutmeldingen op in sessie, gescheiden door <br>
    header("Location: gerecht.php"); // Redirect naar menukaart
    exit(); // Stop de uitvoering van het script
}

// Als er geen POST verzoek is, redirect terug naar menu
header("Location: gerecht.php"); // Redirect naar menukaart als pagina direct wordt opgeroepen
exit(); // Stop de uitvoering van het script

// Let op: Dit is een verwerkingsbestand. Alle code hieronder wordt nooit uitgevoerd
// omdat er in elk mogelijk pad een exit() wordt uitgevoerd.
?>
