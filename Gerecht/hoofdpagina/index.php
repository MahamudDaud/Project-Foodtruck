<?php
session_start();
// Check if there are items in the shopping cart
$hasItems = isset($_SESSION['bestellingen']) && count($_SESSION['bestellingen']) > 0;
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foodtruck - Loempia's en Mexicaanse Gerechten</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container header-container">
            <div class="logo">
                <h1>FOODTRUCK</h1>
            </div>
            <nav>
                <ul id="nav-links">
                    <li><a href="../gerecht.php">Menu</a></li>
                    <li><a href="#">Contact</a></li>
                    <li><a href="#">Home</a></li>
                    <li>
                        <?php if($hasItems): ?>
                            <a href="../bestellingpagine.php" class="cart-link">Winkelwagen (<?php echo count($_SESSION['bestellingen']); ?>)</a>
                        <?php else: ?>
                            <a href="../gerecht.php" class="cart-link">Winkelwagen</a>
                        <?php endif; ?>
                    </li>
                </ul>
                <div class="burger">
            <div class="line1"></div>
            <div class="line2"></div>
            <div class="line3"></div>
        </div>
            </nav>
        </div>
    </header>

    <main>
        <!-- Hero section -->
        <section class="hero">
            <div class="container">
                <h1>LOEMPIA'S &<br>MEXICAANS</h1>
                <div class="brand-partners">
                    <span>🌮</span>
                    <span>🥢</span>
                    <span>🌯</span>
                    <span>🍜</span>
                </div>
            </div>
        </section>

        <!-- Features section -->
        <section class="features">
            <div class="feature pink-bg">
                <div class="container">
                    <h2>VERSE LOEMPIA'S<br>ELKE DAG VERS BEREID</h2>
                    <div class="feature-illustration">
                        <!-- Placeholder for illustration -->
                    </div>
                </div>
            </div>

            <div class="feature lime-bg">
                <div class="container">
                    <h2>AUTHENTIEKE<br>MEXICAANSE<br>GERECHTEN</h2>
                    <div class="feature-illustration">
                        <!-- Placeholder for illustration -->
                    </div>
                </div>
            </div>

            <div class="feature yellow-bg">
                <div class="container">
                    <h2>FLEXIBELE<br>LOCATIES</h2>
                    <div class="feature-illustration">
                        <!-- Placeholder for illustration -->
                    </div>
                </div>
            </div>

            <div class="feature green-bg">
                <div class="container">
                    <h2>HEERLIJK ETEN<br>VOOR<br>IEDEREEN</h2>
                    <div class="feature-illustration">
                        <!-- Placeholder for illustration -->
                    </div>
                </div>
            </div>
        </section>

        <!-- Menu section -->
        <section class="menu">
            <div class="container">
                <h2>ONS MENU</h2>
                <div class="menu-grid">
                    <div class="menu-item">
                        <h3>LOEMPIA<br>ORIGINAL</h3>
                    </div>
                    <div class="menu-item">
                        <h3>LOEMPIA<br>SPECIAAL</h3>
                    </div>
                    <div class="menu-item">
                        <h3>MEXICAANSE<br>TACO'S</h3>
                    </div>
                    <div class="menu-item">
                        <h3>MEXICAANSE<br>BURRITO'S</h3>
                    </div>
                    <div class="menu-item">
                        <h3>NACHO<br>SCHOTEL</h3>
                    </div>
                    <div class="menu-item">
                        <h3>QUESADILLA<br>SPECIAAL</h3>
                    </div>
                </div>
            </div>
        </section>

        <!-- Location section -->
        <section class="growing">
            <div class="container">
                <h2>VIND ONS<br>OP VERSCHILLENDE<br>LOCATIES</h2>
                <a href="../gerecht.php" class="cta-button">BESTEL NU</a>
            </div>
        </section>

        <!-- Info section -->
        <section class="info-section">
            <div class="container">
                <div class="info-box">
                    <h3>PRAKTISCHE INFORMATIE</h3>
                    <div class="info-items">
                        <div class="info-item">
                            <h4>STANDPLAATSEN</h4>
                            <p>Ma, Wo, Vr: Centrum</p>
                            <p>Di, Do: Business Park</p>
                            <p>Feestdagen: Meerdere locaties</p>
                        </div>
                        <div class="info-item">
                            <h4>OPENINGSTIJDEN</h4>
                            <p>Weekdagen: 09:00 - 17:00</p>
                            <p>Weekend: 07:00 - 19:00</p>
                        </div>
                        <div class="info-item">
                            <h4>BESTELLEN</h4>
                            <p>Online via deze website</p>
                            <p>Telefonisch</p>
                            <p>Direct bij de foodtruck</p>
                            <p>Wachttijd: ± 15 min</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-info">
                    <p><strong>Openingstijden:</strong></p>
                    <p>Ma-Vr: 09:00 - 17:00</p>
                    <p>Za-Zo: 07:00 - 19:00</p>
                </div>
                <div class="footer-info">
                    <p><strong>Locaties:</strong></p>
                    <p>Ma, Wo, Vr: Centrum</p>
                    <p>Di, Do: Business Park</p>
                </div>
                <div class="footer-info">
                    <p><strong>Contact:</strong></p>
                    <p>Telefoon: 06-12345678</p>
                    <p>Email: info@foodtruck.nl</p>
                </div>
            </div>
        </div>
    </footer>
    <script src="script.js"></script>
</body>
</html> 