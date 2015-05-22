<?php
include '../lib/ChromePhp.php';
require_once('../lib/db_data.class.php');
require 'invio_email.php';

function query_load_settings() {

  $query = "SELECT option_name, option_value
            FROM Impostazioni";

	$result = mysql_query($query) or die(mysql_error());

	$settings = array();
	if($result){
    while($row = mysql_fetch_assoc($result)) {
      $settings[] = $row;
    }
	} else {
	  $settings = array('error' => 'dead');
	}

	return $settings;
}

function query_load_info($option_name) {

  $query = "SELECT option_value
            FROM Impostazioni
            WHERE option_name='".$option_name."'";

	$result = mysql_query($query) or die(mysql_error());
	$value = mysql_result($result,0);

	return $value;
}

function query_check_cf_email($cf, $email) {
  $query = "SELECT COUNT(*)
            FROM Users
            WHERE email='".$email."'";
	$result = mysql_query($query) or die(mysql_error());
  $email = mysql_result($result,0);

  $query_ = "SELECT COUNT(*)
            FROM Users
            WHERE codfiscale='".$cf."'";
	$result_ = mysql_query($query_) or die(mysql_error());
  $cf = mysql_result($result_,0);

	return array('email' => intval($email), 'cf' => intval($cf));
}

function query_update_settings($settings_name, $settings_value) {

  $query = "UPDATE Impostazioni
            SET option_value='".$settings_value."', option_last_modified=NOW()
            WHERE option_name='".$settings_name."'";

	$result = mysql_query($query) or die(mysql_error());

	if($result) {
	  return true;
	} else {
	  return false;
	}
}

function query_azienda($id_azienda){

  $query = "SELECT *, Aziende.email as email_azienda
            FROM Aziende JOIN Users ON Aziende.id=Users.id_azienda AND Users.principale=1
            WHERE Aziende.id=".$id_azienda;

	$result = mysql_query($query) or die(mysql_error());

	if($result){
    $row = mysql_fetch_assoc($result);
	} else {
	  $row = array('error' => 'dead');
	}
	return $row;
}

function query_elenco_aziende(){

  $query = "SELECT id, nome_azienda, ragione_sociale
	          FROM Aziende
	          WHERE partita_iva IS NOT NULL";

	$result = mysql_query($query) or die(mysql_error());

	$a = array();

	if($result){

    while($row = mysql_fetch_assoc($result)) {
      $a[] = $row;
    }
	} else {
	  $a = array('error' => 'dead');
	}

	return $a;

}

function query_delete_admin($id){

  $query = "SELECT COUNT(*) FROM Users WHERE principale=99";
  $result = mysql_query($query) or die(mysql_error());
  $count = mysql_result($result,0);

  if($count == 1) {
    return 'ultimo_admin';
  } else {
    $query_delete = "DELETE FROM Users WHERE id=".$id;
    $result_delete = mysql_query($query_delete) or die(mysql_error());
    return $result_delete;
  }
}

function query_cerca_admin(){

	$query = "SELECT id, nome, cognome, data_reg FROM Users WHERE principale=99";

	$result = mysql_query($query) or die(mysql_error());

	$a = array();

	if($result){

    while($row = mysql_fetch_assoc($result)) {
      $a[] = $row;
    }
	} else {
	  $a = array('error' => 'dead');
	}

	return $a;
}

function query_cerca_utenti(){

	$query = "SELECT nome, cognome, data_reg,
	                  (SELECT COUNT(*) FROM Richieste WHERE Richieste.id_utente=Users.id) as totale_richieste
	          FROM Users
	          WHERE partita_iva IS NULL AND
	                principale <> 99";

	$result = mysql_query($query) or die(mysql_error());

	$a = array();

	if($result){

    while($row = mysql_fetch_assoc($result)) {
      $a[] = $row;
    }
	} else {
	  $a = array('error' => 'dead');
	}

	return $a;
}

function query_cerca_fornitori(){

	$query = "SELECT nome, cognome, data_reg, nome_azienda, ragione_sociale,
	                  (SELECT COUNT(*) FROM Richieste WHERE Richieste.id_fornitore=Users.id) as totale_richieste,
	                  (SELECT COUNT(*) FROM Richieste WHERE Richieste.id_fornitore=Users.id AND esito='accettata') as richieste_accettate
	          FROM Users LEFT JOIN Aziende ON Users.id_azienda=Aziende.id
	          WHERE Users.partita_iva IS NOT NULL";

	$result = mysql_query($query) or die(mysql_error());

	$a = array();

	if($result){

    while($row = mysql_fetch_assoc($result)) {
      $a[] = $row;
    }
	} else {
	  $a = array('error' => 'dead');
	}

	return $a;
}

function query_ultima_fattura($id_azienda) {

  $query = "SELECT mese, anno
            FROM Fatture
            WHERE id_azienda=".$id_azienda." AND
                  data_fatturazione= (SELECT MAX(data_fatturazione)
                                      FROM Fatture
                                      WHERE id_azienda=".$id_azienda.")";

  $result = mysql_query($query) or die(mysql_error());

	if($result){
    $row = mysql_fetch_assoc($result);
	} else {
	  $row = array('error' => 'dead');
	}

	return $row;

}

