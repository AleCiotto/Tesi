<?php
include '../lib/ChromePhp.php';
require_once('../lib/db_data.class.php');
require('invio_email.php');

function query_cerca($posizione, $artigiano, $distanza, $rating, $mylat, $mylng, $myid){

	$lon1 = $mylng - $distanza / abs(cos(deg2rad($mylat))*69);
	$lon2 = $mylng + $distanza / abs(cos(deg2rad($mylat))*69);
	$lat1 = $mylat - ($distanza / 69);
	$lat2 = $mylat + ($distanza / 69);

	$formula_distanza = "(3956 * 2 * ASIN(SQRT( POWER(SIN((".$mylat." - posizione_x) *
                      pi()/180 / 2), 2) +
                      COS(".$mylat." * pi()/180) * COS(posizione_x * pi()/180) *
                      POWER(SIN((".$mylng." - posizione_y) * pi()/180 / 2), 2) ))
                      ) * 1.609344";

  $contatti_mensili = "(SELECT COUNT(*)
                        FROM Richieste
                        WHERE MONTH(NOW()) = MONTH(Richieste.data_ora) AND
                          id_azienda = Aziende.id)*(SELECT option_value
                                                    FROM Impostazioni
                                                    WHERE option_name = 'costo_richiesta')";

  /* fa il join confrontando la partita iva, sarebbe meglio usare il campo Users.id_azienda? */
	$query = "SELECT *,Users.id as id, Aziende.id as id_azienda, Aziende.num_valutazioni as num_valutazioni_azienda, ".$formula_distanza." as distanza
	          FROM Users RIGHT JOIN Disponibili ON Users.id=Disponibili.id_fornitore RIGHT JOIN Aziende ON Users.partita_iva=Aziende.partita_iva
	          WHERE Aziende.tipo_attivita='".$artigiano."' AND
	                (Aziende.valutazione >=".$rating." OR Aziende.valutazione IS NULL) AND
	                ".$contatti_mensili." < Aziende.limite_spesa AND
	                posizione_x between ".$lat1." and ".$lat2." AND
	                posizione_y between ".$lon1." and ".$lon2." AND
	                disponibile =1 AND
	                id_fornitore <> ".$myid."
	          GROUP BY Users.id
	          HAVING distanza <= ".$distanza;

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

function query_minuti_attesa($id_utente){

  $query = "SELECT minuti_attesa
	          FROM Users
	          WHERE id=".$id_utente;
	$result = mysql_query($query) or die(mysql_error());
	$min = mysql_result($result,0);
	return $min;
}

function query_get_myAddress($id_utente) {

	$query = "SELECT indirizzo
	          FROM Users
	          WHERE id=".$id_utente;
	$result = mysql_query($query) or die(mysql_error());
	$indirizzo = mysql_result($result,0);
	return $indirizzo;

}

function invia_richiesta($id_utente, $id_fornitore, $tipo_richiesta, $minuti, $distanza){

	$query_richieste_sospese = "SELECT TIMESTAMPDIFF(MINUTE, NOW(), data_ora + INTERVAL minuti_risposta MINUTE) as minuti_rimanenti
	                            FROM Richieste
	                            WHERE id_utente=".$id_utente." AND
	                                  esito = 'sospesa' AND
	                                  NOW() < data_ora + INTERVAL minuti_risposta MINUTE";

	$risultato = mysql_query($query_richieste_sospese) or die(mysql_error());
	// se ci sono gia' richieste in sospeso da parte dell'utente (in teoria al max una), non invio nulla
	if (mysql_num_rows($risultato)) {
	  $row = mysql_fetch_object($risultato);
	  $minuti = $row->minuti_rimanenti;
	  $result_array = array('aspetta', $minuti);
	  ChromePhp::log('minuti rimanenti: '.$minuti);
  	return $result_array;
	}

	$query = "INSERT INTO Richieste
	          SET id_utente=".$id_utente.",
	              id_fornitore=".$id_fornitore.",
	              tipo_richiesta='".$tipo_richiesta."',
	              esito='sospesa',
	              data_ora=NOW(),
	              minuti_risposta=".$minuti.",
	              distanza='".$distanza."'";

	$result = mysql_query($query) or die(mysql_error());

	if($result){
	  $query_email = "SELECT email, email_pushbullet
	                  FROM Users
	                  WHERE id=".$id_fornitore;
	  $result_email = mysql_query($query_email) or die(mysql_error());
	  $row_email = mysql_fetch_assoc($result_email);
	  $email_pushbullet = $row_email['email_pushbullet'];
	  if($email_pushbullet) {
	    $metod = 'pushbullet';
	    $email = $email_pushbullet;
	  } else {
	    $metod = 'email';
	    $email = $row_email['email'];
	  }

  	return array(true, $metod, $email);
	} else {
	  return array(false);
	}
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
          case 'cerca':

            if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 4) ) {
              $aResult['error'] = 'Error in arguments!';
            } else {
              // la funzione query_cerca esegue la query e ritorna tutti i risultati trovati
              $result = query_cerca($_POST['arguments'][0],$_POST['arguments'][1],$_POST['arguments'][2],$_POST['arguments'][3],$_POST['arguments'][4],$_POST['arguments'][5],$_POST['arguments'][6]);
              if($result) {
                $aResult['result'] = $result;
              } else {
                $aResult['result'] = false;
              }
            }
            break;

          case 'invia_richiesta':

            // restituisce un array //  input: id_utente, id_fornitore, tipo_richiesta, minuti, distanza
            $result = invia_richiesta($_POST['arguments'][0],$_POST['arguments'][1],$_POST['arguments'][2],$_POST['arguments'][3],$_POST['arguments'][4]);

            if(strcmp($result[0],'aspetta') == 0) {
              $aResult['result'] = 'aspetta';
              $aResult['minuti_rimanenti'] = $result[1];
              break; // lo lascio per sicurezza
            } else if($result[0] == true) {
              $aResult['result'] = true;
              $aResult['metod'] = $result[1];
              $aResult['email'] = $result[2];
            } else {
              $aResult['result'] = false;
            }
            break;

          case 'invia_email_richiesta_ricevuta':
            $email = $_POST['arguments'];
      	    $subject = 'Richiesta Ricevuta';
      	    $body = 'Accedi alla webapp per visualizzare i dettagli e gestire la richiesta.<br><a href="http://130.136.143.33:8010/app/index.php">Esegui il login.</a>';
            $aResult['result'] = sendEmail($email, $subject, $body);
            break;

          case 'get_myAddress':

            $result = query_get_myAddress($_POST['arguments']);
            if($result) {
              $aResult['result'] = $result;
            } else {
              $aResult['result'] = false;
            }
            break;

          case 'minuti_default':

            $aResult['result'] = query_minuti_attesa($_POST['arguments']);
            break;

          default:
             $aResult['error'] = 'Not found function '.$_POST['functionname'].'!';
             break;
      }

  }

  mysql_close();
  echo json_encode($aResult);

?>