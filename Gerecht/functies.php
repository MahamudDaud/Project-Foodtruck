<?php

/**
 * Dit bestand bevat herbruikbare functies voor de foodtruck website
 */

/**
 * Bereken de openings- en sluitingstijden gebaseerd op de dag van de week
 * 
 * @return array Associatieve array met openingstijd_minuten en sluitingstijd_minuten
 */
function berekenOpeningsTijden()
{
    // Haal de huidige tijd op
    $huidige_tijd = time(); // Huidige tijd in seconden sinds 1970 (Unix timestamp)
    $huidige_dag = date('N', $huidige_tijd); // Dag van de week (1=maandag, 7=zondag)

    // Stel openings- en sluitingstijden in
    if ($huidige_dag >= 6) { // Controleer of het weekend is (zaterdag of zondag)
        // Weekend (zaterdag en zondag)
        $openingstijd_minuten = 7 * 60;  // 07:00 = 420 minuten - Zet openingstijd om in minuten sinds middernacht
        $sluitingstijd_minuten = 19 * 60; // 19:00 = 1140 minuten - Zet sluitingstijd om in minuten sinds middernacht
    } else {
        // Weekdagen (maandag t/m vrijdag)
        $openingstijd_minuten = 9 * 60;  // 09:00 = 540 minuten - Zet openingstijd om in minuten sinds middernacht
        $sluitingstijd_minuten = 17 * 60; // 17:00 = 1020 minuten - Zet sluitingstijd om in minuten sinds middernacht
    }

    return [
        'openingstijd_minuten' => $openingstijd_minuten, // Retourneer openingstijd in minuten
        'sluitingstijd_minuten' => $sluitingstijd_minuten // Retourneer sluitingstijd in minuten
    ];
}

/**
 * Bereken het huidige tijdstip in minuten sinds middernacht
 * 
 * @return int Aantal minuten sinds middernacht
 */
function berekenHuidigeTijdMinuten()
{
    $huidig_uur = date('G'); // Haal het huidige uur op in 24-uurs notatie (0-23)
    $huidige_minuut = date('i'); // Haal de huidige minuten op (0-59)

    // Bereken het huidige tijdstip in minuten sinds middernacht
    // Bijvoorbeeld: 14:30 uur = (14 * 60) + 30 = 870 minuten
    $huidige_tijd_minuten = ($huidig_uur * 60) + $huidige_minuut; // Converteer uren naar minuten en tel huidige minuten erbij op

    return $huidige_tijd_minuten; // Retourneer het huidige tijdstip in minuten sinds middernacht
}

/**
 * Controleer of bestellen momenteel mogelijk is
 * 
 * @return array Associatieve array met kan_bestellen (boolean) en tijd_bericht (string)
 */
function controleerBestellingMogelijk()
{
    $huidige_tijd_minuten = berekenHuidigeTijdMinuten(); // Haal de huidige tijd in minuten op
    $tijden = berekenOpeningsTijden(); // Haal de openings- en sluitingstijden op

    $openingstijd_minuten = $tijden['openingstijd_minuten']; // Haal openingstijd uit array
    $sluitingstijd_minuten = $tijden['sluitingstijd_minuten']; // Haal sluitingstijd uit array

    // Standaard: bestellen is mogelijk
    $kan_bestellen = true; // Initialiseer met aanname dat bestellen mogelijk is
    $tijd_bericht = ""; // Initialiseer lege melding

    // Controleer of het te laat is om te bestellen (minder dan 15 minuten voor sluitingstijd)
    if ($huidige_tijd_minuten > ($sluitingstijd_minuten - 15)) { // Controleer of het binnen 15 min voor sluitingstijd is
        $kan_bestellen = false; // Niet meer mogelijk om te bestellen
        $tijd_bericht = "Het is te laat om te bestellen. Bestellingen zijn mogelijk tot 15 minuten voor sluitingstijd."; // Informatiebericht
    }

    // Controleer of de zaak gesloten is
    if ($huidige_tijd_minuten < $openingstijd_minuten || $huidige_tijd_minuten >= $sluitingstijd_minuten) { // Controleer of huidige tijd buiten openingstijden valt
        $kan_bestellen = false; // Niet mogelijk om te bestellen buiten openingstijden
        $tijd_bericht = "We zijn momenteel gesloten. Bestel tijdens onze openingstijden."; // Informatiebericht
    }

    return [
        'kan_bestellen' => $kan_bestellen, // Retourneer of bestellen mogelijk is (true/false)
        'tijd_bericht' => $tijd_bericht // Retourneer eventueel informatiebericht
    ];
}

/**
 * Bereken wanneer de bestelling kan worden opgehaald
 * 
 * @return array Associatieve array met ophaaltijd_minuten, ophaaltijd_uur, ophaaltijd_minuut en ophaaltijd_bericht
 */
