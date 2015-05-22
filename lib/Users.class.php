<?php
// error_reporting(E_ALL | E_DEPRECATED | E_STRICT);
include 'ChromePhp.php';
require 'PHPMailer/PHPMailerAutoload.php';

Class Users{
	/********************************
	SETTING
	*********************************/
	// le credenziali di accesso al database
	private $host_db = 'localhost:3306';
	private $user_db = 'root';
	private $pass_db = '1LcL880y';
	private $name_db = 'webapp';
	// gli url che gestinranno le operazioni di login
	public $Urls = array(
						'login_page' 	=> 'http://130.136.143.33:8010//app/index.php',
						'register_page'	=> 'http://130.136.143.33:8010/app/registrati.php',
						'logout_page'	=> 'http://130.136.143.33:8010/app/logout.php',
						);

	/*risorse di connessione*/
	protected $conn;
	protected $selezione_db;

	/* Parte Utente */
	protected $reg_nome;
	protected $reg_cognome;
	protected $reg_codfiscale;
	protected $reg_indirizzo;
	protected $reg_telefono;
	protected $reg_email;
	protected $reg_pass;
	protected $reg_confirm_pass;
	protected $reg_dati_contatto;
	protected $reg_minuti;
	/* Parte Fornitore */
	protected $reg_partita_iva;
	protected $reg_principale;
	protected $reg_attivita;
	protected $reg_descr_attivita;
	protected $reg_pagamento;
	protected $reg_limite_spesa;
	protected $reg_email_pushbullet;

	/*variabili di login*/
	protected $login_username;
	protected $login_password;
	protected $login_cryptpass;
	protected $login_iduser;
	protected $login_nome;
	protected $login_cognome;

	protected $login_show_message;

	protected $login_fornitore;
	protected $login_principale;

	/* variabili in cui vengono salvati i risultati della ricerca */
	protected $testvar;
	protected $testjson;

	/*variabili per gestire gli errori*/
	public $messages = array(
					1 => 'Il campo username e obbligatorio.',
					2 => 'Il campo email e obbligatorio.',
					3 => 'Il campo password e obbligatorio.',
					4 => 'Le due password non coincidono.',
					5 => 'Il campo username contiene caratteri non validi. Sono consentiti solo lettere, numeri il i seguenti simboli . _ -.',
					6 => 'Inserisci una email con sitassi corretta.',
					7 => 'La password scelta č eccessivamente breve. Scegli una password di almeno 8 caratteri.',
					8 => 'Esiste giŕ un utente registrato con questo username.',
					9 => 'Esiste giŕ un utente registrato con questa email.',
					10 => 'Registrazione effettuata con successo.',
					11 => 'Email o Password non validi.',
					12 => 'Login eseguito con successo.',
					13 => 'Logout eseguito con successo.',
					14 => 'Per accedere a questa pagina occorre essere loggati.'
					);

	public $message_script;

	// il costruttore attiva la connessione a mysql
	public function __construct(){
		$this->connessione();
		}
	/******************
	CONNESSIONE A MYSQL
	******************/
	protected function connessione(){
		$this->conn = mysql_connect($this->host_db, $this->user_db, $this->pass_db) or die(mysql_error());
		$this->selezione_db = mysql_select_db($this->name_db, $this->conn) or die(mysql_error());
		return TRUE;
		}

	/*************************************
	ALCUNI METODI PER ESEGUIRE VALIDAZIONI
	*************************************/

	// verifica campo generico non vuoto (TRUE se non vuoto)
	public function empty_string($string){
		$string = trim($string);
		if($string==''){
			return TRUE;
			}
		else{
			return FALSE;
			}
		}

	// verifica sintassi username
	public function is_username($username){
		$regex = '/^[a-z0-9\.\-_]{3,30}$/i';
		return preg_match($regex, $username);
		}

	// verifica sintassi email (TRUE se ok)
	public function is_email($email){
		$regex = '/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)+$/';
		return preg_match($regex, $email);
		}

	// verifica sintassi password (per semplicitŕ solo lunghezza) (TRUE se ok)
	public function is_secure_password($password){
		if(strlen($password)>=8){
			return TRUE;
			}
		else{
			return FALSE;
			}
		}

	/*****************************************************
	METODI PER VERIFICARE ESISTENZA DI USERNAME E PASSWORD
	******************************************************/

	// verifica esistenza username (TRUE se esiste)
	public function isset_username($username){
		$query = "SELECT COUNT(username) AS count
					FROM Users
					WHERE username='".mysql_real_escape_string($username)."'
					LIMIT 1";
		$result = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_array($result);
		if($row['count']==1){
			return TRUE;
			}
		else{
			return FALSE;
			}
		}

	// verifica esistenza email (TRUE  se esiste)
	public function isset_email($email){
		$query = "SELECT COUNT(email) AS count
					FROM Users
					WHERE email='".mysql_real_escape_string($email)."'
					LIMIT 1";
		$result = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_array($result);
		if($row['count']==1){
			return TRUE;
			}
		else{
			return FALSE;
			}
		}

	/******************************
	I FORM DI LOGIN E REGISTRAZIONE
	******************************/
	public function get_login_form(){

	  $message = $this->get_message();

	  if($message) {
	    $msg =  "<p class=\"small color--red\">".$message."</p>";
	  } else {
	    $msg = "";
	  }
      $html = '
        <section class="styleguide__page-header">
        <div class="page-header">
            <div class="container">
                <h1 class="text-divider">Benvenuto!</h1>
                <p class="page-header__excerpt g-wide--push-1 g-wide--pull-1">Inserisci email e password per accedere.</p>
                '.$msg.'
  			<form action="' .$this->Urls['login_page']. '" method="post" id="form_login">
            <fieldset>
            <div class="g--half g--centered">
              <p><input type="email" class="material" name="username" id="login_user" placeholder="Email" required></input></p>
              <p><input type="password" class="material" name="pass" id="login_pass" placeholder="Password" required></input></p>
            </div>
            <button class="button--primary material" type="submit" name="login" value="Login" id="login_submit">Login</button>
            <a class="button--secondary material" href="registrati.php" id="register_submit">Registrati</a>
            </fieldset>
  		  </form>
  		      <a href="recovery_password.php" id="recovery_password">Hai dimenticato la password?</a>
            </div>
        </div>
        </section>
      ';
		return $html;
		}

	public function get_register_form(){
	  $query = "SELECT option_value
	            FROM Impostazioni
	            WHERE option_name='tempo_default'";
	  $result = mysql_query($query) or die(mysql_error());
	  $minuti = mysql_result($result,0);

		$html = '
		  <section class="styleguide__page-header">
          <div class="page-header">
              <div class="container">
                  <h3 class="xlarge text-divider">Registrati!</h3>
                  <p class="page-header__excerpt g-wide--push-1 g-wide--pull-1" id="label_inserisci_dati">Inserisci i tuoi dati per accedere all\' applicazione.</p>
    			<form method="post" id="form_login">
              <fieldset>
              <div class="color--text g--half" id="div_utente">
              <p><input type="text" name="nome" id="reg_nome" placeholder="Nome" class="material" required/></p>
              <p><input type="text" name="cognome" id="reg_cognome" placeholder="Cognome" class="material" required/></p>
              <p><input type="text" name="codfiscale" id="reg_codfiscale" placeholder="Codice Fiscale" class="material" required/></p>
                <span id="cf_non_valido">* Codice Fiscale non valido.</span>
                <span id="cf_gia_presente">* Risulta già un utente registrato con questo codice fiscale.</span>
              <p><input type="text" name="indirizzo" id="reg_indirizzo" placeholder="Città e Via" class="material" required/><br>
                <input type="checkbox" name="usa_indirizzo" id="usa_indirizzo" class="material" checked /> Usa come dato di contatto</p>
              <p><input type="tel" name="telefono" id="reg_telefono" placeholder="Telefono" class="material" required/><br>
                <input type="checkbox" name="usa_telefono" id="usa_telefono" class="material" checked /> Usa come dato di contatto</p>
              <p><input type="email" name="email" id="reg_email" placeholder="Email" class="material" required/><br>
                <input type="checkbox" name="usa_email" id="usa_email" class="material" checked /> Usa come dato di contatto</p>
                <span id="email_gia_presente">* Risulta già un utente registrato con questa email.</span>
              <p><input type="password" name="pass1" id="reg_pass1" placeholder="Password" class="material" required/></p>
              <p><input type="password" name="pass2" id="reg_pass2" placeholder="Conferma password" class="material" required/></p>
              </div>

              <div class="g--half g--last text-divider" id="div_fornitore">
              <p class="color--highlight"><input type="checkbox" name="fornitore" id="reg_fornitore" class="material" /> Sono un fornitore </p>
              <div id="campi_fornitore" style="display: none;">
                <p><input type="text" name="piva" id="reg_piva" placeholder="Partita Iva" class="material" disabled /></p>
                  <span id="piva_non_valida">* Partita IVA non valida.</span><br>

                <label>Questo campo indica il tempo (in minuti) entro il quale il fornitore si impegna a gestire le richieste.
                  Potrai modificare questo valore in qualsiasi momento dopo la registrazione.</label><br>
                <label><strong>Tempo proposto: </strong><input type="number" name="minuti" id="reg_minuti" placeholder="Tempo di default" value="'.$minuti.'" class="material" required/></label>
                  <br><span id="tempo_max_alert">* Inserisci un tempo minore a <span id="tempo"></span> minuti.</span>
              </div>
              </div>

              <div class="g--half g--centered" id="div_pushbullet" style="display: none;">
                  <label>Inserisci l\' email che hai utilizzato durante l\' installazione di pushbullet. <br>
                          Se non l\' hai già fatto, registrati <a href="https://www.pushbullet.com/" target="_blank">qui</a> e installalo sul tuo dispositivo.
                          Questo ti permetterà di ricevere notifiche push riguardo all\' esito delle tue richieste.</label>
                  <br><input type="email" name="email_pushbullet" id="reg_email_pushbullet" placeholder="Email Pushbullet" style="margin-top: 10px; margin-bottom: 20px;"/><br>
                <input type="checkbox" name="not_pushbullet" id="not_pushbullet" /> Ricevi gli aggiornamenti delle richieste per email invece di utilizzare Pushbullet
                <br>

                <button class="button--primary material" type="submit" name="register" value="Registra" id="reg_submit">Registra</button>
                <button class="button--secondary material" onclick="indietro()">Indietro</button>
              </div> <!-- fine div_pushbullet -->

              </fieldset>
    		  </form>
    		        <div class="g--half g--centered" id="buttons_avanti_indietro">
    		          <button class="button--primary material" onclick="avanti()">Avanti</button>
                  <button class="button--secondary material" onclick="redirect_to_login()">Indietro</button>
                </div>

              </div>
          </div>
          </section>
        ';
		return $html;
		}

		public function get_myAccount_form(){

		  $fornitore = $this->get_fornitore();

		  if($fornitore) {
		    $html = '<div class="g-medium--full g-wide--full"><div class="g--half">';
		    $minuti = '<p>Tempo di risposta della richiesta</p>
          <input type="number" name="minuti" id="my_minuti" placeholder="Minuti" class="material" />';
		  } else {
		    $html = '<div class="g-medium--full g-wide--full">';
		    $minuti = '';
		  }

		  $html .= '
		      <p>Nome</p>
          <input type="text" name="nome" id="my_nome" placeholder="Nome" class="material" />
          <p>Cognome</p>
          <input type="text" name="cognome" id="my_cognome" placeholder="Cognome" class="material" />
          <p>Codice Fiscale</p>
          <input type="text" name="codfiscale" id="my_codfiscale" placeholder="Codice Fiscale" class="material" />
          <p>Indirizzo</p>
          <input type="text" name="indirizzo" id="my_indirizzo" placeholder="Indirizzo" class="material" /><br>
          <input type="checkbox" name="usa_indirizzo" id="usa_indirizzo" class="material" /> Usa come dato di contatto
          <p>Telefono</p>
          <input type="tel" name="telefono" id="my_telefono" placeholder="Telefono" class="material" /><br>
          <input type="checkbox" name="usa_telefono" id="usa_telefono" class="material" /> Usa come dato di contatto
          <p>Indirizzo Email</p>
          <input type="email" name="email" id="my_email" placeholder="Email" class="material" /><br>
          <input type="checkbox" name="usa_email" id="usa_email" class="material" /> Usa come dato di contatto
          <p>Password</p>
          <input type="password" name="pass1" id="my_pass1" placeholder="Password" class="material" />
          <p>Conferma Password</p>
          <input type="password" name="pass2" id="my_pass2" placeholder="Conferma password" class="material" />
          '.$minuti.'
          </div>';
		  if($fornitore) {
		    $html .= '
		      <div class="g--half g--last">
          <p>Partita IVA</p>
          <input type="text" name="piva" id="my_piva" placeholder="Partita Iva" class="material" />
          <p>Nome Azienda</p>
          <input type="text" name="nomeazienda" id="my_nomeazienda" placeholder="Nome Azienda" class="material" />
          <p>Ragione Sociale</p>
          <input type="text" name="ragsociale" id="my_ragsociale" placeholder="Ragione Sociale" class="material" />
          <p>Tipo di attività: <br><select name="attivita" id="my_attivita" class="material" >
            <option value="idraulico">Idraulico</option>
            <option value="falegname">Falegname</option>
            <option value="giardiniere">Giardiniere</option>
            <option value="elettricista">Elettricista</option>
          </select></p>
          <p>Descrizione Attività</p>
          <textarea type="text" name="descrizione" id="my_descrizione" rows="2" placeholder="Descrizione Attività" class="material"></textarea>
          <p>Modalità di pagamento: <select name="modalita" id="my_pagamento" class="material" >
            <option value="paypal">Paypal</option>
            <option value="bonifico">Bonifico Bancario</option>
          </select></p>
          <p>Limite spesa mensile</p>
          <input type="number" name="limite_spesa" id="my_limite_spesa" placeholder="Limite Spesa Mensile" class="material" /><br>
          <p>N.B. Una volta raggiunto il limite mensile verrai escluso in automatico da tutte le ricerche. Puoi tornare qui in ogni momento
              per aumentare o diminuire il limite massimo.</p>
          </div></div>';
		  }

      return $html;
		}

	/*****************************
	LINK LOGOUT
	*****************************/
	public function get_link_logout(){
		if($this->is_logged()){
			return '<a href="'.$this->Urls['logout_page'].'" class="logout">Logout</a>';
			}
		return '';
		}

	/*******************************
	METODO PER CRIPTARE LE PASSWORD
	*******************************/
	public function crypt_pass($pass){
		return sha1($pass);
		}

	/*****************************
	ESECUZIONE DELLA REGISTRAZIONE
	******************************/
	public function esegui_registrazione(){
		// se il form e i suoi input sono stati inviati
		// DA MODIFICARE TODO: DA MODIFICARE TODO: DA MODIFICARE TODO: DA MODIFICARE TODO: DA MODIFICARE TODO: DA MODIFICARE TODO: DA MODIFICARE TODO: DA MODIFICARE //
		if((isset($_POST['register'])) AND (isset($_POST['fornitore'])) AND ($_POST['fornitore']==true)) {

		    if(isset($_POST['register']) AND
  			isset($_POST['nome']) AND
  			isset($_POST['cognome']) AND
  			isset($_POST['codfiscale']) AND
  			isset($_POST['indirizzo']) AND
  			isset($_POST['telefono']) AND
  			isset($_POST['email']) AND
  			isset($_POST['pass1']) AND
  			isset($_POST['pass2']) AND
  			isset($_POST['piva']) AND
  			isset($_POST['email_pushbullet'])){
  			  //valorizziamo alcune variabili
    			$this->reg_nome = trim($_POST['nome']);
    			$this->reg_cognome = trim($_POST['cognome']);
    			$this->reg_codfiscale = trim($_POST['codfiscale']);
    			$this->reg_indirizzo = trim($_POST['indirizzo']);
    			$this->reg_telefono = trim($_POST['telefono']);
    			$this->reg_email = trim($_POST['email']);
    			$this->reg_pass = trim($_POST['pass1']);
    			$this->reg_confirm_pass = trim($_POST['pass2']);
    			$this->reg_dati_contatto = $this->getDatiContatto();
    			$this->reg_minuti = trim($_POST['minuti']);
    			$this->reg_partita_iva = trim($_POST['piva']);
    			$this->reg_principale = $this->query_check_partita_iva($this->reg_partita_iva);
    			if($_POST['not_pushbullet']) {
    			  $this->reg_email_pushbullet = NULL;
    			} else {
    			  $this->reg_email_pushbullet = trim($_POST['email_pushbullet']);
    			}
  				// inseriemo all'interno del database i dati
  				$id = $this->query_insert_registrazione();
  				// aggiunge il fornitore alla tabella `Disponibili` e lo setta come occupato
  				$this->query_insert_occupato($id);
  				// inviamo l'email di registrazione avvenuta
  				if($id) {
  				  $this->invia_email_registrazione($this->reg_email, $this->reg_pass);
  				}
  				$nuova_azienda = $this->check_partita_iva($this->reg_partita_iva, $id);
  				if($nuova_azienda) {
  				  return header("location: index.php");
  				} else {
  				  $this->set_partita_iva($_POST['piva']);
  				  $this->invia_email_azienda($this->reg_partita_iva);
  				  return header("location: registra_nuova_azienda.php");
  				}
  			}
		  } else {

		    if(isset($_POST['register']) AND
  			isset($_POST['nome']) AND
  			isset($_POST['cognome']) AND
  			isset($_POST['codfiscale']) AND
  			isset($_POST['indirizzo']) AND
  			isset($_POST['telefono']) AND
  			isset($_POST['email']) AND
  			isset($_POST['pass1']) AND
  			isset($_POST['pass2']) AND
  			isset($_POST['email_pushbullet'])){
  			  $this->reg_nome = trim($_POST['nome']);
    			$this->reg_cognome = trim($_POST['cognome']);
    			$this->reg_codfiscale = trim($_POST['codfiscale']);
    			$this->reg_indirizzo = trim($_POST['indirizzo']);
    			$this->reg_telefono = trim($_POST['telefono']);
    			$this->reg_email = trim($_POST['email']);
    			$this->reg_pass = trim($_POST['pass1']);
    			$this->reg_confirm_pass = trim($_POST['pass2']);
    			$this->reg_dati_contatto = $this->getDatiContatto();
    			if($_POST['not_pushbullet']) {
    			  $this->reg_email_pushbullet = NULL;
    			} else {
    			  $this->reg_email_pushbullet = trim($_POST['email_pushbullet']);
    			}

    			$valid_input = TRUE;
    			// se sono validi
    			if($valid_input===TRUE){
    				// inseriemo all'interno del database i dati
    				$this->query_insert_registrazione_utente();
    				// inviamo l'email di registrazione avvenuta
    				$this->invia_email_registrazione($this->reg_email, $this->reg_pass);
    				// settiamo il messaggio di successo della registrazione
    				$this->message_script = 10;
    				return header("location: index.php");
    				}
  			}
		  }

	return FALSE;
	}

	protected function getDatiContatto(){
	  $dati_contatto = '';
	  if(isset($_POST['usa_indirizzo'])) {
	    $dati_contatto .= '1';
	  } else {
	    $dati_contatto .= '0';
	  }
	  if(isset($_POST['usa_telefono'])) {
	    $dati_contatto .= '1';
	  } else {
	    $dati_contatto .= '0';
	  }
	  if(isset($_POST['usa_email'])) {
	    $dati_contatto .= '1';
	  } else {
	    $dati_contatto .= '0';
	  }
	  return $dati_contatto;
	}

	protected function check_partita_iva($partita_iva, $id_fornitore){
	  $query = "SELECT id, COUNT(*) as count FROM Aziende WHERE partita_iva='".$partita_iva."'";
	  $result = mysql_query($query) or die(mysql_error());
	  //echo $query;
	  $row = mysql_fetch_assoc($result);
	  $count = $row['count'];
	  $id_azienda = $row['id'];
	  if($count != 0){
	    // questa query serve per collegare il fornitore all' azienda gia' esistente
	    $query_ = "UPDATE Users SET id_azienda=".$id_azienda." WHERE id=".$id_fornitore;
	    $result_ = mysql_query($query_) or die(mysql_error());
	    return true; // reindirizzato a login.php
	  } else {
	    return false; // reindirizzato a registra_nuova_azienda.php
	  }
	}

	protected function invia_email_registrazione($email, $pass) {

    $mail = new PHPMailer;

    //$mail->SMTPDebug = 3;                               // Enable verbose debug output

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'webappartigiani.info@gmail.com';   // SMTP username
    $mail->Password = '1LcL880y';                         // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to

    $mail->From = 'webappartigiani.info@gmail.com';
    $mail->FromName = 'WebAppArtigiani';
    $mail->addAddress($email);                            // Name is optional

    $mail->isHTML(true);                                  // Set email format to HTML

    $mail->Subject = 'WebAppArtigiani - Registrazione account';
    $mail->Body    = '<h2>Registrazione avvenuta con successo.</h2><p>Email: '.$email.'<br>Password: '.$pass.'</p>';

    if(!$mail->send()) {
        echo 'Message could not be sent.\n';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        //echo 'Message has been sent\n';
    }

	}

	protected function invia_email_azienda($partita_iva) {

    $query = "SELECT email FROM Users WHERE principale=1 AND partita_iva='".$partita_iva."'";

	  $result = mysql_query($query) or die(mysql_error());
	  $email = mysql_result($result, 0);

    $mail = new PHPMailer;

    //$mail->SMTPDebug = 3;                               // Enable verbose debug output

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'webappartigiani.info@gmail.com';   // SMTP username
    $mail->Password = '1LcL880y';                         // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to

    $mail->From = 'webappartigiani.info@gmail.com';
    $mail->FromName = 'WebAppArtigiani';
    $mail->addAddress($email);                            // Name is optional

    $mail->isHTML(true);                                  // Set email format to HTML

    $mail->Subject = 'WebAppArtigiani - Registrazione account';
    $mail->Body    = '<p>E\' stato registrato un account collegato alla vostra azienda. <br> Email: '.$email;

    if(!$mail->send()) {
        echo 'Message could not be sent.\n';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'Message has been sent\n';
    }
	}

	// verifica che gli input siano corretti
	protected function check_input_registrazione(){
		if($this->empty_string($this->reg_nome)){
			$this->message_script = 1;
			return FALSE;
			}
		else if($this->empty_string($this->reg_email)){
			$this->message_script = 2;
			return FALSE;
			}
		else if($this->empty_string($this->reg_pass)){
			$this->message_script = 3;
			return FALSE;
			}
		else if($this->reg_pass != $this->reg_confirm_pass){
			$this->message_script = 4;
			return FALSE;
			}
		else if(!$this->is_username($this->reg_nome)){
			$this->message_script = 5;
			return FALSE;
			}
		else if(!$this->is_email($this->reg_email)){
			$this->message_script = 6;
			return FALSE;
			}
		else if(!$this->is_secure_password($this->reg_pass)){
			$this->message_script = 7;
			return FALSE;
			}
		else if($this->isset_username($this->reg_nome)==TRUE){
			$this->message_script = 8;
			return FALSE;
			}
		else if($this->isset_email($this->reg_email)==TRUE){
			$this->message_script = 9;
			return FALSE;
			}
		return TRUE;
		}

	// esecuzione della query insert di registrazione
	protected function query_insert_registrazione(){
	  $today = date("Y/m/d", time());

		$query = "
					INSERT INTO Users
					SET
						nome='".mysql_real_escape_string($this->reg_nome)."',
						cognome='".mysql_real_escape_string($this->reg_cognome)."',
						codfiscale='".mysql_real_escape_string($this->reg_codfiscale)."',
						indirizzo='".mysql_real_escape_string($this->reg_indirizzo)."',
						telefono='".mysql_real_escape_string($this->reg_telefono)."',
						email='".mysql_real_escape_string($this->reg_email)."',
						password='".mysql_real_escape_string($this->reg_confirm_pass)."',
						dati_contatto='".mysql_real_escape_string($this->reg_dati_contatto)."',
						minuti_attesa='".$this->reg_minuti."',

						partita_iva='".mysql_real_escape_string($this->reg_partita_iva)."',
						principale='".mysql_real_escape_string($this->reg_principale)."',

						email_pushbullet='".mysql_real_escape_string($this->reg_email_pushbullet)."',

						data_reg= NOW()";
		$result = mysql_query($query) or die(mysql_error());
		return mysql_insert_id();
		}
	protected function query_insert_registrazione_utente(){
	  $today = date("Y/m/d", time());

		$query = "
					INSERT INTO Users
					SET
						nome='".mysql_real_escape_string($this->reg_nome)."',
						cognome='".mysql_real_escape_string($this->reg_cognome)."',
						codfiscale='".mysql_real_escape_string($this->reg_codfiscale)."',
						indirizzo='".mysql_real_escape_string($this->reg_indirizzo)."',
						telefono='".mysql_real_escape_string($this->reg_telefono)."',
						email='".mysql_real_escape_string($this->reg_email)."',
						password='".mysql_real_escape_string($this->reg_confirm_pass)."',
						dati_contatto='".mysql_real_escape_string($this->reg_dati_contatto)."',

						email_pushbullet='".mysql_real_escape_string($this->reg_email_pushbullet)."',

						data_reg= NOW()";
		$result = mysql_query($query) or die(mysql_error());
		return mysql_insert_id();
		}

	protected function query_check_partita_iva($partita_iva){
	  $query = "
	          SELECT COUNT(*)
	          FROM Users
	          WHERE partita_iva='".$partita_iva."'";
	  $res = mysql_query($query) or die(mysql_error());
	  $result = mysql_result($res,0);
	  if($result){
	    return 0;
	  } else {
	    return 1;
	  }
	}

	// aggiunge il fornitore alla tabella `Disponibili` e lo setta come occupato (chiamata da esegui_registrazione())
	protected function query_insert_occupato($id){
		$query = "
					INSERT INTO Disponibili
					SET
						id_fornitore=".mysql_real_escape_string($id).",
						disponibile=0,
						posizione_x=0,
						posizione_y=0";

		$result = mysql_query($query) or die(mysql_error());
		if($result) {
		  return true;
		} else {
		  return false;
		}
	}

	/*******************
	ESECUZIONE DEL LOGIN
	********************/
	public function esegui_login(){
		// se il form di login e i sui tutti input sono stati inviati
		if(isset($_POST['login']) AND isset($_POST['username']) AND isset($_POST['pass'])){
			// valorizziamo delle variabili
			$this->login_username = trim($_POST['username']);
			$this->login_password = trim($_POST['pass']);
      $this->login_cryptpass = $this->login_password;
			// validiamo i dati (non devono essere vuoti)
			$not_empty_input = $this->check_input_login();
			// se la validazione č andata a buon fine
			if($not_empty_input===TRUE){
				// eseguiamo la query e verifichiamo se individua le credenziali
				if($this->query_select_login()==TRUE){
					// settiamo lo status di utente loggato
					$this->set_logged($this->login_iduser);
					// settiamo l'username
					$this->set_username($this->login_username);
					// settiamo se fornitore o no
				  $this->set_fornitore($this->login_fornitore);
				  // settiamo se principale o no
				  $this->set_principale($this->login_principale);
				  // settiamo di mostrare il messaggio di benvenuto
				  $this->set_welcome_message(true);
					// settiamo il messaggio di successo del login
					$this->message_script = 12;
					return TRUE;
					}
				// se la query non ha trovat utenti con quelle credenziali
				else{
					//  settiamo un messaggio di insuccesso dell'operazone
					$this->message_script = 11;
					}
				}
			}
		return FALSE;
		}

	// verifica che gli input del login non siano vuoti
	protected function check_input_login(){
		if($this->empty_string($this->login_username)){
			$this->message_script = 1;
			return FALSE;
			}
		else if($this->empty_string($this->login_password)){
			$this->message_script = 3;
			return FALSE;
			}
		return TRUE;
		}

	// esecuzione della qeury per verificare il login
	protected function query_select_login(){
		$query = "
					SELECT * FROM Users
					WHERE
						email='".mysql_real_escape_string($this->login_username)."' AND
						password='".mysql_real_escape_string($this->login_cryptpass)."'";
		$result = mysql_query($query) or die(mysql_error());
		// se individua l'utente
		if(mysql_num_rows($result)==1){
			$row = mysql_fetch_assoc($result);
			$this->login_iduser = $row['id'];
			$this->set_nome($row['nome']);
			$this->set_cognome($row['cognome']);
			if($row['partita_iva']) {
			  $this->login_fornitore = TRUE;
			  if($row['principale']) {
			    $this->login_principale = 1;
			  } else {
			    $this->login_principale = 0;
			  }
			}
			if($row['principale'] == 99) {
		    $this->login_principale = 99;
		  }
			return TRUE;
			}
		return FALSE;
		}


	/***********************************
	VERIFICA DELLO STATO DI LOGIN UTENTE
	***********************************/

	// verifica login
	public function is_logged(){
		return isset($_SESSION['auth']);
		}

	// set login
	protected function set_logged($id_user){
		$_SESSION['auth'] = $id_user;
		return;
		}

	// access denied
	public function access_denied(){
		if(!$this->is_logged()){
			header("location: ".$this->Urls['login_page']."?message=14");
			exit;
			}
		return;
		}

	protected function set_username($username){
		$_SESSION['username_logged'] = $username;
		return;
		}

	public function get_username(){
		return isset($_SESSION['username_logged']) ? $_SESSION['username_logged'] : '';
		}

	protected function set_fornitore($fornitore){
		$_SESSION['fornitore_logged'] = $fornitore;
		return;
		}

	public function get_fornitore(){
	  if(isset($_SESSION['fornitore_logged'])) {
	    return 1;
	  } else {
	    return 0;
	  }
	}

	public function set_partita_iva($piva){
	  $_SESSION['partita_iva'] = $piva;
	}

	// usata per autocompletare il campo p.iva nella pagina di registrazione dell' azienda
	public function get_partita_iva(){
	  return $_SESSION['partita_iva'];
	}

	protected function set_principale($principale){
		$_SESSION['principale_logged'] = $principale;
		return;
	}

	public function get_principale(){
	  return $_SESSION['principale_logged'];
	}

	public function checkDisponibilitaFornitore(){
	  $id_fornitore = $this->who_is_logged();
	  $query = "SELECT `disponibile`
					FROM Disponibili
					WHERE `id_fornitore`=".mysql_real_escape_string($id_fornitore);
		$result = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_array($result);
		if($row['disponibile'] == 1){
			return 'checked';
		}	else{
			return;
		}
	}

	public function get_welcome_message(){
	  return $_SESSION['welcome_message'];
	}

	public function set_welcome_message($bool){
	  $_SESSION['welcome_message'] = $bool;
	  return;
	}

	public function get_nome(){
	  return $_SESSION['nome'];
	}

	public function set_nome($nome){
	  $_SESSION['nome'] = $nome;
	  return;
	}

	public function get_cognome(){
	  return $_SESSION['cognome'];
	}

	public function set_cognome($cognome){
	  $_SESSION['cognome'] = $cognome;
	  return;
	}

	// ritorna l'id dell'utente loggato
	public function who_is_logged(){
		return $_SESSION['auth'];
		}

	// logout
	public function logout(){
		session_unset();
		session_destroy();
		setcookie(session_name(), '', time()-42000, '/');
		header("location: ".$this->Urls['login_page']."?message=13");
		return;
		}

	/*****************************
	METODO PER OTTENERE I MESSAGGI
	******************************/
	public function get_message(){
		if(isset($_GET['message'])){
			$this->message_script = $_GET['message'];
			}
		$key = intval($this->message_script);
		if(array_key_exists($key, $this->messages)){
			return $this->messages[$key];
			}
		return FALSE;
		}

	/*************************************************
	METODO PER RESTITUIRE GLI ALTRI SOCI SE PRINCIPALE
	*************************************************/

    public function getEmployee(){

      if($_SESSION['principale_logged']){

        $html = '
        <p class="medium"><i style="display: inline;" class="icon icon-chevron-down"></i> Controlla o modifica gli altri componenti dell\' azienda.</p>

        <table class="table-5">
            <colgroup>
                <col span="1">
                <col span="1">
                <col span="1">
                <col span="1">
                <col span="1">
            </colgroup>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Cognome</th>
                    <th>Email</th>
                    <th>Partita IVA</th>
                    <th>Elimina</th>
                </tr>
            </thead>
            <tbody>';

        $myId = $this->who_is_logged();

    		$query = "SELECT * FROM `Users` WHERE `partita_iva`= (SELECT `partita_iva` FROM `Users` WHERE `id`=".$myId.") AND `id`!= ".$myId;
    		$result = mysql_query($query) or die(mysql_error());

        if(mysql_num_rows($result) == 0) {
    		    return false;
    		  }

    		if($result){
          while($row = mysql_fetch_array($result)) {

            $html .= '
                <tr>
                    <td data-th="Nome">'.$row['nome'].'</td>
                    <td data-th="Cognome">'.$row['cognome'].'</td>
                    <td data-th="Email">'.$row['email'].'</td>
                    <td data-th="Partita IVA">'.$row['partita_iva'].'</td>
                    <td data-th="Elimina"><center><i class="icon icon-close" id="delete_employee" onclick="delete_employee('.$row['id'].')"></i></center></td>
                </tr>';
          }
    		}

        $html .= '
            </tbody>
        </table>';

        return $html;
      }
    }

	}// fine class
?>