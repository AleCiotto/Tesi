<?php
include '../lib/ChromePhp.php';
require_once('../lib/db_data.class.php');

// esecuzione della query per aggiornare la disponibilita' del fornitore
function query_sono_disponibile($id_fornitore, $pos_x, $pos_y){

	$query = "
	      UPDATE Disponibili
	      SET disponibile=1,
						posizione_x='".mysql_real_escape_string($pos_x)."',
						posizione_y='".mysql_real_escape_string($pos_y)."'
				WHERE id_fornitore='".mysql_real_escape_string($id_fornitore)."'";
  $result = mysql_query($query) or die(mysql_error());

	return $result;
}

function query_sono_occupato($id_fornitore){

	$query = "
	      UPDATE Disponibili
	      SET disponibile=0
	      WHERE id_fornitore='".mysql_real_escape_string($id_fornitore)."'";
  $result = mysql_query($query) or die(mysql_error());

	return $result;
}

function query_get_address($id_fornitore){

	$query = "
	      SELECT indirizzo
	      FROM Users
	      WHERE id = ".$id_fornitore."";
  $result = mysql_query($query) or die(mysql_error());

	$a = array();
	if($result){
    while($row = mysql_fetch_assoc($result)) {
      $a = $row;
    }
	} else {
	  $a = array('error' => 'dead');
	}
	return $a;
}

function query_cerca_richieste($id_fornitore){

	$query = "
	      SELECT COUNT(*)
	      FROM Richieste
	      WHERE id_fornitore=".$id_fornitore." AND
	            esito='sospesa'";

  $result = mysql_query($query) or die(mysql_error());

  $count = mysql_result($result,0);

  if($count) {
  	return true;
  } else {
	  return false;
  }
}

function query_ultima_richiesta($id_fornitore){

	$query = "
	      SELECT R.esito, R.data_ora, U.nome, U.cognome
	      FROM Richieste as R JOIN Users as U ON R.id_utente=U.id
	      WHERE R.id_fornitore=".$id_fornitore." AND
	            R.data_ora = (SELECT MAX(data_ora)
	                    FROM Richieste
	                    WHERE id_fornitore=".$id_fornitore.")";

  $result = mysql_query($query) or die(mysql_error());

  $a = array();
	if($result){
    while($row = mysql_fetch_assoc($result)) {
      $a = $row;
    }
	} else {
	  $a = array('error' => 'dead');
	}
	return $a;
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
        case 'sono_disponibile':

           if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 2) ) {
               $aResult['error'] = 'Error in arguments!';
           } else {
              // eseguo la query insert
              $result = query_sono_disponibile($_POST['arguments'][0],$_POST['arguments'][1],$_POST['arguments'][2]);
              if($result) {
                $aResult['result'] = true;
              } else {
                $aResult['result'] = false;
              }
           }
           break;

        case 'sono_occupato':

          $result = query_sono_occupato($_POST['arguments'][0]);
          if($result) {
            $aResult['result'] = true;
          } else {
            $aResult['result'] = false;
          }
          break;

        case 'get_address':

          $result = query_get_address($_POST['arguments']);
          if($result) {
            $aResult['result'] = $result;
          } else {
            $aResult['result'] = false;
          }
          break;

        case 'cerca_richieste':

          $result = query_cerca_richieste($_POST['arguments']);
          if($result) {
            $aResult['result'] = true;
          } else {
            $aResult['result'] = false;
          }
          break;

        case 'cerca_ultima_richiesta':

          $result = query_ultima_richiesta($_POST['arguments']);
          if($result) {
            $aResult['result'] = $result;
          } else {
            $aResult['result'] = false;
          }
          break;

        default:
           $aResult['error'] = 'Not found function '.$_POST['functionname'].'!';
           break;
    }

}

	mysql_close();
  echo json_encode($aResult);
?>