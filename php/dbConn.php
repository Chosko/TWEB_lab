<?php
class DBConn{
	private $db; //La connessione
	private $result; //Il risultato dell'ultima query. Se è vuoto è settato a false.
	private $name;  //Il nome del database
	private $host;  //Il nome dell'host
	private $user;  //L'utente del database con cui si effettua la connessione
	private $password;  //La password
	private $connected;  //Variabile booleana. True se è già connesso. False altrimenti.
	
	// Costruttore
	function DBconn(){
		require_once('db_config.php'); // Importa il file di configurazione del database.
		$this->name = $db_name;
		$this->host = $db_host;
		$this->user = $db_user;
		$this->password = $db_password;
		$this->connected = false;
		$this->result = false;
	}
	
	// Effettua la connessione al database se ce n'è già una attiva.
	function connect(){
		if(!$this->connected){
			$this->db = mysql_connect($this->host, $this->user, $this->password) or die("Impossibile connettersi al database: ".mysql_error());
			mysql_select_db($this->name) or die("Impossibile connettersi al database: ".mysql_error());
			$this->connected = true;
		}
	}
	
	// Effettua la disconnessione dal database.
	function disconnect(){
		if($this->connected){
			mysql_close() or die('Impossibile disconnettersi dal database: '.mysql_error());
			$this->connected = false;
		}
	}
	
	// Effettua una query al database. ATTENZIONE: la stringa $query_str non viene sottoposta a escape. Bisogna assicurarsi di averlo già fatto.
	function query($query_str){
		if($this->connected){
			$this->result = mysql_query($query_str) or die("Impossibile eseguire la query '".$query_str."': ".mysql_error());
		}
		else{
			$this->connect();
			$this->result = mysql_query($query_str) or die("Impossibile eseguire la query: '".$query_str."'".mysql_error());
			$this->disconnect();
		}
		return $this->result;
	}
	
	// Fa eseguire al database l'escape di una stringa.
	function escape($unescaped_str){
		if($unescaped_str == '') die("Impossibile effettuare l'escape di una stringa vuota.");
		if($this->connected){
			$escaped_str = mysql_real_escape_string($unescaped_str) or die("Impossibile effettuare l'escape della stringa '".$unescaped_str."': ".mysql_error());
			return $escaped_str;
		}
		else{
			$this->connect();
			$escaped_str = mysql_real_escape_string($unescaped_str) or die("Impossibile effettuare l'escape della stringa: '".$unescaped_str."'".mysql_error());
			$this->disconnect();
			return $escaped_str;
		}
	}
	
	/* 	Effettua l'inserimento di un record in una tabella del database.
		$table_name -> il nome della tabella 
		$columns_array -> un array con il nome delle colonne in cui inserire i valori
		$values_array -> un array con il valore delle variabili (A CUI è GIà STATO EFFETTUATO L'ESCAPE!!!).
		Si presuppone che i valori di tipo string non abbiano apici o quotes agli estremi (vengono aggiunti automaticamente).*/
	function insert($table_name, $columns_array, $values_array){
		//controlla se le variabili inserite sono state inizializzate correttamente.
		(is_string($table_name) && is_array($columns_array) && is_array($values_array) && count($columns_array) == count($values_array)) 
			or die("Impossibile effettuare l'inserimento: variabili non inizializzate correttamente.");
		
		//Costruisce la query per il database
		$query_str = 'INSERT INTO '.$table_name.' (';
		$n = count($columns_array);
		for($i=0; $i<$n; $i++){
			if($i>0) $query_str .= ',';
			is_string($columns_array[$i]) or die("Impossibile effettuare l'inserimento: variabili non inizializzate correttamente");
			$query_str .= $columns_array[$i];
		}
		$query_str .= ') VALUES (';
		for($i=0; $i<$n; $i++){
			if($i>0) $query_str .= ',';
			if($values_array[$i] === NULL)
				$query_str .= 'NULL ';
			elseif(is_string($values_array[$i]))
				$query_str .= "'".$values_array[$i]."'";
			else
				$query_str .= $values_array[$i];
			
		}
		$query_str .= ') ;';
		
		//Effettua la query
		return $this->query($query_str);
	}
	
