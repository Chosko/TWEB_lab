<?php
//Definisce una generica pagina.
interface IPage{
	//Esegue le operazioni che la pagina deve eseguire prima dell'invio degli header.
	//Restituisce il nome della pagina a cui effettuare redirect. Se non è previsto redirect ritorna false;
	function after_headers();
	
	//Stampa i tag aggiuntivi della head della pagina (NO CSS).
	function head_tags();
	
	//Restituisce una stringa con il valore di default del form di ricerca oppure false se la pagina non ha una ricerca.
	function cerca();
	
	//Restituisce una stringa con il titolo della pagina.
	function title();
	
	//Restituisce true se i privilegi dell'utente sono sufficienti a visualizzare la pagina. False altrimenti.
	function auth();
	
	//Restituisce l'oggetto DBConn associato alla pagina.
	function db();
	
	//Stampa le regole css definite per la pagina, senza tag di apertura e chiusura <style>.
	function css_rules();
	
	//Stampa il contenuto principale della pagina.
	function content();
	
	//Stampa il contenuto secondario della pagina.
	function content2();
	
	//Restituisce true se la pagina deve essere disegnata con il layout standard pensato per il sito.
	//Restituisce false altrimenti. Nel secondo caso la funzione content() stampa tutto il contenuto della pagina,
	//e la funzione content2() blocca l'esecuzione della pagina con un die().
	function standard_layout();
	
}

?>