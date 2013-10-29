<?php

if(!isset($_SESSION['utente']))
	session_start();

$output = '';
$noscript = isset($_POST['noscript']);

//Modifica un dato singolo dell'utente della sessione.
if(isset($_SESSION['utente'])){
	$utente = $_SESSION['utente'];
	if(isset($_POST['column'])){
		$column = $_POST['column'];
		if($column == 'datanascita'){
			
			//controlla l'esistenza della variabile anno
			if(isset($_POST['anno'])){
				$anno = (int)$_POST['anno'];
				//controlla l'esistenza della variabile mese
				if(isset($_POST['mese'])){
					$mese = (int)$_POST['mese'];
					//controlla l'esistenza della variabile giorno
					if(isset($_POST['giorno'])){
						$giorno = (int)$_POST['giorno'];
						//controlla che la data inserita sia valida
						if(!checkdate($mese, $giorno, $anno)){
							$output = 'Errore nella richiesta: data inserita non valida';
						}
						else{
							//controlla che l'utente abbia almeno 13 anni
							if($time = mktime(0,0,0,$mese,$giorno,$anno)){
								$max_time = strtotime('-13 year', time());
								if($time <= $max_time)
									$value = $time;
								else
									$output = 'Errore nella richiesta: non si può registrare chi ha meno di 13 anni';
							}
							else
								$output = 'Errore nella richiesta: errore interno';
						}
					}
					else $output = 'Errore nella richiesta: giorno mancante';
				}
				else
					$output = 'Errore nella richiesta: mese mancante.';
			}
			else
				$output = 'Errore nella richiesta: anno mancante.';
		}
		
		//controlla l'esistenza della variabile value
		elseif(isset($_POST['value']))
			$value = $_POST['value'];
		else $output = 'Errore nella richiesta: Valore mancante.';
		
		//Effettua la modifica
		if(isset($value)){
			if($noscript){
				$db = $this->db;
			}
			else{
				require_once('../php/dbConn.php');
				require_once('../php/functions.php');
				$db = new DBConn();
				$db->connect();
			}
			$elimina = false;
			if(trim($value) != '')
				$res = $db->modifica_dato_utente($utente, $column, $value);
			else{
				$res = $db->elimina_dato_utente($utente, $column);
				$elimina = true;
			}
			$obj = $db->get_utente($utente);
			if(!$noscript)
				$db->disconnect();
			if($res){
				if($elimina)
					$output = 'Eliminato';
				elseif($column == 'datanascita')
					$output = date_translate(date('j F Y', $obj->$column));
				else
					$output = $obj->$column;
			}
			else
				$output = 'Errore nella richiesta: '.$db->error();
		}
	}
	else $output = 'Errore nella richiesta: Campo mancante.';
}
else
	$output = 'Errore: non trovo la sessione';
	
if(!$noscript)
	echo $output;
?>