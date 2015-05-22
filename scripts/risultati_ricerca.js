var mylat, mylng;
var interval; // usata per far partire/fermare setInterval

function updateTime() {
  var minuti = $('#minuti_rimanenti').text();
  console.log('from updateTime '+minuti);
  if(minuti == 0) {
    window.clearTimeout(interval);
    window.location.href = 'risultati_ricerca.php';
  }
  $('#minuti_rimanenti').html(minuti-1);
}

function mostra_dettagli(id){

  var nome = $('#'+id+'_nome').text();
  var azienda = $('#'+id+'_azienda').text();
  var descrizione = $('#'+id+'_descrizione').text();
  var minuti = $('#'+id+'_minuti').text();
  var distanza = $('#'+id+'_distanza').text();
  var rating = $('#'+id+'_rating').text();
  var rating_value = rating.substr(0,3);
  var registrato = $('#'+id+'_registrato').text();

  var stars = '';
  for(var j=1; j<=5; j++) {
    if(j <= rating_value) {
      stars += '<img class="rating" src="images/star_full.png" />';
    } else if((rating_value % 1 != 0) && rating_value < j && rating_value > j-1) {
      stars += '<img class="rating" src="images/star_half.png" />';
    } else {
      stars += '<img class="rating" src="images/star_empty.png" />';
    }
  }

  $('#dettagli_nome').html(nome);
  $('#conferma_azienda').html(azienda);
  $('#dettagli_azienda').html(azienda);
  $('#dettagli_descrizione').html('<b>Descrizione:</b> '+descrizione);
  $('#dettagli_distanza').html('<b>Distanza dalla tua posizione:</b> '+distanza);
  $('#dettagli_minuti').html('<b>Il fornitore si impegna a rispondere entro:</b> '+minuti);
  $('#dettagli_rating').html('<b>Valutazione:</b> '+stars+' '+rating);
  $('#dettagli_registrato').html('<b>Registrato il:</b> '+registrato);

  $('#risultati_query').hide();
  $('#dettagli_fornitore').show();
  console.log(id);
  $('#invia_button').data('id_fornitore', id);
}

function richiedi_conferma(tipo_richiesta) {
  if (tipo_richiesta == 'consulenza') {
    $('#conferma_tipo_richiesta').html('Consulenza');
  } else if (tipo_richiesta == 'preventivo') {
    $('#conferma_tipo_richiesta').html('Preventivo');
  } else {
    alert("Errore: qualcosa e' andato storto.");
  }

  // leggo dal DB il tempo di default della richiesta
  var id_utente = $('#invia_button').data('id_utente');
  $.ajax({
    type: "POST",
    url: 'php/cerca.php',
    dataType: 'json',
    data: {functionname: 'minuti_default', arguments: id_utente},
    success: function (obj) {
      if(obj.result > 0 && obj.result != null) {
        $('#timer').attr("placeholder", obj.result+" minuti (default)");
        $('#timer').data('default', obj.result);
      } else {
        $('#timer').attr("placeholder", 15+" minuti (default)");
        $('#timer').data('default', 15);
      }
    },
    error: function (error, exception) {
      console.log("error load default time " + error.status + " " + error.statusText + " " + exception);
      $('#timer').data('default', 15);
    }
  });

  $('#invia_button').data('tipo_richiesta', tipo_richiesta);

  $('#dettagli_fornitore').hide();
  $('#conferma_richiesta').show();
}

function annulla() {
  $('#risultati_query').show();
  $('#dettagli_fornitore').hide();
}

function annulla_conferma() {
  $('#dettagli_fornitore').show();
  $('#conferma_richiesta').hide();
}

function inviaNotifica() {
  var id_utente = $('#invia_button').data('id_utente');
  var id_fornitore = $('#invia_button').data('id_fornitore');
  var tipo_richiesta = $('#invia_button').data('tipo_richiesta');
  var minuti = $('#min').text();
  console.log(minuti);
  var distanza = $('#'+id_fornitore+'_distanza').text();

  /*if (!minuti) {
    console.log('minuti default');
    minuti = $('#timer').data('default');
  }*/

  $.ajax({
    type: "POST",
    url: 'php/cerca.php',
    dataType: 'json',
    data: {functionname: 'invia_richiesta', arguments: [id_utente, id_fornitore, tipo_richiesta, minuti, distanza]},
    success: function (obj) {
      console.log(obj);
      if(obj.result == true) {
        if(obj.metod == 'pushbullet') {
          // invio la notifica tramite pushbullet
          var res = PushBullet.push("link", null, obj.email, {title: "Richiesta Ricevuta", body: "Accedi alla webapp per visualizzare i dettagli.", url: "http://130.136.143.33:8010/app/index.php"});
          console.log(res);

          $('#conferma_richiesta').hide();
          $('#richiesta_inviata').show();
        } else if(obj.metod == 'email') {
          $.ajax({
            type: "POST",
            url: 'php/cerca.php',
            dataType: 'json',
            data: {functionname: 'invia_email_richiesta_ricevuta', arguments: obj.email},
            success: function (obj) {},
            error: function (e) {alert('errore invio email.');}
          });
          $('#conferma_richiesta').hide();
          $('#richiesta_inviata').show();
        }

      } else if(obj.result == 'aspetta') {

        $('#conferma_richiesta').hide();
        $('#aspetta').show();
        $('#minuti_rimanenti').html(obj.minuti_rimanenti);

        interval = window.setInterval(function() {updateTime()}, 60000); // ogni minuto

      } else {
        alert('errore: obj.result = false' + obj);
      }
    },
    error: function (error, exception) {
      alert("error " + error.status + " " + error.statusText + " " + exception);
    }
  });

}

