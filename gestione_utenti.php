<?php
require_once("php/page_interface.php");
require_once("php/dbConn.php");
require_once("php/functions.php");
require_once("php/modules.php");

//Per un dettaglio sulle funzioni vedere i commenti all'interfaccia IPage
class gestione_utenti implements IPage{
	private $standard_layout;
	private $title;
	private $db;
	private $cerca;
	private $auth;
	private $privilegi;
	
	function gestione_utenti(){
		//Incapsula i valori di tutti i parametri get in omonimi campi dell'oggetto.
		foreach($_GET as $k=>$v){
			$this->$k = $v;
		}
		$this->standard_layout = true;
		$this->title = "Area Riservata";
		$this->db = new DBConn();
		$this->cerca = false;
		$this->auth = array('eliminare utenti', 'privilegiare utenti', 'visualizzare utenti');
	}
	
	function after_headers(){
		
	}
	
	function standard_layout(){
		return $this->standard_layout;
	}
	
	function title(){
		return $this->title;
	}
	
	function cerca(){
		return $this->cerca;
	}
	
	function db(){
		return $this->db;
	}
	
	function auth(){
		if(isset($_SESSION['utente'])){
			$utente = $_SESSION['utente'];
			$this->db->seleziona_privilegi_utente($utente);
			$found = false;
			while($obj = $this->db->fetch_object()){
				for($i=0; $i<count($this->auth) && !$found; $i++){
					if($this->auth[$i] == $obj->privilegio)
						$found = true;
				}
				$this->privilegi[] = $obj->privilegio;
			};
			$this->priorita = $this->db->get_priorita_utente($utente);
			return $found;
		}
		else return false;
	}
	
	function head_tags() { ?>
	<?php }
	
