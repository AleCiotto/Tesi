<?php
include '../lib/ChromePhp.php';
require_once('../lib/db_data.class.php');
require 'invio_email.php';

function query_richieste($fornitore){
  // ho aggiunto R.id perche' altrimenti in id metteva sempre l'id dell' utente
	$query = "SELECT *, R.id, U.num_valutazioni, TIMESTAMPDIFF(MINUTE, NOW(), data_ora + INTERVAL minuti_risposta MINUTE) as minuti_rimanenti,
	                 U.indirizzo, U.email, U.num_valutazioni, U.dati_contatto
	          FROM Richieste as R LEFT JOIN Users as U ON R.id_utente = U.id
	          WHERE R.id_fornitore = ".$fornitore." AND
	                esito = 'sospesa' AND
	                TIMESTAMPDIFF(MINUTE, NOW(), data_ora + INTERVAL minuti_risposta MINUTE) >= 0";

	$result = mysql_query($query) or die(mysql_error());

	$a = array();

	if($result){

    while($row = mysql_fetch_assoc($result)) {
      $a[] = $row;
    }
    $risultati = array('risultati' => $a);
	} else {
	  $a = array('error' => 'dead');
	}

	return $a;

}

function query_costo_richiesta() {
  $query = "SELECT option_value
            FROM Impostazioni
	          WHERE option_name='costo_richiesta'";

	$result = mysql_query($query) or die(mysql_error());
	$costo = mysql_result($result,0);
	return $costo;
}

function query_accetta_richiesta($id_richiesta){
  // ho aggiunto R.id perche' altrimenti in id metteva sempre l'id dell' utente
	$query_update = "UPDATE Richieste
	                 SET esito = 'accettata'
	                 WHERE id = ".$id_richiesta;

	$result_update = mysql_query($query_update) or die(mysql_error());

	$rows_updated = mysql_affected_rows();

	if($rows_updated == 1 || $rows_updated == 0) {

  	// invio email con tutte le informazioni
    invia_email_richiesta_accettata($id_richiesta);

    // creo il feedback nella relativa tabella
    query_insert_feedback($id_richiesta, 'accettata');

    // imposta stato non disponibile se attivo nelle impostazioni
    aggiorna_stato($id_richiesta);
	}

	return true;
}

// questa funzione invia una email riassuntiva al fornitore con i dati dell' utente che lo vuole contattare
function invia_email_richiesta_accettata($id_richiesta) {
  $query = "SELECT U.email as email_fornitore, UU.nome, UU.cognome, UU.telefono, UU.indirizzo, UU.email as email_utente, UU.dati_contatto
            FROM Richieste as R JOIN Users as U ON R.id_fornitore=U.id JOIN Users as UU ON R.id_utente=UU.id
            WHERE R.id=".$id_richiesta;
  $result = mysql_query($query) or die(mysql_error());

	if($result){
    $row = mysql_fetch_assoc($result);
	} else {
	  return false;
	}

	if($row['dati_contatto'][0] == 1) {
	  $indirizzo = $row['indirizzo'];
	} else {
	  $indirizzo = '<i>non visibile</i>';
	}
	if($row['dati_contatto'][1] == 1) {
	  $telefono = $row['telefono'];
	} else {
	  $telefono = '<i>non visibile</i>';
	}
	if($row['dati_contatto'][2] == 1) {
	  $email = $row['email_utente'];
	} else {
	  $email = '<i>non visibile</i>';
	}

  $to = $row['email_fornitore'];
  $subject = "Richiesta Accettata";
  $message = "<html><head>
                <title>Hai accettato una richiesta</title>
              </head><body>
                <p>Riepilogo dati utente:</p>
                <p>Nome: ".$row['nome']." ".$row['cognome']."<br>
                   Telefono: ".$telefono."<br>
                   Email: ".$email."<br>
                   Indirizzo: ".$indirizzo."</p>
                <p>Ricordiamo che per ogni richiesta accettata saranno aggiunti 2€ sul conto aziendale.</p>
              </body></html>";

  $res = sendEmail($to, $subject, $message);

}

function aggiorna_stato($id_richiesta){
  // prende in input l' id della richiesta da cui prendere l' id del fornitore

  // prima controllo le impostazioni dell' app
  $query_settings = "SELECT option_value
                     FROM Impostazioni
                     WHERE option_name='auto_update'";
  $result_settings = mysql_query($query_settings) or die(mysql_error());
  $value = mysql_result($result_settings,0);

  if($value == 'true') {
    // se settato su true cerco l' id del fornitore
    $query_fornitore = "SELECT id_fornitore
                        FROM Richieste
                        WHERE id=".$id_richiesta;
    $result_fornitore = mysql_query($query_fornitore) or die(mysql_error());
    $id_fornitore = mysql_result($result_fornitore,0);

    // aggiorno il suo stato su non disponibile
    $query_update = "UPDATE Disponibili
                     SET disponibile=0
                     WHERE id_fornitore=".$id_fornitore;
    $result_update = mysql_query($query_update) or die(mysql_error());
  }
}

