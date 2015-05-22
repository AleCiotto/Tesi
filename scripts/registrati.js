// Utilizzato in registrati.php
// per abilitare/disabilitare i textbox
// e verificare gli input
var tempo_max = null;

$("document").ready(function(){
  $("#reg_fornitore").change(function() {
    if($('#reg_fornitore').is(':checked')){
        $("#reg_piva").prop('disabled', false);
        $("#campi_fornitore").show();

    } else {
        $("#reg_piva").prop('disabled', true);
        $("#campi_fornitore").hide();
    }
  });
  $.ajax({
      type: "POST",
      url: 'php/administrator.php',
      dataType: 'json',
      data: {functionname: 'load_info', arguments: 'tempo_max'},
      success: function (obj) {
        tempo_max = obj.result;
        $('#tempo').html(tempo_max);
      }
  });

});

function redirect_to_login(){
    window.location.href = "index.php";
  }

function avanti() {
  var usa_telefono = $('#usa_telefono').is(':checked');
  var usa_email = $('#usa_email').is(':checked');
  if(!usa_telefono && !usa_email) {
    alert('Devi abilitare l\' uso del telefono o dell\' indirizzo email per essere contattato.');
    return;
  }

  if(!controlla_input()) {
    return;
  }

  $('#label_inserisci_dati').hide();
  $('#div_utente').hide();
  $('#div_fornitore').hide();
  $('#div_tempo_default').hide();
  $('#buttons_avanti_indietro').hide();
  $('#div_pushbullet').show();
}

function indietro() {
  $('#label_inserisci_dati').show();
  $('#div_utente').show();
  $('#div_fornitore').show();
  $('#div_tempo_default').show();
  $('#buttons_avanti_indietro').show();
  $('#div_pushbullet').hide();
}

function controlla_input() {

  var nome = $("#reg_nome").val();
  var cognome = $("#reg_cognome").val();
  var codfiscale = $("#reg_codfiscale").val();
  var indirizzo = $("#reg_indirizzo").val();
  var telefono = $("#reg_telefono").val();
  var email = $("#reg_email").val();
  var pass1 = $("#reg_pass1").val();
  var pass2 = $("#reg_pass2").val();

  if(nome == '' || cognome == '' || codfiscale == '' || indirizzo == '' || telefono == '' || email == '' || pass1 == '' || pass2 == '') {
    alert('Assicurati di aver riempito tutti i campi.');
    return false;
  }
  if(!controllaCF(codfiscale)) {
    $('#cf_non_valido').show();
    $('#reg_codfiscale').css('border', '1px solid red');
    return false;
  } else {
    $('#reg_codfiscale').css('border', '1px solid #D0D0D0');
    $('#cf_non_valido').hide();
  }

  if($('#reg_fornitore').is(':checked')) {
    var piva = $("#reg_piva").val();
    var tempo = $("#reg_minuti").val();

    if(piva == '') {
      alert('Assicurati di aver riempito tutti i campi.');
      return false;
    }
    if(tempo == 0 || tempo > tempo_max) {
      $('#reg_minuti').css('border', '1px solid red');
      $('#tempo_max_alert').show();
      return false;
    } else {
      $('#reg_minuti').css('border', '1px solid #D0D0D0');
      $('#tempo_max_alert').hide();
    }
    var piva_valida = controllaPIVA(piva);
    if( piva_valida != true) {
      $('#piva_non_valida').html(piva_valida);
      $('#piva_non_valida').show();
      $('#reg_piva').css('border', '1px solid red');
      return false;
    } else {
      $('#reg_piva').css('border', '1px solid #D0D0D0');
      $('#piva_non_valida').hide();
    }
  }
  var ok = false;
  $.ajax({
      type: "POST",
      url: 'php/administrator.php',
      dataType: 'json',
      data: {functionname: 'check_cf_email', arguments: [codfiscale, email]},
      success: function (obj) {
        console.log(obj);
        if(obj.email >= 1) {
          $('#email_gia_presente').show();
          return false;
        } else {
          $('#email_gia_presente').hide();
          ok = true;
        }
        if(obj.cf >= 1) {
          $('#cf_gia_presente').show();
          return false;
        } else {
          $('#cf_gia_presente').hide();
          ok = true;
        }
      }
  });

  return ok;
}

function Trim(stringa) {
  reTrim=/\s+$|^\s+/g;
  return stringa.replace(reTrim,"");
}

function controllaCF(codfiscale) {
  var re = /^[A-Z]{6}\d{2}[A-Z]\d{2}[A-Z]\d{3}[A-Z]$/;
  Codice=Trim(codfiscale.toUpperCase());
  return re.test(Codice);
}

function controllaPIVA(pi) {
  if( pi.length != 11 ) {
    return "la partita IVA dovrebbe essere lunga\n" +
          "esattamente 11 caratteri.\n";
  }
  validi = "0123456789";
  for( i = 0; i < 11; i++ ){
      if( validi.indexOf( pi.charAt(i) ) == -1 )
          return "La partita IVA contiene un carattere non valido `" +
              pi.charAt(i) + "'.\nI caratteri validi sono le cifre.\n";
  }
  s = 0;
  for( i = 0; i <= 9; i += 2 )
      s += pi.charCodeAt(i) - '0'.charCodeAt(0);
  for( i = 1; i <= 9; i += 2 ){
      c = 2*( pi.charCodeAt(i) - '0'.charCodeAt(0) );
      if( c > 9 )  c = c - 9;
      s += c;
  }
  /*if( ( 10 - s%10 )%10 != pi.charCodeAt(10) - '0'.charCodeAt(0) )
    return "il codice di controllo non corrisponde.\n";*/
  return true;
}