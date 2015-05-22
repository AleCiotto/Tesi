
$("document").ready(function(){

  $.ajax({
    type: "POST",
    url: '../php/administrator.php',
    dataType: 'json',
    data: {functionname: 'cerca_utenti', arguments: null},
    success: function (obj) {
      if(obj.result) {

      var table_admin = '<table class="table-3">'+
                        '<colgroup><col span="1"><col span="1"><col span="1"></colgroup>'+
                        '<thead><tr><th>Amministratore</th><th>Data registrazione</th><th>Elimina</th></tr></thead>'+
                        '<tbody>';
      var table_utenti = '<table class="table-3">'+
                         '<colgroup><col span="1"><col span="1"><col span="1"></colgroup>'+
                         '<thead><tr><th>Utente</th><th>Totale Richieste</th><th>Data registrazione</th></tr></thead>'+
                         '<tbody>';
      var table_fornitori = '<table class="table-5">'+
                            '<colgroup><col span="1"><col span="1"><col span="1"><col span="1"><col span="1"></colgroup>'+
                            '<thead><tr><th>Azienda</th><th>Fornitore</th><th>Totale Richieste</th><th>Richieste Accettate</th><th>Data Registrazione</th></tr></thead>'+
                            '<tbody>';

      for(var i=0; i < obj.result.admin.length; i++){
        var admin = obj.result.admin[i];
        table_admin += '<tr><td data-th="Amministratore"><center>'+admin.nome+' '+admin.cognome+'</center></td>'+
                       '<td data-th="Data registrazione"><center>'+admin.data_reg+'</center></td>'+
                       '<td data-th="Elimina"><center><i class="icon icon-close manina" onclick="elimina('+admin.id+')"></i></center></td></tr>';
      }
      for(var i=0; i < obj.result.utenti.length; i++){
        var utente = obj.result.utenti[i];
        table_utenti += '<tr><td data-th="Utente"><center>'+utente.nome+' '+utente.cognome+'</center></td>'+
                       '<td data-th="Totale Richieste"><center>'+utente.totale_richieste+'</center></td>'+
                       '<td data-th="Data Registrazione"><center>'+utente.data_reg+'</center></td></tr>';
      }
      for(var i=0; i < obj.result.fornitori.length; i++){
        var fornitore = obj.result.fornitori[i];
        table_fornitori += '<tr><td data-th="Azienda"><center>'+fornitore.nome_azienda+' '+fornitore.ragione_sociale+'</center></td>'+
                           '<td data-th="Fornitore"><center>'+fornitore.nome+' '+fornitore.cognome+'</center></td>'+
                           '<td data-th="Totale Richieste"><center>'+fornitore.totale_richieste+'</center></td>'+
                           '<td data-th="Richieste Accettate"><center>'+fornitore.richieste_accettate+'</center></td>'+
                           '<td data-th="Data Registrazione"><center>'+fornitore.data_reg+'</center></td></tr>';
      }

      table_admin += '</tbody></table>';
      table_utenti += '</tbody></table>';
      table_fornitori += '</tbody></table>';

      $('#table_admin').html(table_admin);
      $('#table_utenti').html(table_utenti);
      $('#table_fornitori').html(table_fornitori);

      } else {
        alert('errore: obj.result = false' + obj);
      }
    },
    error: function (error, exception) {
      alert("error " + error.status + " " + error.statusText + " " + exception);
    }
  });

});

function elimina(id) {
  var r = confirm("Sei sicuro?");
  if (r == true) {
    $.ajax({
      type: "POST",
      url: '../php/administrator.php',
      dataType: 'json',
      data: {functionname: 'delete_admin', arguments: id},
      success: function (obj) {
        if(obj.result == 'ultimo_admin') {
          alert('Non puoi eliminare tutti gli amministratori.');
        } else if(obj.result) {
          location.reload();
        } else {
          alert('Si è verificato un errore.');
        }
      },
      error: function (error, exception) {
        alert("error " + error.status + " " + error.statusText + " " + exception);
      }
    });
  }
}

var showed_admin = true;
var showed_utenti = true;
var showed_fornitori = true;

function mostra_admin() {
  if(!showed_admin) {
    $('#table_admin').show();
    showed_admin = true;
  } else {
    $('#table_admin').hide();
    showed_admin = false;
  }
}
function mostra_utenti() {
  if(!showed_utenti) {
    $('#table_utenti').show();
    showed_utenti = true;
  } else {
    $('#table_utenti').hide();
    showed_utenti = false;
  }

}
function mostra_fornitori() {
  if(!showed_fornitori) {
    $('#table_fornitori').show();
    showed_fornitori = true;
  } else {
    $('#table_fornitori').hide();
    showed_fornitori = false;
  }

}