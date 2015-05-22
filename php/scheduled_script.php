<?php
require 'invio_email.php';

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

function aggiorna_stato($id_fornitore){

  // prima controllo le impostazioni dell' app
  $query_settings = "SELECT option_value
                     FROM Impostazioni
                     WHERE option_name='auto_update'";
  $result_settings = mysql_query($query_settings) or die(mysql_error());
  $value = mysql_result($result_settings,0);

  if($value == 'true' || $value == true ) {
    // se settato su true aggiorno il suo stato su non disponibile
    $query_update = "UPDATE Disponibili
                     SET disponibile=0
                     WHERE id_fornitore=".$id_fornitore;
    $result_update = mysql_query($query_update) or die(mysql_error());

    $result = mysql_affected_rows();
    if($result > 0) {
      return true;
    } else {
      return false;
    }
  }
}

  // se da errore eseguire da terminale: sudo apt-get install php5-curl
  require 'PushBullet.class.php';

  $host_db = '127.0.0.1:3306';
	$user_db = 'root';
	$pass_db = '1LcL880y';
	$name_db = 'webapp';

	/*risorse di connessione*/
	$conn;
	$selezione_db;

  $conn = mysql_connect($host_db, $user_db, $pass_db) or die(mysql_error());
	$selezione_db = mysql_select_db($name_db, $conn) or die(mysql_error());

	$query = "SELECT Richieste.id, Users.email_pushbullet, Users.email, Richieste.id_fornitore
	          FROM Richieste LEFT JOIN Users ON Richieste.id_utente=Users.id
	          WHERE `data_ora`+ INTERVAL `minuti_risposta` MINUTE < NOW() AND
	          esito = 'sospesa'";
	$result = mysql_query($query) or die(mysql_error());

	$scriptLog = fopen("logfile.txt", "a");
	$now = date('Y-m-d H:i:s');
	$date_time = "\n[".$now."] ";

	if(mysql_num_rows($result)>0){

	  $emails = array();

	  $first = true;
	  $update_query = "UPDATE Richieste
	                   SET esito = 'scaduta'
	                   WHERE id IN (";

    while($row = mysql_fetch_assoc($result)) {
      $emails[] = $row['email_pushbullet'];
      $emails_noPushbullet[] = $row['email'];
      $id_fornitori[] = $row['id_fornitore'];
      query_insert_feedback($row['id'], 'scaduta');
      if ($first) {
        $update_query .= $row['id'];
        $first = false;
      } else {
        $update_query .= ",".$row['id'];
      }
    }
    $update_query .= ")";
    echo $update_query;

    $result_update = mysql_query($update_query) or die(mysql_error());
    if ($result_update) {
      fwrite($scriptLog, $date_time." ".mysql_num_rows($result)." richieste scadute.");
      for($i = 0; $i < count($emails); $i++) {
      	try {
      	  $email_pushbullet = $emails[$i];
        	if($email_pushbullet != null) {
            #### AUTHENTICATION ####
            // Get your API key here: https://www.pushbullet.com/account
            $p = new PushBullet('wdOrDjb4HEReeravUaCt8PVTz9FgEQST');
            $p->pushNote($email_pushbullet, 'Richiesta scaduta!', 'Il fornitore ha esaurito il tempo a disposizione.');
      	  } else {
      	    $email = $emails_noPushbullet[$i];
      	    $subject = 'Richeista Scaduta';
      	    $body = 'La informiamo che la sua richiesta è scaduta.<br>Il fornitore ha esaurito il tempo a disposizione.';
      	    $emailResult = sendEmail($email, $subject, $body);
      	    fwrite($scriptLog, $date_time." emailResult: ".$emailResult.".");
      	  }
        } catch (PushBulletException $e) {
          die($e->getMessage());
        }
      }

      fwrite($scriptLog, $date_time."id_fornitori[] size ".count($id_fornitori));

      for($j = 0; $j < count($id_fornitori); $j++) {
      	  $id_fornitore = $id_fornitori[$j];
          $res = aggiorna_stato($id_fornitore);
          if($res == false){
            fwrite($scriptLog, $date_time."errore durante l' aggiornamento dello stato del fornitore.");
          } else {
            fwrite($scriptLog, $date_time."stato fornitore id ".$id_fornitore." aggiornato.");
          }

      }


    } else {
      fwrite($scriptLog, $date_time."errore nella eliminazione di richieste sospese.");
    }

	} else {
	  fwrite($scriptLog, $date_time."nessun risultato.");
	}

	mysql_close();
	fclose($scriptLog);

?>