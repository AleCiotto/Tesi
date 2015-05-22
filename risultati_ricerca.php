<?php
session_start();
require_once('lib/Users.class.php');
$login = New Users;
$login->access_denied();

if (isset($_SESSION['posizione']) && $_SESSION['posizione'] == 'altro_indirizzo') {
} else {
  $_SESSION['indirizzo'] = 'null';
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

    <!-- Page styles -->
    <link rel="stylesheet" href="styles/main_v2.css">
    <link rel="stylesheet" href="styles/form.css">
    <link href='http://fonts.googleapis.com/css?family=Roboto+Condensed' rel='stylesheet' type='text/css'>

    <!-- JQuery -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

    <script type="text/javascript" src="scripts/geocoder.js"></script>
    <script type="text/javascript" src="scripts/risultati_ricerca.js"></script>
    <script type="text/javascript" src="scripts/pushbullet.js"></script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js"></script>
    <script type="text/javascript" src="lib/sortable.min.js"></script>
    <link rel="stylesheet" href="lib/sortable-theme-light.css" />
    <script type="text/javascript" src="scripts/utenti_da_valutare.js"></script>

    <script>
      $("document").ready(function(){
        cerca('<?php echo $login->who_is_logged() ?>','<?php echo $_SESSION['posizione'] ?>','<?php echo $_SESSION['artigiano'] ?>','<?php echo $_SESSION['distanza'] ?>','<?php echo $_SESSION['rating'] ?>','<?php echo $_SESSION['indirizzo'] ?>');
      });
    </script>

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
        <?php if($login->get_fornitore()) echo '<li><a href="fornitore.php">Pagina Fornitore</a></li>'; ?>
        <li><a href="my_account.php">Il mio account</a></li>
        <li><a href="richieste.php">Le mie richieste</a></li>
        <li><a href="feedback.php">Feedback</a></li>
        <li><a href="info.php">Info</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </nav>

    <main>

      <div class="g-medium--full g-wide--full card material";>

      <div id="loading"> <center><p style="large">Caricamento...</p> <hr></center></div>

      <div id="risultati_query"></div>

      <div id="dettagli_fornitore" style="display: none;">
        <section class="styleguide__centered-list">
        <div class="container">
            <ul class="list-guides-intro list-centered list--reset clear">
             <li class="g-medium--full g-wide--full g--centered theme--introduction-to-media">
                <a href="#ignore-click" class="themed">
                    <h3 id="dettagli_azienda" class="xlarge"></h3>
                </a>
                <h3 id="dettagli_nome" class="large text-divider"></h3>
                <p id="dettagli_descrizione"></p>
                <p id="dettagli_minuti"></p>
                <p id="dettagli_distanza"></p>
                <p id="dettagli_rating"></p>
                <p id="dettagli_registrato"></p>
                <br>
                <p><button class="button--primary material" name="consulenza" onclick="richiedi_conferma(name)">Richiedi Consulenza</button>
                   <button class="button--primary material" name="preventivo" onclick="richiedi_conferma(name)">Richiedi Preventivo</button>
                </p>
                <p><button class="button--secondary material" onclick="annulla()">Torna all' elenco</button>
                </p>
              </li>
            </ul>
        </div>
        </section>
      </div>

      <div id="conferma_richiesta" style="display: none;">
        <section class="styleguide__centered-list">
        <div class="container">
            <ul class="list-guides-intro list-centered list--reset clear">
             <li class="g-medium--full g-wide--full g--centered theme--introduction-to-media">
               <h1 class="color--danger">Sei sicuro?</h1>
               <p class="medium">Stai inviando la seguente richiesta:</p>
                <p class="medium">A: <span class="color--remember" id="conferma_azienda"></span></p>
                <p class="medium">Tipo richiesta: <span class="color--remember" id="conferma_tipo_richiesta"></span></p>
                <!--<p class="medium">Inserisci il tempo entro il quale il fornitore deve dare una risposta: <input type="number" class="color--remember" style="width: 170px;" id="timer" placeholder="15 minuti (default)"/></p>-->
                <p class="small">Non potrai inviare altre richieste fino a quando questa non verrà accettata, rifiutata o annullata al termine del tempo massimo.</p>
                <br>
                <p><button class="button--primary material" data-id_utente="<?php echo $login->who_is_logged() ?>" onclick="inviaNotifica()" id="invia_button">Invia</button>
                   <button class="button--secondary material" onclick="annulla_conferma()">Annulla</button>
                </p>
              </li>
            </ul>
        </div>
        </section>
      </div>

    <div id="aspetta" style="display: none;">
      <div class="highlight-module  highlight-module--right   highlight-module--remember">
        <div class="highlight-module__container  icon-exclamation ">
          <div class="highlight-module__content   g-wide--push-1 g-wide--pull-1  g-medium--pull-1   ">
              <p class="highlight-module__title"> Attenzione</p>
              <p class="highlight-module__text"> Hai già inviato una richiesta, aspetta che questa si concluda prima di inviarne un' altra. </p>
              <p class="highlight-module__text"> Il fornitore ha ancora <span id="minuti_rimanenti"></span> minuti a disposizione. </p>
          </div>
        </div>
      </div>
    </div>

    <section class="styleguide__page-header" style="display: none;" id="richiesta_inviata">
      <div class="page-header">
        <div class="container">
            <h3 class="xxlarge text-divider">Richiesta inviata!</h3>
            <p class="page-header__excerpt g-wide--push-1 g-wide--pull-1">Riceverai una notifica che ti informerà sull' esito della richiesta.</p>
        </div>
      </div>
    </section>

    </div> <!-- fine div material -->

    </main>

    <script src="scripts/main.js"></script>

    <footer id="gc-footer">
      <div class="container">
        <p><a href="#"><i class="icon icon-chevron-up"></i> Back to top</a></p>
      </div>
    </footer>

  </body>
</html>
