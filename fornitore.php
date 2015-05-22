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
    <link rel="stylesheet" href="styles/form.css">
    <link href='http://fonts.googleapis.com/css?family=Roboto+Condensed' rel='stylesheet' type='text/css'>

    <!-- JQuery -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

     <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js"></script>
     <script type="text/javascript" src="scripts/maps.js"></script>
     <script type="text/javascript" src="scripts/fornitore.js"></script>
     <script type="text/javascript" src="scripts/geocoder.js"></script>
     <script src="http://code.jquery.com/ui/1.11.2/jquery-ui.min.js"></script>
     <script type="text/javascript" src="scripts/utenti_da_valutare.js"></script>

    <script>
      $(document).ready(function() {
        var show_message = <?php echo $login->get_welcome_message() ? 'true' : 'false'; ?>;
        if(show_message) {
          $('#benvenuto').fadeIn(400).delay(3000).fadeOut(400);
          <?php $login->set_welcome_message(false); ?>
        }
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
        <li><a href="fornitore.php">Pagina Fornitore</a></li>
        <li><a href="my_account.php">Il mio account</a></li>
        <li><a href="richieste.php">Le mie richieste</a></li>
        <li><a href="feedback.php">Feedback</a></li>
        <li><a href="info.php">Info</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </nav>

    <main>

      <div id="richieste_sospese" class="g-medium--full g-wide--full material card" style="margin-top: 15px; background-color: #FAFAFA; padding: 0px 10px 20px 10px; display: none;">

        <center><div id="div_data" class="color--danger manina" data-id_fornitore="<?php echo $login->who_is_logged() ?>">
          <p class="large" onclick="window.location.href = 'richieste_sospese.php';">
            <i class="icon icon-chevron-right" style="display: inline;"></i>
              Hai una o più richieste in sospeso!
            <i class="icon icon-chevron-left" style="display: inline;"></i>
          </p>
        </div></center>

      </div>

      <div class="g-medium--full g-wide--full material card">

      <div class="g--half">
        <div id="map-canvas" class="material" style="max-height:450px;"></div>
      </div>

      <div class="g--half g--last">
        <p class="xlarge centered" style="padding: 10px;">Rendimi Disponibile</p>

        <form>
          <label for="frmArtigiano" style="font-weight: bold;">Posizione:</label> <br>
          <div style="margin-left: 15px;">
            <input type="radio" name="posizione" value="usa_posizione" class="material" checked/><label> Usa la mia posizione attuale.</label><br>
            <input type="radio" name="posizione" value="usa_indirizzo" class="material"/><label> Usa l' indirizzo di default invece della mia posizione attuale.</label><br>
          </div><br>
          <div class="onoffswitch g--centered material" >
            <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" value="<?php echo $login->who_is_logged(); ?>" <?php echo $login->checkDisponibilitaFornitore(); ?>>
            <label class="onoffswitch-label" for="myonoffswitch">
                <span class="onoffswitch-inner">
                    <span class="onoffswitch-active"><span class="onoffswitch-switch">Disponibile</span></span>
                    <span class="onoffswitch-inactive"><span class="onoffswitch-switch">Occupato</span></span>
                </span>
            </label>
          </div>

        </form>
      </div>


    <div class="g-medium--full g-wide--full" style="margin: 10px;">
      <div id="ultima_richiesta" class="color--gray"></div>
    </div>

    </div>

    <div class='toast' id="disponibile" style='display:none'>Stato impostato su <i>disponibile</i></div>
    <div class='toast' id="occupato" style='display:none'>Stato impostato su <i>non disponibile</i></div>
    <div class='toast_large' id="benvenuto" style='display:none'>Benvenuto <i><?php echo $login->get_nome().' '.$login->get_cognome() ?></i></div>

    </main>

    <script src="scripts/main.js"></script>

      <footer id="gc-footer">
        <div class="container">
          <p><a href="#"><i class="icon icon-chevron-up"></i> Back to top</a></p>
        </div>
      </footer>

  </body>
</html>