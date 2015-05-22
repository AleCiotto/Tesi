
function nuovaPassword() {
  var email = $('#email').val();
  console.log(email);

  $.ajax({
    type: "POST",
    url: 'php/update.php',
    dataType: 'json',
    data: {functionname: 'recovery_password', arguments: email},
    success: function (obj) {
      console.log(obj);
      if(obj.result) {
        window.location.href = "index.php";
      } else {
        alert('Abbiamo riscontrato un errore. Riprova più tardi.');
      }

    },
    error: function (error, exception) {
      alert("error " + error.status + " " + error.statusText + " " + exception);
    }
  });
}