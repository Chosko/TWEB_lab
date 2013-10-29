<?php

function post($str){
	return isset($_POST[$str]) ? $_POST[$str] : false;
}

//modifica un evento
if(!isset($_SESSION['utente']))
	session_start();

$output = false;
$noscript = isset($_POST['noscript']);
$bad_chars = "/[\|\+=<>()%@#\*]|(!=)|-{2}/";

$utente = isset($_SESSION['utente']) ? $_SESSION['utente'] : false;
$num_contenuto = post('num_contenuto');
$titolo = post('titolo');
$giorno = post('giorno');
$mese = post('mese');
$anno = post('anno');
$ora = post('ora');
$minuto = post('minuto');
$categoria = post('categoria');
$testo = post('testo');
$peso = post('peso');
$in_cima = post('in_cima');
$elimina_contenuto = post('elimina_contenuto');

$privilegio = 'eventi';
$output_error = true;
$output_titolo = false;
$output_data = false;
$output_testo = false;
$output_categoria = false;
$output_peso = false;
$output_in_cima = false;
$output_orario = false;

//controlla che esista l'utente della sessione
if($utente){
	
	//se la pagina non è stata chiamata con ajax la variabile $db esiste già
	if($noscript){
		$db = $this->db;
	}
	//altrimenti la crea e importa i files da importare (la pagina è a sè stante)
	else{
		require_once('../php/dbConn.php');
		require_once('../php/functions.php');
		require_once('../php/site_config.php');
		$db = new DBConn();
	}
	//controlla che l'utente abbia il privilegio necessario per modificare gli eventi
	if($db->utente_ha_privilegio($utente, $privilegio)){
		
		//controlla che esista l'id del contenuto
		if($num_contenuto){
			
			//se il contenuto è da eliminare
			if($elimina_contenuto){
				$db->elimina_contenuto($num_contenuto);
			}
			else{
				//controllo del campo titolo
				if($titolo && ($titolo = trim((string)$titolo)) != ''){
					if(preg_match($bad_chars, $titolo))
						$output_titolo = "- Caratteri non consentiti: | + -- = &lt; &gt; != ( ) % @ # *";
					else
						//converte \n e \r in <br> e i caratteri speciali in entità html
						$titolo = $db->escape(str_replace(array("\r", "\r\n", "\n"), "<br>", htmlentities($titolo, ENT_COMPAT, "UTF-8")));
				}
				else
					$output_titolo = "- titolo mancante";
				
				//controllo del campo data
				if($giorno && $mese && $anno){
					if(has_integer($giorno) && has_integer($mese) && has_integer($anno)){
						$giorno = (int)$giorno;
						$mese = (int)$mese;
						$anno = (int)$anno;
						if(!checkdate($mese, $giorno, $anno))
							$output_data = "- la data inserita non &egrave; valida";
					}
					else
						$output_data = "- i campi della data non sono numerici";
				}
				else
					$output_data = "- data incompleta";
				
				if(has_integer($ora) && has_integer($minuto)){
					$ora = (int)$ora;
					$minuto = (int)$minuto;
					if(!($ora < 24 && $ora >= 0 && $minuto < 60 && $minuto >= 0)){
						$output_orario = "- l'orario inserito non &egrave; valido.";
					}
				}
				else
					$output_orario = "- i campi dell'orario non sono numerici";
				
				//controllo del campo testo
				if($testo && ($testo = trim((string)$testo)) != ''){
					if(preg_match($bad_chars, $testo))
						$output_testo = "- Caratteri non consentiti: | + -- = &lt; &gt; != ( ) % @ # *";
					else
						//converte \n e \r in <br> e i caratteri speciali in entità html
						$testo = $db->escape(str_replace(array("\r", "\r\n", "\n"), "<br>", htmlentities($testo, ENT_COMPAT, "UTF-8")));
				}
				else
					$output_testo = "- testo mancante";
				
				//controllo del campo peso
				if($peso){
					if(has_integer($peso)){
						$peso = (int)$peso;
						if(!($peso > 0 && $peso <= 50))
							$output_peso = "- il peso deve essere compreso tra 1 e 50";
					}
					else
						$output_peso = "- peso non numerico";
				}
				else
					$output_peso = "- peso mancante";
				
				$categoria = $db->escape(str_replace(array("\r", "\r\n", "\n"), "<br>", htmlentities($categoria, ENT_COMPAT, "UTF-8")));
				
				$in_cima = (bool)$in_cima;
				
				if(!$output_titolo && !$output_data && !$output_testo && !$output_categoria && !$output_peso && !$output_in_cima && !$output_orario){
					//INSERIMENTO NEL DATABASE
					$data = mktime($ora, $minuto, 0, $mese, $giorno, $anno);
					$columns_array = array('titolo', 'testo', 'data_creazione', 'data_2', 'peso', 'categoriacontenuto', 'in_cima');
					$values_array = array($titolo, $testo, time(), $data, $peso, $categoria, $in_cima ? 1:0);
					if($num_contenuto != 'insert')
						$db->update('contenuti', $columns_array, $values_array, 'num_contenuto = '.$num_contenuto);
					else{
						$columns_array[] = 'utente';
						$values_array[] = $utente;
						$db->insert('contenuti', $columns_array, $values_array);
						$num_contenuto = $db->get_last_contenuto()->num_contenuto;
					}
					$evento = $db->get_contenuto($num_contenuto);
					$output_titolo = addslashes($evento->titolo);
					$output_testo = addslashes($evento->testo);
					$output_peso = $evento->peso;
					$output_data = date_translate(date('l j F Y - H:i',$evento->data_2));
					$output_in_cima = $evento->in_cima;
					$output_categoria = addslashes($evento->categoriacontenuto);
					$output_orario = date('H:i',$evento->data_2);
					$output_error = false;
				}
				else{
					//INVENTARSI QUALCOSA QUA
					$output = 'Non &egrave; stato possibile eseguire la richiesta per i seguenti errori:';
				}
			}
		}
		else
			$output = "Errore nella richiesta: si &egrave; verificato un errore interno.";
	}
	else
		$output = "Errore nella richiesta: l'utente $utente non possiede i privilegi necessari.";
}
else
	$output = 'Errore nella richiesta: non trovo la sessione.';

if(!$noscript){
	if($output_error)
		echo 'output_error = true;
		';
	else
		echo 'output_error = false;
		';
	
	if($output_in_cima)
		echo 'output_in_cima = true;
		';
	else
		echo 'output_in_cima = false;
		';
		
	echo "output_id = $num_contenuto;
		output = '$output';
		output_titolo = '$output_titolo';
		output_testo = '$output_testo';
		output_peso = '$output_peso';
		output_data = '$output_data';
		output_orario = '$output_orario';
		output_categoria = '$output_categoria';
	"; 
}
?>