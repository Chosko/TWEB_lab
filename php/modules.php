<?php
//########################
// MODULO INFO
//########################
function info_module($db){
	echo '<h1 class="shadowed">Info</h1>';
	$db->seleziona_info();
	$i = 0;
	while($obj = $db->fetch_object()){
		echo '<div class="content2_riquadro" onclick="expand(this)" >
				<div class="full2">
					<h1>', $obj->titolo , '</h1>
					<p>' , $obj->testo , '</p>
				</div>
			</div>';
		$i++;
	}
	if($i == 0){
		echo '<h4>Non ci sono informazioni da visualizzare</h3>';
	}	
}

//#######################
// MODULO GESTISCI
//#######################
function gestisci_css(){?>
    #content2{
        background-color: #FC6;
        box-shadow: 0px 0px 12px #555;
    }
    
    .content2_menu h1{
        color: #FFF;
    }
    
    .content2_menu a{
        color: #FFF;
        text-decoration: none !important;
    }
    
    .content2_menu_button{
        background-color: #FA0;
    }
    
    .content2_menu_button_selected{
        background-color: #F90;
    }
<?php 
}

function gestisci_module($page, $privilegi){
	echo '<div class="content2_menu">
				<h1>Gestisci</h1>';
		if(!(array_search('profilo', $privilegi) === false)){
			print_menu_button('Profilo', 'profilo', $page == 'profilo');
		}
		if(!(array_search('eventi', $privilegi) === false)){
			print_menu_button('Gestisci Eventi', 'gestione_eventi', $page == 'gestione_eventi');
		}
		if(!(array_search('info', $privilegi)=== false)){
			print_menu_button('Gestisci Info', 'gestione_info', $page == 'gestione_info');
		}
		if(!(array_search('eliminare utenti', $privilegi)=== false) || !(array_search('privilegiare utenti', $privilegi)=== false) || !(array_search('visualizzare utenti', $privilegi)=== false)){
			print_menu_button('Gestisci Utenti', 'gestione_utenti', $page == 'gestione_utenti');
		}
		if(!(array_search('categorie utenti', $privilegi)=== false)){
			print_menu_button('Gestisci Privilegi', 'gestione_privilegi', $page == 'gestione_privilegi');
		}
		echo '</div>';
}

function print_menu_button($title, $target, $selected){
	if($selected)
		echo '<span class="content2_menu_button_selected">',$title,'</span>';
	else
		echo '<a href="index.php?q=',$target,'"><span class="content2_menu_button">',$title,'</span></a>';
}

//##############################
// MODULO LOGIN
//##############################
function login_module($q, $notify = ''){
	if(!isset($_SESSION['utente'])){ ?>
    <form  action="index.php?q=<?php echo $q; ?>" method="post"  id="login_form" onsubmit="return check_login()">
    	<div id="login_span">Non sei autenticato.<br>
        	<a onclick="login_toggle();">Login</a> | <a href="index.php?q=registrazione">Registrati</a>
        </div>
        <div id="login_notify"><?php echo $notify; ?></div>
        <div id="login_div" style="display:inline">
            <label for="login_username">Username</label>
            <input type="text" name="login_username" id="login_username" value="" onblur="check_login_username()" onchange="check_login_username()"><br>
            <label for="login_password">Password</label>
            <input type="password" name="login_password" id="login_password" value="" onblur="check_login_password()" onchange="check_login_password()"><br>
            <label for="login_ricordami">Ricordami</label>
            <input type="checkbox" name="login_ricordami" id="login_ricordami" value="1">
            <input type="hidden" name="login_action" value="login">
            <input type="submit" name="login" value="Login" >
        </div>
    </form>
    <script type="text/javascript"> login_close(); </script>
    <?php } else{ ?>
        <form action="index.php?q=<?php echo $q; ?>" method="post" id="login_form">
            <div style="display:inline">Benvenuto <b><?php echo $_SESSION['utente']; ?></b>
            <input type="hidden" name="login_action" value="logout" >
            <input type="submit" name="logout" value="Esci" ></div>
        </form>
    <?php }
}

function login_css($color){?>
	#login_form{
        position: absolute;
        margin-left: 748px;
        background-image: url(img/content.png);
        padding: 5px;
        border-bottom-left-radius: 8px;
        border-bottom-right-radius: 8px;
        font-size: 10px;
        box-shadow: 0px 0px 31px #FFF;
        max-width: 200px;
    }
    
    #login_form label{
    	font-size: 10px !important;
        font-weight: bold;
    }
    
    #login_div{
    	margin-top: 4px;
    }
    
    #login_notify{
    	color:red;
        font-weight: bold;
    }
    
    #login_span a{
        font-weight: bold;
    }
    
    #login_form input{
        font-size: 10px;
        max-width: 70px;
        padding: 1px;
        height: 10px;
    }
    
    #login_form input[type=submit]{
        padding: 0 1px;
        height: 20px;
    }
<?php
}

function login_javascript(){?>
    var login_open = false;
    
    function login_show(){
    	var div = document.getElementById('login_div');
        var span = document.getElementById('login_span');
        div.style.display = 'block';
        span.style.display = 'inline';
    	login_open = true;
    }
    
    function login_close(){
    	var div = document.getElementById('login_div');
        var span = document.getElementById('login_span');
        div.style.display = 'none';
        span.style.display = 'block';
    	login_open = false;
    }
    
    function login_toggle(){
    	if(login_open)
        	login_close();
        else
        	login_show();
    }
    
    function check_login(){
        if(check_login_username() && check_login_password()){
        	return true;
        }
        else
        	return false;
    }
    
    function check_login_username(){
    	var user = document.getElementById('login_username');
        if(trim(user.value) == ''){
        	user.setAttribute('style', 'border-color: red;');
            return false;
        }
        else{
        	user.removeAttribute('style');
            return true;
        }
    }
    
    function check_login_password(){
    	var pass = document.getElementById('login_password');
        if(trim(pass.value) == ''){
        	pass.setAttribute('style', 'border-color: red;');
            return false;
        }
        else{
        	pass.removeAttribute('style');
            return true;
        }
    }
<?php
}

//#############################
// MODULO CERCA
//#############################
function cerca_javascript($cerca_str){?>
	var defaultCercaValue = "Cerca <?php echo $cerca_str; ?>";
		
    //cancella il testo di cerca se è al suo valore iniziale.
    function tryCancelCerca(){
        var input = document.getElementById("input_cerca");
        if(trim(input.value) == defaultCercaValue)
            cancel(input);
    }
    
    //reimposta il testo di cerca al suo valore iniziale se è vuoto.
    function tryResetCerca(){
        var input = document.getElementById("input_cerca");
        if(trim(input.value) == "")
            input.value = defaultCercaValue;
    }
    
    function checkCerca(){
        var input = document.getElementById("input_cerca");
        if(trim(input.value) == defaultCercaValue){
            cancel(input);
            input.focus();
            return false;
        }
        else return true;
    }
<?php
}

function cerca_module($cerca_str, $q){?>
	<form id="cerca" action="index.php" onSubmit="return checkCerca(this)">
        <p>
        <input id="input_cerca" name="input_cerca" type="text" onfocus="tryCancelCerca(this);" onblur="tryResetCerca(this);" 
        	value="<?php if(isset($_GET['input_cerca'])) echo $_GET['input_cerca']; else echo 'Cerca ',$cerca_str;?>" >
        <input name="submit_cerca" type="submit" value="">
        <input name="q" value="<?php echo $q; ?>" type="hidden">
        </p>
    </form>
<?php
}

?>