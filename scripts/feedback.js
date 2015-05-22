// L' id che prende in input e' l'id della richiesta conclusa
function update_rating() {
  var id = $('#save_button').data('id_feedback');
  var val_da_modificare = $('#save_button').data('val_da_modificare');
  console.log(val_da_modificare+' '+id);

  var rating = document.getElementById("new_rating").value;
  var non_valutare = document.getElementById("non_valutare").checked;
  if(((rating % 0.5 != 0) || rating > 5 || rating == 0) && !non_valutare) {
    document.getElementById("error_rating").style.display = 'inline';
    return;
  } else {

  if(non_valutare) {
    rating = 0;
  }

  $.ajax({
    type: "POST",
    url: 'php/update.php',
    dataType: 'json',
    data: {functionname: 'update_rating', arguments: [id, rating, val_da_modificare]},
    success: function (obj) {
      //alert("Modifica salvata. \nRicarica la pagina per visualizzare le modifiche.");
      location.reload();
    },
    error: function (error, exception) {
      alert("error " + error.status + " " + error.statusText + " " + exception);
    }
  });
    cancel_input();
    el = document.getElementById("overlay");
    el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
  }
}

// mostra/nasconde il popup
function cancel(id, val_da_modificare) {
  cancel_input();
  $('#save_button').data('id_feedback', id);
  $('#save_button').data('val_da_modificare', val_da_modificare);
  el = document.getElementById("overlay");
  el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
}

function cancel_input(){
  document.getElementById("new_rating").value = '';
  document.getElementById("error_rating").style.display = 'none';
}

// viene chiamata da load_table prima di terminare
function load_user_data(mio_id) {
  $.ajax({
    type: "POST",
    url: 'php/update.php',
    dataType: 'json',
    data: {functionname: 'mia_valutazione', arguments: mio_id},
    success: function (obj) {
      console.log(obj);
      var nome = obj.result.nome;
      var cognome = obj.result.cognome;
      var num_val = obj.result.num_valutazioni;
      var val = obj.result.rating;
      var stars = '';
      for(var i=1; i <= 5; i++) {
        if(i <= val) {
          stars += '<img class="rating" src="images/star_full.png" />';
        } else if((val % 1 >= 0.5) && val < i && val > i-1) {
          stars += '<img class="rating" src="images/star_half.png" />';
        } else {
          stars += '<img class="rating" src="images/star_empty.png" />';
        }
      }
      if(obj.result) {
        $('#val_utente').html(nome+' '+cognome+'<br>'+stars+'('+num_val+')');
      } else {
        alert('errore: qualcosa è andato storto!'+obj);
      }

    },
    error: function (error, exception) {
      alert("error " + error.status + " " + error.statusText + " " + exception);
    }
  });
}

