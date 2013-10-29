<?php
require_once("php/page_interface.php");
require_once("php/dbConn.php");

//Questa classe definisce l'Home Page. Per un dettaglio sulle funzioni vedere i commenti all'interfaccia IPage
class gestione_menu implements IPage{
	private $standard_layout;
	private $title;
	private $color;
	private $db;
	private $cerca;
	
	function gestione_menu(){
		//Incapsula i valori di tutti i parametri get in omonimi campi dell'oggetto.
		foreach($_GET as $k=>$v){
			$this->$k = $v;
		}
		$this->standard_layout = true;
		$this->title = "Area Riservata";
		$this->db = new DBConn();
		$this->color = "#F60";		
		$this->cerca = false;
	}
	
	function after_headers(){
		
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
	
	function color(){
		return $this->color;
	}
	
	function head_tags() { ?>
	<?php }
	
	function css_rules(){ 
	?>
    	
	<?php 
	}
	
	function content(){?>
        <h1>Accesso Negato</h1>
        <p>
        	Non possiedi i privilegi necessari per visualizzare la pagina che stavi cercando di raggiungere.<br>
            <span style="font-size:small">Torna alla <a href="index.php">home</a></span>
        </p>
	<?php }
	
	function content2(){
		require_once('php/modules.php');
		info_module($this->db);
	}
}

?>