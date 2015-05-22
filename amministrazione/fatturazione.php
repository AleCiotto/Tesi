<?php
session_start();
require_once('../lib/Users.class.php');
$login = New Users;
$login->access_denied();
if($login->get_principale() != 99){
  header("location: home.php");
}
?>
<!doctype html>
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

    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="scripts/fatturazione.js"></script>

    <!-- Page styles -->
    <link rel="stylesheet" href="../styles/main_v2.css">
    <link rel="stylesheet" href="../styles/form.css">
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

      <p>Fornitore:  <select name="fornitore" id="elenco_aziende" class="material">
        <option value="0" selected disabled>Seleziona Azienda</option>
      </select></p>
      <p>Mensilità:  <input type="month" id="datepicker" placeholder="yyyy-mm" class="material"/></p>
      <p class="color--gray medium" id="mostra_ultima_fattura" >Ultima fattura inviata nel mese di <span id="ultima_fattura">-</span> </p>

      <center><button class="button--primary material" onclick="crea_fattura()">Visualizza Fattura</button></center>
      <br><br>
      <div id="fattura" style="display: none; border:1px solid black; padding: 15px; margin: -15px;"></div>
      <center><button class="button--primary material" id="invia_button" onclick="invia_fattura()" style="display: none; margin-top: 25px;">Invia Fattura</button></center>

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