function query_crea_fattura($id_azienda, $mese, $anno) {

  $query = "SELECT U.nome, U.cognome, R.tipo_richiesta, R.data_ora
            FROM Richieste as R JOIN Users as U ON R.id_fornitore=U.id
            WHERE R.esito = 'accettata' AND
                  MONTH(R.data_ora) = ".$mese." AND
                  YEAR(R.data_ora) = ".$anno." AND
                  U.id_azienda=".$id_azienda;

  $result = mysql_query($query) or die(mysql_error());

  $a = array();
	if($result){
    while($row = mysql_fetch_assoc($result)) {
      $a[] = $row;
    }
	} else {
	  $a = array('error' => 'dead');
	}
	return $a;

}

function query_statistiche() {

  $query_u = "SELECT COUNT(*) FROM Users WHERE partita_iva IS NULL";
  $result_u = mysql_query($query_u) or die(mysql_error());

  $query_f = "SELECT COUNT(*) FROM Users WHERE partita_iva IS NOT NULL";
  $result_f = mysql_query($query_f) or die(mysql_error());

  $query_r = "SELECT COUNT(*) FROM Richieste";
  $result_r = mysql_query($query_r) or die(mysql_error());

  $query_ra = "SELECT COUNT(*) FROM Richieste WHERE esito='accettata'";
  $result_ra = mysql_query($query_ra) or die(mysql_error());

  $query_rr = "SELECT COUNT(*) FROM Richieste WHERE esito='rifiutata'";
  $result_rr = mysql_query($query_rr) or die(mysql_error());

  $query_rs = "SELECT COUNT(*) FROM Richieste WHERE esito='scaduta'";
  $result_rs = mysql_query($query_rs) or die(mysql_error());

  $u = mysql_result($result_u,0);
  $f = mysql_result($result_f,0);
  $r = mysql_result($result_r,0);
  $ra = mysql_result($result_ra,0);
  $rr = mysql_result($result_rr,0);
  $rs = mysql_result($result_rs,0);

  $query_u_for_month = "SELECT
                   (SELECT COUNT(*)
                    FROM Users
                    WHERE data_reg<=NOW()) as mese_6,
                  (SELECT COUNT(*)
                    FROM Users
                    WHERE data_reg<=LAST_DAY(DATE_ADD(NOW(), INTERVAL -1 MONTH))) as mese_5,
                  (SELECT COUNT(*)
                    FROM Users
                    WHERE data_reg<=LAST_DAY(DATE_ADD(NOW(), INTERVAL -2 MONTH))) as mese_4,
                  (SELECT COUNT(*)
                    FROM Users
                    WHERE data_reg<=LAST_DAY(DATE_ADD(NOW(), INTERVAL -3 MONTH))) as mese_3,
                  (SELECT COUNT(*)
                    FROM Users
                    WHERE data_reg<=LAST_DAY(DATE_ADD(NOW(), INTERVAL -4 MONTH))) as mese_2,
                  (SELECT COUNT(*)
                    FROM Users
                    WHERE data_reg<=LAST_DAY(DATE_ADD(NOW(), INTERVAL -5 MONTH))) as mese_1,
                  (SELECT COUNT(*)
                    FROM Users
                    WHERE data_reg<=LAST_DAY(DATE_ADD(NOW(), INTERVAL -6 MONTH))) as mese_0
                  ";
  $result_u_for_month = mysql_query($query_u_for_month) or die(mysql_error());

  $query_f_for_month = "SELECT
                   (SELECT COUNT(*)
                    FROM Users
                    WHERE partita_iva IS NOT NULL AND
                    data_reg<=NOW()) as mese_6,
                  (SELECT COUNT(*)
                    FROM Users
                    WHERE partita_iva IS NOT NULL AND
                    data_reg<=LAST_DAY(DATE_ADD(NOW(), INTERVAL -1 MONTH))) as mese_5,
                  (SELECT COUNT(*)
                    FROM Users
                    WHERE partita_iva IS NOT NULL AND
                    data_reg<=LAST_DAY(DATE_ADD(NOW(), INTERVAL -2 MONTH))) as mese_4,
                  (SELECT COUNT(*)
                    FROM Users
                    WHERE partita_iva IS NOT NULL AND
                    data_reg<=LAST_DAY(DATE_ADD(NOW(), INTERVAL -3 MONTH))) as mese_3,
                  (SELECT COUNT(*)
                    FROM Users
                    WHERE partita_iva IS NOT NULL AND
                    data_reg<=LAST_DAY(DATE_ADD(NOW(), INTERVAL -4 MONTH))) as mese_2,
                  (SELECT COUNT(*)
                    FROM Users
                    WHERE partita_iva IS NOT NULL AND
                    data_reg<=LAST_DAY(DATE_ADD(NOW(), INTERVAL -5 MONTH))) as mese_1,
                  (SELECT COUNT(*)
                    FROM Users
                    WHERE partita_iva IS NOT NULL AND
                    data_reg<=LAST_DAY(DATE_ADD(NOW(), INTERVAL -6 MONTH))) as mese_0
                  ";
  $result_f_for_month = mysql_query($query_f_for_month) or die(mysql_error());

  $query_r_for_month = "SELECT
                   (SELECT COUNT(*)
                    FROM Richieste
                    WHERE data_ora<=NOW()) as mese_6,
                  (SELECT COUNT(*)
                    FROM Richieste
                    WHERE data_ora<=LAST_DAY(DATE_ADD(NOW(), INTERVAL -1 MONTH))) as mese_5,
                  (SELECT COUNT(*)
                    FROM Richieste
                    WHERE data_ora<=LAST_DAY(DATE_ADD(NOW(), INTERVAL -2 MONTH))) as mese_4,
                  (SELECT COUNT(*)
                    FROM Richieste
                    WHERE data_ora<=LAST_DAY(DATE_ADD(NOW(), INTERVAL -3 MONTH))) as mese_3,
                  (SELECT COUNT(*)
                    FROM Richieste
                    WHERE data_ora<=LAST_DAY(DATE_ADD(NOW(), INTERVAL -4 MONTH))) as mese_2,
                  (SELECT COUNT(*)
                    FROM Richieste
                    WHERE data_ora<=LAST_DAY(DATE_ADD(NOW(), INTERVAL -5 MONTH))) as mese_1,
                  (SELECT COUNT(*)
                    FROM Richieste
                    WHERE data_ora<=LAST_DAY(DATE_ADD(NOW(), INTERVAL -6 MONTH))) as mese_0
                  ";
  $result_r_for_month = mysql_query($query_r_for_month) or die(mysql_error());

    $a = array();

    $row_u = mysql_fetch_assoc($result_u_for_month);
      $a['utenti'] = $row_u;
    $row_f = mysql_fetch_assoc($result_f_for_month);
      $a['fornitori'] = $row_f;
    $row_r = mysql_fetch_assoc($result_r_for_month);
      $a['richieste'] = $row_r;

  $result = array('utenti' => $u, 'fornitori' => $f, 'richieste' => $r, 'richieste_accettate' => $ra, 'richieste_rifiutate' => $rr, 'richieste_scadute' => $rs, 'line_chart' => $a);
  return $result;

}


  $aResult = array();

  if( !isset($_POST['functionname']) ) { $aResult['error'] = 'No function name!'; }

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

          case 'delete_admin':

            $aResult['result'] = query_delete_admin($_POST['arguments']);
            break;

          case 'get_statistiche':

            $result = query_statistiche();
            $aResult['result'] = $result;
            break;

          case 'cerca_utenti':

            $admin = query_cerca_admin();
            $utenti = query_cerca_utenti();
            $fornitori = query_cerca_fornitori();

            $result = array('admin' => $admin, 'utenti' => $utenti, 'fornitori' => $fornitori);

            if($result) {
              $aResult['result'] = $result;
            } else {
              $aResult['result'] = false;
            }
            break;

          case 'elenco_aziende':

            $result = query_elenco_aziende();

            if($result) {
              $aResult['result'] = $result;
            } else {
              $aResult['result'] = false;
            }
            break;

          case 'ultima_fattura':

            $result = query_ultima_fattura($_POST['arguments']);

            if($result) {
              $aResult['result'] = $result;
            } else {
              $aResult['result'] = false;
            }

            break;

          case 'crea_fattura':

            // 0: id_azienda, 1: mese, 2: anno.
            $result = query_crea_fattura($_POST['arguments'][0],$_POST['arguments'][1],$_POST['arguments'][2]);

            if($result) {
              $aResult['result'] = $result;
              $aResult['azienda'] = query_azienda($_POST['arguments'][0]);
              // passo come argomento option_name presente in DB
              $aResult['costo'] = query_load_info('costo_richiesta');
              $aResult['iban'] = query_load_info('iban');
              $aResult['paypal'] = query_load_info('paypal');
            } else {
              $aResult['result'] = false;
            }
            break;

          case 'load_info':

            $aResult['result'] = query_load_info($_POST['arguments']);
            break;

          case 'check_cf_email':

            $aResult = query_check_cf_email($_POST['arguments'][0],$_POST['arguments'][1]);
            break;

          case 'invia_fattura':

            // 0: email, 1: subject, 2: body.
            $result = invia_fattura($_POST['arguments'][0],$_POST['arguments'][1],$_POST['arguments'][2]);
            if($result) {
              $aResult['result'] = true;
            } else {
              $aResult['result'] = false;
            }
            break;

          case 'load_settings':

            $aResult['result'] = query_load_settings();
            break;

          case 'update_settings':

            $aResult['result'] = query_update_settings($_POST['arguments'][0],$_POST['arguments'][1]);
            break;

          default:
             $aResult['error'] = 'Not found function '.$_POST['functionname'].'!';
             break;
      }

  }

  mysql_close();
  echo json_encode($aResult);

?>