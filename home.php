<?php
require_once("php/page_interface.php");
require_once("php/dbConn.php");
require_once("php/functions.php");

//Questa classe definisce l'Home Page. Per un dettaglio sulle funzioni vedere i commenti all'interfaccia IPage
class home implements IPage{
	private $standard_layout;
	private $title;
	private $color;
	private $db;
	private $cerca;
	
	function home(){
		//Incapsula i valori di tutti i parametri get in omonimi campi dell'oggetto.
		foreach($_GET as $k=>$v){
			$this->$k = $v;
		}
		$this->standard_layout = true;
		$this->title = "Eventi";
		$this->db = new DBConn();
		$this->color = "#F60";		
		$this->cerca = "Eventi";
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
    	.pulsante_ricerca{
        	border-radius: 8px;
            border: none;
        	color:white;
            padding: 4px;
            box-shadow: 2px 2px 2px #888;
            cursor: pointer;
            font-size: 14px;
        	background-color: <?php echo $this->color(); ?>;
        }
        
        .pulsante_ricerca_premuto{
        	border-radius: 8px;
            border: none;
        	color:white;
            padding: 4px;
            box-shadow: 2px 2px 2px #888 inset;
            cursor: pointer;
            font-size: 14px;
         	background-color: #F20;
        }
	<?php 
	}
	
	function content(){
		if(!isset($this->input_cerca)){
			echo '<h1 class="shadowed">Eventi in programma</h1>';
			$this->db->seleziona_eventi();
		}
		else{
			if(isset($this->seleziona_prossimi)){
				$_SESSION['cerca_eventi'] = 'prossimi';
			}
			if(isset($this->seleziona_tutti)){
				$_SESSION['cerca_eventi'] = 'tutti';
			}
			if(isset($this->seleziona_passati)){
				$_SESSION['cerca_eventi'] = 'passati';
			}
			if(!isset($_SESSION['cerca_eventi'])){
				$_SESSION['cerca_eventi'] = 'prossimi';
			}
			$seleziona = $_SESSION['cerca_eventi'];
			
			switch($seleziona){
				case 'prossimi':
					$this->db->cerca_eventi($this->input_cerca, time(), NULL);
					break;
				case 'tutti':
					$this->db->cerca_eventi($this->input_cerca, 0, NULL);
					break;
				case 'passati':
					$this->db->cerca_eventi($this->input_cerca, 0, time());
					break;
			}
			?>
			<h1 class="shadowed">Risultati della ricerca</h1>
            <form action="index.php" method="GET" id="ricerca_avanzata">
            	<p>Cerca tra gli eventi: 
                    <input type="hidden" name="q" value="home" >
                    <input type="hidden" name="input_cerca" value="<?php echo $this->input_cerca; ?>">
                    <input type="submit" 
                    	class="pulsante_ricerca<?php if($seleziona=='tutti')echo '_premuto'; ?>" 
                        name="seleziona_tutti" value="Tutti" >
                    <input type="submit" 
                    	class="pulsante_ricerca<?php if($seleziona=='prossimi')echo '_premuto'; ?>" 
                        name="seleziona_prossimi" value="In Programma" >
                    <input type="submit" 
                    	class="pulsante_ricerca<?php if($seleziona=='passati')echo '_premuto'; ?>" 
                        name="seleziona_passati" value="Passati" >
                </p>
            </form>
        <?php
		}
		$i = 0;
		while($obj = $this->db->fetch_object()){
			if($i==0){
				echo '<div  class="content1_riquadro"';
				if(isset($obj->foto))
					echo 'style="background-image: url(img/' , $obj->foto , '); background-repeat:no-repeat; background-size:cover; background-position:center center;" ';
				echo '>' , isset($obj->foto) ? '<div class="full">' : '<div class="full_noimg">'
					,'<h1>', $obj->titolo , '</h1>
					<h3>', date_translate(date('l j F Y' , $obj->data_2)) , ' - ', date('H:i' , $obj->data_2) , '</h3>
					<p>' , $obj->testo , '</p>
				</div>
				</div>';
			}
			else{
				echo '<div class="content1_piccolo" onclick="expand(this)"';
				echo '>' , isset($obj->foto) ? '<div class="full">' : '<div class="full_noimg">'
					,'<h1>' , $obj->titolo , '</h1>
					<h3>', date_translate(date('l j F Y' , $obj->data_2)) , ' - ', date('H:i' , $obj->data_2) , '</h3>
					<p>' , $obj->testo , '</p>
				</div>
				</div>';
			}
			$i++;
			if($i==0){
				echo '<h4>Non ci sono eventi in programma</h4>';
			}
		}
	}
	
	function content2(){
		require_once('php/modules.php');
		info_module($this->db);
	}
}

?>