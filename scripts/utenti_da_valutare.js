
$("document").ready(function() {

  var mio_id = $('.app-bar-actions').data('user');
  $.ajax({
    type: "POST",
    url: 'php/update.php',
    dataType: 'json',
    data: {functionname: 'num_feedback', arguments: mio_id},
    success: function (obj) {
      if(obj.result == 0) {
        return;
      } else if(obj.result > 0) {
        $('.app-bar-actions').append('<button id="feedback_da_valutare" class="" onclick="window.location=\'feedback.php\'">'+
                                '<img src="images/pen_2_white.png"> <span id="num_feedback">'+obj.result+' Feedback da lasciare</span><span id="num_feedback_mobile">'+obj.result+'</span></button>');
      }
    }
  });
})

