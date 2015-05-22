<?php
include '../lib/ChromePhp.php';
require_once('../lib/db_data.class.php');
require('invio_email.php');

// esecuzione della qeury per cercare i feedback
function query_load_table($id_utente){

	$query = "SELECT F.id, F.id_utente, F.id_fornitore, F.id_azienda, F.valutazione_utente, F.valutazione_fornitore, F.data, F.esito,
	                 U.nome as nome_utente, U.cognome as cognome_utente, UU.nome as nome_fornitore, UU.cognome as cognome_fornitore,
	                 A.nome_azienda, A.ragione_sociale
	          FROM Feedback as F LEFT JOIN Aziende as A ON F.id_azienda=A.id LEFT JOIN Users as U ON F.id_utente=U.id LEFT JOIN Users as UU ON F.id_fornitore=UU.id
	          WHERE id_utente=".$id_utente." OR id_fornitore=".$id_utente."
	          "; //provare ORDER BY F.valutazione_fornitore IS NULL DESC, F.valutazione_fornitore DESC
	$result = mysql_query($query) or die(mysql_error());

	$a = array();

	if($result){

    while($row = mysql_fetch_assoc($result)) {
      $a[] = $row;
    }
    $feedback = array('feedback' => $a);
	} else {
	  $a = array('error' => 'dead');
	}

	return $feedback;
}

function query_num_feedback($id_utente){

	$query = "SELECT COUNT(*)
	          FROM Feedback
	          WHERE (id_utente=".$id_utente." AND valutazione_fornitore IS NULL) OR (id_fornitore=".$id_utente." AND valutazione_utente IS NULL AND esito='accettata')";
	$result = mysql_query($query) or die(mysql_error());

	$num_feedback = mysql_result($result,0);

	return $num_feedback;
}

function query_my_val($id_utente) {

  $query = "SELECT nome, cognome, rating, num_valutazioni
            FROM Users
            WHERE id=".$id_utente;
	$result = mysql_query($query) or die(mysql_error());

	if($result){
    $row = mysql_fetch_assoc($result);
	} else {
	  $row = array('error' => 'dead');
	}
	return $row;
}

// funzione che prende in input l'id della richiesta conlcusa e il voto da inserire/aggiornare
function query_update_rating($id, $rating, $val_da_modificare) {

	if($val_da_modificare == "val_utente") {
	  $campo_valutazione = "valutazione_utente";
	} else {
	  $campo_valutazione = "valutazione_fornitore";
	}
	$query = "UPDATE Feedback SET ".$campo_valutazione."=".$rating." WHERE id=".$id;

	mysql_query($query) or die(mysql_error());
	//restituisce il numero di righe aggiornate, -1 indica un errore
	$affected = mysql_affected_rows();

	if($rating != 0 && $affected > 0) {
		/* Ora aggiorno la media del fornitore */
		if($val_da_modificare == "val_utente") {
		  $campo_utente = "id_utente";
		} else {
		  $campo_utente = "id_azienda";
		}
		$query_getFornitore = "SELECT ".$campo_utente." FROM Feedback WHERE id=".$id;

		$res = mysql_query($query_getFornitore) or die(mysql_error());
		$id_utente = mysql_result($res,0);

		if($val_da_modificare == "val_utente") {
		  $tab_r = "Users";
		  $campo_val = "rating";
		} else {
		  $tab_r = "Aziende";
		  $campo_val = "valutazione";
		}
		$query_r = "UPDATE ".$tab_r."
		            SET ".$campo_val."=(SELECT AVG(".$campo_valutazione.") FROM Feedback WHERE ".$campo_utente."=".$id_utente." AND ".$campo_valutazione.">0),
		                num_valutazioni=num_valutazioni+1
		            WHERE id=".$id_utente;

		$new_result = mysql_query($query_r) or die(mysql_error());
		$new_affected = mysql_affected_rows();

		// controllo le righe aggiornate e il risultato
		if($new_affected > 0){
			  return true;
			}
		return false;
	} else {
	  if($affected > 0) {
	    return true;
	  }
	  return false;
	}
}

// funzione usata per prelevare tutte le info dell' utente
function get_myAccount($id_utente){

	$query = "SELECT *, Users.indirizzo, Users.email FROM Users LEFT JOIN Aziende ON Users.id_azienda=Aziende.id WHERE Users.id=".$id_utente;
	$result = mysql_query($query) or die(mysql_error());
  $myAccount = array('myAccount' => mysql_fetch_assoc($result));

	return $myAccount;
}

