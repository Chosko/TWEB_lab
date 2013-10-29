<?php

$output = '';
$output_utente = false;
$output_email = false; 
$noscript = isset($_POST['noscript']);

$utente = false;
$email = false;
if(isset($_POST['utente']))
	$utente = $_POST['utente'];
if(isset($_POST['email']))
	$email = $_POST['email'];

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

if($utente){
	if($db->get_utente($utente)){
		$output = '<span style="color: red; font-weight: bold;">non disponibile!</span>';
		$output_utente = true;
	}
	else{
		$output = '<span style="color: green; font-weight: bold;">disponibile!</span>';
	}
}

if($email){
	if($db->get_utente_by_email($email)){
		$output = '<span style="color: red; font-weight: bold;">gi&agrave; registrata!</span>';
		$output_email = true;
	}
	else{
		$output = '<span style="color: green; font-weight: bold;">disponibile!</span>';
	}
}
	
if(!$noscript)
	echo '
	var output_error = ',($output_utente || $output_email) ? 'true' : 'false' ,';
	var output = \'', $output ,'\'
	';
?>