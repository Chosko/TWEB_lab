<?php
//Genera una stringa con al fondo un numero random
function random_header($string, $max){
	$rand = rand(0, $max);
	return $string.$rand.'.jpg';
}

//Controlla che il nome di una pagina sia tra quelle permesse nel sito.
function check_page($page_name){
	require_once('site_config.php');
	$valid = false;
	global $allowed_pages;
	for($i=0; $i<count($allowed_pages) && !$valid; $i++){
		if($allowed_pages[$i] == $page_name)
			$valid = true;
	}
	return $valid ? $page_name : false;
}

//Controlla se la variabile inserita ha un valore numerico intero.
function has_integer($var){
	if(is_int($var))
		return true;
	else if(is_string($var)){
		if(preg_match('/^[0-9]*$/', $var))
			return true;
	}
	return false;
}

//Ritorna il nome italiano del mese specificato dall'intero
function date_inttomese($month_number){
	return date_translate(date('F', mktime(0,0,0,$month_number)));
}

//Ritorna la stringa di una data inglese in italiano
function date_translate($eng_str){
	$translations = array(
		'Jan'=>'Gen',
		'Jun'=>'Giu',
		'Jul'=>'Lug',
		'Oct'=>'Ott',
		'Dec'=>'Dic',
		'January'=>'Gennaio',
		'February'=>'Febbraio',
		'March'=>'Marzo',
		'April'=>'Aprile',
		'May'=>'Maggio',
		'June'=>'Giugno',
		'July'=>'Luglio',
		'August'=>'Agosto',
		'September'=>'Settembre',
		'October'=>'Ottobre',
		'November'=>'Novembre',
		'December'=>'Dicembre',
		'Sun'=>'Dom',
		'Mon'=>'Lun',
		'Tue'=>'Mar',
		'Wed'=>'Mer',
		'Thu'=>'Gio',
		'Fri'=>'Ven',
		'Sat'=>'Sab',
		'Sunday'=>'Domenica',
		'Monday'=>'Luned&igrave;',
		'Tuesday'=>'Marted&igrave;',
		'Wednesday'=>'Mercoled&igrave;',
		'Thursday'=>'Gioved&igrave;',
		'Friday'=>'Venerd&igrave;',
		'Saturday'=>'Sabato'
	);
	return strtr($eng_str,$translations);
}

//Ricampiona un immagine, ridimensionandola e salvandola come copia nel percorso scelto.
function resize_img($src_path, $dst_path, $dst_height, $dst_width = -1){
	list($src_width, $src_height) = getimagesize($src_path); //ricavo la dimensione dell'immagine sorgente
	if($dst_width == -1){ //ricavo la larghezza dell'immagine destinazione se non è stata impostata
		$aspect_ratio = (float)$src_width / (float)$src_height; //ricavo il rapporto tra la larghezza e l'altezza dell'immagine sorgente (cast a float per evitare divisione intera con troncamento)
		$dst_width = (int)($dst_height * $aspect_ratio); //ricavo la larghezza dell'immagine destinazione (cast a int per troncare il risultato)
	}
	$dst_img = imagecreatetruecolor($dst_width, $dst_height);
	$src_img = imagecreatefromjpeg($src_path);
	imagecopyresampled($dst_img, $src_img, 0,0,0,0, $dst_width, $dst_height, $src_width, $src_height);
	imagejpeg($dst_img, $dst_path, 75);
}

?>