	/* 	Effettua la modifica dei campi di una tabella del database.
		$table_name -> il nome della tabella 
		$columns_array -> un array con il nome delle colonne di cui modificare i valori
		$values_array -> un array con il valore delle variabili (A CUI è GIà STATO EFFETTUATO L'ESCAPE!!!).
		Si presuppone che i valori di tipo string non abbiano apici o quotes agli estremi (vengono aggiunti automaticamente).*/
	function update($table_name, $columns_array, $values_array, $where){
		//controlla se le variabili inserite sono state inizializzate correttamente.
		(is_string($table_name) && is_array($columns_array) && is_array($values_array) && count($columns_array) == count($values_array)) 
			or die("Impossibile effettuare l'inserimento: variabili non inizializzate correttamente.");
		
		//Costruisce la query per il database
		$query_str = 'UPDATE '.$table_name.' SET ';
		$n = count($columns_array);
		for($i=0; $i<$n; $i++){
			if($i>0) $query_str .= ',';
			is_string($columns_array[$i]) or die("Impossibile effettuare la modifica: variabili non inizializzate correttamente");
			$query_str .= $columns_array[$i].' = ';
			if($values_array[$i] === NULL)
				$query_str .= 'NULL ';
			elseif(is_string($values_array[$i]))
				$query_str .= "'".$values_array[$i]."' ";
			else
				$query_str .= $values_array[$i].' ';
		}
		$query_str .= "WHERE $where;";
		
		//Effettua la query
		return $this->query($query_str);
	}
	
	//Elimina dati dal database. NON FA NESSUN TIPO DI CONTROLLO
	function delete($table_name, $where){
		$query_str = 'DELETE FROM '.$table_name.' WHERE '.$where;
		return $this->query($query_str);
	}
	
	// Preleva dal risultato dell'ultima query il prossimo record.
	function fetch_object(){
		if(!$obj = mysql_fetch_object($this->result))
			$this->result = false;
		return $obj;
	}
	
	// Preleva da un risultato esterno di query il prossimo record.
	function fetch_external_object($result){
		return mysql_fetch_object($result);
	}
	
	
	/* #################################################
		DA QUI COMINCIANO LE QUERY PREDEFINITE
		Le query predefinite sono costruite ad hoc per
		QUESTO database, mentre la parte sopra posso
		riutilizzarla per altri progetti.
	*/ #################################################
	
	//Serve per conservare lo stato di connessione del database prima e dopo la query.
	private $was_connected;
	
	//Stringa di errore. Contiene l'ultimo errore avvenuto.
	private $error;
	
	//Ritorna una stringa con l'ultimo errore verificatosi.
	function error(){
		return $this->error;
	}
	
	/*	Le due prossime funzioni servono per conservare lo stato di connessione del database.
		In questo modo se prima della query predefinita il database non era connesso, dopo la query verrà disconnesso.
		Utilizzo questo metodo perchè altrimenti se la connessione non è attiva, è possibile che alcune query effettuino
		parecchie connessioni e disconnessioni consecutive (per esempio per l'escape dei valori). 
	*/
	
	//Da chiamare all'inizio di ogni query predefinita
	private function begin_query(){
		$this->was_connected = $this->connected;
		if(!$this->was_connected){
			$was_connected = false;
			$this->connect();
		}
	}
	
	//Da chiamare alla fine di ogni query predefinita
	private function end_query($query_str){
		$res = $this->query($query_str);
		if(!$this->was_connected)
			$this->disconnect();
		return $res;
	}
	
	//Seleziona gli eventi dal database. Di default ne seleziona 5 che devono ancora avvenire, ordinati per in_cima, peso e data_2. 
	function seleziona_eventi($timestamp_inizio = 'now', $timestamp_fine = NULL, $order_by = "in_cima DESC, peso, data_2", $limit = "0,5", $creato_da = NULL){
		$this->begin_query();
		if((string)$timestamp_inizio == 'now')
			$timestamp_inizio = time();
		$query_str = '
		SELECT * FROM contenuti, tipicontenuti, categoriecontenuti 
		WHERE contenuti.categoriacontenuto = categoriecontenuti.categoriacontenuto 
			AND categoriecontenuti.tipocontenuto = tipicontenuti.tipocontenuto 
			AND tipicontenuti.tipocontenuto = "evento" 
			AND data_2 >= ' . (int)$timestamp_inizio;
		if($timestamp_fine != NULL)
			$query_str .= ' AND data_2 <= ' . (int)$timestamp_fine;
		if($creato_da != NULL){
			$query_str .= ' AND contenuti.utente = "' . $this->escape($creato_da) . '"';
		}
		if($order_by != NULL)
			$query_str .= " ORDER BY $order_by";
		if($limit != NULL)
			$query_str .= " LIMIT $limit";
		$query_str .= ';';
		return $this->end_query($query_str);
	}
	
