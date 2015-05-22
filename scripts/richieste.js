
function load_richieste_utente(id_utente, fornitore, principale) {
// fornitore e' un booleano 1/true o 0/false
  $.ajax({
      type: "POST",
      url: 'php/richieste.php',
      dataType: 'json',
      data: {functionname: 'load_richieste', arguments: [id_utente, fornitore, principale]},
      success: function (obj) {
        console.log(obj);
        if(obj.richieste.length == 0) {
          var no_ri = '<p class="large"><i style="display: inline;" class="icon icon-chevron-right"></i> Richieste inviate.</p>'+
                      '<p style="margin-left: 10%; color: gray;">Non hai inviato nessuna richiesta</p>';
          $('#richieste').html(no_ri);
        } else {
          var html = '<p class="large"><i style="display: inline;" class="icon icon-chevron-right"></i> Richieste inviate.</p>'+
                     '<table id="table_richieste_utente" class="table-6" data-sortable>'+
                     '<colgroup><col span="1"><col span="1"><col span="1"><col span="1"></colgroup>'+
                     '<thead><tr><th>Fornitore</th><th>Tipo</th><th>Esito</th><th>Tempo rimasto</th><th>Rating</th><th>Data</th></tr></thead>'+
                     '<tbody>';
          for(i=0; i < obj.richieste.length; i++) {
            if(obj.richieste[i].esito == 'sospesa') {
              var tempo = obj.richieste[i].tempo_rimasto;
            } else {
              var tempo = 0;
            }
            if(obj.richieste[i].rating == null) {
              var rating = 'non valutato';
            } else {
              var rating = obj.richieste[i].rating;
            }
            html += '<tr><td data-th="Fornitore">'+obj.richieste[i].nome+' '+obj.richieste[i].cognome+' <br> '+obj.richieste[i].nome_azienda+' '+obj.richieste[i].ragione_sociale+'</td>'+
                        '<td data-th="Tipo Richiesta">'+obj.richieste[i].tipo_richiesta+'</td>'+
                        '<td data-th="Esito">'+obj.richieste[i].esito+'</td>'+
                        '<td data-th="Tempo rimasto">'+tempo+' minuti</td>'+
                        '<td data-th="Rating">'+rating+'</td>'+
                        '<td data-th="Data">'+obj.richieste[i].data_ora+'</td></tr>';
          }
          html += '</tbody></table>';

          $('#richieste').html(html);
          // funzione javascript per rendere la tabella sortable
          Sortable.init();
        }
        if(fornitore) {
          if(obj.richieste_fornitore.length == 0) {
            var no_r = '<p class="large"><i style="display: inline;" class="icon icon-chevron-right"></i> Richieste ricevute.</p>'+
                        '<p style="margin-left: 10%; color: gray;">Non hai ricevuto nessuna richiesta</p>';
            $('#richieste_fornitore').html(no_r);
            return;
          }
          var html_f = '<p class="large"><i style="display: inline;" class="icon icon-chevron-right"></i> Richieste ricevute.</p>'+
                        '<table id="table_richieste_utente" class="table-6" data-sortable>'+
                        '<colgroup><col span="1"><col span="1"><col span="1"><col span="1"></colgroup>'+
                        '<thead><tr><th>Utente</th><th>Tipo</th><th>Esito</th><th>Tempo rimasto</th><th>Rating</th><th>Data</th></tr></thead>'+
                        '<tbody>';
          for(j=0; j < obj.richieste_fornitore.length; j++) {
            if(obj.richieste_fornitore[j].esito == 'sospesa') {
              var tempo = obj.richieste_fornitore[j].tempo_rimasto;
            } else {
              var tempo = 0;
            }
            if(obj.richieste_fornitore[j].rating == null) {
              var rating = 'non valutato';
            } else {
              var rating = obj.richieste_fornitore[j].rating;
            }
            html_f += '<tr><td data-th="Utente">'+obj.richieste_fornitore[j].nome+' '+obj.richieste_fornitore[j].cognome+'</td>'+
                        '<td data-th="Tipo Richiesta">'+obj.richieste_fornitore[j].tipo_richiesta+'</td>'+
                        '<td data-th="Esito">'+obj.richieste_fornitore[j].esito+'</td>'+
                        '<td data-th="Tempo rimasto">'+tempo+' minuti</td>'+
                        '<td data-th="Rating">'+rating+'</td>'+
                        '<td data-th="Data">'+obj.richieste_fornitore[j].data_ora+'</td></tr>';
          }
          html_f += '</tbody></table>';

          $('#richieste_fornitore').html(html_f);
          // funzione javascript per rendere la tabella sortable
          Sortable.init();
        }
      },
      error: function (error, exception) {
        alert("error " + error.status + " " + error.statusText + " " + exception);
      }
    });

}