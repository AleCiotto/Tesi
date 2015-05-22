<?php
session_start();
require_once('lib/Users.class.php');
$login = New Users;
$login->access_denied();

if($login->get_principale() === 99){
    header("location: amministrazione/administrator.php");
}

if (isset($_POST['posizione']) AND isset($_POST['distanza']) AND isset($_POST['artigiano']) AND isset($_POST['rating'])) {
  $_SESSION['posizione'] = $_POST['posizione'];
  if ($_POST['posizione'] == 'altro_indirizzo') {
    $_SESSION['indirizzo'] = $_POST['indirizzo'];
  }
  $_SESSION['distanza'] = $_POST['distanza'];
  $_SESSION['rating'] = $_POST['rating'];
  $_SESSION['artigiano'] = $_POST['artigiano'];
  header("location: risultati_ricerca.php");
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

    <!-- Page styles -->
    <link rel="stylesheet" href="styles/main_v2.css">
    <link rel="stylesheet" href="styles/form.css">
    <link href='http://fonts.googleapis.com/css?family=Roboto+Condensed' rel='stylesheet' type='text/css'>

    <!-- JQuery -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=geometry"></script>
    <script type="text/javascript" src="scripts/maps.js"></script>
    <script type="text/javascript" src="scripts/home.js"></script>
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

    <div class="g--half">
      <div id="map-canvas" style="max-height:450px;" class="material"></div>
    </div>

    <div class="g--half g--last">
      <p class="xlarge centered" style="padding: 10px;">Compila il form e avvia una ricerca!</p>
      <form method="post">
        <label for="frmArtigiano" style="font-weight: bold;">Posizione:</label> <br>
        <div style="margin-left: 15px;">
          <input type="radio" name="posizione" value="map" class="material" <?php echo (!isset($_SESSION['posizione'])||($_SESSION['posizione'] == 'map')) ? 'checked' : ''; ?>> Usa la mia posizione <br>
          <input type="radio" name="posizione" value="altro_indirizzo" class="material" <?php echo (isset($_SESSION['posizione'])&&($_SESSION['posizione'] == 'altro_indirizzo')) ? 'checked' : ''; ?>> Utilizza un altro indirizzo <br>
          <input type="radio" name="posizione" value="mio_indirizzo" class="material" <?php echo (isset($_SESSION['posizione'])&&($_SESSION['posizione'] == 'mio_indirizzo')) ? 'checked' : ''; ?>> Utilizza il mio indirizzo <br>
        </div>

        <div id="altro_indirizzo" style="display: <?php echo (isset($_SESSION['indirizzo'])&&$_SESSION['indirizzo']!= 'null') ? 'inline' : 'none' ?>;">
          <label for="frmArtigiano" style="font-weight: bold;" >Inserisci indirizzo: </label>
          <input type="text" style="margin-left: 15px;" name="indirizzo" placeholder="Città e Indirizzo" value="<?php echo (isset($_SESSION['indirizzo'])&&$_SESSION['indirizzo']!= 'null') ? $_SESSION['indirizzo'] : '' ?>" class="material" /> <br>
        </div>

        <div id="div_artigiano">
        <label for="frmArtigiano" style="font-weight: bold;">Tipo artigiano:</label>
        <select name="artigiano" size="1" id="artigiano" style="margin-left: 15px;" class="material" required>
          <option value="seleziona" <?php echo (!isset($_SESSION['artigiano'])) ? 'selected' : ''; ?> disabled>Seleziona</option>
          <option value="falegname" <?php echo (isset($_SESSION['artigiano'])&&($_SESSION['artigiano'] == 'falegname')) ? 'selected' : ''; ?>>Falegname</option>
          <option value="idraulico" <?php echo (isset($_SESSION['artigiano'])&&($_SESSION['artigiano'] == 'idraulico')) ? 'selected' : ''; ?>>Idraulico</option>
          <option value="elettricista" <?php echo (isset($_SESSION['artigiano'])&&($_SESSION['artigiano'] == 'elettricista')) ? 'selected' : ''; ?>>Elettricista</option>
          <option value="giardiniere" <?php echo (isset($_SESSION['artigiano'])&&($_SESSION['artigiano'] == 'giardiniere')) ? 'selected' : ''; ?>>Giardiniere</option>
        </select><br>
        </div>

        <div class="range-slider">
        <label for="frmDistanza" style="font-weight: bold;">Distante non oltre: (km) </label> <br>
          <input class="input-range material" type="range" name="distanza" min="1" max="50" value="<?php echo (isset($_SESSION['distanza'])) ? $_SESSION['distanza'] : '25' ?>" id="distanza" style="margin-left: 15px;">
          <span class="range-value material" id="distanza-value"></span>
        </div>

        <div class="range-slider">
        <label for="frmRating" style="font-weight: bold;">Rating non inferiore a: </label> <br>
          <input class="input-range material" type="range" name="rating" min="1" max="5" value="<?php echo (isset($_SESSION['rating'])) ? $_SESSION['rating'] : '3' ?>" id="rating" style="margin-left: 15px;">
          <span class="range-value material" id="rating-value"></span>
        </div>

        <center><button type="submit" class="button--primary material" value="Cerca" id="cerca">Cerca</button></center>

      </form>
    </div>

    </div>

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