	//Seleziona gli eventi dal database. Di default ne seleziona 5 che devono ancora avvenire, ordinati per in_cima, peso e data_2. 
	function cerca_eventi($string, $timestamp_inizio = 'now', $timestamp_fine = NULL){
		$this->begin_query();
		if((string)$timestamp_inizio == 'now')
			$timestamp_inizio = time();
		$query_str = '
		SELECT * FROM contenuti, tipicontenuti, categoriecontenuti 
		WHERE contenuti.categoriacontenuto = categoriecontenuti.categoriacontenuto 
			AND categoriecontenuti.tipocontenuto = tipicontenuti.tipocontenuto 
			AND tipicontenuti.tipocontenuto = "evento" 
			AND data_2 >= ' . (int)$timestamp_inizio;
		if($timestamp_fine != NULL)
			$query_str .= ' AND data_2 <= ' . (int)$timestamp_fine;
		if($string != NULL)
			$query_str .= ' AND (contenuti.titolo LIKE "%'.$this->escape($string).'%" OR contenuti.testo LIKE "%'.$this->escape($string).'%") ';
		$query_str .= ';';
		return $this->end_query($query_str);
	}
	
	//Seleziona e ritorna l'evento con id num_contenuto
	function get_contenuto($num_contenuto){
		$this->begin_query();
		$query_str = '
		SELECT * FROM contenuti WHERE num_contenuto = '.(int)$num_contenuto.';';
		$res = $this->end_query($query_str);
		return $this->fetch_object();
	}
	
	//Seleziona le info dal database. Di default ne seleziona 4 che non sono ancora scaduti, ordinati per in_cima, peso e data di scadenza.
	function seleziona_info($timestamp_inizio = 'now', $timestamp_fine = NULL, $order_by = "in_cima DESC, peso, data_2", $limit = "0,5", $creato_da = NULL){
		$this->begin_query();
		if((string)$timestamp_inizio == 'now')
			$timestamp_inizio = time();
		$query_str = '
		SELECT * FROM contenuti, tipicontenuti, categoriecontenuti 
		WHERE contenuti.categoriacontenuto = categoriecontenuti.categoriacontenuto 
			AND categoriecontenuti.tipocontenuto = tipicontenuti.tipocontenuto 
			AND tipicontenuti.tipocontenuto = "info" 
			AND data_2 >= ' . (int)$timestamp_inizio;
		if($timestamp_fine != NULL)
			$query_str .= ' AND data_2 <= ' . (int)$timestamp_fine;
		if($creato_da != NULL){
			$query_str .= ' AND contenuti.utente = "' . $this->escape($creato_da) . '"';
		}
		if($order_by != NULL)
			$query_str .= " ORDER BY $order_by";
		if($limit != NULL)
			$query_str .= " LIMIT $limit";
		$query_str .= ';';
		return $this->end_query($query_str);
	}
	
	//Seleziona gli utenti dal database7
	function seleziona_utenti($categoriautente = NULL, $priorita = NULL ,$where = NULL){
		$this->begin_query();
		$query_str = '
		SELECT * FROM utenti, categorieutenti
		WHERE utenti.categoriautente = categorieutenti.categoriautente ';
		if($categoriautente != NULL)
			$query_str .= 'AND utenti.categoriautente = "' . $this->escape($categoriautente) . '" ';
		if($where != NULL)
			$query_str .= 'AND ' . $where . ' ';
		if($priorita != NULL)
			$query_str .= 'AND categorieutenti.priorita <= '.(int)$priorita;
		$query_str .= ';';
		return $this->end_query($query_str);
	}
	
