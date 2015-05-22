<?php
session_start();
require_once('../lib/Users.class.php');
$login = New Users;
$login->access_denied();
if($login->get_principale() != 99){
  header("location: home.php");
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

    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="scripts/impostazioni.js"></script>

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

        <p style="display: inline;" class="large"> Aggiorna automaticamente lo stato del fornitore
          <div class="onoffswitch material" style="float: right;">
            <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="auto_update">
            <label class="onoffswitch-label" for="auto_update">
                <span class="onoffswitch-inner">
                    <span class="onoffswitch-active"><span class="onoffswitch-switch">Attivo</span></span>
                    <span class="onoffswitch-inactive"><span class="onoffswitch-switch">Disattivo</span></span>
                </span>
            </label>
          </div>
          <p class="descrizione medium color--blue">Se attivo, imposta automaticamente lo stato del fornitore su <u>non disponibile</u> dopo aver accettato una richiesta.</p>
          </p>
        <br>
        <p style="display: inline;" class="large"> Tempo di default
          <div id="div_tempo_default">
            <input type="number" id="tempo_default" class="material">
            <button id="salva" class="button--primary material" name="tempo_default" onclick="salva(name)">Salva</button>
          </div>
          <p class="descrizione medium color--blue">Tempo (in minuti) proposto al fornitore in fase di registrazione per la gestione della richiesta.</p>
        </p>
        <br>
        <p style="display: inline;" class="large"> Tempo massimo
          <div id="div_tempo_default">
            <input type="number" id="tempo_max" class="material">
            <button id="salva" class="button--primary material" name="tempo_max" onclick="salva(name)">Salva</button>
          </div>
          <p class="descrizione medium color--blue">Tempo (in minuti) massimo entro il quale il fornitore si impegna di gestire la richiesta.</p>
        </p>
        <br>
        <p style="display: inline;" class="large"> Costo della richiesta
          <div id="div_tempo_default">
            <input type="number" id="costo_richiesta" class="material">
            <button id="salva" class="button--primary material" name="costo_richiesta" onclick="salva(name)">Salva</button>
          </div>
          <p class="descrizione medium color--blue">Costo addebitato all' azienda per ogni richiesta accettata.</p>
        </p>
        <br>
        <p style="display: inline;" class="large"> IBAN
          <div id="div_tempo_default">
            <input type="text" id="iban" class="material" placeholder="IBAN">
            <button id="salva" class="button--primary material" name="iban" onclick="salva(name)">Salva</button>
          </div>
          <p class="descrizione medium color--blue">Coordinate bancarie comunicate via email per la ricezione dei pagamenti.</p>
        </p>
        <br>
        <p style="display: inline;" class="large"> Paypal
          <div id="div_tempo_default">
            <input type="email" id="paypal" class="material" placeholder="Paypal">
            <button id="salva" class="button--primary material" name="paypal" onclick="salva(name)">Salva</button>
          </div>
          <p class="descrizione medium color--blue">Indirizzo Paypal comunicato via email per la ricezione dei pagamenti.</p>
        </p>


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