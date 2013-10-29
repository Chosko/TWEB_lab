<?php
require_once("php/page_interface.php");
require_once("php/dbConn.php");
require_once("php/functions.php");


class info implements IPage{
	private $standard_layout;
	private $title;
	private $color;
	private $db;
	private $cerca;
	
	function info(){
		//Incapsula i valori di tutti i parametri get in omonimi campi dell'oggetto.
		foreach($_GET as $k=>$v){
			$this->$k = $v;
		}
		$this->standard_layout = true;
		$this->title = "Informazioni";
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
		<h1 class="shadowed">Informazioni</h1>
        <h3 class="title_info"> Il Ristorante </h3>
        <p class="justify">	
        	A pochi passi dalla Gran Madre si trova il Ristorante The Royal Princess dei fratelli Palladino, che vanta dal 1870 una tradizione di raffinata ospitalità. Per loro &egrave; un privilegio poter offrire agli ospiti i risultati della loro ricerca gastronomica, sempre personale e sensibile ai suggerimenti dei prodotti stagionali. 
        </p>
        <h3 class="title_info">La cucina</h3>
   		<p class="justify">
        	Regno fantastico dove si creano e si studiano nuove ricette e sapori cercando di miscelare la tradizione con l'innovazione. 
			Non mancano piatti a base di pesce, di foie gras e, nel periodo autunnale, sua maestà il tartufo bianco. 
			I menù variano frequentemente per garantire sempre la massima qualità della materia prima, che per noi &egrave; un punto essenziale.
        </p>
        <h3 class="title_info">Le sale </h3>
        <p class="justify">
         Le luminose sale con i soffitti a volta, messe in risalto dall'immensa vetrata d'ingresso, accolgono circa 150 coperti in un'atmosfera di sobria, confortevole eleganza. 
		 Intima e preziosa, la cantina &egrave; un angolo dedicato ai momenti particolari ed offre i colori e i profumi delle migliori etichette italiane, abbinate ad una vetrina di tipicità esclusive: dai salumi ai formaggi, alle raffinate produzioni ortofrutticole artigianali. 
		 Nella bella stagione &egrave; protagonista la terrazza estiva, incorniciata nel cortile del palazzo, che offre l'opportunità di fresche serate, degustando le prelibatezze degli chef. Le vecchie mura, illuminate di sera, e il sottofondo musicale rendono ogni appuntamento piacevolmente romantico ed apprezzato dalla clientela nazionale ed internazionale. 
    	</p>
    <?php
    }
	
	function content2(){
		require_once('php/modules.php');
		info_module($this->db);
	}
}

?>