<?php
session_start();
require_once('lib/Users.class.php');
$login = New Users;
$login->esegui_login();
if($login->is_logged()==TRUE) {
  if($login->get_fornitore()) {
    header("location: fornitore.php");
  } else {
    header("location: home.php");
  }
};
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
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <link href='http://fonts.googleapis.com/css?family=Roboto+Condensed' rel='stylesheet' type='text/css'>

  </head>
  <body>
    <header class="app-bar promote-layer">
      <div class="app-bar-container">
        <h1 class="logo">WebApp<strong>Artigiani</strong></h1>
        <section class="app-bar-actions">
        </section>
      </div>
    </header>
    <main>

    <div class="g-medium--full g-wide--full material card">
    	<?php echo $login->get_login_form(); ?>
    </div>

    </main>
    <footer id="gc-footer">
      <div class="container">
         <a>
           <i class="icon icon-chevron-large" style="display:inline-block; vertical-align:middle"></i> © Copyright 2014 - All Rights Reserved - Developed by Alessandro Mercurio.
         </a>
      </div>
    </footer>

  </body>
  </html>
</html>