// funzione per salvare le modifiche all' account
function modifica_myAccount($id, $nome, $cognome, $codfiscale, $indirizzo, $telefono, $email, $pass, $rag_sociale, $piva, $attivita, $descrizione, $pagamento, $limite_spesa, $nome_azienda, $dati_contatto, $minuti_attesa, $fornitore, $principale){

  if($fornitore == 1) {
    $minuti = ", minuti_attesa=".$minuti_attesa."";
  } else {
    $minuti = "";
  }

	$query = "
	      UPDATE Users
	      SET nome='".$nome."', cognome='".$cognome."', codfiscale='".$codfiscale."', indirizzo='".mysql_real_escape_string($indirizzo)."', telefono='".$telefono."', email='".$email."',
	          password='".$pass."', dati_contatto='".$dati_contatto."'".$minuti."
	      WHERE id=".$id;
	$r = mysql_query($query) or die(mysql_error());
	$affected = mysql_affected_rows(); //restituisce il numero di righe aggiornate, -1 indica un errore

	if($principale == 1) {

    $query_ = "UPDATE Aziende
	          SET nome_azienda='".$nome_azienda."', ragione_sociale='".$rag_sociale."', tipo_attivita='".$attivita."', descrizione='".$descrizione."', modalita_pagamento='".$pagamento."',
	            limite_spesa='".$limite_spesa."'
	          WHERE partita_iva='".$piva."'";
    $r_ = mysql_query($query_) or die(mysql_error());
    $affected = mysql_affected_rows();
	}
	if($affected >= 0){
	  return true;
	} else {
	  return false;
	}
}

// funzione che permette di eliminare un socio/dipendente
// N.B. elimina solo i campi relativi al fornitore, l' utente inteso come possibile cliente continua ad esistere
function delete_employee($id_utente){

	$query = "
	      UPDATE Users
	      SET partita_iva=NULL, principale=NULL, id_azienda=NULL, tipo_attivita=NULL, descrizione=NULL, modalita_pagamento=NULL, limite_spesa=NULL, contatti_mensili=NULL
	      WHERE id='".$id_utente."'";

	$result = mysql_query($query) or die(mysql_error());
	$affected = mysql_affected_rows();
	if($affected > 0){

	  $query_ = "DELETE FROM Disponibili WHERE id='".$id_utente."'";

	  $result_ = mysql_query($query_) or die(mysql_error());
	  $affected_ = mysql_affected_rows();

	    if($affected_ > 0) {
			  return true;
	    }
		}
	return false;
}

function delete_account($id_utente){

	$query_select = "SELECT partita_iva, principale, id_azienda FROM Users WHERE id='".$id_utente."'";

  $result_select = mysql_query($query_select) or die(mysql_error());
  $row = mysql_fetch_assoc($result_select);
  $partita_iva = $row['partita_iva'];
  $principale = $row['principale'];
  $id_azienda = $row['id_azienda'];

  if($partita_iva) {
    // elimino il fornitore dalla tabella Disponibili
		$query = "DELETE FROM Disponibili WHERE id_fornitore='".$id_utente."'";
		$result = mysql_query($query) or die(mysql_error());
		$affected = mysql_affected_rows();
		if($affected == 0) {
		}
	}

  if($principale) {
    // elimino l'azienda se si cancella l' account principale
    $query_ = "DELETE FROM Aziende WHERE id='".$id_azienda."'";
    $result_= mysql_query($query_) or die(mysql_error());
    $affected_ = mysql_affected_rows();
    if($affected == 0) {
    }
  }

  $query_delete_user = "DELETE FROM Users WHERE id='".$id_utente."'";
  $result_delete_user = mysql_query($query_delete_user) or die(mysql_error());

  if($result_delete_user) {
    return true;
  }
	return false;
}

