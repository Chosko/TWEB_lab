<?php
require_once("php/page_interface.php");
require_once("php/dbConn.php");
require_once("php/functions.php");


class inattivo implements IPage{
	private $standard_layout;
	private $title;
	private $db;
	private $cerca;
	
	function inattivo(){
		//Incapsula i valori di tutti i parametri get in omonimi campi dell'oggetto.
		foreach($_GET as $k=>$v){
			$this->$k = $v;
		}
		$this->standard_layout = true;
		$this->title = "Utente inattivo";
		$this->db = new DBConn();	
		$this->cerca = false;
	}
	
	function after_headers(){
		return false;
	}
	
	function standard_layout(){
		return $this->standard_layout;
	}
	
	function db(){
		return $this->db;
	}
	
	function auth(){
		return true;
	}
	
	function title(){
		return $this->title;
	}
	
	function cerca(){
		return $this->cerca;
	}
	
	function head_tags() { ?>
	<?php }
	
	function css_rules(){ 
	?>
    	
	<?php 
	}
	
	function content(){?>
		<h1 class="shadowed">Utente inattivo</h1>
        <p>
        	Il tuo profilo &egrave; registrato correttamente, ma &egrave; stato disattivato o &egrave; ancora in fase di approvazione.<br>
			Potrai utilizzarlo non appena un amministratore lo attiver&agrave;.
        </p>
    <?php
    }
	
	function content2(){
		require_once('php/modules.php');
		info_module($this->db);
	}
}

?>