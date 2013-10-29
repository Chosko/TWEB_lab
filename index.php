<?php
if(!is_file('php/db_config.php'))
	header('location:install.php');

require_once('php/site_config.php');
require_once('php/functions.php');
require_once('php/modules.php');

session_start();

//Controlla e decide la pagina da visualizzare
if(!isset($_GET['q']) || !$q = check_page($_GET['q'])) 
	$q = $default_page;

//Importa la pagina da visualizzare
require_once($q.'.php');
$p = new $q(); //Inizializza la pagina

//Tenta il login o il logout se sono richiesti
$login_notify = '';
if(isset($_POST['login_action'])){
	$login_action = $_POST['login_action'];
	session_unset();
	session_destroy();
	if(isset($_COOKIE['utente'])){
		$_COOKIE['utente'] = '';
		setcookie('utente', '', -1);
	}
	if(isset($_COOKIE['firm'])){
		$_COOKIE['firm'] = '';
		setcookie('firm', '', -1);
	}
	
	if($login_action == 'login'){
		if(isset($_POST['login_username']) && isset($_POST['login_password'])){
			$login_username = trim($_POST['login_username']);
			$login_password = $_POST['login_password'];
			if($login_username != '' && $login_password != ''){
				$login_response = $p->db()->login($login_username, $login_password);
				if($login_response){
					if($login_response == -1){
						$login_notify = '<a href="index.php?q=inattivo">Utente inattivo</a>';
					}
					else{
						session_start();
						$_SESSION['utente'] = $login_response;
						if(isset($_POST['login_ricordami']) && $_POST['login_ricordami'] == 1){
							setcookie('utente',$login_response,time()+15*24*60*60);
							setcookie('firm',md5($login_password),time()+15*24*60*60);
						}	
					}
				}
				else
					$login_notify = 'Autenticazione fallita';
			}
			else
				$login_notify = 'Hai lasciato i campi vuoti!';
		}
		else
			$login_notify = 'Autenticazione fallita';
	}
}

//Controlla se l'utente è autenticato
$logged = false;
if(isset($_SESSION['utente'])){
	$logged = true;
}
elseif(isset($_COOKIE['utente']) && isset($_COOKIE['firm'])){
	$login_cookie_username = $_COOKIE['utente'];
	$login_cookie_firm = $_COOKIE['firm'];
	if(trim($login_cookie_username) != '' && trim($login_cookie_firm) != ''){
		$login_cookie_response = $p->db()->login($login_cookie_username, $login_cookie_firm, false);
		if($login_cookie_response && $login_cookie_response != -1){
			$_SESSION['utente'] = $login_cookie_response;
			$logged = true;
		}
	}
}

//Controlla se l'utente dispone dei privilegi necessari per visualizzare la pagina
if(!$p->auth()){
	header('location: index.php?q=denied');
}

if(!$p->cerca())
	$_SESSION['input_cerca'] = NULL;
if(isset($_GET['input_cerca']))
	$_SESSION['input_cerca'] = $_GET['input_cerca'];

if($redirect = $p->after_headers()){
	if(is_bool($redirect))
		$redirect = $default_logged_page;
	else
		$redirect = check_page($redirect);
	header('location: index.php?q='.$redirect);
};
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
    <?php 
        //Stampa i tag aggiuntivi della head della pagina
        $p->head_tags();
    ?>
    
    <title><?php
        //Stampa il titolo della pagina 
        echo "$site_name | ", $p->title() ;
    ?></title>
    
    <style type="text/css">
    @import url("css/style.css");
	
	a, .a{
		color: #F60;
	}
	
	a:hover, .a:hover{
		color: #F60;
		text-shadow: 0 0 15px #F60;
	}
	
	#header{
		background: url(img/<?php echo random_header('header', 12); ?>) no-repeat;
	}
	
	#menu li:active{
		box-shadow: 0 0 15px #F60;
		color: #F60;
	}
	
	#cerca input{
		border: 4px solid #F60;
		box-shadow: 0 0 26px #F60;	
	}
	
	#cerca input[type=submit]{
		background-color: #F60;
	}
	
	#big_title:hover{
		text-shadow: 0 0 12px #F60;
	}
	
	.content1_riquadro{
		background-color: #F60;
	}
	
	.content1_riquadro h3{
		color: #F60;
	}
	
	.content1_piccolo {
		background-color: #F60;
	}
	
	.content1_piccolo h3{
		color: #F60;
	}
	
	label{
		color: #F60;
	}
	
    <?php
		//Stampa le regole css per il modulo di login
		login_css("#F60");
	
        //Stampa le regole css aggiuntive della pagina 
        $p->css_rules();
    ?>
    </style>
    <script type="text/javascript" src="js/functions.js"></script>
    <script type="text/javascript" src="js/ajax.js" ></script>
    <script type="text/javascript">
		//Cancella il testo di un input
		function cancel(input){
			input.value = "";
		}
    	<?php 
			if($p->cerca()) cerca_javascript($p->cerca()); 
			login_javascript();
		?>
	</script>
</head>


<body>
	<div id="container">
    	<div id="header">
        	<?php 
				if($p->cerca())
					cerca_module($p->cerca(), $q);
				login_module($q, $login_notify);
			?>
        	<a href="index.php" class="no-decoration"><span id="big_title"><?php echo $site_name; ?></span></a>
            <div id="menu">
                <ul id="menu_ul">
                <?php
					//Stampa i bottoni del menu' se l'utente è loggato
					if($logged)
						foreach($menu_buttons_logged as $k => $v){
                        	echo '<li style="width: ',930/count($menu_buttons_logged) - 2 ,'px;"><a href="index.php?q=',$v,'">',$k,'</a></li>';
                    	}
					//Stampa i bottoni del menù se l'utente non è loggato
					else
						foreach($menu_buttons as $k => $v){
							echo '<li style="width: ',930/count($menu_buttons) - 2 ,'px;"><a href="index.php?q=',$v,'">',$k,'</a></li>';
						}
                ?>
                </ul>
            </div>
        </div>
		<?php if($p->standard_layout()): //INIZIO DEL BLOCCO DELLA PAGINA CON LAYOUT STANDARD ?>
            <div id="content">
                <?php
                    //Stampa il contenuto principale della pagina
                    $p->content();
                ?>
            </div>
            <div id="content2">
                <?php
                    //Stampa il contenuto secondario della pagina
                    $p->content2();
                ?>            
            </div>
        <?php endif; //FINE DEL BLOCCO DELLA PAGINA CON LAYOUT STANDARD
            if(!$p->standard_layout())
                $p->content();	//Stampa il contenuto della pagina con layout personalizzato
        ?>
        <div id="footer">
        	<p> --- Informazioni e mail ---<br>
            	Sito realizzato da: Ruben Caliandro.
            </p>
        </div>
    </div>
    <div id="validations">
        <div class="validate">
            <a href="http://validator.w3.org/check?uri=referer"><img src="http://www.w3.org/Icons/valid-html401" alt="Valid HTML 4.01 Strict" height="31" width="88" ></a>
        </div>
        <div class="validate">
            <a href="http://jigsaw.w3.org/css-validator/check/referer"><img style="border:0;width:88px;height:31px" src="http://jigsaw.w3.org/css-validator/images/vcss-blue" alt="CSS Valido!" ></a>
        </div>
    </div>
</body>
</html>