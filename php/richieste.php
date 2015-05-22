<?php
require_once('../lib/db_data.class.php');

// esecuzione della qeury per aggiornare la disponibilita' del fornitore
function query_load_richieste($id_utente, $fornitore, $principale){
// fornitore e principale sono un booleano
	$query = "SELECT Users.nome, Users.cognome, Richieste.tipo_richiesta, Richieste.esito, Richieste.distanza, Aziende.nome_azienda, Aziende.ragione_sociale,
              TIMESTAMPDIFF(MINUTE, NOW(), Richieste.data_ora + INTERVAL Richieste.minuti_risposta MINUTE) as tempo_rimasto, Aziende.valutazione as rating, Richieste.data_ora
	          FROM Richieste JOIN Users ON Richieste.id_fornitore=Users.id JOIN Aziende ON Users.partita_iva=Aziende.partita_iva
	          WHERE id_utente=".$id_utente."
	          ORDER BY Richieste.id DESC";
	$result = mysql_query($query) or die(mysql_error());

	$a = array();

	if($result){
    while($row = mysql_fetch_assoc($result)) {
      $a[] = $row;
    }
    if($fornitore && $principale) {
      $query_p = "SELECT Users.nome, Users.cognome, Richieste.tipo_richiesta, Richieste.esito, Richieste.distanza,
                  TIMESTAMPDIFF(MINUTE, NOW(), Richieste.data_ora + INTERVAL Richieste.minuti_risposta MINUTE) as tempo_rimasto, Users.rating, Richieste.data_ora
    	          FROM Richieste JOIN Users ON Richieste.id_utente=Users.id
    	          WHERE Richieste.id_fornitore IN (SELECT id FROM Users WHERE partita_iva = (SELECT partita_iva FROM Users WHERE id=".$id_utente."))
    	          ORDER BY Richieste.id DESC";
    	$result_p = mysql_query($query_p) or die(mysql_error());
    	$b = array();
    	if($result_p){
        while($row = mysql_fetch_assoc($result_p)) {
          $b[] = $row;
        }
      }
    } else if($fornitore) {
      $query_ = "SELECT Users.nome, Users.cognome, Richieste.tipo_richiesta, Richieste.esito, Richieste.distanza,
                  TIMESTAMPDIFF(MINUTE, NOW(), Richieste.data_ora + INTERVAL Richieste.minuti_risposta MINUTE) as tempo_rimasto, Users.rating, Richieste.data_ora
    	          FROM Richieste JOIN Users ON Richieste.id_utente=Users.id
    	          WHERE Richieste.id_fornitore=".$id_utente."
    	          ORDER BY Richieste.id DESC";
    	$result_ = mysql_query($query_) or die(mysql_error());
    	$b = array();
    	if($result_){
        while($row = mysql_fetch_assoc($result_)) {
          $b[] = $row;
        }
      }
    } else { $b = null; } // fine if - else if - else
    $richieste = array('richieste' => $a, 'richieste_fornitore' => $b);
	} else {
	  return array('error' => 'dead');
	}
	return $richieste;
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

        case 'load_richieste':

          $aResult = query_load_richieste($_POST['arguments'][0],$_POST['arguments'][1],$_POST['arguments'][2]);
          break;

        default:
           $aResult['error'] = 'Not found function '.$_POST['functionname'].'!';
           break;
    }

}

mysql_close();
echo json_encode($aResult);
?>