	//Seleziona le categorie di utenti
	function seleziona_categorie_utenti($priorita = NULL, $where = NULL){
		$this->begin_query();
		$query_str = '
		SELECT categoriautente FROM categorieutenti WHERE 1 ';
		if(!($priorita === NULL)){
			$query_str .= ' AND priorita <= ' . (int)$priorita;
		}
		if($where != NULL)
			$query_str .= ' AND ' . $where . ' ';
		$query_str .= ' ORDER BY priorita;';
		return $this->end_query($query_str);
		
	}
	
	//Ritorna la priorità della categoria a cui appartiene l'utente
	function get_priorita_utente($utente){
		$this->begin_query();
		$query_str = '
		SELECT priorita FROM utenti, categorieutenti
		WHERE utenti.categoriautente = categorieutenti.categoriautente
			AND utenti.utente = "'.$this->escape($utente).'" ; ';
		$this->end_query($query_str);
		if($obj = $this->fetch_object()){
			return $obj->priorita;
		}
		else return false;
	}
	
	//Ritorna la priorità della categoria a cui appartiene l'utente
	function get_priorita($categoriautente){
		$this->begin_query();
		$query_str = '
		SELECT priorita FROM categorieutenti
		WHERE categoriautente = "'.$this->escape($categoriautente).'" ; ';
		$this->end_query($query_str);
		if($obj = $this->fetch_object()){
			return $obj->priorita;
		}
		else return false;
	}
	
	//Seleziona tutti i privilegi che ha un utente
	function seleziona_privilegi_utente($utente){
		$this->begin_query();
		$query_str = '
		SELECT P.privilegio, P.descrizione FROM utenti AS U, categorieutenti AS C, categorieutenti_privilegi AS CP, privilegi AS P
		WHERE U.categoriautente = C.categoriautente
			AND C.categoriautente = CP.categoriautente
			AND CP.privilegio = P.privilegio
			AND U.utente = "'.$this->escape($utente).'"; ';
		return $this->end_query($query_str);
	}
	
	//Ritorna true se l'utente ha il privilegio. False altrimenti.
	function utente_ha_privilegio($utente, $privilegio){
		$this->begin_query();
		$query_str = '
		SELECT P.privilegio FROM utenti AS U, categorieutenti AS C, categorieutenti_privilegi AS CP, privilegi as P
		WHERE U.categoriautente = C.categoriautente
			AND C.categoriautente = CP.categoriautente
			AND CP.privilegio = P.privilegio
			AND U.utente = "'.$this->escape($utente).'"
			AND P.privilegio = "'.$this->escape($privilegio).'";';
		$this->end_query($query_str);
		if(($obj = $this->fetch_object()) && $obj->privilegio == $privilegio)
			return true;
		else return false;
	}
	
	//Seleziona tutte le categoriecontenuti che sono di tipocontenuto "evento"
	function seleziona_categorie_eventi(){
		$this->begin_query();
		$query_str = '
		SELECT C.categoriacontenuto FROM categoriecontenuti AS C, tipicontenuti AS T
		WHERE C.tipocontenuto = T.tipocontenuto
			AND T.tipocontenuto = "evento"
			ORDER BY C.categoriacontenuto
		;';
		return $this->end_query($query_str);
	}
	
	function seleziona_privilegi($categoriautente = NULL){
		$this->begin_query();
		$query_str = '
		SELECT privilegi.privilegio, privilegi.descrizione FROM privilegi , categorieutenti, categorieutenti_privilegi
		WHERE 1 ';
		if($categoriautente != NULL){
			$query_str .= '
			AND privilegi.privilegio = categorieutenti_privilegi.privilegio
			AND categorieutenti.categoriautente = categorieutenti_privilegi.categoriautente
			AND categorieutenti.categoriautente = "'.$this->escape($categoriautente).'" ';
		}
		$query_str .= ';';
		return $this->end_query($query_str);
	}
	
	function aggiungi_privilegio($categoriautente, $privilegio){
		if(!isset($categoriautente) || !isset($privilegio) || trim($categoriautente) == '' || trim($privilegio == '')){
			$this->error = 'I campi sono incompleti.';
			return false;
		}
		$this->begin_query();
		$query_str = '
		INSERT INTO categorieutenti_privilegi (categoriautente,privilegio) VALUES ("'. $this->escape($categoriautente).'","'.$this->escape($privilegio).'") ;';
		$res = $this->end_query($query_str);
		if(!$res)
			$this->error = mysql_error();
		return $res;
	}
	