	function css_rules(){ 
		gestisci_css();
	?> 
    	.utenti_all h1{
            margin: 0 10px;
            font-size: 25px;
            text-align: left;
            font-weight: normal;
            letter-spacing: 1px;
            color: #000;
            padding: 0 4px;
        }
        
        .utenti_all .utente:first-child{
        	border-top: 1px solid #CCC;
        }
        
        .utenti_all .utente{
        	border-bottom: 1px solid #CCC;
            padding: 8px 0;
            cursor: pointer;
            display: none;
        }
        
        .utenti_all .utente_full{
        	border-bottom: 1px solid #CCC;
            padding: 0 0;
        }
        
        .utenti_all .utente:hover{
            background-color: #FDB;
        }
        
    	.utenti_all h3{
        	font-size: 20px;
        	color: #F60;
           	margin: 0 10px;
            font-weight:normal;
            padding: 0 4px;
        }
        
        .utenti_all p{
        	text-align:justify;
            margin: 10px;
            padding: 0 4px;
        }
        
        .utenti_all .inner{
            background-color: #FDB;
            padding: 8px 0;
            cursor: pointer;
        }
        
        .utenti_all .inner:hover{
            background-color: #FB6;
        }
        
        .utenti_all .profilo{
            float: right;
            margin-right: 10px;
            font-size: 16px;
            color: #666;
        }
        
        .utenti_all .attivo{
        	float: right;
            background-color: #0C0;
            font-size: 16px;
            color: #FFF;
            padding: 3px;
			border-radius: 8px;
            margin-right: 10px;
			box-shadow: 0.9px 0.9px 8px #0C0;
        }
        
        .utenti_all .inattivo{
        	float: right;
            background-color: #999;
            font-size: 16px;
            color: #FFF;
            padding: 3px;
			border-radius: 8px;
            margin-right: 10px;
        }
        
        .pulsante_ricerca{
        	border-radius: 8px;
            border: none;
        	color:white;
            padding: 4px;
            box-shadow: 2px 2px 2px #888;
            cursor: pointer;
            font-size: 14px;
        	background-color: #F60;
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
        
        .left{
        	position: absolute;
        }
        
        .middle{
        	position: absolute;
            margin-left: 300px;
        }
        
        .right{
        	font-size: 14px;
            float:right;
            margin-left: 15px;
        }
        
        .vuoto{
        	color: #999;
        }
        
        .subtitle{
        	color: #F60;
        }
        
        .response{
        	text-align: center;
            margin: 5px;
        }
        
        .red{
        	color: red;
            background-color: #FCC;
            border: 2px solid red;
            padding: 5px;
        }
        
        .yellow{
        	color: #AA0;
            background-color: #FFD;
            border: 2px solid yellow;
            padding: 5px;
        }
        
        .green{
        	color: green;
            background-color: #8F8;
            border: 2px solid green;
            padding: 5px;
        }
        
        .nuovo {
            font-size: 16px;
            padding-bottom: 4px;
            line-height: 30px;
            margin-left: 10px;
        }
	<?php 
	}
	
	function content(){
		$response = false;
		if(isset($this->a) && isset($this->id)){
			switch($this->a){
				case 'attiva':
					if(!(array_search('privilegiare utenti', $this->privilegi)=== false)){
						if($this->id != $_SESSION['utente']){
							if(trim($this->id != '')){
								if($this->db->attiva_utente($this->id))
									$response = '<span class="green">'.$this->id.' attivato con successo!</span>';
								else
									$response = '<span class="red">'.$this->id.' non &egrave; stato attivato. Riprovare</span>';
							}
							else
								$response = '<span class="red">Attivazione non eseguita: si &egrave; verificato un errore nella richiesta</span>';
						}
						else
							$response = '<span class="red">Non puoi eseguire operazioni sul tuo stesso utente!</span>';
					}
					else
						$response = '<span class="red">Non possiedi i privilegi necessari per effettuare questo tipo di operazione!</span>';
					break;
				case 'disattiva':
					if(!(array_search('privilegiare utenti', $this->privilegi)===false)){
						if($this->id != $_SESSION['utente']){
							if(trim($this->id != '')){
								if($this->db->disattiva_utente($this->id))
									$response = '<span class="yellow">'.$this->id.' disattivato con successo!</span>';
								else
									$response = '<span class="red">'.$this->id.' non &egrave; stato disattivato. Riprovare</span>';
							}
							else
								$response = '<span class="red">Disttivazione non eseguita: si &egrave; verificato un errore nella richiesta</span>';
						}
						else
							$response = '<span class="red">Non puoi eseguire operazioni sul tuo stesso utente!</span>';
					}
					else
						$response = '<span class="red">Non possiedi i privilegi necessari per effettuare questo tipo di operazione!</span>';
					break;
				case 'elimina':
					if(!(array_search('eliminare utenti', $this->privilegi)=== false)){
						if($this->id != $_SESSION['utente']){
							if(trim($this->id != '')){
								if($this->db->elimina_utente($this->id))
									$response = '<span class="yellow">'.$this->id.' eliminato con successo!</span>';
								else
									$response = '<span class="red">'.$this->id.' non &egrave; stato eliminato. Riprovare</span>';
							}
							else
								$response = '<span class="red">Eliminazione non eseguita: si &egrave; verificato un errore nella richiesta</span>';
						}
						else
							$response = '<span class="red">Non puoi eseguire operazioni sul tuo stesso utente!</span>';
					}
					else
						$response = '<span class="red">Non possiedi i privilegi necessari per effettuare questo tipo di operazione!</span>';
					break;
				case 'cambia_profilo':
					if(!(array_search('privilegiare utenti', $this->privilegi)=== false)){
						if($this->id != $_SESSION['utente']){
							if(trim($this->id != '' && isset($this->nuova_categoriautente) && trim($this->nuova_categoriautente) != '')){
								if($this->db->modifica_dato_utente($this->id, 'categoriautente', $this->nuova_categoriautente))
									$response = '<span class="green">'.$this->id.' &egrave; diventato '.$this->nuova_categoriautente.'!</span>';
								else
									$response = '<span class="red">'.$this->id.' non &egrave; diventato '.$this->nuova_categoriautente.': '.$this->db->error().'</span>';
							}
							else
								$response = '<span class="red">Eliminazione non eseguita: si &egrave; verificato un errore nella richiesta</span>';
						}
						else
							$response = '<span class="red">Non puoi eseguire operazioni sul tuo stesso utente!</span>';
					}
					else
						$response = '<span class="red">Non possiedi i privilegi necessari per effettuare questo tipo di operazione!</span>';
					break;
			}
		}
		
		$res_categorie = $this->db->seleziona_categorie_utenti($this->priorita);
		$categorie_utenti = NULL;
		while($obj_categorie = $this->db->fetch_object()){
			$categorie_utenti[] = $obj_categorie->categoriautente;
		}
		
		if(isset($this->seleziona_inattivi)){
			$_SESSION['seleziona_gestione_utenti'] = 'inattivi';
		}
		if(isset($this->seleziona_attivi)){
			$_SESSION['seleziona_gestione_utenti'] = 'attivi';
		}
		if(isset($this->seleziona_tutti)){
			$_SESSION['seleziona_gestione_utenti'] = 'tutti';
		}
		foreach($categorie_utenti as $cat){
			if(isset($this->$cat)){
				$_SESSION['seleziona_gestione_utenti'] = $cat;
			}
		}
		if(!isset($_SESSION['seleziona_gestione_utenti'])){
			$_SESSION['seleziona_gestione_utenti'] = 'inattivi';
		}
		$seleziona = $_SESSION['seleziona_gestione_utenti'];
		
		switch($seleziona){
			case 'inattivi':
				$utenti_res = $this->db->seleziona_utenti(NULL, $this->priorita, 'attivo = 0');
				break;
			case 'tutti':
				$utenti_res = $this->db->seleziona_utenti(NULL, $this->priorita, NULL);
				break;
			case 'attivi':
				$utenti_res = $this->db->seleziona_utenti(NULL, $this->priorita, 'attivo = 1');
				break;
		}
		foreach($categorie_utenti as $cat){
			if($seleziona == $cat){
				$utenti_res = $this->db->seleziona_utenti($cat, $this->priorita, NULL);
			}
		}
		
		?>
    	<script type="text/javascript">
			
			function get_elem(id, elem){
				return document.getElementById(elem+id);
			}
			
			function close_utente(id){
				var short = get_elem(id, 'short');
				var full = get_elem(id, 'full');
				short.style.display = 'block';
				full.style.display = 'none';
			}
			
			function show_utente(id){
				var short = get_elem(id, 'short');
				var full = get_elem(id, 'full');
				short.style.display = 'none';
				full.style.display = 'block';
			}
			
			function mod_utente(id){
				
			}
		</script>
        
		<h1>Gestione Utenti</h1>
        <form action="index.php" method="GET">
            	<p>Seleziona: 
                    <input type="hidden" name="q" value="gestione_utenti" >
                    <input type="submit" 
                    	class="pulsante_ricerca<?php if($seleziona=='tutti')echo '_premuto'; ?>" 
                        name="seleziona_tutti" value="Tutti"  >
                    <input type="submit" 
                    	class="pulsante_ricerca<?php if($seleziona=='inattivi')echo '_premuto'; ?>" 
                        name="seleziona_inattivi" value="Inattivi" >
                    <input type="submit" 
                    	class="pulsante_ricerca<?php if($seleziona=='attivi')echo '_premuto'; ?>" 
                        name="seleziona_attivi" value="Attivi" >
                    <?php
					foreach($categorie_utenti as $cat){
						echo '<input type="submit" 
							class="pulsante_ricerca', ($seleziona==$cat ? '_premuto' : '') ,'" 
							name="' , $cat ,'" value="',$cat,'"  > ';
					}
					?>
            	</p>
        </form>
        
        <div class="response"><?php if($response) echo $response; ?></div>
        <a class="nuovo" href="index.php?q=registrazione">+nuovo</a>
        <div class="utenti_all">
        <?php
            while($obj = $this->db->fetch_external_object($utenti_res)){?>
                <div class="utente" id="short<?php echo $obj->utente ?>" onclick="show_utente('<?php echo $obj->utente ?>')">
                	<?php $utente_attivo = $obj->attivo ? 'attivo' : 'inattivo' ?>
                    <span class="<?php echo $utente_attivo ?>"><?php echo $utente_attivo ?></span>
                    <h1><?php echo $obj->utente ?></h1>
                    <span class="profilo"><?php echo $obj->categoriautente ?></span>
                    <h3><?php echo $obj->email ?></h3>
                </div>
                <div class="utente_full" id="full<?php echo $obj->utente ?>">
                    <div class="inner" onclick="close_utente('<?php echo $obj->utente ?>')">
                    	<?php $utente_attivo = $obj->attivo ? 'attivo' : 'inattivo' ?>
                        <span class="<?php echo $utente_attivo ?>"><?php echo $utente_attivo ?></span>
                        <h1><?php echo $obj->utente ?></h1>
                        <span class="profilo"><?php echo $obj->categoriautente ?></span>
                        <h3><?php echo $obj->email ?></h3>
                    </div>
                    	<p>
                        	<span class="left"><span class="subtitle">Nome: </span>
								<?php echo $obj->nome ? $obj->nome : '<span class="vuoto">vuoto</span>'; ?></span>
                           	<span class="middle"><span class="subtitle">Cellulare: </span>
								<?php echo $obj->telefono ? $obj->telefono : '<span class="vuoto">vuoto</span>'; ?></span><br>
                            <span class="left"><span class="subtitle">Cognome:</span>
                            	<?php echo $obj->cognome ? $obj->cognome : '<span class="vuoto">vuoto</span>'; ?></span>
                            <span class="middle"><span class="subtitle">Telefono:</span>
								<?php echo $obj->telefono2 ? $obj->telefono2 : '<span class="vuoto">vuoto</span>'; ?></span><br>
                            <span class="left"><span class="subtitle">Data di nascita:</span>
								<?php echo $obj->datanascita ? date_translate(date('j F Y',$obj->datanascita)) : '<span class="vuoto">vuoto</span>'; ?></span><br>
                            <span class="left"><span class="subtitle">Data di registrazione:</span>
								<?php echo $obj->dataregistrazione ? date_translate(date('j F Y',$obj->dataregistrazione)) : '<span class="vuoto">vuoto</span>'; ?></span><br>
                            	<?php
								if($obj->utente != $_SESSION['utente']){
									if(!(array_search('eliminare utenti', $this->privilegi)=== false)){
										echo '<a onclick="return confirm(\'Stai per eliminare definitivamente ',$obj->utente,'.\nSei sicuro di procedere?\')" class="right" href="index.php?q=gestione_utenti&amp;a=elimina&amp;id=',$obj->utente,'">Elimina</a>';
									}
									if(!(array_search('privilegiare utenti', $this->privilegi)=== false)) {
										if($obj->attivo)
											echo '<a class="right" href="index.php?q=gestione_utenti&amp;a=disattiva&amp;id=',$obj->utente,'">Disattiva</a> ';
										else
											echo '<a class="right" href="index.php?q=gestione_utenti&amp;a=attiva&amp;id=',$obj->utente,'">Attiva</a> ';
										?>
                                        <form action="index.php" method="get">
                                        	<p>
                                            	<label for="nuova_categoriautente_<?php echo $obj->utente ?>"> Fai diventare: </label>
                                        		<input type="hidden" name="q" value="gestione_utenti" >
                                                <input type="hidden" name="a" value="cambia_profilo" >
                                                <input type="hidden" name="id" value="<?php echo $obj->utente ?>">
                                                <select name="nuova_categoriautente" id="nuova_categoriautente_<?php echo $obj->utente ?>">
                                                <?php
													foreach($categorie_utenti as $cat){
														if($cat == $obj->categoriautente)
															echo "<option value=\"$cat\" selected=\"selected\">$cat</option>";	
														else
															echo "<option value=\"$cat\">$cat</option>";
													}
                                                ?>
                                                </select>
                                                <input type="submit" value="Ok">
                                            </p>
                                        </form>
                                        <?php
									}
								}
                                ?>
                        </p>
                    <div>
                    	
                    </div>
                </div>
                <script type="text/javascript">
					close_utente('<?php echo $obj->utente ?>');
					<?php if(isset($this->id) && $this->id == $obj->utente)
						 echo 'show_utente(\'',$obj->utente,'\')'; ?>
                </script>
            <?php
            }
        ?>
        </div>
	<?php
    }
	
	function content2(){
		require_once('php/modules.php');
		gestisci_module('gestione_utenti', $this->privilegi);
	}
	
	//Stampa un messaggio di errore, perchè l'utente non ha il privilegio necessario a visualizzare un determinato pannello.
	private function print_error(){ ?>
		<h1>Accesso Negato</h1>
        <p>
        	Non possiedi i privilegi necessari per visualizzare la pagina che stavi cercando di raggiungere.<br>
            <span style="font-size:small">Torna alla <a href="index.php">home</a></span>
        </p>
	<?php }
}
?>