<?php
require_once("php/page_interface.php");
require_once("php/dbConn.php");
require_once("php/functions.php");
require_once("php/modules.php");

//Questa classe definisce l'Home Page. Per un dettaglio sulle funzioni vedere i commenti all'interfaccia IPage
class profilo implements IPage{
	private $standard_layout;
	private $title;
	private $color;
	private $db;
	private $cerca;
	private $auth;
	private $privilegi;
	
	function profilo(){
		//Incapsula i valori di tutti i parametri get in omonimi campi dell'oggetto.
		foreach($_GET as $k=>$v){
			$this->$k = $v;
		}
		$this->standard_layout = true;
		$this->title = "Area Riservata";
		$this->db = new DBConn();
		$this->color = "#F60";
		$this->cerca = false;
		$this->auth = array('profilo');
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
	
	function color(){
		return $this->color;
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
				$this->descrizione_privilegi[] = $obj->descrizione;
			};
			return $found;
		}
		else return false;
	}
	
	function head_tags() { ?>
	<?php }
	
	function css_rules(){
		gestisci_css();
	?> 
    	#profilo{
        	margin-left:16px;
            line-height: 1.5em;
		}
        
    	#profilo .campo{
        	color: <?php echo $this->color(); ?>;
            font-size: 1.2em;
            letter-spacing: 1px;
            margin-right: 8px;
        }
        
        #profilo .none{
        	display:none;
        }
        
        #profilo form > div{
        	displiy:inline;
        }
        
        #profilo a{
            font-size: 0.8em;
            margin-left: 5px;
        }
	<?php 
	}
	
	function content(){
		$this->db->connect();
		$obj = $this->db->get_utente($_SESSION['utente']);
		$mod = '';
		if(isset($_POST['noscript'])){
			require_once('ajax/change_profile.php');
			$mod = $_POST['column'];
			
			//Assume valore -1 se errore, 1 se ok, 0 se eliminato
			$mod_error = stristr($output, "Errore") ? true : false;
		}
		?>
		<script type="text/javascript">
			var previous_nome = '<?php echo $obj->nome; ?>';
			var previous_cognome = '<?php echo $obj->cognome; ?>';
			var previous_telefono = '<?php echo $obj->telefono; ?>';
			var previous_telefono2 = '<?php echo $obj->telefono2; ?>';
			var previous_indirizzo = '<?php echo $obj->indirizzo; ?>';
			var previous_datanascita = '<?php echo date_translate(date('j F Y', $obj->datanascita)); ?>';
			
			//Controlla se il nome può essere modificato in base ai criteri di validazione dei form, e lo modifica tramite AJAX
			function check_nome(){
				var value = trim(document.getElementById("nome_input").value);
				var notify = document.getElementById("nome_notify");
				if(value == previous_nome){
					notify.style.display = "block";
					notify.innerHTML = "Inserisci un valore diverso dal precedente, altrimenti clicca su Annulla.";
					return false;
				}
				else if(badChars.test(value)){
					notify.style.display = "block";
					notify.innerHTML = "Caratteri non consentiti: | + -- = &lt; &gt; != ( ) % @ # *";
					return false;
				}
				else{
					new Ajax("POST", "ajax/change_profile.php", "column=nome&value="+value, wait, end, no_retry, 'nome');
					return false;
				}
			}
			
			//Controlla se il cognome può essere modificato in base ai criteri di validazione dei form, e lo modifica tramite AJAX
			function check_cognome(){
				var value = trim(document.getElementById("cognome_input").value);
				var notify = document.getElementById("cognome_notify");
				if(value == previous_cognome){
					notify.style.display = "block";
					notify.innerHTML = "Inserisci un valore diverso dal precedente, altrimenti clicca su Annulla.";
					return false;
				}
				else if(badChars.test(value)){
					notify.style.display = "block";
					notify.innerHTML = "Caratteri non consentiti: | + -- = &lt; &gt; != ( ) % @ # *";
					return false;
				}
				else{
					new Ajax("POST", "ajax/change_profile.php", "column=cognome&value="+value, wait, end, no_retry, 'cognome');
					return false;
				}
			}
			
			//Controlla se il telefono può essere modificato in base ai criteri di validazione dei form, e lo modifica tramite AJAX
			function check_telefono(){
				var value = trim(document.getElementById("telefono_input").value);
				var notify = document.getElementById("telefono_notify");
				if(value == previous_telefono){
					notify.style.display = "block";
					notify.innerHTML = "Inserisci un valore diverso dal precedente, altrimenti clicca su Annulla.";
					return false;
				}
				else if(isNaN(value)){
					notify.style.display = "block";
					notify.innerHTML = "Inserisci un numero di telefono valido.";
					return false;
				}
				else{
					new Ajax("POST", "ajax/change_profile.php", "column=telefono&value="+value, wait, end, no_retry, 'telefono');
					return false;
				}
			}
			
			//Controlla se il telefono2 può essere modificato in base ai criteri di validazione dei form, e lo modifica tramite AJAX
			function check_telefono2(){
				var value = trim(document.getElementById("telefono2_input").value);
				var notify = document.getElementById("telefono2_notify");
				if(value == previous_telefono2){
					notify.style.display = "block";
					notify.innerHTML = "Inserisci un valore diverso dal precedente, altrimenti clicca su Annulla.";
					return false;
				}
				else if(isNaN(value)){
					notify.style.display = "block";
					notify.innerHTML = "Inserisci un numero di telefono valido.";
					return false;
				}
				else{
					new Ajax("POST", "ajax/change_profile.php", "column=telefono2&value="+value, wait, end, no_retry, 'telefono2');
					return false;
				}
			}
			
			//Controlla se l'indirizzo può essere modificato in base ai criteri di validazione dei form, e lo modifica tramite AJAX
			function check_indirizzo(){
				var value = trim(document.getElementById("indirizzo_input").value);
				var notify = document.getElementById("indirizzo_notify");
				if(value == previous_indirizzo){
					notify.style.display = "block";
					notify.innerHTML = "Inserisci un valore diverso dal precedente, altrimenti clicca su Annulla.";
					return false;
				}
				else if(badChars.test(value)){
					notify.style.display = "block";
					notify.innerHTML = "Caratteri non consentiti: | + -- = &lt; &gt; != ( ) % @ # *";
					return false;
				}
				else{
					new Ajax("POST", "ajax/change_profile.php", "column=indirizzo&value="+value, wait, end, no_retry, 'indirizzo');
					return false;
				}
			}
			
			//Non controlla la data di nascita perchè il form viene creato ad hoc via php. I controlli importanti saranno effettuati via php.
			function check_datanascita(){
				var anno = document.getElementById("datanascita_input_anno").value;
				var mese = document.getElementById("datanascita_input_mese").value;
				var giorno = document.getElementById("datanascita_input_giorno").value;
				new Ajax("POST", "ajax/change_profile.php", "column=datanascita&anno="+anno+"&mese="+mese+"&giorno="+giorno, wait, end, no_retry, 'datanascita');
				return false;
			}
			
			//Modifica il valore predefinito per l'id
			function change_previous(str, id){
				switch(id){
					case 'nome':
						previous_nome = str;
						break;
					case 'cognome':
						previous_cognome = str;
						break;
					case 'telefono':
						previous_telefono = str;
						break;
					case 'telefono2':
						previous_telefono2 = str;
						break;
					case 'indirizzo':
						previous_indirizzo = str;
						break;
					case 'datanascita':
						previous_datanascita = str;
						break;
				}
			}
			
			//Ritorna il valore predefinito per l'id
			function previous(id){
				switch(id){
					case 'nome':
						return previous_nome
					case 'cognome':
						return previous_cognome;
					case 'telefono':
						return previous_telefono;
					case 'telefono2':
						return previous_telefono2;
					case 'indirizzo':
						return previous_indirizzo;
					case 'datanascita':
						return previous_datanascita;
				}
			}
			
			//Fa partire l'animazione di attesa per AJAX
			function wait(id){
				deactivate(id);
				var p = document.getElementById(id);
				var a = document.getElementById(id+'_a');
				a.innerHTML = '';
				p.innerHTML = '<img src="img/loading.gif" alt="loading">';
			}
			
			function no_retry(id){
				var ajax = new Object();
				ajax.responseText = 'Errore di connessione. Riprovare';
				end(ajax, 'telefono2');
			}
			
			//Esegue la modifica del form in base al risultato della richiesta
			function end(ajax, id){
				var p = document.getElementById(id);
				var form = document.getElementById(id+'_form');
				var a = document.getElementById(id+'_a');
				var notify = document.getElementById(id+'_notify');
				if(ajax.responseText.indexOf("Errore") == -1){
					if(ajax.responseText.indexOf("Eliminato") == -1){
						p.innerHTML = ajax.responseText;
						a.innerHTML = "modifica";
						notify.style.display = "none";
						notify.innerHTML = "";
						change_previous(ajax.responseText, id);
					}
					else{
						change_previous('', id);
						a.innerHTML = "+aggiungi";
						p.innerHTML = "";
					}
				}
				else{
					notify.style.display = 'block';
					notify.innerHTML = ajax.responseText;
					p.innerHTML = previous(id);
					a.innerHTML = "modifica";
				}
			}
			
			//fa comparire un form per modificare i valori
			function activate(id){
				var p = document.getElementById(id);
				var form = document.getElementById(id + '_form');
				var a = document.getElementById(id + '_a');
				var notify = document.getElementById(id+'_notify');
				p.style.display = "none";
				a.style.display = "none";
				notify.style.display = "none";
				notify.innerHTML = "";
				form.style.display = "inline-block";
				if(id != 'datanascita'){
					var input = document.getElementById(id + '_input');
					input.value = previous(id);
					input.focus();
					input.select();
				}
			}
			
			//fa scomparire il form per modificare i valori
			function deactivate(id){
				var p = document.getElementById(id);
				var form = document.getElementById(id + '_form');
				var a = document.getElementById(id + '_a');
				var notify = document.getElementById(id+'_notify');
				notify.style.display = "none";
				notify.innerHTML = "";
				p.style.display = "inline";
				a.style.display = "inline";
				form.style.display = "none";
			}
			
			function show_privilegi(){
				var a = document.getElementById('privilegi_a');
				var ul = document.getElementById('privilegi_ul');
				a.innerHTML = '-nascondi privilegi';
				ul.style.display = 'block';
				a.style.display = 'inline';
				privilegi = true;
			}
			
			function close_privilegi(){
				var a = document.getElementById('privilegi_a');
				var ul = document.getElementById('privilegi_ul');
				a.innerHTML = '+mostra privilegi';
				ul.style.display = 'none';
				a.style.display = 'inline';
				privilegi = false;
			}
			
			function toggle_privilegi(){
				if(privilegi)
					close_privilegi();
				else
					show_privilegi();
			}
			
		</script>
        
        <h1>Dettagli del profilo</h1>
        <div id="profilo">
                <span class="campo">Username:</span> <?php echo $obj->utente; ?><br>
                <span class="campo">E-mail:</span> <?php echo $obj->email; ?><br>
                
                <div class="notify" id="nome_notify"><?php if($mod == 'nome' && $mod_error) echo $output; ?></div>
                <span class="campo">Nome:</span> 
                <span id="nome"><?php echo $obj->nome; ?></span>
                <a id="nome_a" class="none" onclick="activate('nome');"><?php echo ($obj->nome != NULL) ? 'modifica' : '+aggiungi'?></a>
                <form id="nome_form" action="index.php?q=profilo&amp;panel=profilo" method="post" onsubmit="return check_nome();">
                    <div>
                        <input id="nome_input" name="value" type="text" value="<?php echo $obj->nome; ?>" />
                        <input name="nome_submit" type="submit" value="Salva" />
                        <input type="button" onclick="deactivate('nome');" value="Annulla" />
                        <input type="hidden" name="column" value="nome" />
                        <input type="hidden" name="noscript" />
                    </div>
                </form><br>
                
                <div class="notify" id="cognome_notify"><?php if($mod == 'cognome' && $mod_error) echo $output; ?></div>
                <span class="campo">Cognome:</span> 
                <span id="cognome"><?php echo $obj->cognome; ?></span>
                <a id="cognome_a" class="none" onclick="activate('cognome');"><?php echo ($obj->cognome != NULL) ? 'modifica' : '+aggiungi'?></a>
                <form id="cognome_form" action="index.php?q=profilo&amp;panel=profilo" method="post" onsubmit="return check_cognome();">
                    <div>
                        <input id="cognome_input" name="value" type="text" value="<?php echo $obj->cognome; ?>" />
                        <input name="cognome_submit" type="submit" value="Salva" />
                        <input type="button" onclick="deactivate('cognome');" value="Annulla" />
                        <input type="hidden" name="column" value="cognome" />
                        <input type="hidden" name="noscript" />
                    </div>
                </form><br>
                
                <div class="notify" id="telefono_notify"><?php if($mod == 'telefono' && $mod_error) echo $output; ?></div>
                <span class="campo">Cellulare:</span> 
                <span id="telefono"><?php echo $obj->telefono; ?></span>
                <a id="telefono_a" class="none" onclick="activate('telefono');"><?php echo ($obj->telefono != NULL) ? 'modifica' : '+aggiungi'?></a>
                <form id="telefono_form" action="index.php?q=profilo&amp;panel=profilo" method="post" onsubmit="return check_telefono();">
                    <div>
                        <input id="telefono_input" name="value" type="text" value="<?php echo $obj->telefono; ?>" />
                        <input name="telefono_submit" type="submit" value="Salva" />
                        <input type="button" onclick="deactivate('telefono');" value="Annulla" />
                        <input type="hidden" name="column" value="telefono" />
                        <input type="hidden" name="noscript" />
                    </div>
                </form><br>
                
                <div class="notify" id="telefono2_notify"><?php if($mod == 'telefono2' && $mod_error) echo $output; ?></div>
                <span class="campo">Telefono:</span> 
                <span id="telefono2"><?php echo $obj->telefono2; ?></span>
                <a id="telefono2_a" class="none" onclick="activate('telefono2');"><?php echo ($obj->telefono2 != NULL) ? 'modifica' : '+aggiungi'?></a>
                <form id="telefono2_form" action="index.php?q=profilo&amp;panel=profilo" method="post" onsubmit="return check_telefono2();">
                    <div>
                    	<input id="telefono2_input" name="value" type="text" value="<?php echo $obj->telefono2; ?>" />
                        <input name="telefono2_submit" type="submit" value="Salva" />
                        <input type="button" onclick="deactivate('telefono2');" value="Annulla" />
                        <input type="hidden" name="column" value="telefono2" />
                        <input type="hidden" name="noscript" />
					</div>
                </form><br>
                
                <div class="notify" id="indirizzo_notify"><?php if($mod == 'indirizzo' && $mod_error) echo $output; ?></div>
                <span class="campo">Indirizzo:</span> 
                <span id="indirizzo"><?php echo $obj->indirizzo; ?></span>
                <a id="indirizzo_a" class="none" onclick="activate('indirizzo');"><?php echo ($obj->indirizzo != NULL) ? 'modifica' : '+aggiungi'?></a>
                <form id="indirizzo_form" action="index.php?q=profilo&amp;panel=profilo" method="post" onsubmit="return check_indirizzo();">
                    <div>
                        <input id="indirizzo_input" name="value" type="text" value="<?php echo $obj->indirizzo; ?>" />
                        <input name="indirizzo_submit" type="submit" value="Salva" />
                        <input type="button" onclick="deactivate('indirizzo');" value="Annulla" />
                        <input type="hidden" name="column" value="indirizzo" />
                        <input type="hidden" name="noscript" />
                    </div>
                </form><br>
                
                <div class="notify" id="datanascita_notify"><?php if($mod == 'datanascita' && $mod_error) echo $output; ?></div>
                <span class="campo">Data di nascita:</span> 
                <span id="datanascita"><?php echo date_translate(date('j F Y', $obj->datanascita)); ?></span>
                <a id="datanascita_a" class="none" onclick="activate('datanascita');"><?php echo ($obj->datanascita != NULL) ? 'modifica' : '+aggiungi'?></a>
                <form id="datanascita_form" action="index.php?q=profilo&amp;panel=profilo" method="post" onsubmit="return check_datanascita();">
                    <div>
                    	<select id="datanascita_input_giorno" name="giorno">
                        	<?php
							for($i=1; $i<32; $i++){
								if((int)date('j', $obj->datanascita) == $i)
									echo "<option value=\"$i\" selected=\"selected\">$i</option>";
								else
									echo "<option value=\"$i\">$i</option>";
							}
							?>
                        </select>
                        <select id="datanascita_input_mese" name="mese">
                        	<?php
							for($i=1; $i<13; $i++)
								if((int)date('n', $obj->datanascita) == $i)
									echo "<option value=\"$i\" selected=\"selected\">",date_inttomese($i),"</option>";
								else
									echo "<option value=\"$i\">",date_inttomese($i),"</option>";
							?>
                        </select>
                        <select id="datanascita_input_anno" name="anno">
                        	<?php 
							for($i=(date("Y")-13); $i>=1901; $i--)
								if((int)date('Y', $obj->datanascita) == $i)
									echo "<option value=\"$i\" selected=\"selected\">$i</option>";
								else
									echo "<option value=\"$i\">$i</option>";
							?>
                        </select>
                        <input name="datanascita_submit" type="submit" value="Salva" />
                        <input type="button" onclick="deactivate('datanascita');" value="Annulla" />
                        <input type="hidden" name="column" value="datanascita" />
                        <input type="hidden" name="noscript" />
                    </div>
                </form><br>
				<span class="campo">Profilo:</span> <?php echo $obj->categoriautente; ?>
                <a id="privilegi_a" class="none" onclick="toggle_privilegi();"></a>
                <ul id="privilegi_ul">
                	<?php 
					for($i=0; $i<count($this->descrizione_privilegi); $i++){
						echo '<li>',$this->descrizione_privilegi[$i],'</li>';
					}?>
                </ul>
                
                <script type="text/javascript">
					deactivate('nome');
					deactivate('cognome');
					deactivate('telefono');
					deactivate('telefono2');
					deactivate('indirizzo');
					deactivate('datanascita');
					close_privilegi();
					var privilegi = false;
				</script>
        </div>
        <?php
		$this->db->disconnect();
	}
	
	function content2(){
		gestisci_module('profilo', $this->privilegi);
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