function salva_nuova_azienda($nome, $ragione_sociale, $citta, $indirizzo, $telefono, $email, $partita_iva, $attivita, $descrizione, $pagamento, $limite_spesa){

	$query = "INSERT INTO Aziende(nome_azienda, ragione_sociale, citta, indirizzo, email, partita_iva, tipo_attivita, descrizione, modalita_pagamento, limite_spesa)
	          VALUES ('".$nome."','".$ragione_sociale."','".$citta."','".$indirizzo."','".$email."', '".$partita_iva."','".$attivita."','".$descrizione."','".$pagamento."',".$limite_spesa.")";

	$result = mysql_query($query) or die(mysql_error());
	if($result){
  	  $query = "
  	      UPDATE Users
  	      SET id_azienda= (SELECT id FROM Aziende WHERE partita_iva='".$partita_iva."')
  	      WHERE partita_iva='".$partita_iva."'";

  	  $result = mysql_query($query) or die(mysql_error());
  	    if($result) {
		      return true;
  	    } else {
		      return false;
  	    }
		}
	return false;
}

function invia_nuova_password($email){

  $password = randomPassword();

	$query = "UPDATE Users SET password='".$password."' WHERE email='".$email."'";

	$result = mysql_query($query) or die(mysql_error());
	if($result){
	  // invio email
	  $subject = 'Nuova password';
	  $message = '<p>E\' stata richiesta una nuova password per il tuo account ('.$email.').<br>Nuova password: '.$password.'</p>';
	  sendEmail($email, $subject, $message);

    return true;
	} else {
	  return false;
	}
}

function randomPassword() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
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

        case 'load_table':

          $aResult = query_load_table($_POST['arguments']);
          break;

        case 'num_feedback':

          $aResult['result'] = query_num_feedback($_POST['arguments']);
          break;

        case 'mia_valutazione':

          $aResult['result'] = query_my_val($_POST['arguments']);
          break;

        case 'update_rating':

          if( !is_array($_POST['arguments']) || (count($_POST['arguments']) < 3) ) {
               $aResult['error'] = 'Error in arguments!';
          } else {
            $result = query_update_rating($_POST['arguments'][0],$_POST['arguments'][1],$_POST['arguments'][2]);
            if($result) {
              $aResult['result'] = true;
            } else {
              $aResult['result'] = false;
            }
          }
          break;

        case 'get_myAccount':
          // resituisce tutte le informazione dell' account
          $aResult = get_myAccount($_POST['arguments']);
          break;

        case 'modifica_myAccount':
          // 0: id_utente; 1: nome; 2: cognome; 3: codfiscale; 4: indirizzo; 5: telefono; 6: email; 7: pass; 8: ragsociale; 9: partita_iva; 10: attivita';
          // 11: descrizione; 12: mod_pagamento; 13: limite_spesa; 14: nome_azienda; 15: dati_contatto; 16: tempo_richiesta; 17: fornitore; 18 principale;
          $aResult['result'] = modifica_myAccount($_POST['arguments'][0],$_POST['arguments'][1],$_POST['arguments'][2],$_POST['arguments'][3],$_POST['arguments'][4],$_POST['arguments'][5],
                                $_POST['arguments'][6],$_POST['arguments'][7],$_POST['arguments'][8],$_POST['arguments'][9],$_POST['arguments'][10],$_POST['arguments'][11],
                                $_POST['arguments'][12],$_POST['arguments'][13],$_POST['arguments'][14],$_POST['arguments'][15],$_POST['arguments'][16],$_POST['arguments'][17],$_POST['arguments'][17]);
          break;

        case 'elimina_socio':
          // elimina un dipendente della stessa azienda
          $aResult['result'] = delete_employee($_POST['arguments']);
          break;

        case 'elimina_account':
          // elimina account con cui si e' loggati
          $aResult['result'] = delete_account($_POST['arguments']);
          break;

        case 'salva_nuova_azienda':
          // salva una nuova azienda
          $aResult['result'] = salva_nuova_azienda($_POST['arguments'][0],$_POST['arguments'][1],$_POST['arguments'][2],$_POST['arguments'][3],$_POST['arguments'][4],$_POST['arguments'][5],
                                $_POST['arguments'][6],$_POST['arguments'][7],$_POST['arguments'][8],$_POST['arguments'][9],$_POST['arguments'][10]);
          break;

        case 'recovery_password':
          // crea ed invia una nuova password
          $aResult['result'] = invia_nuova_password($_POST['arguments']);
          break;

        default:
           $aResult['error'] = 'Not found function '.$_POST['functionname'].'!';
           break;
    }

}

mysql_close();
echo json_encode($aResult);
?>
