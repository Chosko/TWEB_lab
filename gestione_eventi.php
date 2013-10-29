<?php
require_once("php/page_interface.php");
require_once("php/dbConn.php");
require_once("php/functions.php");
require_once("php/modules.php");

//Questa classe definisce l'Home Page. Per un dettaglio sulle funzioni vedere i commenti all'interfaccia IPage
class gestione_eventi implements IPage{
	private $standard_layout;
	private $title;
	private $color;
	private $db;
	private $cerca;
	private $auth;
	private $privilegi;
	
	function gestione_eventi(){
		//Incapsula i valori di tutti i parametri get in omonimi campi dell'oggetto.
		foreach($_GET as $k=>$v){
			$this->$k = $v;
		}
		$this->standard_layout = true;
		$this->title = "Area Riservata";
		$this->db = new DBConn();
		$this->color = "#F60";
		$this->cerca = false;
		$this->auth = array('eventi');
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
    	.eventi_all h1{
            margin: 0 10px;
            font-size: 25px;
            text-align: left;
            font-weight: normal;
            letter-spacing: 1px;
            color: #000;
            padding: 0 4px;
        }
        
        .eventi_all .evento:first-child{
        	border-top: 1px solid #CCC;
        }
        
        .eventi_all .evento{
        	border-bottom: 1px solid #CCC;
            padding: 8px 0;
            cursor: pointer;
            display: none;
        }
        
        .eventi_all .evento_full{
        	border-bottom: 1px solid #CCC;
            padding: 0 0;
        }
        
        .eventi_all .evento:hover{
            background-color: #FDB;
        }
        
    	.eventi_all h3{
        	font-size: 20px;
        	color: <?php echo $this->color(); ?>;
           	margin: 0 10px;
            font-weight:normal;
            padding: 0 4px;
        }
        
        .eventi_all p{
        	text-align:justify;
            margin: 10px;
            padding: 0 4px;
        }
        
        .eventi_all .inner{
            background-color: #FDB;
            padding: 8px 0;
            cursor: pointer;
        }
        
        .eventi_all .inner:hover{
            background-color: #FB6;
        }
        
        .small{
        	font-size: 14px;
        }
        
        .text{
        	display: none;
        }
        
        .modifica{
            font-size: 14px;
            float:right;
            display:none;
        }
        
        .nuovo{
            font-size: 16px;
            padding-bottom: 4px;
            line-height: 30px;
        }
        
        .largeinput{
        	width: 100%;
        }
        
        .incima{
            float: right;
            background-color: <?php echo $this->color(); ?>;
            font-size: 16px;
            color: #FFF;
            padding: 3px;
			border-radius: 8px;
            margin-right: 10px;
			box-shadow: 0.9px 0.9px 8px <?php echo $this->color(); ?>;
        }
        
        .categoria{
        	float: right;
            margin-right: 10px;
            font-size: 16px;
            color: #666;
        }
        
        .formelimina{
        	display: inline;
            float: right;
            font-size: 14px;
        }
        
        .elimina{
        	display: inline;
            border: 0;
            margin: 0px 12px 0px 10px;
            padding: 0;
            background-color: transparent;
            font-size: 15px;
        }
        
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
		$mod = false;
		
		if(isset($_POST['noscript'])){
			require_once('ajax/mod_content.php');
			$mod = true;
		}
		?>
    	<script type="text/javascript">
			
			//ritorna l'elemento cercato del form id
			function get_elem(id, elem){
				return document.getElementById(elem+id);
			}
			
			//chiude il riquadro espanso di un evento visualizzato
			function close_evento(id){
				var short = get_elem(id, 'short');
				var full = get_elem(id, 'full');
				var text = get_elem(id, 'text');
				var form = get_elem(id, 'form');
				var a = get_elem(id, 'a');
				var notify = get_elem(id, 'notify');
				notify.style.display = 'none';
				short.style.display = 'block';
				full.style.display = 'none';
				text.style.display = 'none';
				form.style.display = 'none';
				a.style.display = 'none';
			}
			
			//mostra il riquadro espanso di un evento visualizzato
			function show_evento(id){
				var short = get_elem(id, 'short');
				var full = get_elem(id, 'full');
				var text = get_elem(id, 'text');
				var form = get_elem(id, 'form');
				var a = get_elem(id, 'a');
				var notify = get_elem(id, 'notify');
				notify.style.display = 'none';
				short.style.display = 'none';
				full.style.display = 'block';
				text.style.display = 'block';
				form.style.display = 'none';
				a.style.display = 'inline-block';
			}
			
			//mostra il form di modifica di un evento visualizzato
			function mod_evento(id){
				var short = get_elem(id, 'short');
				var full = get_elem(id, 'full');
				var text = get_elem(id, 'text');
				var form = get_elem(id, 'form');
				var a = get_elem(id, 'a');
				var notify = get_elem(id, 'notify');
				notify.style.display = 'none';
				short.style.display = 'none';
				full.style.display = 'block';
				text.style.display = 'none';
				form.style.display = 'block';
				a.style.display = 'none';
			}
			
			//ritorna true se non ci sono notifiche di errore per il form id
			function no_notify(id){
				return !(error['titolo'+id] && error['testo'+id]);
			}
			
			//valida i campi del form id
			function check_form(id){	
				var testo = get_elem(id, 'testo');
				var peso = get_elem(id, 'peso');
				var titolo = get_elem(id, 'titolo');
				var giorno = get_elem(id, 'giorno');
				var mese = get_elem(id, 'mese');
				var anno = get_elem(id, 'anno');
				var ora = get_elem(id, 'ora');
				var minuto = get_elem(id, 'minuto');
				var categoria = get_elem(id, 'categoria');
				var in_cima = get_elem(id, 'in_cima');
				
				if(check_titolo(id) && check_testo(id)){
					if(id == 'insert')
						return true;
					else{
						var post_str = "num_contenuto="+id+"&testo="+testo.value+"&peso="+peso.value+"&titolo="+titolo.value+"&giorno="+giorno.value+"&mese="+mese.value+"&anno="+anno.value+"&categoria="+categoria.value+"&ora="+ora.value+"&minuto="+minuto.value;
						if(in_cima.checked)
							post_str += "&in_cima="+in_cima.checked;
						new Ajax("POST", "ajax/mod_content.php", post_str, wait, end, retry, id);
					}
					return false;
				}
				else
					return false;
			}
			
			function disable(input){
				input.setAttribute('disabled', 'disabled');
			}
			
			function enable(input){
				input.removeAttribute('disabled');
			}
			
			function wait(id){
				var testo = get_elem(id, 'testo');
				var peso = get_elem(id, 'peso');
				var titolo = get_elem(id, 'titolo');
				var giorno = get_elem(id, 'giorno');
				var mese = get_elem(id, 'mese');
				var anno = get_elem(id, 'anno');
				var ora = get_elem(id, 'ora');
				var minuto = get_elem(id, 'minuto');
				var categoria = get_elem(id, 'categoria');
				var in_cima = get_elem(id, 'in_cima');
				var submitspan = get_elem(id, 'submitspan');
				var waitspan = get_elem(id, 'waitspan');
				disable(testo);
				disable(peso);
				disable(titolo);
				disable(giorno);
				disable(mese);
				disable(anno);
				disable(ora);
				disable(minuto);
				disable(categoria);
				disable(in_cima);	
				submitspan.style.display = 'none';
				waitspan.style.display = 'inline';						
			}
			
			function retry(id){
				if(confirm('Si è verificato un errore di connessione. Riprovo a eseguire la richiesta?'))
					check_form(id);
			}
			
			function end(ajax, id){
				//variabili di output ajax
				var output_error;
				var output;
				var output_id;
				var output_titolo;
				var output_testo;
				var output_peso;
				var output_data;
				var output_in_cima;
				var output_categoria;
				var output_orario;
				
				eval(ajax.responseText);
				
				if(id=='insert' && !output_error){
					id = output_id;
					create_evento(id);
				}
				
				//variabili di input
				var testo = get_elem(id, 'testo');
				var peso = get_elem(id, 'peso');
				var titolo = get_elem(id, 'titolo');
				var giorno = get_elem(id, 'giorno');
				var mese = get_elem(id, 'mese');
				var anno = get_elem(id, 'anno');
				var ora = get_elem(id, 'ora');
				var minuto = get_elem(id, 'minuto');
				var categoria = get_elem(id, 'categoria');
				var in_cima = get_elem(id, 'in_cima');
				
				//variabili di testo
				var shortdata = get_elem(id, 'short_data');
				var shorttitolo = get_elem(id, 'short_titolo');
				var fulltesto = get_elem(id, 'full_testo');
				var fulltitolo = get_elem(id, 'full_titolo');
				var fulldata = get_elem(id, 'full_data');
				var shortcategoria = get_elem(id, 'short_categoria');
				var fullcategoria = get_elem(id, 'full_categoria');
				var shortincima = get_elem(id, 'short_incima');
				var fullincima = get_elem(id, 'full_incima');
				
				//variabili di notify
				var notify = get_elem(id, 'notify');
				var notify_titolo = get_elem(id, 'notify_titolo');
				var notify_data = get_elem(id, 'notify_data');
				var notify_categoria = get_elem(id, 'notify_categoria');
				var notify_testo = get_elem(id, 'notify_testo');
				var notify_peso = get_elem(id, 'notify_peso');
				var notify_in_cima = get_elem(id, 'notify_in_cima');
				var notify_orario = get_elem(id, 'notify_orario');
				var notify_php = get_elem(id, 'notify_php');
				
				var submitspan = get_elem(id, 'submitspan');
				var waitspan = get_elem(id, 'waitspan');
				
				if(output_error){
					notify.style.display = 'block';
					notify_titolo.innerHTML = output_titolo;
					notify_testo.innerHTML = output_testo;
					notify_peso.innerHTML = output_peso;
					notify_data.innerHTML = output_data;
					notify_categoria.innerHTML = output_categoria;
					notify_orario.innerHTML = output_orario;
					notify_php.innerHTML = output;
				}
				else{
					shortdata.innerHTML = output_data;
					shorttitolo.innerHTML = output_titolo;
					fulltesto.innerHTML = output_testo;
					fulltitolo.innerHTML = output_titolo;
					fulldata.innerHTML = output_data;
					shortcategoria.innerHTML = output_categoria;
					fullcategoria.innerHTML = output_categoria;
					if(output_in_cima){
						shortincima.style.display = 'inline';
						fullincima.style.display = 'inline';
					}
					else{
						shortincima.style.display = 'none';
						fullincima.style.display = 'none';
					}
					show_evento(id);	
				}
				submitspan.style.display = 'inline';
				waitspan.style.display = 'none';	
				enable(testo);
				enable(peso);
				enable(titolo);
				enable(giorno);
				enable(mese);
				enable(anno);
				enable(ora);
				enable(minuto);
				enable(categoria);
				enable(in_cima);
			}
			
			//valida il campo titolo del form id
			function check_titolo(id){
				var titolo = get_elem(id, 'titolo');
				var notify = get_elem(id, 'notify');
				var notify_titolo = get_elem(id, 'notify_titolo');
				
				if(trim(titolo.value) == ''){
					notify_titolo.innerHTML = 'Il titolo non pu&ograve; essere lasciato vuoto.';
					notify.style.display = 'block';
					titolo.setAttribute('style', 'border: solid red 2px;');
					error['titolo'+id] = true;
					return false;
				}
				if(badChars.test(titolo.value)){
					notify_titolo.innerHTML = "Caratteri non consentiti: | + -- = &lt; &gt; != ( ) % @ # *";
					notify.style.display = 'block';
					titolo.setAttribute('style', 'border: solid red 2px;');
					error['titolo'+id] = true;
					return false;
				}
				else{
					notify_titolo.innerHTML = "";
					titolo.removeAttribute('style');
					error['titolo'+id] = false;
					if(no_notify(id)){
						notify.style.display = 'none';
					}
					return true;
				}
			}
			
			//valida il campo testo del form id
			function check_testo(id){
				var testo = get_elem(id, 'testo');
				var notify = get_elem(id, 'notify');
				var notify_testo = get_elem(id, 'notify_testo');
				
				if(trim(testo.value) == ''){
					notify_testo.innerHTML = 'Il testo non pu&ograve; essere lasciato vuoto.';
					notify.style.display = 'block';
					testo.setAttribute('style', 'border: solid red 2px;');
					error['testo'+id] = true;
					return false;
				}
				if(badChars.test(testo.value)){
					notify_testo.innerHTML = "Caratteri non consentiti: | + -- = &lt; &gt; != ( ) % @ # *";
					notify.style.display = 'block';
					testo.setAttribute('style', 'border: solid red 2px;');
					error['testo'+id] = true;
					return false;
				}
				else{
					notify_testo.innerHTML = "";
					testo.removeAttribute('style');
					error['testo'+id] = false;
					if(no_notify(id)){
						notify.style.display = 'none';
					}
					return true;
				}
			}
			
			function close_insert(){
				var form = document.getElementById('forminsert');
				var a = document.getElementById('ainsert');
				var notify = document.getElementById('notifyinsert');
				a.style.display = 'inline';
				form.style.display = 'none';
				notify.style.display = 'none';
			}
			
			function show_insert(){
				var form = document.getElementById('forminsert');
				var a = document.getElementById('ainsert');
				var notify = document.getElementById('notifyinsert');
				a.style.display = 'none';
				form.style.display = 'block';
				notify.style.display = 'none';
			}
			
			var error = new Array();
		</script>
        
    	<?php
		
		if(isset($this->seleziona_prossimi)){
			$_SESSION['seleziona_gestione_eventi'] = 'prossimi';
		}
		if(isset($this->seleziona_tutti)){
			$_SESSION['seleziona_gestione_eventi'] = 'tutti';
		}
		if(isset($this->seleziona_passati)){
			$_SESSION['seleziona_gestione_eventi'] = 'passati';
		}
		if(isset($this->seleziona_creati)){
			$_SESSION['seleziona_gestione_eventi'] = 'creati';
		}
		if(!isset($_SESSION['seleziona_gestione_eventi'])){
			$_SESSION['seleziona_gestione_eventi'] = 'prossimi';
		}
		$seleziona = $_SESSION['seleziona_gestione_eventi'];
		
		switch($seleziona){
			case 'prossimi':
				$res_eventi = $this->db->seleziona_eventi(time(), NULL, "in_cima DESC, peso, data_2", NULL);
				break;
			case 'tutti':
				$res_eventi = $this->db->seleziona_eventi(0, NULL, "in_cima DESC, peso, data_2", NULL);
				break;
			case 'passati':
				$res_eventi = $this->db->seleziona_eventi(0, time(), "in_cima DESC, peso, data_2", NULL);
				break;
			case 'creati':
				$res_eventi = $this->db->seleziona_eventi(0, NULL, "in_cima DESC, peso, data_2", NULL, $_SESSION['utente']);
				break;
		}
			$insert_error = ($mod && $num_contenuto == 'insert' && $output_error);
		?>	<h1>Gestione eventi</h1>
        	<form action="index.php" method="GET">
            	<p>Cerca tra gli eventi: 
                    <input type="hidden" name="q" value="gestione_eventi" >
                    <input type="submit" 
                    	class="pulsante_ricerca<?php if($seleziona=='tutti')echo '_premuto'; ?>" 
                        name="seleziona_tutti" value="Tutti" >
                    <input type="submit" 
                    	class="pulsante_ricerca<?php if($seleziona=='prossimi')echo '_premuto'; ?>" 
                        name="seleziona_prossimi" value="In Programma" >
                    <input type="submit" 
                    	class="pulsante_ricerca<?php if($seleziona=='passati')echo '_premuto'; ?>" 
                        name="seleziona_passati" value="Passati" >
                    <input type="submit" 
                    	class="pulsante_ricerca<?php if($seleziona=='creati')echo '_premuto'; ?>" 
                        name="seleziona_creati" value="Creati da me" >
                </p>
            </form>
			<p id="ainsert" style="display:none;">
            	<a class="nuovo" onclick="show_insert()">+nuovo</a>
            </p>
            <form id="forminsert" action="index.php?q=gestione_eventi" method="POST" onsubmit="return check_form('insert')">
                    <div id="notifyinsert" class="notify">
                    	<div id="notify_phpinsert"><?php if($insert_error) echo $output; ?></div>
                        <div id="notify_titoloinsert"><?php if($insert_error) echo $output_titolo; ?></div>
                        <div id="notify_datainsert"><?php if($insert_error) echo $output_data; ?></div>
                        <div id="notify_orarioinsert"><?php if($insert_error) echo $output_orario; ?></div>
                        <div id="notify_categoriainsert"><?php if($insert_error) echo $output_categoria; ?></div>
                        <div id="notify_testoinsert"><?php if($insert_error) echo $output_testo; ?></div>
                        <div id="notify_pesoinsert"><?php if($insert_error) echo $output_peso; ?></div>
                        <div id="notify_in_cimainsert"><?php if($insert_error) echo $output_in_cima; ?></div>
                    </div>
                    <p>
                    	<input type="hidden" name="noscript" value="1" >
                        <input type="hidden" name="num_contenuto" value="insert" >
                        <label for="titoloinsert">Titolo</label><br>
                        <input id="titoloinsert" class="largeinput" name="titolo" type="text" onkeyup="check_titolo('insert');" value="" >
                    </p>
                    <p>
                    	<label style="position:absolute; margin: 3px 0 0 258px;" for="orainsert">Ora</label>
                        <label style="position:absolute; margin: 3px 0 0 430px;" for="categoriainsert">Categoria</label>
                        <label for="giornoinsert">Data</label><br>
                        <select style="position:absolute; margin: 3px 0 0 258px;" id="orainsert" name="ora">
                        	<?php
								$ora_default = (int)date('H');
								for($j=0; $j<24; $j++){
									if($j == $ora_default)
										echo "<option value=\"$j\" selected=\"selected\">",date('H', mktime($j)),"</option>";
									else
										echo "<option value=\"$j\">",date('H', mktime($j)),"</option>";
								}
							?>
                        </select>
                        <span style="position:absolute; margin: 0 0 0 300px;">:</span>
                        <select style="position:absolute; margin: 3px 0 0 311px;" id="minutoinsert" name="minuto">
                        	<?php
								$ora_default = (int)date('i');
								for($j=0; $j<60; $j++){
									if($j == $ora_default)
										echo "<option value=\"$j\" selected=\"selected\">",date('i', mktime(0,$j)),"</option>";
									else
										echo "<option value=\"$j\">",date('i', mktime(0,$j)),"</option>";
								}
							?>
                        </select>
                        <select name="categoria" style="position:absolute; margin: 3px 0 0 430px;" id="categoriainsert">
                        	<?php
								$res = $this->db->seleziona_categorie_eventi();
								while($cat = $this->db->fetch_object()){
									echo '<option value="',$cat->categoriacontenuto,'">',$cat->categoriacontenuto,'</option>';
								}
							?>
                        </select>
                        <select id="giornoinsert" name="giorno">
                        	<?php
								$giorno_default = (int)date('j');
								for($j=1; $j<=31; $j++){
									if($j == $giorno_default)
										echo "<option value=\"$j\" selected=\"selected\">$j</option>";
									else
										echo "<option value=\"$j\">$j</option>";
								}
							?>
                        </select>
                        <select id="meseinsert" name="mese">
                        	<?php
								$mese_default = (int)date('n');
								for($j=1; $j<=12; $j++){
									if($j == $mese_default)
										echo "<option value=\"$j\" selected=\"selected\">",date_inttomese($j),"</option>";
									else
										echo "<option value=\"$j\">",date_inttomese($j),"</option>";
								}
								$anno_corrente = (int)date('Y');
							?>
                        </select>
                        <select id="annoinsert" name="anno">
                        	<option value="<?php echo $anno_corrente; ?>" <?php if($anno_corrente)echo'selected="selected"'; ?>><?php echo ($anno_corrente); ?></option>
                            <option value="<?php echo $anno_corrente+1; ?>"><?php echo ($anno_corrente+1); ?></option>
                        </select>
                    </p>
                    <p>
                    	<label for="testoinsert">Testo</label><br>
                        <textarea id="testoinsert" onkeyup="check_testo('insert');" name="testo" cols="75" rows="5"></textarea>
                    </p>
                    <p>
                    	<label for="pesoinsert">Peso</label>
						<select id="pesoinsert" name="peso">
                        	<?php
								for($j=1; $j<51; $j++){
									if($j == 25)
										echo "<option value=\"$j\" selected=\"selected\">$j</option>"; 
									else
                        				echo "<option value=\"$j\">$j</option>";
								}
							?>
                        </select>
                        <label style="margin-left: 80px;" for="in_cimainsert">In cima alla lista</label>
                        <input id="in_cimainsert" name="in_cima" type="checkbox" value="true">
                        <span id="submitspaninsert">
                        	<input style="float:right; margin-right: 30px;" type="submit" value="Salva Modifiche">
                        	<input style="float:right;" type="reset" value="Annulla" onclick="close_insert()">
                        </span>
                        <span id="waitspan" style="display:none;">
                        	<img style="float:right; margin-right: 30px;" src="img/loading.gif" alt="loading" />
                        </span>
                    </p>
                </form>
                
			<div id="all" class="eventi_all">
		<?php 
		$i=0;
		$id = array();
		while($obj = $this->db->fetch_external_object($res_eventi)){ ?>
            <div class="evento" id="short<?php echo $obj->num_contenuto ?>" onclick="show_evento(<?php echo $obj->num_contenuto ?>)">
            	<span id="short_incima<?php echo $obj->num_contenuto ?>" class="incima" <?php if (!$obj->in_cima) echo 'style="display:none;"';?>> In cima </span>
                <h1 id="short_titolo<?php echo $obj->num_contenuto ?>"><?php echo $obj->titolo ?></h1>
                <span id="short_categoria<?php echo $obj->num_contenuto ?>" class="categoria"><?php echo $obj->categoriacontenuto ?></span>
                <h3 id="short_data<?php echo $obj->num_contenuto ?>"><?php echo date_translate(date('l j F Y - H:i' ,$obj->data_2)) ?></h3>
            </div>
            <div class="evento_full" id="full<?php echo $obj->num_contenuto ?>">
                <div class="inner" onclick="close_evento(<?php echo $obj->num_contenuto ?>)">
                	<span id="full_incima<?php echo $obj->num_contenuto ?>" class="incima" <?php if (!$obj->in_cima) echo 'style="display:none;"';?>> In cima </span>
                    <h1 id="full_titolo<?php echo $obj->num_contenuto ?>"><?php echo $obj->titolo ?></h1>
                    <span id="full_categoria<?php echo $obj->num_contenuto ?>" class="categoria"><?php echo $obj->categoriacontenuto ?></span>
                	<h3 id="full_data<?php echo $obj->num_contenuto ?>"><?php echo date_translate(date('l j F Y - H:i' ,$obj->data_2)) ?></h3>
                </div>
                <div class="text" id="text<?php echo $obj->num_contenuto ?>">
                    <p id="full_testo<?php echo $obj->num_contenuto ?>"><?php echo $obj->testo ?></p>
                    <form action="index.php?q=gestione_eventi" method="POST" onsubmit="return confirm('Stai per eliminare questo post. Procedo?');" class="formelimina">
                    <div>
                    	<input type="hidden" name="noscript" value="1" >
                        <input type="hidden" name="num_contenuto" value="<?php echo $obj->num_contenuto ?>">
                        <input type="hidden" name="elimina_contenuto" value="1">
                        <input type="submit" name="elimina" value="elimina" class="elimina a" id="elimina<?php echo $obj->num_contenuto ?>">
                    </div>
                    </form>
                    <p class="small">
                        Creato da <strong><?php echo $obj->utente ?></strong>
                        <?php echo date_translate(date('l j F Y' ,$obj->data_creazione)) ?> alle <?php echo date('H:i', $obj->data_creazione); ?>
                        <a class="modifica" id="a<?php echo $obj->num_contenuto ?>" onclick="mod_evento(<?php echo $obj->num_contenuto ?>)">modifica</a>
                    </p>
                </div>
                <form id="form<?php echo $obj->num_contenuto ?>" action="index.php?q=gestione_eventi" method="POST" onsubmit="return check_form(<?php echo $obj->num_contenuto ?>)">
                	<?php $mod_error = ($mod && $num_contenuto == $obj->num_contenuto && $output_error); ?>
                	<div id="notify<?php echo $obj->num_contenuto ?>" class="notify">
                    	<div id="notify_php<?php echo $obj->num_contenuto ?>"><?php if($mod_error) echo $output; ?></div>
                        <div id="notify_titolo<?php echo $obj->num_contenuto ?>"><?php if($mod_error) echo $output_titolo; ?></div>
                        <div id="notify_data<?php echo $obj->num_contenuto ?>"><?php if($mod_error) echo $output_data; ?></div>
                        <div id="notify_orario<?php echo $obj->num_contenuto ?>"><?php if($mod_error) echo $output_orario; ?></div>
                        <div id="notify_categoria<?php echo $obj->num_contenuto ?>"><?php if($mod_error) echo $output_categoria; ?></div>
                        <div id="notify_testo<?php echo $obj->num_contenuto ?>"><?php if($mod_error) echo $output_testo; ?></div>
                        <div id="notify_peso<?php echo $obj->num_contenuto ?>"><?php if($mod_error) echo $output_peso; ?></div>
                        <div id="notify_in_cima<?php echo $obj->num_contenuto ?>"><?php if($mod_error) echo $output_in_cima; ?></div>
                    </div>
                    <p>
                    	<input type="hidden" name="noscript" value="1" >
                        <input type="hidden" name="num_contenuto" value="<?php echo $obj->num_contenuto ?>">
                        <label for="titolo<?php echo $obj->num_contenuto ?>">Titolo</label><br>
                        <input id="titolo<?php echo $obj->num_contenuto ?>" onkeyup="check_titolo('<?php echo $obj->num_contenuto ?>');" class="largeinput" name="titolo" type="text" value="<?php echo $obj->titolo ?>">
                    </p>
                    <p>
                    	<label style="position:absolute; margin: 3px 0 0 258px;" for="ora<?php echo $obj->num_contenuto ?>">Ora</label>
                        <label style="position:absolute; margin: 3px 0 0 430px;" for="categoria<?php echo $obj->num_contenuto ?>">Categoria</label>
                        <label for="giorno<?php echo $obj->num_contenuto ?>">Data</label><br>
                        <select style="position:absolute; margin: 3px 0 0 258px;" id="ora<?php echo $obj->num_contenuto ?>" name="ora">
                        	<?php
								$ora_default = (int)date('H', $obj->data_2);
								for($j=0; $j<24; $j++){
									if($j == $ora_default)
										echo "<option value=\"$j\" selected=\"selected\">",date('H', mktime($j)),"</option>";
									else
										echo "<option value=\"$j\">",date('H', mktime($j)),"</option>";
								}
							?>
                        </select>
                        <span style="position:absolute; margin: 0 0 0 300px;">:</span>
                        <select style="position:absolute; margin: 3px 0 0 311px;" id="minuto<?php echo $obj->num_contenuto ?>" name="minuto">
                        	<?php
								$ora_default = (int)date('i', $obj->data_2);
								for($j=0; $j<60; $j++){
									if($j == $ora_default)
										echo "<option value=\"$j\" selected=\"selected\">",date('i', mktime(0,$j)),"</option>";
									else
										echo "<option value=\"$j\">",date('i', mktime(0,$j)),"</option>";
								}
							?>
                        </select>
                        <select name="categoria" style="position:absolute; margin: 3px 0 0 430px;" id="categoria<?php echo $obj->num_contenuto ?>">
                        	<?php
								$res = $this->db->seleziona_categorie_eventi();
								while($cat = $this->db->fetch_object()){
									if($obj->categoriacontenuto == $cat->categoriacontenuto)
										echo '<option value="',$cat->categoriacontenuto,'" selected="selected">',$cat->categoriacontenuto,'</option>';
									else
										echo '<option value="',$cat->categoriacontenuto,'">',$cat->categoriacontenuto,'</option>';
								}
							?>
                        </select>
                        <select id="giorno<?php echo $obj->num_contenuto ?>" name="giorno">
                        	<?php
								$giorno_default = (int)date('j', $obj->data_2);
								for($j=1; $j<=31; $j++){
									if($j == $giorno_default)
										echo "<option value=\"$j\" selected=\"selected\">$j</option>";
									else
										echo "<option value=\"$j\">$j</option>";
								}
							?>
                        </select>
                        <select id="mese<?php echo $obj->num_contenuto ?>" name="mese">
                        	<?php
								$mese_default = (int)date('n', $obj->data_2);
								for($j=1; $j<=12; $j++){
									if($j == $mese_default)
										echo "<option value=\"$j\" selected=\"selected\">",date_inttomese($j),"</option>";
									else
										echo "<option value=\"$j\">",date_inttomese($j),"</option>";
								}
								$anno_corrente = (int)date('Y');
								$anno_default = (int)date('Y', $obj->data_2);
								$anno_selected = $anno_corrente == $anno_default; 
							?>
                        </select>
                        <select id="anno<?php echo $obj->num_contenuto ?>" name="anno">
                        	<option value="<?php echo $anno_corrente; ?>" <?php if($anno_selected)echo'selected="selected"'; ?>><?php echo ($anno_corrente); ?></option>
                            <option value="<?php echo $anno_corrente+1; ?>"><?php echo ($anno_corrente+1); ?></option>
                        </select>
                    </p>
                    <p>
                    	<label for="testo<?php echo $obj->num_contenuto ?>">Testo</label><br>
                        <textarea id="testo<?php echo $obj->num_contenuto ?>" onkeyup="check_testo('<?php echo $obj->num_contenuto ?>');" name="testo" cols="75" rows="5"><?php echo str_replace('<br>', '
', $obj->testo) ?></textarea>
                    </p>
                    <p>
                    	<label for="peso<?php echo $obj->num_contenuto ?>">Peso</label>
						<select id="peso<?php echo $obj->num_contenuto ?>" name="peso">
                        	<?php
								for($j=1; $j<51; $j++){
									if($obj->peso == $j)
										echo "<option value=\"$j\" selected=\"selected\">$j</option>"; 
									else
                        				echo "<option value=\"$j\">$j</option>";
								}
							?>
                        </select>
                        <label style="margin-left: 80px;" for="in_cima<?php echo $obj->num_contenuto ?>">In cima alla lista</label>
                        <input id="in_cima<?php echo $obj->num_contenuto ?>" name="in_cima" type="checkbox" value="true" <?php if($obj->in_cima) echo 'checked="checked"'; ?> />
                        <span id="submitspan<?php echo $obj->num_contenuto ?>">
                        	<input style="float:right; margin-right: 30px;" type="submit" value="Salva Modifiche">
                        	<input style="float:right;" type="reset" value="Annulla" onclick="show_evento(<?php echo $obj->num_contenuto ?>)" />
                        </span>
                        <span id="waitspan<?php echo $obj->num_contenuto ?>" style="display:none;">
                        	<img style="float:right; margin-right: 30px;" src="img/loading.gif" alt="loading" />
                        </span>
                    </p>
                </form>
            </div>
            <script type="text/javascript">
				error['titolo<?php echo $obj->num_contenuto ?>'] = false;
				error['testo<?php echo $obj->num_contenuto ?>'] = false;
			</script>
			<?php
            $id[$i] = $obj->num_contenuto;
			$i++; 
		}
		?></div><?php
		echo '<script type="text/javascript">';
		for($j=0; $j<$i; $j++){
			echo 'close_evento(String(',$id[$j],'));';
		}
		if(!$insert_error)
			echo 'close_insert();';
		echo '</script>';
	}
	
	function content2(){
		require_once('php/modules.php');
		gestisci_module('gestione_eventi', $this->privilegi);
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