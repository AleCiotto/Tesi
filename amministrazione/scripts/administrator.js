
$("document").ready(function(){
  var richieste_accettate;

  $.ajax({
    type: "POST",
    url: '../php/administrator.php',
    dataType: 'json',
    data: {functionname: 'get_statistiche', arguments: null},
    success: function (obj) {
      console.log(obj);

      var accettate = parseInt(obj.result.richieste_accettate);
      var rifiutate = parseInt(obj.result.richieste_rifiutate);
      var scadute = parseInt(obj.result.richieste_scadute);

      $('#tot_utenti').html(parseInt(obj.result.utenti)+parseInt(obj.result.fornitori));
      $('#tot_fornitori').html(obj.result.fornitori);
      $('#tot_richieste').html(obj.result.richieste);
      $('#tot_richieste_').html(obj.result.richieste);
      $('#tot_accettate').html(obj.result.richieste_accettate);
      $('#tot_rifiutate').html(obj.result.richieste_rifiutate);
      $('#tot_scadute').html(obj.result.richieste_scadute);

      var d = new Date();
      var month = new Array();
        month[0] = "Gennaio";
        month[1] = "Febbraio";
        month[2] = "Marzo";
        month[3] = "Aprile";
        month[4] = "Maggio";
        month[5] = "Giugno";
        month[6] = "Luglio";
        month[7] = "Agosto";
        month[8] = "Settembre";
        month[9] = "Ottobre";
        month[10] = "Novembre";
        month[11] = "Dicembre";
      var this_month = month[d.getMonth()];

      // utenti
      var u0 = obj.result.line_chart.utenti.mese_0;
      var u1 = obj.result.line_chart.utenti.mese_1;
      var u2 = obj.result.line_chart.utenti.mese_2;
      var u3 = obj.result.line_chart.utenti.mese_3;
      var u4 = obj.result.line_chart.utenti.mese_4;
      var u5 = obj.result.line_chart.utenti.mese_5;
      var u6 = obj.result.line_chart.utenti.mese_6;
      // fornitori
      var f0 = obj.result.line_chart.fornitori.mese_0;
      var f1 = obj.result.line_chart.fornitori.mese_1;
      var f2 = obj.result.line_chart.fornitori.mese_2;
      var f3 = obj.result.line_chart.fornitori.mese_3;
      var f4 = obj.result.line_chart.fornitori.mese_4;
      var f5 = obj.result.line_chart.fornitori.mese_5;
      var f6 = obj.result.line_chart.fornitori.mese_6;
      // richieste
      var r0 = obj.result.line_chart.richieste.mese_0;
      var r1 = obj.result.line_chart.richieste.mese_1;
      var r2 = obj.result.line_chart.richieste.mese_2;
      var r3 = obj.result.line_chart.richieste.mese_3;
      var r4 = obj.result.line_chart.richieste.mese_4;
      var r5 = obj.result.line_chart.richieste.mese_5;
      var r6 = obj.result.line_chart.richieste.mese_6;

        // LINE CHART

      // Get context with jQuery - using jQuery's .get() method.
      var ctx = $("#chartUtenti").get(0).getContext("2d");
      // This will get the first returned node in the jQuery collection.
      var myNewChart = new Chart(ctx);

      Chart.defaults.global.responsive = true;

      var data = {
        labels: [month[(d.getMonth()+6)%12], month[(d.getMonth()+7)%12], month[(d.getMonth()+8)%12], month[(d.getMonth()+9)%12], month[(d.getMonth()+10)%12], month[(d.getMonth()+11)%12], month[d.getMonth()]],
        datasets: [
            {
                label: "Fornitori",
                fillColor: "rgba(111,137,215,0.2)",
                strokeColor: "rgba(111,137,215,1)",
                pointColor: "rgba(111,137,215,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(111,137,205,1)",
                data: [f0, f1, f2, f3, f4, f5, f6]
            },
            {
                label: "Richieste",
                fillColor: "rgba(255,0,0,0.05)",
                strokeColor: "rgba(255,0,0,1)",
                pointColor: "rgba(255,0,0,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(220,220,220,1)",

                data: [r0, r1, r2, r3, r4, r5, r6]
            },
            {
                label: "Utenti",
                fillColor: "rgba(151,187,205,0.1)",
                strokeColor: "rgba(151,187,205,1)",
                pointColor: "rgba(151,187,205,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(151,187,205,1)",
                data: [u0, u1, u2, u3, u4, u5, u6]
            }
        ]
      };
      var myLineChart = new Chart(ctx).Line(data, {
        bezierCurve: false
      });

        // PIE CHART

      // Get context with jQuery - using jQuery's .get() method.
      var ctx_ = $("#chartRichieste").get(0).getContext("2d");
      // This will get the first returned node in the jQuery collection.
      var myNewChart_ = new Chart(ctx_);

      var data_ = [
        {
            value: accettate,
            color:"#00CC00",
            highlight: "#008F00",
            label: "Accettate"
        },
        {
            value: rifiutate,
            color: "#FF3300",
            highlight: "#CC2900",
            label: "Rifiutate"
        },
        {
            value: scadute,
            color: "#3399FF",
            highlight: "#246BB2",
            label: "Scadute"
        }
    ]
    // For a pie chart
    var myPieChart = new Chart(ctx_).Pie(data_, {
      animateScale: true
    }); // fine success:
    },
    error: function (error, exception) {
      alert("error " + error.status + " " + error.statusText + " " + exception);
    }
  });
});