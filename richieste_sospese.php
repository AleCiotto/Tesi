<?php
session_start();
require_once('lib/Users.class.php');
$login = New Users;
$login->access_denied();
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

    <!-- Page styles -->
    <link rel="stylesheet" href="styles/main_v2.css">
    <link href='http://fonts.googleapis.com/css?family=Roboto+Condensed' rel='stylesheet' type='text/css'>

    <!-- JQuery -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

    <script type="text/javascript" src="scripts/richieste_sospese.js"></script>
    <script type="text/javascript" src="scripts/pushbullet.js"></script>
    <script src="http://code.jquery.com/ui/1.11.2/jquery-ui.min.js"></script>
    <script type="text/javascript" src="scripts/utenti_da_valutare.js"></script>

  </head>
  <body>
    <header class="app-bar promote-layer">
      <div class="app-bar-container">
        <button class="menu"><img src="images/hamburger.png" alt="Menu"></button>
        <h1 class="logo">WebApp<strong>Artigiani</strong></h1>
        <section class="app-bar-actions" data-user="<?php echo $login->who_is_logged(); ?>">
        </section>
      </div>
    </header>

    <nav class="navdrawer-container promote-layer">
      <h4><?php echo $login->get_nome().' '.$login->get_cognome() ?></h4>
      <ul>
        <li><a href="home.php">Home</a></li>
        <li><a href="fornitore.php">Pagina Fornitore</a></li>
        <li><a href="my_account.php">Il mio account</a></li>
        <li><a href="richieste.php">Le mie richieste</a></li>
        <li><a href="feedback.php">Feedback</a></li>
        <li><a href="info.php">Info</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </nav>

    <main>

      <div class="g-medium--full g-wide--full material card" style="padding-bottom: 30px;">

      <center>
        <div id="richieste_sospese" class="color--danger manina">
          <p class="large">
            <i class="icon icon-chevron-right" style="display: inline;"></i>
             Hai una o più richieste in sospeso!
            <i class="icon icon-chevron-left" style="display: inline;"></i>
          </p>
        </div>
      </center>

      <center><div id="nessuna_richiesta" class="color--muted manina" style="display: none;">
        <p class="large"> <br>
          <i class="icon icon-chevron-right" style="display: inline;"></i>
             Non hai nessuna richiesta!
          <i class="icon icon-chevron-left" style="display: inline;"></i>
        </p>
      </div></center>

      </div>

      <div id="richieste_container" class="g-medium--full g-wide--full card material">

      <!-- riempio il div tramite javascript -->
      <div id="richieste" data-id_fornitore="<?php echo $login->who_is_logged() ?>"></div>

      <div id="dettagli_utente" style="display: none;">
        <section class="styleguide__centered-list">
        <div class="container">
            <ul class="list-guides-intro list-centered list--reset clear">
             <li class="g-medium--full g-wide--full g--centered theme--introduction-to-media">
                <a href="#ignore-click" class="themed">
                    <h3 id="dettagli_nome" class="xlarge text-divider"></h3>
                </a>
                <p>Distanza: <span id="dettagli_distanza"></span> km</p>
                <p>Valutazione: <span id="dettagli_rating"></span></p>
                <p>Registrato il: <span id="dettagli_registrato"></span></p>
                <p>Telefono: <span id="dettagli_telefono" class="color--remember"></span></p>
                <p>Indirizzo: <span id="dettagli_indirizzo" class="color--remember"></span></p>
                <p>Email: <span id="dettagli_email" class="color--remember"></span></p>
                <br>
                <p><button class="button--secondary material" onclick="torna_alle_richieste()">Torna all' elenco</button>
                </p>
              </li>
            </ul>
        </div>
      </div>

    </div>

    </main>

    <script src="scripts/main.js"></script>

    <footer id="gc-footer">
      <div class="container">
        <p><a href="#"><i class="icon icon-chevron-up"></i> Back to top</a></p>
      </div>
    </footer>

  </body>
</html>