	function rimuovi_privilegio($categoriautente, $privilegio){
		if(!isset($categoriautente) || !isset($privilegio) || trim($categoriautente) == '' || trim($privilegio == '')){
			$this->error = 'I campi sono incompleti.';
			return false;
		}
		$this->begin_query();
		$query_str = '
		DELETE FROM categorieutenti_privilegi WHERE categoriautente = "'. $this->escape($categoriautente).'" AND privilegio = "'.$this->escape($privilegio).'" ;';
		$res = $this->end_query($query_str);
		if(!$res)
			$this->error = mysql_error();
		return $res;
	}
	
	function inserisci_categoriautente($nome, $priorita){
		if(!isset($nome) || !isset($priorita) || trim($nome) == '' || !has_integer($priorita) || (int)$priorita >= 99 || (int)$priorita <= 0){
			$this->error = 'I campi sono incompleti.';
			return false;
		}
		
		$this->begin_query();
		$res = $this->insert('categorieutenti', array('categoriautente', 'priorita'), array($this->escape($nome), (int)$priorita));
		if(!$res)
			$this->error = mysql_error();
		return $res;
	}
	
	function elimina_categoriautente($categoriautente){
		if(!isset($categoriautente) || trim($categoriautente) == ''){
			$this->error = 'I campi sono incompleti.';
			return false;
		}
		$this->begin_query();
		$res = $this->delete('categorieutenti', 'categoriautente = "'.$this->escape($categoriautente).'"');
		if(!$res)
			$this->error = mysql_error();
		return $res;
	}
	
	function categoria_ha_utenti($categoriautente){
		if(!isset($categoriautente) || trim($categoriautente) == ''){
			$this->error = 'I campi sono incompleti.';
			return false;
		}
		$this->begin_query();
		$query_str = '
		SELECT * FROM utenti as U, categorieutenti as C
		WHERE C.categoriautente = U.categoriautente
		AND C.categoriautente = "'.$this->escape($categoriautente).'" ;';
		$res = $this->end_query($query_str);
		if($obj = $this->fetch_object())
			return true;
		else
			return false;
	}
	
	//Ritorna true se l'utente esiste con la password ad esso associata. False altrimenti.
	function login($utente, $password, $use_md5 = true){
		$this->begin_query();
		if($use_md5)
			$password = md5($password);
		$query_str = '
		SELECT U.utente, U.attivo FROM utenti AS U
		WHERE U.utente = "'.$this->escape($utente).'"
			AND U.password = "'.$password.'";
		';
		$this->end_query($query_str);
		$i=0;
		while($obj = $this->fetch_object()){
			if($obj->attivo)
				$utente_return = $obj->utente;
			else
				$utente_return = -1;
			$i++;
		}
		if($i==1)
			return $utente_return;
		else
			return false;
	}
	
	//Attiva l'utente
	function attiva_utente($utente){
		$this->begin_query();
		$query_str = '
		UPDATE utenti AS U SET U.attivo = 1 
		WHERE U.utente = "'.$this->escape($utente).'";
		';
		$this->query($query_str);
		$obj = $this->get_utente($utente);
		if(!$this->was_connected)
			$this->disconnect();
		return $obj->attivo;
	}
	
	//Disattiva l'utente
	function disattiva_utente($utente){
		$this->begin_query();
		$query_str = '
		UPDATE utenti AS U SET U.attivo = 0 
		WHERE U.utente = "'.$this->escape($utente).'";
		';
		$this->query($query_str);
		$obj = $this->get_utente($utente);
		if(!$this->was_connected)
			$this->disconnect();
		return !$obj->attivo;
	}
	
	//Disattiva l'utente
	function elimina_utente($utente){
		$this->begin_query();
		$query_str = '
		DELETE FROM utenti WHERE utente = "'.$this->escape($utente).'";
		';
		$this->query($query_str);
		$obj = $this->get_utente($utente);
		if(!$this->was_connected)
			$this->disconnect();
		if($obj)
			return false;
		else
			return true;
	}
	