function load_table(mio_id, fornitore){

  $.ajax({
    type: "POST",
    url: 'php/update.php',
    dataType: 'json',
    data: {functionname: 'load_table', arguments: mio_id},
    success: function (obj) {
      if( obj ) {
        var utente = 'Utente';

        // stringa usata per costruire la tabella
        var table_i = '<colgroup><col span="1"><col span="1"><col span="1"><col span="1"></colgroup>'+
                     '<thead><tr><th>'+utente+'</th><th>Esito</th><th>Punteggio</th><th>Data</th></tr></thead>'+
                     '<tbody>';
        var table_r = '<colgroup><col span="1"><col span="1"><col span="1"></colgroup>'+
                      '<thead><tr><th>Esito</th><th>Punteggio</th><th>Data</th></tr></thead>'+
                      '<tbody>';

        for(var i=0; i<obj.feedback.length; i++) {
          // se l'utente loggato e' il fornitore della richiesta allora e' colui che ha fornito il servizio,
          // altrimenti vuol dire che e' colui che ha effettuato la ricerca.
          if(obj.feedback[i].id_fornitore == mio_id) {

            if(obj.feedback[i].esito === 'accettata') {
              // pulsante a destra per inserire/modificare un feedback
              var edit = '<a href="#ignore" class="pen" style="float:right;" onclick="cancel(\''+obj.feedback[i].id+'\',\'val_utente\');"><img src="images/pen_1.png"/></a>';
              // stringa usata per aggiungere le immagini delle stelline per ogni punto
              var stars = '';

              var val_utente = obj.feedback[i].valutazione_utente;
              var val = '';
              if(val_utente == null) {
                stars = '<i>da valutare</i>'+edit;
              } else if(val_utente == 0) {
                stars = '<i>non valutato</i>';
              } else {

                for(var n=1; n<=5; n++) {
                  if(n <= val_utente) {
                    stars += '<img class="rating" src="images/star_full.png" />';
                  } else if((val_utente % 1 != 0) && val_utente < n && val_utente > n-1) {
                    stars += '<img class="rating" src="images/star_half.png" />';
                  } else {
                    stars += '<img class="rating" src="images/star_empty.png" />';
                  }
                }
                val = val_utente;
              }

              table_i += '<tr><td data-th="'+utente+'">'+obj.feedback[i].nome_utente+' '+obj.feedback[i].cognome_utente+'</td>'+
                        '<td data-th="Esito">'+obj.feedback[i].esito+'</td>'+
                        '<td data-th="Punteggio"><div id="stars-'+i+'"></div>'+stars+'<span style="float: right;">'+val+'</span></td>'+
                        '<td data-th="Data">'+obj.feedback[i].data+'</td></tr>';
            }

            /* ======================================================================== */

            // stringa usata per aggiungere le immagini delle stelline per ogni punto
            var stars_r = '';

            var val_r = '';
            if(obj.feedback[i].valutazione_fornitore == null) {
              stars_r = '<i>non sei ancora stato valutato</i>';
            } else if(obj.feedback[i].valutazione_fornitore == 0) {
              stars_r = '<i>non valutato</i>';
            } else {

              for(var j=1; j<=5; j++) {
                if(j <= obj.feedback[i].valutazione_fornitore) {
                  stars_r += '<img class="rating" src="images/star_full.png" />';
                } else if((obj.feedback[i].valutazione_fornitore % 1 != 0)  && obj.feedback[i].valutazione_fornitore < n && obj.feedback[i].valutazione_fornitore > n-1) {
                  stars_r += '<img class="rating" src="images/star_half.png" />';
                } else {
                  stars_r += '<img class="rating" src="images/star_empty.png" />';
                }
              }
              val_r = obj.feedback[i].valutazione_fornitore;
            }

            table_r += '<tr><td data-th="Esito">'+obj.feedback[i].esito+'</td>'+
                      '<td data-th="Punteggio"><div id="stars-'+i+'"></div>'+stars_r+'<span style="float: right;">'+val_r+'</span></td>'+
                      '<td data-th="Data">'+obj.feedback[i].data+'</td></tr>';


          } else if(obj.feedback[i].id_utente == mio_id) {

            // pulsante a destra per inserire/modificare un feedback
            var edit = '<a href="#ignore" class="pen" style="float:right;" onclick="cancel(\''+obj.feedback[i].id+'\',\'val_fornitore\');"><img src="images/pen_1.png"/></a>';
            // stringa usata per aggiungere le immagini delle stelline per ogni punto
            var stars = '';

            var val = '';
            if(obj.feedback[i].valutazione_fornitore == null) {
              stars = '<i>da valutare</i>'+edit;
            } else if(obj.feedback[i].valutazione_fornitore == 0) {
              stars = '<i>non valutato</i>';
            } else {

              for(var j=1; j<=5; j++) {
                if(j <= obj.feedback[i].valutazione_fornitore) {
                  stars += '<img class="rating" src="images/star_full.png" />';
                } else if((obj.feedback[i].valutazione_fornitore % 1 != 0) && obj.feedback[i].valutazione_fornitore < j && obj.feedback[i].valutazione_fornitore > j-1) {
                  stars += '<img class="rating" src="images/star_half.png" />';
                } else {
                  stars += '<img class="rating" src="images/star_empty.png" />';
                }
              }
              val = obj.feedback[i].valutazione_fornitore;
            }

            table_i += '<tr><td data-th="'+utente+'">'+obj.feedback[i].nome_azienda+' '+obj.feedback[i].ragione_sociale+'</td>'+
                      '<td data-th="Esito">'+obj.feedback[i].esito+'</td>'+
                      '<td data-th="Punteggio"><div id="stars-'+i+'"></div>'+stars+'<span style="float: right;">'+val+'</span></td>'+
                      '<td data-th="Data">'+obj.feedback[i].data+'</td></tr>';

            /* ======================================================================== */

            if(obj.feedback[i].esito === 'accettata') {
              // stringa usata per aggiungere le immagini delle stelline per ogni punto
              var stars_r = '';

              var val_r = '';
              if(obj.feedback[i].valutazione_utente == null) {
                stars_r = '<i>non sei ancora stato valutato</i>';
              } else if(obj.feedback[i].valutazione_utente == 0) {
                stars_r = '<i>non valutato</i>';
              } else {

                for(var j=1; j<=5; j++) {
                  if(j <= obj.feedback[i].valutazione_utente) {
                    stars_r += '<img class="rating" src="images/star_full.png" />';
                  } else if((obj.feedback[i].valutazione_utente % 1 != 0) && obj.feedback[i].valutazione_utente < j && obj.feedback[i].valutazione_utente > j-1) {
                    stars_r += '<img class="rating" src="images/star_half.png" />';
                  } else {
                    stars_r += '<img class="rating" src="images/star_empty.png" />';
                  }
                }
                val_r = obj.feedback[i].valutazione_utente;
              }

              table_r += '<tr><td data-th="Esito">'+obj.feedback[i].esito+'</td>'+
                        '<td data-th="Punteggio"><div id="stars-'+i+'"></div>'+stars_r+'<span style="float: right;">'+val_r+'</span></td>'+
                        '<td data-th="Data">'+obj.feedback[i].data+'</td></tr>';
            }

          } // fine if-else controllo mio_id
        } // fine for

        table_i += '</tbody>';
        // aggiungo il corpo della tabella al div vuoto
        $('#table_feedback').append(table_i);
        table_r += '</tbody>';
        // aggiungo il corpo della tabella al div vuoto
        $('#table_feedback_ricevuti').append(table_r);
        // funzione javascript per rendere la tabella sortable
        Sortable.init();

      } else {
          alert("error");
      }
    },
    error: function (error, exception) {
      alert("error " + error.status + " " + error.statusText + " " + exception);
    }
  });
  load_user_data(mio_id);
}