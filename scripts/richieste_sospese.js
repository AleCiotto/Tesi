var array_results = [];

function accettaRichiesta(id_richiesta, pos_in_array) {
  $.ajax({
    type: "POST",
    url: 'php/richieste_sospese.php',
    dataType: 'json',
    data: {functionname: 'accetta_richiesta', arguments: id_richiesta},
    success: function (obj) {
      console.log(obj);
      if(obj.result) {

        var utente = array_results[pos_in_array];
        $('#dettagli_nome').html(utente.nome+' '+utente.cognome);
        $('#dettagli_distanza').html(utente.distanza);
        $('#dettagli_rating').html(utente.rating+' ('+utente.num_valutazioni+')');
        $('#dettagli_registrato').html(utente.data_reg);
        if(utente.dati_contatto.charAt(1) == 1) {
          $('#dettagli_telefono').html(utente.telefono);
        } else {
          $('#dettagli_telefono').html('<i>non visibile</i>');
        }
        if(utente.dati_contatto.charAt(0) == 1) {
          $('#dettagli_indirizzo').html(utente.indirizzo);
        } else {
          $('#dettagli_indirizzo').html('<i>non visibile</i>');
        }
        if(utente.dati_contatto.charAt(2) == 1) {
          $('#dettagli_email').html(utente.email);
        } else {
          $('#dettagli_email').html('<i>non visibile</i>');
        }
        $('#richieste_sospese').hide();
        $('#richieste').hide();
        $('#dettagli_utente').show();

        if(utente.email_pushbullet) {
          // invio la notifica tramite pushbullet
          var res = PushBullet.push("link", null, utente.email_pushbullet, {title: "Richiesta Accettata", body: "La tua richiesta è stata accettata, presto verrai contattato.", url: "http://130.136.143.33:8010/app/index.php"});
        } else {
          $.ajax({
            type: "POST",
            url: 'php/richieste_sospese.php',
            dataType: 'json',
            data: {functionname: 'invia_email_richiesta_accettata', arguments: utente.email},
            success: function (obj) {},
            error: function (e) {alert('errore invio email.');}
          });
        }
      } else {
        alert("Si è verificato un errore.");
      }
    },
    error: function (error, exception) {
      alert("error " + error.status + " " + error.statusText + " " + exception);
    }
  });
}

function rifiutaRichiesta(id_richiesta, pos_in_array) {
  $.ajax({
    type: "POST",
    url: 'php/richieste_sospese.php',
    dataType: 'json',
    data: {functionname: 'rifiuta_richiesta', arguments: id_richiesta},
    success: function (obj) {
      if(obj.result) {
        var utente = array_results[pos_in_array];
        if(utente.email_pushbullet) {
          // invio la notifica tramite pushbullet
          var res = PushBullet.push("link", null, utente.email_pushbullet, {title: "Richiesta Rifiutata", body: "La tua richiesta è stata rifiutata.", url: "http://130.136.143.33:8010/app/index.php"});
        } else {
          $.ajax({
            type: "POST",
            url: 'php/richieste_sospese.php',
            dataType: 'json',
            data: {functionname: 'invia_email_richiesta_rifiutata', arguments: utente.email},
            success: function (obj) {},
            error: function (e) {alert('errore invio email.');}
          });
        }
        location.reload();
      } else {
        alert("Si è verificato un errore.");
      }
    },
    error: function (error, exception) {
      alert("error " + error.status + " " + error.statusText + " " + exception);
    }
  });
}

function torna_alle_richieste(){
  location.reload();
}

function updateTime() {
  var minuti = $('#minuti_rimanenti').text();
  console.log('from updateTime '+minuti);
  if(minuti == 0) {
    window.clearTimeout(interval);
    window.location.reload(); // ricarico la pagina
  }
  $('#minuti_rimanenti').html(minuti-1);
}

$("document").ready(function(){
  var id_fornitore = $('#richieste').data('id_fornitore');

  // cerco richieste sospese
  $.ajax({
    type: "POST",
    url: 'php/richieste_sospese.php',
    dataType: 'json',
    data: {functionname: 'richieste_sospese', arguments: id_fornitore},
    success: function (obj) {
      console.log(obj);
      if(obj.result) {
        var html = '<section class="styleguide__centered-list"><div class="container" ><ul class="list-guides-intro list-centered list--reset clear">';

        for(var i=0; i<obj.result.length; i++) {
          array_results[i] = obj.result[i];
          if(obj.result[i].rating == null) {
            obj.result[i].rating = 'non valutato';
          }
          html += '<li class="g--half g--last theme--introduction-to-media">' +
                    '<a href="#ignore-click" class="themed">' +
                        '<h3 class="xlarge text-divider">'+obj.result[i].nome+' '+obj.result[i].cognome+'</h3>' +
                    '</a>' +
                    '<p><b>Richiede:</b> '+obj.result[i].tipo_richiesta+'.</p>' +
                    '<p><b>Distanza:</b> '+obj.result[i].distanza+' km</p>' +
                    '<p><b>Punteggio:</b> '+obj.result[i].rating+' ('+obj.result[i].num_valutazioni+')</p>' +
                    '<p><b>Registrato il:</b> '+obj.result[i].data_reg+'</p>' +
                    '<p><b>Tempo rimanente:</b> <span class="color--danger"><span id="minuti_rimanenti">'+obj.result[i].minuti_rimanenti+'</span> minuti</span></p>' +
                    '<div id="div_data" data-id_richiesta="'+obj.result[i].id+'">' +
                    '<label class="small"><img src="images/alert.png" style="display: inline;" height="26" width="26" > Accettando ti verrà addebitata la somma di '+obj.costo+'€</label><br>' +
                      '<button class="button--primary material" onclick="accettaRichiesta('+obj.result[i].id+','+i+')">Accetta</button> ' +
                      '<button class="button--secondary material" onclick="rifiutaRichiesta('+obj.result[i].id+','+i+')">Rifiuta</button>' +
                    '</div>' +
                  '</li>';
        }
        html += '</ul></div></section>';
        $('#richieste').html(html);
        interval = window.setInterval(function() {updateTime()}, 60000); // ogni minuto
      } else {
        $('#richieste_sospese').hide();
        $('#richieste_container').hide();
        $('#nessuna_richiesta').show();
      }
    },
    error: function (error, exception) {
      alert("error " + error.status + " " + error.statusText + " " + exception);
    }
  });
});