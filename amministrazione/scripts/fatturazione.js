
$("document").ready(function(){

  $('#elenco_aziende').change(function() {
    var id_azienda = $('#elenco_aziende').val();
    console.log(id_azienda);

    $.ajax({
      type: "POST",
      url: '../php/administrator.php',
      dataType: 'json',
      data: {functionname: 'ultima_fattura', arguments: id_azienda},
      success: function (obj) {
        console.log(obj);
        if(obj.result) {

          var mese = parseMese(parseInt(obj.result.mese));

          $('#mostra_ultima_fattura').html('Ultima fattura inviata nel mese di <span id="ultima_fattura">-</span>');
          $('#ultima_fattura').html(mese +' '+obj.result.anno);
          //$('#mostra_ultima_fattura').show();
        } else {
          $('#mostra_ultima_fattura').html('Non sono presenti altre fatture nel database');
        }
      },
      error: function (error, exception) {
        alert("error " + error.status + " " + error.statusText + " " + exception);
      }
    });
  }); // fine change

  // chiede al server l' elenco delle aziende
  $.ajax({
      type: "POST",
      url: '../php/administrator.php',
      dataType: 'json',
      data: {functionname: 'elenco_aziende', arguments: null},
      success: function (obj) {
        if(obj.result) {
          var elenco;
          for(var i=0; i < obj.result.length; i++) {
            var azienda = obj.result[i];
            elenco += '<option value="'+azienda.id+'">'+azienda.nome_azienda+' '+azienda.ragione_sociale+'</option>';
          }
          $('#elenco_aziende').append(elenco);
        }
      },
      error: function (error, exception) {
        alert("error " + error.status + " " + error.statusText + " " + exception);
      }
    });
});

function crea_fattura() {
  var id_azienda = $('#elenco_aziende option:selected').val();
  var mese = $('#datepicker').val(); // 2015-01
      mese = mese.charAt(5)+mese.charAt(6);
  var anno = $('#datepicker').val(); // 2015-01
      anno = anno.charAt(0)+anno.charAt(1)+anno.charAt(2)+anno.charAt(3);

  if(id_azienda == 0) {
    alert('Seleziona un fornitore prima di procedere.');
    $('#elenco_aziende').css('border', '1px solid red');
    return;
  } else {
    $('#elenco_aziende').css('border', '1px solid #D0D0D0');
  }

  if(mese == '' || anno == '') {
    alert('Seleziona una data.');
    $('#datepicker').css('border', '1px solid red');
    return;
  } else {
    $('#datepicker').css('border', '1px solid #D0D0D0');
  }

  $.ajax({
      type: "POST",
      url: '../php/administrator.php',
      dataType: 'json',
      data: {functionname: 'crea_fattura', arguments: [id_azienda, mese, anno]},
      success: function (obj) {
        console.log(obj);
        if(obj.result) {
          mese = parseMese(parseInt(mese));
          var azienda = obj.azienda;
          var costo_richiesta = obj.costo;
          var IBAN = obj.iban;
          var paypal = obj.paypal;

          var html = '<html>'+
                      '<body><h2>Fattura '+mese+' '+anno+'</h2>'+
                      '<p>Spett. Ditta <b>'+azienda.nome_azienda+' '+azienda.ragione_sociale+'</b>,<br>'+
                              'con la seguente email le presentiamo la fattura corrispondente al mese di '+mese+' '+anno+'.</p>'+
                      '<p>Di seguito le proponiamo una tabella riassuntiva riguardo alle richieste da Lei accettate durante il periodo in analisi.</p>'+
                      '<table><tr><th><b>Utente</b></th><th><b>Tipo Richiesta</b></th><th><b>Data</b></th></tr>';

          for(var i=0; i < obj.result.length; i++) {
            var r = obj.result[i];
            html += '<tr><td>'+r.nome+' '+r.cognome+'</td><td>'+r.tipo_richiesta+'</td><td>'+r.data_ora+'</td></tr>';
          }

          html += '</table>'+
                  '<p>L\' ammontare da pagare è pari a <b>'+(obj.result.length * costo_richiesta)+'€</b></p>'+
                  '<p>Il pagamento dovrà essere effettuato entro 15 giorni dalla ricezione di questa email tramite bonifico bancario o Paypal.</p>'+
                  '<p>Se si vuole procedere tramite PayPal, inviare il pagamento al seguente indirizzo email: '+paypal+'. <br>'+
                  'Le coordinate per il saldo tramite bonifico bancario sono le seguenti: </p>'+
                  '<label>WebAppArtigiani srl <br> IBAN: '+IBAN+' <br> Causale: saldo '+mese+' '+anno+'</label>'+
                  '<p>Grazie,<br> WebAppArtigiani srl.</p>';

          html += '</body></html>';
          $('#fattura').html(html);
          $('#invia_button').data('email_azienda', azienda.email_azienda);
          $('#fattura').show();
          $('#invia_button').show();
        } else {
          $('#fattura').html('<p>Non ci sono richieste accettate nel periodo selezionato.</p>');
          $('#fattura').show();
        }
      },
      error: function (error, exception) {
        alert("error " + error.status + " " + error.statusText + " " + exception);
      }
    });
}

function invia_fattura() {
  var id_azienda = $('#elenco_aziende').val();
  var mese = $('#datepicker').val(); // 2015-01
      mese = mese.charAt(5)+mese.charAt(6);
      mese = parseMese(parseInt(mese));
  var anno = $('#datepicker').val(); // 2015-01
      anno = anno.charAt(0)+anno.charAt(1)+anno.charAt(2)+anno.charAt(3);

  var email_azienda = $('#invia_button').data('email_azienda');
  var subject = 'WebAppArtigiani - Fattura di '+mese+' '+anno;
  var body = $('#fattura').html();

  $.ajax({
      type: "POST",
      url: '../php/administrator.php',
      dataType: 'json',
      data: {functionname: 'invia_fattura', arguments: [email_azienda, subject, body]},
      success: function (obj) {
        console.log(obj);
        if(obj.result) {
          alert('Fattura inviata correttamente!');
          $('#fattura').hide();
          $('#invia_button').hide();
        } else {
          alert('Invio fattura fallito!');
        }
      },
      error: function (error, exception) {
        alert("error " + error.status + " " + error.statusText + " " + exception);
      }
    });
}

function parseMese(intMese){
  var mese;
  switch(intMese) {
            case 1:
              mese = 'Gennaio';
              break;
            case 2:
              mese = 'Febbraio';
              break;
            case 3:
              mese = 'Marzo';
              break;
            case 4:
              mese = 'Aprile';
              break;
            case 5:
              mese = 'Maggio';
              break;
            case 6:
              mese = 'Giugno';
              break;
            case 7:
              mese = 'Luglio';
              break;
            case 8:
              mese = 'Agosto';
              break;
            case 9:
              mese = 'Settemre';
              break;
            case 10:
              mese = 'Ottobre';
              break;
            case 11:
              mese = 'Novembre';
              break;
            case 12:
              mese = 'Dicembre';
              break;
            default:
              mese = 'undefined';
              break;
          }
  return mese;
}