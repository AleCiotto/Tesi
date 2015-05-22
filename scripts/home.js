
$(document).ready(function() {
  // aggiornamento valore range
  var range_distanza = $('#distanza');
  var range_rating = $('#rating');
  var value_distanza = $('#distanza-value');
  var value_rating = $('#rating-value');

  value_distanza.html(range_distanza.attr('value'));
  value_rating.html(range_rating.attr('value'));

  range_distanza.on('input', function(){
      value_distanza.html(this.value);
  });

  range_rating.on('input', function(){
      value_rating.html(this.value);
  });

  // mostra/nasconde il campo di inserimento dell' indirizzo se viene selezionato il rispettivo radio button
  $('input[type=radio][name=posizione]').change(function() {
      if (this.value == 'altro_indirizzo') {
          $('#altro_indirizzo').show();
      }
      else {
          $('#altro_indirizzo').hide();
      }
  });
});