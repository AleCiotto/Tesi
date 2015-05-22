
// funzione chiamata quando si preme sulla X nella tabella dei soci/dipendenti
function delete_employee(id) {
    var confirmed = confirm('Sei sicuro?');
    if(confirmed) {
      $.ajax({
      type: "POST",
      url: 'php/update.php',
      dataType: 'json',
      data: {functionname: 'elimina_socio', arguments: id},
      success: function (obj) {
        if(obj.result) {
          location.reload();
        }
      },
      error: function (error, exception) {
        alert("error " + error.status + " " + error.statusText + " " + exception);
        }
      });
    }
}

function delete_account(id) {
  var confirmed = confirm('Sei sicuro? \nQuesto account verrà eliminato definitivamente.');
  if(confirmed) {
    $.ajax({
      type: "POST",
      url: 'php/update.php',
      dataType: 'json',
      data: {functionname: 'elimina_account', arguments: id},
      success: function (obj) {
        alert(obj);
        if(obj.result) {
          window.location.href = "logout.php";
        }
      },
      error: function (error, exception) {
        alert("error " + error.status + " " + error.statusText + " " + exception);
      }
    });
  }
}

// funzione per salvare le modifiche effettuate
function save_myAccount(fornitore, id, principale){
  //fornitore e' 1 se l'utente e' un fornitore, 0 altrimenti
  var a = $("#my_nome").val();
  var b = $("#my_cognome").val();
  var c = $("#my_codfiscale").val();
  var d = $("#my_indirizzo").val();
  var e = $("#my_telefono").val();
  var f = $("#my_email").val();
  var g = $("#my_pass1").val();
  var h = $("#my_pass2").val();
  var y = '';
  if($("#usa_indirizzo").prop('checked')){
    y += '1';
  } else {
    y += '0';
  }
  if($("#usa_telefono").prop('checked')){
    y += '1';
  } else {
    y += '0';
  }
  if($("#usa_email").prop('checked')){
    y += '1';
  } else {
    y += '0';
  }
  var z = $("#my_minuti").val();
  if(z == '' || z == 0) {
    z = 'NULL';
  }

  if(y == '000') {
    alert('Devi abilitare l\' uso del telefono o dell\' indirizzo email per essere contattato.');
    return;
  }

  if(a==null || a=="",b==null || b=="",c==null || c=="",d==null || d=="",e==null || e=="",f==null || f=="",g==null || g=="",h!=g){

    alert("Assicurati di aver compilato tutti i campi.");

  } else {

      if(fornitore && principale) {
        var i = $("#my_ragsociale").val();
        var j = $("#my_piva").val();
        var k = $("#my_attivita").val();
        var l = $("#my_descrizione").val();
        var m = $("#my_pagamento").val();
        var n = $("#my_limite_spesa").val();
        var o = $("#my_nomeazienda").val();

        if(i==null || i=="",j==null || j=="",k==null || k=="",l==null || l=="",m==null || m=="",n==null || n=="", o==null || o==""){
          alert("Assicurati di aver compilato tutti i campi.");
        }
      } else {
        var i = null;
        var j = null;
        var k = null;
        var l = null;
        var m = null;
        var n = null;
        var o = null;
      }

    // se tutti i campi sono compilati
    $.ajax({
    type: "POST",
    url: 'php/update.php',
    dataType: 'json',
    data: {functionname: 'modifica_myAccount', arguments: [id, a, b, c, d, e, f, g, i, j, k, l, m, n, o, y, z, fornitore, principale]},
    success: function (obj) {
      console.log(obj);
      if(obj.result) {
        alert("Modifiche salvate correttamente.");
      }
    },
    error: function (error, exception) {
      alert("error " + error.status + " " + error.statusText + " " + exception);
      }
    });

  }
}

function torna_alla_home() {
  window.location.href = "home.php";
}

// chiede tutti i dati dell'utente e popola le textbox
function get_myAccount(id){
  $.ajax({
    type: "POST",
    url: 'php/update.php',
    dataType: 'json',
    data: {functionname: 'get_myAccount', arguments: id},
    success: function (obj) {
	console.log(obj);
      $("#my_nome").val(obj.myAccount.nome);
      $("#my_cognome").val(obj.myAccount.cognome);
      $("#my_codfiscale").val(obj.myAccount.codfiscale);
      $("#my_indirizzo").val(obj.myAccount.indirizzo);
      $("#my_telefono").val(obj.myAccount.telefono);
      $("#my_email").val(obj.myAccount.email);
      $("#my_pass1").val(obj.myAccount.password);
      $("#my_pass2").val(obj.myAccount.password);
      $("#my_minuti").val(obj.myAccount.minuti_attesa);
      var dati_contatto = ''+obj.myAccount.dati_contatto;
      if(dati_contatto.substring(0,1) == 1) {
        $('#usa_indirizzo').prop('checked', true);
      }
      if(dati_contatto.substring(1,2) == 1) {
        $('#usa_telefono').prop('checked', true);
      }
      if(dati_contatto.substring(2,3) == 1) {
        $('#usa_email').prop('checked', true);
      }

      if(obj.myAccount.partita_iva != null && obj.myAccount.partita_iva != ''){
        $("#my_nomeazienda").val(obj.myAccount.nome_azienda);
        $("#my_ragsociale").val(obj.myAccount.ragione_sociale);
        $("#my_piva").val(obj.myAccount.partita_iva);
        $("#my_attivita").val(obj.myAccount.tipo_attivita);//
        $("#my_descrizione").val(obj.myAccount.descrizione);
        $("#my_pagamento").val(obj.myAccount.modalita_pagamento);//
        $("#my_limite_spesa").val(obj.myAccount.limite_spesa);
        if(obj.myAccount.principale == 0) { //principale e' un booleano
          $("#my_nomeazienda").prop("disabled", true);
          $("#my_ragsociale").prop("disabled", true);
          $("#my_piva").prop("disabled",true);
          $("#my_attivita").prop("disabled",true);
          $("#my_descrizione").prop("disabled",true);
          $("#my_pagamento").prop("disabled",true);
          $("#my_limite_spesa").prop("disabled",true);
        }
      }
    },
    error: function (error, exception) {
      alert("error " + error.status + " " + error.statusText + " " + exception);
    }
  });
}
