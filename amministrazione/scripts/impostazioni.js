
$("document").ready(function(){

  load_settings();

  // aggiorna lo stato del fornitore ad ogni click dello switch
  $('#auto_update').click(function () {
    if(this.checked) {
      console.log('auto_update: true');
      update_settings('auto_update', true);
    } else {
      console.log('auto_update: false');
      update_settings('auto_update', false);
    }
  });
});

function load_settings() {
  $.ajax({
    type: "POST",
    url: '../php/administrator.php',
    dataType: 'json',
    data: {functionname: 'load_settings', arguments: null},
    success: function (obj) {
      console.log(obj);
      if(obj.result[0].option_value == 'true') {
        console.log('auto_update = true');
        //$('#auto_update').val('checked');
        $('#auto_update').attr("checked", "");
      }
      $('#tempo_default').val(obj.result[1].option_value);
      $('#tempo_max').val(obj.result[3].option_value);
      $('#costo_richiesta').val(obj.result[2].option_value);
      $('#paypal').val(obj.result[4].option_value);
      $('#iban').val(obj.result[5].option_value);
    },
    error: function (error, exception) {
      alert("error " + error.status + " " + error.statusText + " " + exception);
    }
  });
}

function update_settings(settings_name, settings_value) {
  $.ajax({
    type: "POST",
    url: '../php/administrator.php',
    dataType: 'json',
    data: {functionname: 'update_settings', arguments: [settings_name, settings_value]},
    success: function (obj) {
      if(obj.result == 'true') {
        console.log('aggiornato con successo');
      }
    },
    error: function (error, exception) {
      alert("error " + error.status + " " + error.statusText + " " + exception);
    }
  });
}

function salva(name) {
  if(name == 'tempo_default') {
    var settings_name = 'tempo_default';
    var settings_value = $('#tempo_default').val();
  } else if(name == 'tempo_max') {
    var settings_name = 'tempo_max';
    var settings_value = $('#tempo_max').val();
  } else if(name == 'costo_richiesta') {
    var settings_name = 'costo_richiesta';
    var settings_value = $('#costo_richiesta').val();
  } else if(name == 'iban') {
    var settings_name = 'iban';
    var settings_value = $('#iban').val();
    if(settings_value.length < 27) {
      alert('L\' IBAN deve avere una lunghezza di 27 caratteri');
      $('#iban').css('border', '1px solid red');
      return;
    } else {
      $('#iban').css('border', '1px solid #D0D0D0');
    }
  } else if(name == 'paypal') {
    var settings_name = 'paypal';
    var settings_value = $('#paypal').val();
  }

  $.ajax({
    type: "POST",
    url: '../php/administrator.php',
    dataType: 'json',
    data: {functionname: 'update_settings', arguments: [settings_name, settings_value]},
    success: function (obj) {
      if(obj.result == 'false') {
        alert('Errore: qualcosa è andato storto.');
      }
    },
    error: function (error, exception) {
      alert("error " + error.status + " " + error.statusText + " " + exception);
    }
  });
}