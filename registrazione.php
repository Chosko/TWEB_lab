<?php
require_once("php/page_interface.php");
require_once("php/dbConn.php");
require_once("php/functions.php");

//Questa classe definisce la pagina di registrazione. Per un dettaglio sulle funzioni vedere i commenti all'interfaccia IPage
class registrazione implements IPage{
	private $standard_layout;
	private $title;
	private $color;
	private $db;
	private $cerca;
	
	function registrazione(){
		//Incapsula i valori di tutti i parametri get in omonimi campi dell'oggetto.
		foreach($_GET as $k=>$v){
			$this->$k = $v;
		}
		$this->standard_layout = true;
		$this->title = "Registrazione";
		$this->db = new DBConn();
		$this->color = "#F60";		
		$this->cerca = false;
	}
	
	function after_headers(){
		require_once('php/check_registrazione.php');
		if($registrazione_ok)
			return 'registrato';
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
    	#registrazione_form input{
        	width: 35%;
        }
        
        #registrazione_form input[type=submit],
        #registrazione_form input[type=reset],
        #registrazione_form input[type=button]{
        	width: 20%;
            height: 30px;
        }
        
        .registrazione_separa{
        	width: 15%;
            display: inline-block;
        }
        
        .red{
        	border: 2px solid red;
        }
        
        .green{
        	border: 2px solid green;
        }
	<?php 
	}
	
	function content(){?>
    	<script type="text/javascript" src="js/ajax.js"></script>
    	<script type="text/javascript">
		
		var errors = new Array();
		errors['nome'] = false;
		errors['cognome'] = false;
		errors['telefono'] = false;
		errors['telefono2'] = false;
		errors['indirizzo'] = false;
		errors['username'] = false;
		errors['email'] = false;
		errors['conferma_email'] = false;
		errors['password'] = false;
		errors['conferma_password'] = false;
		errors['php'] = false;
		errors['ajax_username'] = true;
		errors['ajax_email'] = true;
		<?php if($this->error_string) echo "errors['php'] = true;"; ?>
		
		function check_form(){
			check_campo('nome', false, false);
			check_campo('cognome', false, false);
			check_campo('telefono', true, true);
			check_campo('telefono2', false, true);
			check_campo('indirizzo', false, false);
			check_campo('username', true, false);
			check_email();
			check_conferma('email');
			check_campo('password', true, false);
			check_conferma('password');
			return no_errors();
		}
		
		function check_email(){
			var input = document.getElementById('email');
			var notify = document.getElementById('notify_email');
			var error = false;
			if(trim(input.value) == ''){
				error = 'Il campo email &egrave; obbligatorio!';
			}
			else if(!goodEmail.test(trim(input.value))){
				error = 'Inserire una email valida!';
			}
			else{
				ajax = new Ajax('POST', 'ajax/check_user.php', 'email='+input.value, wait, end, retry, 'email'); 
				errors['email'] = false;
				input.removeAttribute('class');
				notify.innerHTML = '';
				close_notify();
			}
			if(error){
				errors['email'] = true;
				input.setAttribute('class', 'red');
				notify.innerHTML = error;
				open_notify();
			}
		}
		
		function check_conferma(nome){
			var email = document.getElementById(nome);
			var input = document.getElementById('conferma_' + nome);
			var notify = document.getElementById('notify_conferma_' + nome);
			var error = false;
			if(input.value != email.value)
				error = 'I campi ' + nome + ' e Conferma ' + nome + ' non coincidono';
			if(error){
				errors['conferma_' + nome] = true;
				input.setAttribute('class', 'red');
				notify.innerHTML = error;
				open_notify();
			}
			else{
				errors['conferma_' + nome] = false;
				input.setAttribute('class', 'green');
				notify.innerHTML = '';
				close_notify();
			}
		}
		
		function check_campo(nome, obbligatorio, numerico){
			var input = document.getElementById(nome);
			var notify = document.getElementById('notify_' + nome);
			var error = false;
			if(obbligatorio){
				if(trim(input.value) == ''){
					error = 'Il campo ' + nome + ' &egrave; obbligatorio!';
				}
			}
			if(!error){
				if(numerico){
					if(isNaN(trim(input.value))){
						error = 'Inserire un ' + nome + ' valido!';
					}
				}
				else{
					if(badChars.test(input.value)){
						error = 'Caratteri non consentiti: | + -- = &lt; &gt; != ( ) % @ # *';
					}
					else if(nome == 'username'){
						new Ajax('POST', 'ajax/check_user.php', 'utente='+input.value, wait, end, retry, 'username');  
					}
				}
			}
			if(error){
				errors[nome] = true;
				input.setAttribute('class', 'red');
				notify.innerHTML = error;
				open_notify();
			}
			else{
				errors[nome] = false;
				input.removeAttribute('class');
				notify.innerHTML = '';
				close_notify();
			}
		}
		
		function close_notify(){
			var notify = document.getElementById('notify_registrazione');
			if(no_normal_errors()) notify.style.display = 'none';
		}
		
		function open_notify(){
			var notify = document.getElementById('notify_registrazione');
			notify.style.display = 'block';
		}
		
		function no_errors(){
			return !(errors['nome'] || errors['cognome'] || errors['telefono'] || errors['telefono2'] || errors['indirizzo'] || errors['username'] || errors['email'] || errors['conferma_email'] || errors['password'] || errors['conferma_password'] || errors['ajax_username'] || errors['ajax_email']);
		}
		
		function no_normal_errors(){
			return !(errors['nome'] || errors['cognome'] || errors['telefono'] || errors['telefono2'] || errors['indirizzo'] || errors['username'] || errors['email'] || errors['conferma_email'] || errors['password'] || errors['conferma_password']);
		}
		
		function wait(id){
			var span = document.getElementById('ajax_' + id);
			span.innerHTML = '<img src="img/loading.gif" alt="loading" >';
		}
		
		function end(ajax, id){
			var span = document.getElementById('ajax_' + id);
			var notify = document.getElementById('notify_' + id);
			var input = document.getElementById(id);
			eval(ajax.responseText);
			span.innerHTML = output;
			if(output_error){
				errors['ajax_' + id] = true;
				input.setAttribute('class', 'red');
				if(id == 'username')
					notify.innerHTML = 'L\'username che hai scelto &egrave; gi&agrave; in utilizzo!';
				else if(id == 'email')
					notify.innerHTML = 'L\'email che hai inserito &egrave; gi&agrave; registrata!';
				open_notify();
			}
			else{
				errors['ajax_' + id] = false;
			}
		}
		
		function retry(id){
			var input = document.getElementById(id);
			if(id == 'username')
				ajax = new Ajax('POST', 'ajax/check_user.php', 'utente='+input.value, wait, end, no_retry, 'username');
			else if(id == 'email')
				ajax = new Ajax('POST', 'ajax/check_user.php', 'email='+input.value, wait, end, no_retry, 'email');
		}
		
		function no_retry(id){
			var span = document.getElementById('ajax_' + id);
			span.innerHTML = 'Errore di connessione. Riprovare.'
		}
		</script>
    
		<h1 class="shadowed">Modulo di Registrazione</h1>
        <p style="color:<?php echo $this->color; ?>; font-size:14px; text-align:center;">I campi contrassegnati con un asterisco (*) sono obbligatori</p>
        <div class="notify" id="notify_registrazione">
            <div id="notify_php"><?php echo $this->error_string; ?></div>
        	<div id="notify_nome"></div>
            <div id="notify_cognome"></div>
            <div id="notify_telefono"></div>
            <div id="notify_telefono2"></div>
            <div id="notify_indirizzo"></div>
            <div id="notify_username"></div>
            <div id="notify_email"></div>
            <div id="notify_conferma_email"></div>
            <div id="notify_password"></div>
            <div id="notify_conferma_password"></div>
        </div>
        <form id="registrazione_form" action="index.php?q=registrazione" method="post" onsubmit="return check_form()">
        	<fieldset>
            	<legend>Informazioni Personali</legend>
                
                <input type="hidden" name="noscript" value="1" >
                
            	<label for="cognome" style="position:absolute; margin-left: 328px">Cognome</label>
                <label for="nome">Nome</label>
                <br>
            	
                <input id="nome" name="nome" type="text" value="<?php echo htmlentities($this->nome); ?>" onblur="check_campo('nome', false, false)">
                <div class="registrazione_separa">&nbsp;</div>
                <input id="cognome" name="cognome" type="text" value="<?php echo htmlentities($this->cognome); ?>" onblur="check_campo('cognome', false, false)"><br>
                
                <label for="telefono2" style="position:absolute; margin-left: 328px">Telefono</label>
                <label for="telefono">Cellulare *</label><br>
                
                <input id="telefono" name="telefono" type="text" value="<?php echo htmlentities($this->telefono); ?>" onblur="check_campo('telefono', true, true)">
                <div class="registrazione_separa">&nbsp;</div>
                <input id="telefono2" name="telefono2" type="text" value="<?php echo htmlentities($this->telefono2); ?>" onblur="check_campo('telefono2', false, true)"><br>
                
                <label for="indirizzo">Indirizzo</label><br>
                <input style="width: 95%;" id="indirizzo" name="indirizzo" type="text" value="<?php echo htmlentities($this->indirizzo); ?>" onblur="check_campo('indirizzo', false, false)"><br>
                
                <label for="giorno">Data di nascita *</label><br>
                <select id="giorno" name="giorno">
					<?php
                    for($i=1; $i<31; $i++)
                        echo "<option value=\"$i\">$i</option>";
					echo '<option value="31" selected="selected">31</option>';
                    ?>
                </select>
                <select id="mese" name="mese">
                    <?php
                    for($i=1; $i<12; $i++)
                        echo "<option value=\"$i\">",date_inttomese($i),"</option>";
					echo "<option value=\"12\">",date_inttomese(12),"</option>";
                    ?>
                </select>
                <select id="anno" name="anno">
                    <?php 
                    for($i=(date("Y")-13); $i>=1901; $i--)
                        echo "<option value=\"$i\">$i</option>";
                    ?>
                </select>
                
            </fieldset>
            <fieldset>
            	<legend>Informazioni di Contatto</legend>
            
            	<label for="username">Username *</label> <span id="ajax_username"></span><br>
            	<input id="username" name="username" type="text" value="<?php echo htmlentities($this->username); ?>" onblur="check_campo('username', true, false)"><br>
                
                <label for="conferma_email" style="position:absolute; margin-left: 328px">Conferma E-Mail *</label>
                <label for="email">E-Mail *</label> <span id="ajax_email"></span><br>
                
                <input id="email" name="email" type="text" value="<?php echo htmlentities($this->email); ?>" onblur="check_email()" onkeyup="check_conferma('email')">
                <div class="registrazione_separa">&nbsp;</div>
                <input id="conferma_email" name="conferma_email" type="text" value="" onkeyup="check_conferma('email')"><br>
                
                <label for="conferma_password" style="position:absolute; margin-left: 328px">Conferma Password *</label>
                <label for="password">Password *</label><br>
                
                <input id="password" name="password" type="password" value="" onblur="check_campo('password', true, false)" onkeyup="check_conferma('password')">
                <div class="registrazione_separa">&nbsp;</div>
                <input id="conferma_password" name="conferma_password" type="password" value="" onkeyup="check_conferma('password')">
            </fieldset>
            <div style="text-align:center;">
            	<input type="reset" value="Reimposta">
            	<input type="submit" name="submit" value="Invia dati">
            </div>
        </form>
        
        <script type="text/javascript">
		if(!errors['php'])
			close_notify();
		</script>
	<?php
    }
	
	function content2(){
		require_once('php/modules.php');
		info_module($this->db);
	}
}

?>