<?php
session_start();
require_once('lib/Users.class.php');
$login = New Users;
$login->access_denied();
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
    <link href='http://fonts.googleapis.com/css?family=Roboto+Condensed' rel='stylesheet' type='text/css'>

    <!-- JQuery -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="scripts/feedback.js"></script>
    <script type="text/javascript" src="lib/sortable.min.js"></script>
    <link rel="stylesheet" href="lib/sortable-theme-light.css" />
    <script type="text/javascript" src="scripts/utenti_da_valutare.js"></script>

    <script>
      $("document").ready(function(){
        var id = <?php echo $login->who_is_logged() ?>;
        var fornitore = <?php echo $login->get_fornitore() ?>; //restituire 1(true) o 0(false)
        load_table(id, fornitore);
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
      <h4>Navigation</h4>
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

    <div class="g-medium--full g-wide--full material card">

    <div id="div_utente" style="">
      <center><p class="large" id="val_utente"></p></center>
    </div>

    <p class="large"><i style="display: inline;" class="icon icon-chevron-right"></i> Feedback inviati.</p>

    <table id='table_feedback' class="table-4" data-sortable>
    </table>

    <p class="large"><i style="display: inline;" class="icon icon-chevron-down"></i> Feedback ricevuti.</p>

    <table id='table_feedback_ricevuti' class="table-3" data-sortable>
    </table>

  <div id="overlay" class="overlay_popup">
    <div>
      <p>Inserisci una valutazione compresa tra 1 e 5.</p>
      <p style="display: none; color:red;" id="error_rating">Hai inserito un valore non valido!</p>
      <input type="number" id="new_rating" min="0" max="5" step="0.5"> <br>
      <label><input type="checkbox" id="non_valutare"> Non valutare</label>
      <br>

      <p>
        <button id="save_button" onclick="update_rating()">Salva</button>
        <button onclick="cancel()">Annulla</button>
      </p>
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