function berekenOphaaltijd()
{
    $huidige_tijd_minuten = berekenHuidigeTijdMinuten(); // Haal de huidige tijd in minuten op
    $tijden = berekenOpeningsTijden(); // Haal de openings- en sluitingstijden op

    $openingstijd_minuten = $tijden['openingstijd_minuten']; // Haal openingstijd uit array

    // Bereken wanneer de bestelling kan worden opgehaald (minimaal 15 minuten na nu)
    $ophaaltijd_minuten = max($huidige_tijd_minuten + 15, $openingstijd_minuten + 15); // Kies de laatste van: nu+15min of openingstijd+15min
    $ophaaltijd_uur = floor($ophaaltijd_minuten / 60); // Bereken het uur door te delen door 60 en naar beneden af te ronden
    $ophaaltijd_minuut = $ophaaltijd_minuten % 60;     // Bereken de minuten als rest na deling door 60
    $geformatteerde_ophaaltijd = sprintf("%02d:%02d", $ophaaltijd_uur, $ophaaltijd_minuut); // Formatteert tijd als "uu:mm"
    $ophaaltijd_bericht = "Je bestelling kan worden opgehaald vanaf " . $geformatteerde_ophaaltijd . "."; // Informatiebericht

    return [
        'ophaaltijd_minuten' => $ophaaltijd_minuten, // Retourneer ophaaltijd in minuten
        'ophaaltijd_uur' => $ophaaltijd_uur, // Retourneer ophaaltijd uur
        'ophaaltijd_minuut' => $ophaaltijd_minuut, // Retourneer ophaaltijd minuut
        'geformatteerde_ophaaltijd' => $geformatteerde_ophaaltijd, // Retourneer geformatteerde ophaaltijd
        'ophaaltijd_bericht' => $ophaaltijd_bericht // Retourneer ophaaltijdbericht
    ];
}

/**
 * Bereken het totaalbedrag van de bestellingen
 * 
 * @param array $bestellingen Array met alle bestellingen
 * @return float Totaalbedrag
 */
function berekenTotaal($bestellingen)
{
    $totaal = 0; // Initialiseer totaalbedrag op 0
    foreach ($bestellingen as $bestelling) { // Loop door elk item in de bestellingen array
        $totaal += $bestelling['prijs'] * $bestelling['aantal']; // Tel prijs * aantal op bij totaal voor elk item
    }
    return $totaal; // Retourneer het totaalbedrag
}

/**
 * Controleer of er bestellingen zijn in de sessie
 * 
 * @return boolean True als er bestellingen zijn, anders false
 */
function heeftBestellingen()
{
    return isset($_SESSION['bestellingen']) && count($_SESSION['bestellingen']) > 0; // Controleert of bestellingen bestaan in sessie en niet leeg zijn
}

/**
 * Genereer een uniek bestelnummer als dat nog niet bestaat
 * 
 * @return array Associatieve array met bestelnummer, display_bestelnummer en vervaltijd
 */
function genereerBestelnummer()
{
    // Genereer een uniek bestelnummer als dat nog niet bestaat
    if (!isset($_SESSION['uniek_bestelnummer'])) { // Controleer of er nog geen bestelnummer in de sessie is
        // Combineer huidige tijd met een willekeurig getal voor unieke code
        $_SESSION['uniek_bestelnummer'] = time() . rand(1000, 9999); // Genereer uniek nummer uit timestamp + random getal
        // Stel in dat het bestelnummer 1,5 uur geldig is (90 minuten = 5400 seconden)
        $_SESSION['bestelnummer_vervaltijd'] = time() + (90 * 60); // Bereken vervaltijd (nu + 90 minuten)
    }

    $bestelnummer = $_SESSION['uniek_bestelnummer']; // Haal bestelnummer uit sessie
    $bestelnummer_vervaltijd = $_SESSION['bestelnummer_vervaltijd']; // Haal vervaltijd uit sessie

    // Maak een kortere versie van het bestelnummer voor weergave (laatste 6 cijfers)
    $display_bestelnummer = substr($bestelnummer, -6); // Verkort het bestelnummer voor display-doeleinden

    // Formatteer de vervaltijd naar leesbare tijd (uren:minuten)
    $vervaltijd_format = date('H:i', $bestelnummer_vervaltijd); // Formatteer timestamp naar leesbare tijd

    return [
        'bestelnummer' => $bestelnummer, // Retourneer het volledige bestelnummer
        'display_bestelnummer' => $display_bestelnummer, // Retourneer verkorte versie voor weergave
        'vervaltijd_format' => $vervaltijd_format, // Retourneer geformatteerde vervaltijd
        'bestelnummer_vervaltijd' => $bestelnummer_vervaltijd // Retourneer timestamp van vervaltijd
    ];
}
