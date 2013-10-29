<?php
require_once("php/page_interface.php");
require_once("php/dbConn.php");
require_once("php/functions.php");
require_once("php/modules.php");

//Per un dettaglio sulle funzioni vedere i commenti all'interfaccia IPage
class gestione_privilegi implements IPage{
	private $standard_layout;
	private $title;
	private $db;
	private $cerca;
	private $auth;
	private $privilegi;
	
	function gestione_privilegi(){
		//Incapsula i valori di tutti i parametri get in omonimi campi dell'oggetto.
		foreach($_GET as $k=>$v){
			$this->$k = $v;
		}
		$this->standard_layout = true;
		$this->title = "Area Riservata";
		$this->db = new DBConn();
		$this->cerca = false;
		$this->auth = array('categorie utenti');
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
        
        .nuovo {
            font-size: 16px;
            padding-bottom: 4px;
            line-height: 30px;
            margin-left: 10px;
        }
        
        .none{
        	display: none;
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
        
        .nuovo_profilo{
        	padding: 10px;
        }
        
        .small{
        	color: #777;
            font-size: 12px;
        }
	<?php 
	}
	
	function content(){
		$res_categorie = $this->db->seleziona_categorie_utenti($this->priorita-1);
		$categorie_utenti = NULL;
		$privilegi_categorie = NULL;
		while($obj_categorie = $this->db->fetch_object()){
			$categorie_utenti[] = $obj_categorie->categoriautente;
		}
		
		$response = false;
		$bad_chars = "/[\|\+=<>()%@#\*]|(!=)|-{2}/";
		if(isset($this->a) && isset($this->id) && isset($this->privilegio)){
			switch($this->a){
				case 'aggiungi':
					if(trim($this->id) != '' && trim($this->privilegio) != ''){
						if($this->db->aggiungi_privilegio($this->id, $this->privilegio))
							$response = '<div class="green">Privilegio aggiunto.</div>';
						else
							$response = '<div class="red">Operazione non eseguita: '.$this->db->error().'</div>';
					}
					else
						$response = '<div class="red">Operazione non eseguita: si &egrave; verificato un errore nella richiesta</div>';
					break;
				case 'rimuovi':
					if(trim($this->id) != '' && trim($this->privilegio) != ''){
						if($this->db->rimuovi_privilegio($this->id, $this->privilegio))
							$response = '<div class="yellow">Privilegio rimosso correttamente.</div>';
						else
							$response = '<div class="red">Operazione non eseguita: '.$this->db->error().'</div>';
					}
					else
						$response = '<div class="red">Operazione non eseguita: si &egrave; verificato un errore nella richiesta</div>';
					break;
				case 'nuovo':
					if(isset($this->priorita_profilo) && trim($this->priorita_profilo) != '' && trim($this->id) != ''){
						if(!preg_match($bad_chars, $this->id)){
							if($this->priorita_profilo < $this->priorita){
								if($this->db->inserisci_categoriautente($this->id, $this->priorita_profilo))
									$response = '<div class="green">Profilo creato correttamente.</div>';
								else
									$response = '<div class="red">Operazione non eseguita: '.$this->db->error().'</div>';
							}
							else
								$response = '<div class="red">Operazione non eseguita: La priorit&agrave; massima che puoi assegnare &egrave;'.($this->priorita -1).'</div>';
						}
						else
							$response = '<div class="red">Operazione non eseguita: Caratteri non consentiti: | + -- = &lt; &gt; != ( ) % @ # *</div>';
					}
					else
						$response = '<div class="red">Operazione non eseguita: si &egrave; verificato un errore nella richiesta</div>';
					break;
				case 'elimina_profilo':
					if(trim($this->id) != ''){
						if(trim($this->id) != 'admin' && trim($this->id) != 'guest'){
							if(!(isset($this->ha_utenti) && $this->ha_utenti)){
								if($this->db->elimina_categoriautente($this->id))
									$response = '<div class="yellow">Profilo eliminato correttamente.</div>';
								else
									$response = '<div class="red">Operazione non eseguita: '.$this->db->error().'</div>';
							}
							else{
								if(isset($this->a_utenti) && trim($this->a_utenti) != ''){
									if($this->a_utenti == 'elimina_utenti'){
										if($this->db->delete('utenti', 'categoriautente = '.$this->db->escape($this->id))){
											if($this->db->elimina_categoriautente($this->id))
												$response = '<div class="yellow">Ho eliminato tutti gli utenti che appartenevano al profilo '.$this->id.'<br>Profilo eliminato correttamente.</div>';
											else
												$response = '<div class="red">Ho eliminato tutti gli utenti che appartenevano al profilo '.$this->id.', ma non sono riuscito a eliminare il profilo: '.$this->db->error().'</div>';
										}
										else 
											$response = '<div class="red">Operazione non eseguita: '.$this->db->error().'</div>';
									}
									else{
										$trovato = false;
										foreach($categorie_utenti as $cat){
											if($this->a_utenti == $cat){
												$trovato = true;
												if($this->db->sposta_utenti($this->id, $cat)){
													if($this->db->elimina_categoriautente($this->id))
														$response = '<div class="yellow">Ho spostato tutti gli utenti che appartenevano al profilo '.$this->id.'<br>Profilo eliminato correttamente.</div>';
													else
														$response = '<div class="red">Ho spostato tutti gli utenti che appartenevano al profilo '.$this->id.', ma non sono riuscito a eliminare il profilo: '.$this->db->error().'</div>';
												}
												else
													$response = '<div class="red">Operazione non eseguita: '.$this->db->error().'</div>';
											}
										}
										if(!$trovato)
											$response = '<div class="red">Operazione non eseguita: si &egrave; verificato un errore nella richiesta</div>';
									}
								}
							}
						}
						else
							$response = '<div class="red">Operazione non eseguita: Non puoi eliminare i profili \'admin\' e \'guest\'!</div>';
					}
					else
						$response = '<div class="red">Operazione non eseguita: si &egrave; verificato un errore nella richiesta</div>';
					break;
				default:
					$response = '<div class="red">Operazione non eseguita: si &egrave; verificato un errore nella richiesta</div>';
					break;
			}
		}
		
		$res_categorie = $this->db->seleziona_categorie_utenti($this->priorita-1);
		$categorie_utenti = NULL;
		$privilegi_categorie = NULL;
		while($obj_categorie = $this->db->fetch_object()){
			$categorie_utenti[] = $obj_categorie->categoriautente;
		}
		
		if(isset($_SESSION['seleziona_gestione_privilegi']) && array_search($_SESSION['seleziona_gestione_privilegi'], $categorie_utenti) === false)
			$_SESSION['seleziona_gestione_privilegi'] = NULL;
		foreach($categorie_utenti as $cat){
			if(isset($this->$cat)){
				$_SESSION['seleziona_gestione_privilegi'] = $cat;
			}
		}
		if(!isset($_SESSION['seleziona_gestione_privilegi'])){
			$_SESSION['seleziona_gestione_privilegi'] = 'guest';
		}
		$seleziona = $_SESSION['seleziona_gestione_privilegi'];
		foreach($categorie_utenti as $cat){
			if($seleziona == $cat){
				$privilegi_res = $this->db->seleziona_privilegi($cat);
				while($obj_privilegi = $this->db->fetch_object()){
					$privilegi_categorie[$obj_privilegi->privilegio] = $obj_privilegi->descrizione;
				}
			}
		}
		
		?>
        <script type="text/javascript">
        var opened = true;
		
		function conferma(){
			return confirm('Stai per eliminare un\'intera categoria di utenti! Sei sicuro?');	
		}
		function open_nuovo_profilo(){
			var form = document.getElementById('nuovo_profilo');
			var a = document.getElementById('nuovo');
			a.style.display = 'inline';
			form.style.display = 'block';
			opened = true;
		}
		function close_nuovo_profilo(){
			var form = document.getElementById('nuovo_profilo');
			var a = document.getElementById('nuovo');
			a.style.display = 'inline';
			form.style.display = 'none';
			opened = false;
		}
		function toggle_nuovo_profilo(){
			if(opened)
				close_nuovo_profilo();
			else
				open_nuovo_profilo();
		}
        </script>
        
		<h1>Gestione Privilegi</h1>
        <form action="index.php" method="GET">
            	<p>Gestisci Profili: 
                    <input type="hidden" name="q" value="gestione_privilegi" >
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
        <a class="nuovo none" id="nuovo" onclick="toggle_nuovo_profilo()">+nuovo profilo</a>
        <?php 
		if($seleziona != 'guest' && $seleziona != 'superuser' && $seleziona != 'admin'){
			if(!$this->db->categoria_ha_utenti($seleziona)){?>
				<a class="nuovo" href="index.php?q=gestione_privilegi&amp;a=elimina_profilo&amp;privilegio=no&amp;id=<?php echo $seleziona ?>" onclick="return conferma()">-elimina profilo '<?php echo $seleziona ?>'</a>
			<?php
            }
			else{?>
            	<script type="text/javascript">
					var elimina_opened = false;
					
					function open_elimina(){
						var form = document.getElementById('elimina_profilo');
						var a = document.getElementById('elimina');
						a.style.display = 'inline';
						form.style.display = 'block';
						elimina_opened = true;
					}
					function close_elimina(){
						var form = document.getElementById('elimina_profilo');
						var a = document.getElementById('elimina');
						a.style.display = 'inline';
						form.style.display = 'none';
						elimina_opened = false;
					}
					function toggle_elimina(){
						if(elimina_opened)
							close_elimina();
						else
							open_elimina();
					}
				</script>
				<a id="elimina" class="nuovo null" onclick="toggle_elimina()">-elimina profilo</a>
                <div class="nuovo_profilo" id="elimina_profilo">
                	<form action="index.php" onsubmit="return conferma()">
                        <div>
                            Eliminazione del proflio <?php echo $seleziona ?><br>
                            <input type="hidden" name="q" value="gestione_privilegi" >
                            <input type="hidden" name="a" value="elimina_profilo" >
                            <input type="hidden" name="id" value="<?php echo $seleziona ?>" >
                            <input type="hidden" name="ha_utenti" value="1">
                            <span class="small">Il  profilo che vuoi eliminare contiene utenti. Cosa faccio di questi utenti?</span><br>
                            <select name="a_utenti">
                            	<?php
									foreach($categorie_utenti as $cat){
										if($cat != $seleziona)
											echo '<option value="',$cat,'">Fai diventare ',$cat,'</option>';
									}
								?>
                            	<option value="elimina_utenti">Eliminali</option>
                            </select> <input type="submit" name="privilegio" value="Elimina Profilo">
                        </div>
                    </form>
                </div>
                <script type="text/javascript">close_elimina();</script>
			<?php
            }
		}
		?>
        <div class="nuovo_profilo" id="nuovo_profilo">
        	<form action="index.php">
            	<div>
                	Inserimento nuovo profilo<br>
                	<input type="hidden" name="q" value="gestione_privilegi" >
                    <input type="hidden" name="a" value="nuovo" >
                    <label for="nome_profilo">Nome del Profilo</label>
                    <input type="text" id="nome_profilo" name="id" value="" >
                    <label for="priorita">Priorit&agrave; del Profilo</label>
                    <select id="priorita" name="priorita_profilo">
                    	<?php 
							for($i=1; $i<$this->priorita; $i++){
								echo '<option value="',$i,'">',$i,'</option>';
							}
						?>
                    </select>
                    <input type="submit" name="privilegio" value="Inserisci"><br>
                    <span class="small">La priorit&agrave; indica il valore di importanza del profilo in una scala da 1 a 100.<br>
                    I valori pi&ugrave; alti sono quelli pi&ugrave; importanti, mentre puoi assegnare solo valori pi&uacute; bassi del tuo livello di priorit&agrave;.<br>
                    &Egrave; opportuno scegliere in modo adeguato la priorit&agrave;, tenendo presente che utenti con priorit&agrave; minore non possono modificare gli utenti con priorit&agrave; maggiore.</span>
                </div>
            </form>
        </div>
        <div style="border-top: 1px solid #888;">
        	<p style="text-align:center" class="small">Priorit&agrave; del profilo '<?php echo $seleziona; ?>': <?php echo $this->db->get_priorita($seleziona); ?></p>
            <h3  style="text-align:center;">Il profilo '<?php echo $seleziona ?>' possiede i seguenti privilegi:</h3>
            <p style="text-align:center; font-size:14px">
                <?php
					if($privilegi_categorie){
						foreach($privilegi_categorie as $privilegio => $descrizione){
							echo $descrizione , ' <a href="index.php?q=gestione_privilegi&amp;a=rimuovi&amp;privilegio=',urlencode($privilegio),'&amp;id=',urlencode($seleziona),'">-rimuovi</a><br>';
						}
					}
                ?>
            </p>
            <?php
				$res_privilegi_all = $this->db->seleziona_privilegi(NULL);
				$privilegi_non_posseduti = false;
				while($obj_privilegi_all = $this->db->fetch_object()){
					$non_posseduto = true;
					if($privilegi_categorie){
						foreach($privilegi_categorie as $priv_cat => $priv_desc){
							if($priv_cat == $obj_privilegi_all->privilegio){
								$non_posseduto = false;
								break;
							}
						}
					}
					if($non_posseduto)
						$privilegi_non_posseduti[$obj_privilegi_all->privilegio] = $obj_privilegi_all->descrizione;
				}
				if($privilegi_non_posseduti){
					echo '	
					<h3  style="text-align:center;">Privilegi che si possono aggiungere:</h3>
            		<p style="text-align:center; font-size:14px">';
					foreach($privilegi_non_posseduti as $non_privilegio => $non_descrizione){
						echo $non_descrizione , ' <a href="index.php?q=gestione_privilegi&amp;a=aggiungi&amp;privilegio=',urlencode($non_privilegio),'&amp;id=',urlencode($seleziona),'">+aggiungi</a><br>';
					}
					echo '</p>';
				}
			?>
        </div>
        <script type="text/javascript">close_nuovo_profilo();</script>
	<?php
    }
	
	function content2(){
		require_once('php/modules.php');
		gestisci_module('gestione_privilegi', $this->privilegi);
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