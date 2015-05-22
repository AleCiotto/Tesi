<?php
session_start();
require_once('../lib/Users.class.php');
$login = New Users;
$login->access_denied();

if($login->get_principale() !== 99){
  header("location: ../home.php");
}
?>
<html lang="">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="A front-end template that helps you build fast, modern mobile web apps.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WebApp Artigiani</title>

    <!-- Add to homescreen for Chrome on Android -->
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="icon" sizes="192x192" href="images/app_icon.png">

    <!-- Add to homescreen for Safari on iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="WebApp Artigiani">
    <link rel="apple-touch-icon-precomposed" href="images/app_icon.png">

    <!-- Tile icon for Win8 (144x144 + tile color) -->
    <meta name="msapplication-TileImage" content="images/app_icon.png">
    <meta name="msapplication-TileColor" content="#FAFAFA">

    <script src="../lib/Chart.js-master/Chart.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="scripts/administrator.js"></script>

    <!-- Page styles -->
    <link rel="stylesheet" href="../styles/main_v2.css">
    <link href='http://fonts.googleapis.com/css?family=Roboto+Condensed' rel='stylesheet' type='text/css'>

  </head>
  <body>
    <header class="app-bar promote-layer">
      <div class="app-bar-container">
        <button class="menu"><img src="../images/hamburger.png" alt="Menu"></button>
        <h1 class="logo">WebApp<strong>Artigiani</strong></h1>
        <section class="app-bar-actions">
        </section>
      </div>
    </header>

    <nav class="navdrawer-container promote-layer">
      <h4>Navigation</h4>
      <ul>
        <li><a href="administrator.php">Home</a></li>
        <li><a href="utenti.php">Elenco Utenti</a></li>
        <li><a href="fatturazione.php">Fatturazione</a></li>
        <li><a href="impostazioni.php">Impostazioni</a></li>
        <li><a href="../logout.php">Logout</a></li>
      </ul>
    </nav>

    <main>

      <div class="g-medium--full g-wide--full material card" style="padding: 20px;">

      <div class="g-wide--3 g-medium--half text-divider">
        <canvas id="chartUtenti" max-width="700" max-height="400"></canvas>
      </div>
      <div class="g-wide--1 g-wide--last g-medium--half g--last">
        <p class="medium" style="color: rgb(151,187,205);">Utenti: <span id="tot_utenti">44</span></p>
        <p class="medium" style="color: rgb(111,137,215);"> Fornitori: <span id="tot_fornitori">29</span></p>
        <p class="medium" style="color: rgb(255,0,0);"> Richieste: <span id="tot_richieste">37</span></p>
      </div>

      <div class="g-wide--3 g-medium--half text-divider">
        <canvas id="chartRichieste" max-width="700" max-height="400"></canvas>
      </div>
      <div class="g-wide--1 g-wide--last g-medium--half g--last">
        <p class="medium" style="color: #000000;"> Totale Richieste: <span id="tot_richieste_">37</span></p>
        <p class="medium" style="color: #00CC00;"> Richieste accettate: <span id="tot_accettate">20</span></p>
        <p class="medium" style="color: #FF3300;"> Richieste rifiutate: <span id="tot_rifiutate">10</span></p>
        <p class="medium" style="color: #3399FF;"> Richieste scadute: <span id="tot_scadute">7</span></p>
      </div>

    </div>

    </main>

    <script src="../scripts/main.js"></script>

      <footer id="gc-footer">
      <div class="container">
        <p><a href="#"><i class="icon icon-chevron-up"></i> Back to top</a></p>
      </div>
    </footer>

  </body>
</html>