// funzione cerca: calcola le coord in base al tipo di posizione selezionata
function cerca(my_id, posizione, artigiano, distanza, rating, indirizzo){

  console.log('id:'+my_id+' posizione:'+posizione+' artigiano:'+artigiano+' distanza:'+distanza+' rating<'+rating+' indirizzo:'+indirizzo);

  if(posizione == 'map') {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        var pos = new google.maps.LatLng(position.coords.latitude,
                                         position.coords.longitude);
        mylat = position.coords.latitude;
        mylng = position.coords.longitude;
        console.log(mylat+" "+mylng);
        query_cerca(my_id, posizione, artigiano, distanza, rating, mylat, mylng);
      });
    } else {
        alert("Geolocation is not supported by this browser.");
    }
  } else if(posizione == 'altro_indirizzo') {
    // codeAddressCerca converte l'indirizzo in lat/lng e chiama query_cerca()
    // per evitare problemi di sincronizzazione
    // in geocoder.js
    codeAddressCerca(my_id, posizione, artigiano, distanza, rating, indirizzo);
  } else if(posizione == 'mio_indirizzo') {
    $.ajax({
      type: "POST",
      url: 'php/cerca.php',
      dataType: 'json',
      data: {functionname: 'get_myAddress', arguments: my_id},
      success: function (obj) {
        // codeAddressCerca converte l'indirizzo in lat/lng e chiama query_cerca()
        // per evitare problemi di sincronizzazione
        // in geocoder.js
        codeAddressCerca(my_id, posizione, artigiano, distanza, rating, obj.result);
      },
      error: function (error, exception) {
        alert("error " + error.status + " " + error.statusText + " " + exception);
      }
    });
  } else {
    alert('Si è verificato un errore');
  }
}

function query_cerca(my_id, posizione, artigiano, distanza, rating, lat, lng){

  console.log(posizione+artigiano+distanza+rating+lat+lng);

  $.ajax({
    type: "POST",
    url: 'php/cerca.php',
    dataType: 'json',
    data: {functionname: 'cerca', arguments: [posizione, artigiano, distanza, rating, lat, lng, my_id]},
    success: function (obj) {
      console.log(obj)
      if(obj.result != false) {
        var table = '<table id="table_risultati" class="table-5 " data-sortable><colgroup><col span="1"><col span="1"><col span="1"><col span="1"><col span="1"></colgroup>'+
                      '<thead><tr><th>Azienda</th><th>Nome</th><th>Distanza</th><th>Tempo Risposta</th><th>Punteggio</th></tr></thead>'+
                      '<tbody>';

        for(j=0; j< obj.result.length; j++) {
          var ris = obj.result[j];
          var stars = '';

          for(var n=1; n<=5; n++) {
            if(n <= ris.valutazione) {
              stars += '<img class="rating" src="images/star_full.png" />';
            } else if(ris.valutazione % 1 != 0 && ris.valutazione < n && ris.valutazione > n-1) {
              stars += '<img class="rating" src="images/star_half.png" />';
            } else {
              stars += '<img class="rating" src="images/star_empty.png" />';
            }
          }

          if(ris.valutazione != null) {
            var valutazione = ris.valutazione;
          } else {
            var valutazione = '';
          }

          table += '<tr class="manina" onclick="mostra_dettagli('+ris.id+')">'+
                      '<td data-th="Azienda" class="risultati">'+
                        '<span id="'+ris.id+'_azienda">'+ris.nome_azienda+' '+ris.ragione_sociale+'</span>'+
                        '</td>'+
                      '<td data-th="Nome" id="'+ris.id+'_nome" class="risultati">'+ris.nome+' '+ris.cognome+'</td>'+
                      '<td data-th="Distanza" id="'+ris.id+'_distanza" class="risultati">'+parseFloat(ris.distanza).toFixed(1)+' km</td>'+
                      '<td data-th="Tempo Risposta" id="'+ris.id+'_minuti" class="risultati"><span id="min">'+ris.minuti_attesa+'</span>   minuti</td>'+
                      '<td data-th="Punteggio" id="'+ris.id+'_rating" class="risultati">'+stars+'<span style="float: right;">'+valutazione+' ('+ris.num_valutazioni_azienda+')</span></td>'+
                        '<span id="'+ris.id+'_descrizione" style="display: none;">'+ris.descrizione+'</span>'+
                        '<span id="'+ris.id+'_registrato" style="display: none;">'+ris.data_reg+'</span>'+
                    '</tr>';
        }

        table += '</tbody></table><br>'+
                  '<i style="display: inline; color: #3372df;" class="icon icon-chevron-left medium"> </i><a href="home.php" class="medium centered">Modifica i parametri di ricerca</a>';

        $('#loading').hide();
        $('#risultati_query').append(table);

        // funzione javascript per rendere la tabella sortable
        Sortable.init();
      } else {
        $('#loading').hide();
        $('#risultati_query').html('<center><p style="large" class="text-divider">Nessun risultato</p><a href="home.php">Modifica i parametri di ricerca</a></center></div>');
      }
    },
    error: function (error, exception) {
      alert("error " + error.status + " " + error.statusText + " " + exception);
      }
  });

}