function query_rifiuta_richiesta($id_richiesta){

  // ho aggiunto R.id perche' altrimenti in id metteva sempre l'id dell' utente
	$query_update = "UPDATE Richieste
	                 SET esito = 'rifiutata'
	                 WHERE id = ".$id_richiesta;

	$result_update = mysql_query($query_update) or die(mysql_error());

	$rows_updated = mysql_affected_rows();

	if($rows_updated > 0 ) {
    // creo il feedback nella relativa tabella
    query_insert_feedback($id_richiesta, 'rifiutata');

	  return true;
	}

	return false;
}

function query_insert_feedback($id_richiesta, $esito) {
  $query_select = "SELECT R.id_utente, R.id_fornitore, A.id as id_azienda, R.data_ora
	                   FROM Richieste as R LEFT JOIN Users as U ON R.id_fornitore = U.id LEFT JOIN Aziende as A ON U.id_azienda = A.id
	                   WHERE R.id = ".$id_richiesta;

	  $result_select = mysql_query($query_select) or die(mysql_error());
	  $a = array();

  	if($result_select){

      while($row = mysql_fetch_assoc($result_select)) {
        $a = $row;
      }
  	} else {
  	  $a = array('error' => 'dead');
  	}

	  $query_insert = "INSERT INTO Feedback
	                   SET id_utente=".$a['id_utente'].",
	                       id_fornitore=".$a['id_fornitore'].",
	                       id_azienda=".$a['id_azienda'].",
	                       data=NOW(),
	                       esito='".$esito."'";
	  $result_insert = mysql_query($query_insert) or die(mysql_error());
}

  $aResult = array();

  if( !isset($_POST['functionname']) ) { $aResult['error'] = 'No function name!'; }

  if( !isset($_POST['arguments']) ) { $aResult['error'] = 'No function arguments!'; }

  if( !isset($aResult['error']) ) {

    $db = New db_data;
  	$array = $db->get_db_data();
  	$host_db = $array['host_db'];
  	$user_db = $array['user_db'];
  	$pass_db = $array['pass_db'];
    $name_db = $array['name_db'];

  	/*risorse di connessione*/
  	$conn;
  	$selezione_db;

    $conn = mysql_connect($host_db, $user_db, $pass_db) or die(mysql_error());
  	$selezione_db = mysql_select_db($name_db, $conn) or die(mysql_error());

      switch($_POST['functionname']) {
          case 'richieste_sospese':

            if( !isset($_POST['arguments']) ) {
              $aResult['error'] = 'Error in arguments!';
            } else {
              // la funzione query_cerca esegue la query e ritorna tutti i risultati trovati
              $result = query_richieste($_POST['arguments']);
              if($result) {
                $aResult['result'] = $result;
                $aResult['costo'] = query_costo_richiesta();
              } else {
                $aResult['result'] = false;
              }
            }
            break;

          case 'accetta_richiesta':

            if( !isset($_POST['arguments']) ) {
              $aResult['error'] = 'Error in arguments!';
            } else {
              // la funzione query_cerca esegue la query e ritorna tutti i risultati trovati
              $result = query_accetta_richiesta($_POST['arguments']);
              if($result) {
                $aResult['result'] = $result;
              } else {
                $aResult['result'] = false;
              }
            }
            break;

          case 'invia_email_richiesta_accettata':
            // invia una email se l' utente non utilizza pushbullet
            $email = $_POST['arguments'];
      	    $subject = 'Richiesta Accettata';
      	    $body = 'La tua richiesta è stata accettata, presto verrai contattato.';
            $aResult['result'] = sendEmail($email, $subject, $body);
            break;

          case 'rifiuta_richiesta':

            if( !isset($_POST['arguments']) ) {
              $aResult['error'] = 'Error in arguments!';
            } else {
              // la funzione query_cerca esegue la query e ritorna tutti i risultati trovati
              $result = query_rifiuta_richiesta($_POST['arguments']);
              if($result) {
                $aResult['result'] = $result;
              } else {
                $aResult['result'] = false;
              }
            }
            break;

          case 'invia_email_richiesta_rifiutata':
            // invia una email se l' utente non utilizza pushbullet
            $email = $_POST['arguments'];
      	    $subject = 'Richiesta Rifiutata';
      	    $body = 'La tua richiesta è stata rifiutata.<br>Se vuoi effettuare una nuova ricerca, accedi alla WebApp cliccando <a href="http://130.136.143.33:8010/app/index.php">qui</a>.';
            $aResult['result'] = sendEmail($email, $subject, $body);
            break;

          default:
             $aResult['error'] = 'Not found function '.$_POST['functionname'].'!';
             break;
      }
  }

  mysql_close();
  echo json_encode($aResult);

?>