	//Seleziona tutti i dati del profilo.
	function get_utente($utente){
		$this->begin_query();
		$query_str = ' SELECT * FROM utenti WHERE utente = "'.$this->escape($utente).'";';
		$this->end_query($query_str);
		$i=0;
		while($obj = $this->fetch_object()){
			$utente_return = $obj;
			$i++;
		}
		if($i==1)
			return $utente_return;
		else
			return false;
	}
	
	//Seleziona tutti i dati del profilo, selezionati tramite email.
	function get_utente_by_email($email){
		$this->begin_query();
		$query_str = ' SELECT * FROM utenti WHERE email = "'.$this->escape($email).'";';
		$this->end_query($query_str);
		$i=0;
		while($obj = $this->fetch_object()){
			$utente_return = $obj;
			$i++;
		}
		if($i==1)
			return $utente_return;
		else
			return false;
	}
	
	//Ritorna l'ultimo contenuto inserito
	function get_last_contenuto(){
		$query_str = 'SELECT * FROM contenuti WHERE 1 ORDER BY num_contenuto DESC;';
		$this->query($query_str);
		$obj = $this->fetch_object();
		return $obj;
	}
	
	//Modifica un dato dell'utente.
	function modifica_dato_utente($utente, $campo, $valore){
		require_once('functions.php');
		require_once('site_config.php');
		$bad_chars = "/[\|\+=<>()%@#\*]|(!=)|-{2}/";
		
		switch($campo){
			case 'nome':
			case 'cognome':
			case 'indirizzo':
			case 'categoriautente':
				$valore = trim((string)$valore);
				if(preg_match($bad_chars, $valore)){
					$this->error = "Il valore corrispondente al $campo contiene caratteri non validi.";
					return false;
				}
				break;
			case 'telefono':
			case 'telefono2':
				$valore = trim((string)$valore);
				//CONTINUARE DA QUA
				if(!has_integer($valore)){
					$this->error = "Il valore non è numerico.";
					return false;
				}
				break;
			case 'datanascita':
				if(!has_integer($valore)){
					$this->error = "Il valore non è numerico.";
					return false;
				}
				$valore = (int)$valore;
				break;
			default:
				$this->error = "Campo da modificare non riconosciuto.";
				return false;
				break;
		}
		$this->begin_query();
		if(is_string($valore))
			$valore = $this->escape($valore);
		$res = $this->update('utenti', array($campo), array($valore), 'utente = "'.$utente.'"');
		if(!$res)
			$this->error = mysql_error();
		if(!$this->was_connected)
			$this->disconnect();
		return $res;
	}
	
	//Elimina un dato dell'utente.
	function elimina_dato_utente($utente, $campo){
		require_once('functions.php');
		
		$allowed = array('nome', 'cognome', 'indirizzo', 'telefono2', 'indirizzo', 'datanascita');
		if(array_search($campo, $allowed)=== false){
			$this->error = "Impossibile eliminare il campo $campo.";
			return false;
		}
		$this->begin_query();
		$res = $this->update('utenti', array($campo), array(NULL), 'utente = "'.$utente.'"');
		if(!$this->was_connected)
			$this->disconnect();
		return $res;
	}
	
	//Elimina un contenuto
	function elimina_contenuto($num_contenuto){
		require_once('functions.php');
		if(has_integer($num_contenuto)){
			$num_contenuto = (int)$num_contenuto;
			return $this->delete('contenuti', 'num_contenuto = '.$num_contenuto);
		}
		else{
			$this->error = 'Impossibile eliminare il contenuto: identificativo non valido';
			return false;
		}
	}
	
	//Sposta gli utenti di una categoria a un'altra categoria
	function sposta_utenti($categoria_partenza, $categoria_destinazione){
		if(isset($categoria_partenza) && isset($categoria_destinazione) && trim($categoria_partenza != '') && trim($categoria_destinazione != '')){
			$this->begin_query();
			$query_str = 'UPDATE utenti, categorieutenti SET utenti.categoriautente = "'.$this->escape($categoria_destinazione).'"
			WHERE utenti.categoriautente = categorieutenti.categoriautente AND utenti.categoriautente = "'.$this->escape($categoria_partenza).'" ;';
			return $this->end_query($query_str);
		}
		else{
			$this->error = 'I campi sono incompleti';
			return false;	
		}
	}
}
?>