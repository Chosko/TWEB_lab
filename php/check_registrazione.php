<?php
require_once('functions.php');

function post($str){
	return isset($_POST[$str]) ? $_POST[$str] : false;
}

function nullify($var){
	$var = trim((string)$var);
	return $var == '' ? NULL : $var;
}

$registrazione_ok = true;

$this->nome = post('nome');
$this->cognome = post('cognome');
$this->telefono = post('telefono');
$this->telefono2 = post('telefono2');
$this->indirizzo = post('indirizzo');
$this->username = post('username');
$this->email = post('email');
$giorno = post('giorno');
$mese = post('mese');
$anno = post('anno');
$submit = post('submit');
$conferma_email = post('conferma_email');
$password = post('password');
$conferma_password = post('conferma_password');
$bad_chars = "/[\|\+=<>()%@#\*]|(!=)|-{2}/";
$good_email = "/^[0-9a-zA-Z]+([-\._][0-9a-zA-Z])*@[0-9a-zA-Z]+([-\._][0-9a-zA-Z])*\.[a-zA-Z]{2,4}$/";
$this->error_string = false;

if($submit){
	if($this->telefono && $this->username && $this->email && $conferma_email && $password && $conferma_password && $giorno && $mese && $anno){
		$this->error_string = 'Registrazione non eseguita per i seguenti motivi:';
		if($this->nome){
			if(preg_match($bad_chars, $this->nome)){
				$registrazione_ok = false;
				$this->error_string .= "<br>- Caratteri non consentiti nel nome: | + -- = &lt; &gt; != ( ) % @ # *";
			}
		}
		if($this->cognome){
			if(preg_match($bad_chars, $this->cognome)){
				$registrazione_ok = false;
				$this->error_string .= "<br>- Caratteri non consentiti nel cognome: | + -- = &lt; &gt; != ( ) % @ # *";
			}
		}
		if($this->indirizzo){
			if(preg_match($bad_chars, $this->indirizzo)){
				$registrazione_ok = false;
				$this->error_string .= "<br>- Caratteri non consentiti nell' indirizzo: | + -- = &lt; &gt; != ( ) % @ # *";
			}
		}
		if($this->telefono2){
			if(!has_integer($this->telefono2)){
				$registrazione_ok = false;
				$this->error_string .= "<br>- Inserire un numero di telefono valido";
			}
		}
		if(!has_integer($this->telefono)){
			$registrazione_ok = false;
			$this->error_string .= "<br>- Inserire un numero di cellulare valido";
		}
		if(trim($this->username) == ''){
			$registrazione_ok = false;
			$this->error_string .= "<br>- Il campo username &egrave; obbligatorio";
		}
		else if(preg_match($bad_chars, $this->username)){
			$registrazione_ok = false;
			$this->error_string .= "<br>- Caratteri non consentiti nello username: | + -- = &lt; &gt; != ( ) % @ # *";
		}
		else if($this->db->get_utente($this->username)){
			$registrazione_ok = false;
			$this->error_string .= "<br>- Lo username che hai scelto non &egrave; disponibile";
		}
		if(trim($this->email) == ''){
			$registrazione_ok = false;
			$this->error_string .= "<br>- Il campo email &egrave; obbligatorio";
		}
		else if(!preg_match($good_email, $this->email)){
			$registrazione_ok = false;
			$this->error_string .= "<br>- Inserisci un indirizzo e-mail valido";
		}
		else if($this->db->get_utente_by_email($this->email)){
			$registrazione_ok = false;
			$this->error_string .= "<br>- L'email che hai scelto &egrave; gi&agrave; registrata!";
		}
		if($password == ''){
			$registrazione_ok = false;
			$this->error_string .= "<br>- Il campo password &egrave; obbligatorio";
		}
		else if(preg_match($bad_chars, $password)){
			$registrazione_ok = false;
			$this->error_string .= "<br>- Caratteri non consentiti nella password: | + -- = &lt; &gt; != ( ) % @ # *";
		}
		else if($this->email != $conferma_email){
			$registrazione_ok = false;
			$this->error_string .= "<br>- I campi E-mail e Conferma E-mail non coincidono!";
		}
		else if($password != $conferma_password){
			$registrazione_ok = false;
			$this->error_string .= "<br>- I campi Password e Conferma Password non coincidono!";
		}
		$anno = (int)$anno;
		$mese = (int)$mese;
		$giorno = (int)$giorno;
		if(!checkdate($mese, $giorno, $anno)){
			$registrazione_ok = false;
			$this->error_string .= "<br>- data inserita non valida";
		}
		else{
			if($time = mktime(0,0,0,$mese,$giorno,$anno)){
				$max_time = strtotime('-13 year', time());
				if($time > $max_time){
					$registrazione_ok = false;
					$this->error_string .= "<br>- non si pu&ograve; registrare chi ha meno di 13 anni. Siamo spiacenti.";
				}
			}
			else{
				$registrazione_ok = false;
				$this->error_string .= "<br>- si &egrave; verificato un errore interno.";
			}
		}
		if($registrazione_ok){
			$this->nome = nullify($this->nome);
			$this->cognome = nullify($this->cognome);
			$this->telefono2 = nullify($this->telefono2);
			$this->indirizzo = nullify($this->indirizzo);
			$this->telefono = trim($this->telefono);
			$this->username = trim($this->username);
			$this->email = trim($this->email);
			$array_columns = array('utente', 'password', 'email', 'telefono', 'nome', 'cognome', 'telefono2', 'indirizzo', 'dataregistrazione', 'datanascita');
			$array_values = array($this->username, md5($password), $this->email, $this->telefono, $this->nome, $this->cognome, $this->telefono2, $this->indirizzo, time(), $time);
			$this->db->insert('utenti', $array_columns, $array_values);
			header('location:index.php?q=registrato');
		}
	}
	else{
		$registrazione_ok = false;
		$error = true;
		$this->error_string = 'Registrazione non eseguita: Non hai compilato tutti i campi obbligatori';
	}
}
else{	
	 $registrazione_ok = false;
	 $error = false;
}
?>