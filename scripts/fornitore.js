function set_disponibile(id_fornitore, from_position){
  // from_position = 1 se l'utente vuole inserire le coord della propria posizione,
  // from_position = 0 se l'utente vuole inserire le coord del proprio indirizzo/aziendale?

  var pos_x;
  var pos_y;

  if(from_position) {
    // chiede le coordinate da maps.js
    pos_x = getPos_x();
    pos_y = getPos_y();
    console.log(pos_x +" "+pos_y);
    query_set_disponibile(id_fornitore, pos_x, pos_y);
  } else {

  // recupero l'indirizzo AZIENDALE dal DB
  $.ajax({
    type: "POST",
    url: 'php/fornitore.php',
    dataType: 'json',
    data: {functionname: 'get_address', arguments: id_fornitore},
    success: function (obj) {
      console.log(obj.result);
      if(obj.result) {
        var address = obj.result.indirizzo;
        // chiede le coordinate a geocoder.js
        var res = codeAddress(address, id_fornitore);
        /* ho spostato la chiamata alla funzione query_set_disponibile() in geocoder.js perche' qui la eseguiva prima che codeAddress() finisse */
      } else {
        //alert('result of get_address == false');
      }
    },
    error: function (error, exception) {
      alert("error " + error.status + " " + error.statusText + " " + exception);
    }
  }); //fine ajax
  } // fine else
}

function query_set_disponibile(id_fornitore, lat, lng){
  $.ajax({
    type: "POST",
    url: 'php/fornitore.php',
    dataType: 'json',
    data: {functionname: 'sono_disponibile', arguments: [id_fornitore, lat, lng]},
    success: function (obj) {
      if(obj.result) {
        $('#disponibile').fadeIn(400).delay(3000).fadeOut(400);
      }
    },
    error: function (error, exception) {
      alert("error " + error.status + " " + error.statusText + " " + exception);
    }
  });
}

function set_occupato(id_fornitore){

  $.ajax({
    type: "POST",
    url: 'php/fornitore.php',
    dataType: 'json',
    data: {functionname: 'sono_occupato', arguments: [id_fornitore]},
    success: function (obj) {
      if(obj.result) {
        $('#occupato').fadeIn(400).delay(3000).fadeOut(400);
      }
    },
    error: function (error, exception) {
      alert("error " + error.status + " " + error.statusText + " " + exception);
      }
    });
}

  $(document).ready(function() {
    // mostra/nasconde la mappa (disattivato)
    $('input[type=radio][name=posizione]').change(function() {
        if (this.value == 'usa_posizione') {
            //$('#map-canvas').show();
            //initialize(); //necessario per una corretta visualizzazione della mappa
        }
        else if (this.value == 'usa_indirizzo') {
            //$('#map-canvas').hide();
        }
    });

    // aggiorna lo stato del fornitore ad ogni click dello switch
    $('#myonoffswitch').click(function () {
      if(this.checked) {
        if($('input[type=radio][name=posizione]:checked').val() == 'usa_posizione') {
          console.log('usa_pozisione');
          set_disponibile(this.value, 1);
        } else {
          console.log('usa_indirizzo');
          set_disponibile(this.value, 0);
        }
      } else {
        set_occupato(this.value);
      }
    });

    var id_fornitore = $('#div_data').data('id_fornitore');
    // verifico se ci sono richieste da gestire
    $.ajax({
      type: "POST",
      url: 'php/fornitore.php',
      dataType: 'json',
      data: {functionname: 'cerca_richieste', arguments: id_fornitore},
      success: function (obj) {
        console.log(obj);
        if(obj.result) {
          $('#richieste_sospese').show();
        }
      },
      error: function (error, exception) {
        alert("error " + error.status + " " + error.statusText + " " + exception);
      }
    });

    // cerco e visualizzo l'esito (e data) dell' ultima richieste
    $.ajax({
      type: "POST",
      url: 'php/fornitore.php',
      dataType: 'json',
      data: {functionname: 'cerca_ultima_richiesta', arguments: id_fornitore},
      success: function (obj) {
        console.log(obj);
        if(obj.result) {
          var html = '<label>La tua ultima richiesta risale al '+obj.result.data_ora+' e il suo esito è: '+obj.result.esito+'.</label>'
          $('#ultima_richiesta').html(html);
        }
      },
      error: function (error, exception) {
        alert("error " + error.status + " " + error.statusText + " " + exception);
      }
    });
});