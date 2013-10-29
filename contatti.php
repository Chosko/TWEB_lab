<?php
require_once("php/page_interface.php");
require_once("php/dbConn.php");
require_once("php/functions.php");


class contatti implements IPage{
	private $standard_layout;
	private $title;
	private $color;
	private $db;
	private $cerca;
	
	function contatti(){
		//Incapsula i valori di tutti i parametri get in omonimi campi dell'oggetto.
		foreach($_GET as $k=>$v){
			$this->$k = $v;
		}
		$this->standard_layout = true;
		$this->title = "Contatti";
		$this->db = new DBConn();
		$this->color = "#F60";		
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
        <h1 class="shadowed">Contatti</h1>
        	
        <h1>The Royal Princess</h1> 
        <p class="center">
            Ristorante in Torino<br>
            Via Po 43 D, 10124<br>
            <span class="bold">P.iva:</span> 07539270012<br>
            <span class="bold">Telefono e Fax:</span> 011 535948<br>
            <span class="bold">Email: </span><a href="mailto:info@theroyalprincess.com">info@theroyalprincess.com</a><br><br>
            Chiusura settimanale:<br>
            Lunedì e Domenica<br><br><br>
            <img src="img/map.jpg" alt="mappa">
       	</p>
    <?php
    }
	
	function content2(){
		require_once('php/modules.php');
		info_module($this->db);
	}
}

?>