<?php
session_start();
require_once('lib/Users.class.php');
$login = New Users;
//$login->access_denied();
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
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

    <script type="text/javascript" src="scripts/registra_nuova_azienda.js"></script>

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

    <div class="g-medium--full g-wide--full card material">

      <p class="xlarge">Registrala la tua azienda!</p>
      <p style="padding-left: 10px; margin-bottom: 15px;">Non risulta nessuna società collegata alla tua partita IVA.</p>

    <div class="g--half" style="padding-left: 20px;">
      <p class="bold">Nome Azienda</p>
      <input type="text" id="azienda_nome" placeholder="Nome" class="material"/>
      <p>Ragione Sociale</p>
      <input type="text" id="azienda_ragsociale" placeholder="Ragione Sociale" class="material" />
      <p>Città</p>
      <input type="text" id="azienda_citta" placeholder="Città" class="material" />
      <p>Indirizzo</p>
      <input type="text" id="azienda_indirizzo" placeholder="Indirizzo" class="material" />
      <p>Telefono</p>
      <input type="tel" id="azienda_telefono" placeholder="Telefono" class="material" />
      <p>Indirizzo Email</p>
      <input type="email" id="azienda_email" placeholder="Email" class="material" /> <br>
    </div>

    <div class="g--half g--last" style="padding-left: 20px;">
      <p>Partita IVA</p>
        <input type="text" id="azienda_piva" placeholder="Partita Iva" value="<?php echo $login->get_partita_iva(); ?>" class="material" disabled/> <br>
      <p class="bold">Tipo Attività</p>
        <select name="attivita" id="azienda_attivita" class="material">
          <option value="seleziona" selected disabled>Tipo di attività:</option>
          <option value="idraulico">Idraulico</option>
          <option value="falegname">Falegname</option>
          <option value="giardiniere">Giardiniere</option>
          <option value="elettricista">Elettricista</option>
        </select>
      <p class="bold">Descrizione</p>
        <textarea type="text" name="descrizione" id="azienda_descrizione" placeholder="Descrizione Attività" class="material"></textarea>
      <p class="bold">Modalità di pagamento</p>
        <select name="modalita" id="azienda_pagamento" class="material">
          <option value="seleziona" selected disabled>Modalità di pagamento:</option>
          <option value="paypal">Paypal</option>
          <option value="bonifico">Bonifico bancario</option>
        </select>
      <p class="bold">Limite spesa mensile</p>
        <input type="number" name="limite_spesa" id="azienda_limite_spesa" placeholder="Limite Spesa Mensile" class="material" />
    </div>

    <div class="g-medium--full g-wide--full centered">
      <button class="button--primary material" onclick="salva_azienda()">Salva</button>
    </div>

    </div>

    </main>

    <footer id="gc-footer">
      <div class="container">
        <p><a href="#"><i class="icon icon-chevron-up"></i> Back to top</a></p>
      </div>
    </footer>

  </body>
</html>