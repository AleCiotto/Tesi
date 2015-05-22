function salva_azienda(){
  var nome = $('#azienda_nome').val();
  var ragione_sociale = $('#azienda_ragsociale').val();
  var citta = $('#azienda_citta').val();
  var indirizzo = $('#azienda_indirizzo').val();
  var telefono = $('#azienda_telefono').val();
  var email = $('#azienda_email').val();
  var partita_iva = $('#azienda_piva').val();

  var attivita = $("#azienda_attivita option:selected").val();
  var descrizione = $("#azienda_descrizione").val();
  var pagamento = $("#azienda_pagamento option:selected").val();
  var limite_spesa = $("#azienda_limite_spesa").val();

  if(controlla_input(nome, ragione_sociale, citta, indirizzo, telefono, email, attivita, descrizione, pagamento, limite_spesa)){
    $.ajax({
      type: "POST",
      url: 'php/update.php',
      dataType: 'json',
      data: {functionname: 'salva_nuova_azienda', arguments: [nome, ragione_sociale, citta, indirizzo, telefono, email, partita_iva, attivita, descrizione, pagamento, limite_spesa]},
      success: function (obj) {
        if(obj.result) {
          alert("Azienda salvata correttamente.");
          window.location.href = "index.php";
        }
      },
      error: function (error, exception) {
        alert("error " + error.status + " " + error.statusText + " " + exception);
        }
      });
  }
}

function controlla_input(nome, ragione_sociale, citta, indirizzo, telefono, email, attivita, descrizione, pagamento, limite_spesa) {

  if(nome == '' || ragione_sociale == '' || citta == '' || indirizzo == '' || telefono == '' || email == '' || descrizione == '' || limite_spesa == '') {
    alert('Assicurati di aver riempito tutti i campi');
    return false;
  }
  if(attivita == 'seleziona') {
    $('#azienda_attivita').css('border', '1px solid red');
    return false;
  } else {
    $('#azienda_attivita').css('border', '1px solid #D0D0D0');
  }
  if(pagamento == 'seleziona') {
    $('#azienda_pagamento').css('border', '1px solid red');
    return false;
  } else {
    $('#azienda_pagamento').css('border', '1px solid #D0D0D0');
  }

  return true;
}