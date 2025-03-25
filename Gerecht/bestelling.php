<?php
session_start();

//De gerechten en hun prijzen
$gerechten = [
    "Loempia" => 5.50,
    "Burritos"  => 6.50,
    "Nachos"    => 10.50,
    "Pozole"    => 6.00,
    "Tacos"     => 7.00,
    
];

// Associatie tussen gerechten en hun afbeeldingen
$gerechtAfbeeldingen = [
    "Loempia" => "images/Loempia's.jpg",
    "Burritos" => "images/Burritos.jpg",
    "Nachos" => "images/Nachos.jpg",
    "Pozole" => "images/Pozole.jpg",
    "Tacos" => "images/Tacos.jpg"
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bestel'])) {
    if (!empty($_POST['gerecht']) && is_array($_POST['gerecht'])) {
        $bestellingen = [];
        foreach ($_POST['gerecht'] as $gerecht) {
            // Haal het aantal op voor het geselecteerde gerecht
            $aantal = isset($_POST['aantal'][$gerecht]) ? intval($_POST['aantal'][$gerecht]) : 0;
            if ($aantal > 0 && isset($gerechten[$gerecht])) {
                $bestellingen[] = [
                    'gerecht' => $gerecht,
                    'aantal'  => $aantal,
                    'prijs'   => $gerechten[$gerecht],
                    'afbeelding' => isset($gerechtAfbeeldingen[$gerecht]) ? $gerechtAfbeeldingen[$gerecht] : 'images/default-gerecht.jpg'
                ];
            }
        }
        if (!empty($bestellingen)) {
            // Als je eerdere bestellingen wilt behouden, voeg ze toe
            if (!isset($_SESSION['bestellingen'])) {
                $_SESSION['bestellingen'] = [];
            }
            $_SESSION['bestellingen'] = array_merge($_SESSION['bestellingen'], $bestellingen);
            header("Location: bestellingpagine.php"); // Verwijs door naar de overzichtspagina
            exit;
        } else {
            $error = "Vul voor de geselecteerde gerechten een geldig aantal in.";
        }
    } else {
        $error = "Selecteer minimaal één gerecht.";
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Gerechten Bestellen</title>
    <link rel="stylesheet" href="bestel.css?v=<?php echo time(); ?>">
    <style>
        .gerecht img {
            max-width: 100px !important;
            max-height: 100px !important;
            width: 100px !important;
            height: 100px !important;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gerechten Bestellen</h1>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form action="bestelling.php" method="post">
            <?php
            // Loop door de gerechten en maak voor elk een bestelformulierregel
            foreach ($gerechten as $naam => $prijs) {
                // Gebruik de juiste afbeelding voor elk gerecht
                $imagepad = isset($gerechtAfbeeldingen[$naam]) ? $gerechtAfbeeldingen[$naam] : 'images/default-gerecht.jpg';
                echo "
                <div class='gerecht'>
                    <img src='{$imagepad}' alt='".htmlspecialchars($naam, ENT_QUOTES, 'UTF-8')."' loading='lazy' width='100' height='100' style='width:100px; height:100px; object-fit:cover;'>
                    <div>
                        <h3>".htmlspecialchars($naam, ENT_QUOTES, 'UTF-8')."</h3>
                        <h5>€" . number_format($prijs, 2, ',', '.') . "</h5>
                    </div>
                    <div class='bestel'>
                        <label>
                            <input type='checkbox' name='gerecht[]' value='".htmlspecialchars($naam, ENT_QUOTES, 'UTF-8')."'> Bestel
                        </label>
                        <input type='number' name='aantal[".htmlspecialchars($naam, ENT_QUOTES, 'UTF-8')."]' min='1' value='1'>
                    </div>
                </div>";
            }
            ?>
            <input type="submit" name="bestel" value="Bestel" class="button">
        </form>
    </div>
</body>
</html>
