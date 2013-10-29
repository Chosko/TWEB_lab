<?php
function post($str){
	return isset($_POST[$str]) ? $_POST[$str] : false;
}

//Installazione del database
function install(){
	$dump = file_get_contents('php/dump.sql');
	$rows = explode(';
', $dump);
	foreach($rows as $row){
		if(trim($row) != ''){
			$ok = mysql_query($row);
			if(!$ok) break;
		}
	}
	return $ok;
}

if(is_file('php/db_config.php')){
	header('location:index.php');
}

$submit = post('submit');
$error = false;
if($submit){
	$username = post('username');
	$password = post('password');
	$host = post('host');
	$database = post('database');
	$svuota = post('svuota');
	if(is_file('php/dump.sql')){
		if($username && $password && $host && $database){
			$connection = mysql_connect($host, $username, $password);
			if($connection){
				$res = true;
				$db_exists = mysql_select_db($database);
				if($svuota && $db_exists){
					$sql = 'DROP DATABASE '.$database.' ;';
					$res = mysql_query($sql);
				}
				if($res){
					$sql = 'CREATE DATABASE IF NOT EXISTS '.$database.' ;';
					if(mysql_query($sql)){
						if(mysql_select_db($database)){
							if(install()){
								if($config = fopen('php/db_config.php', 'w')){
								
									fwrite($config, '<?php

$db_user = "'.$username.'";
$db_password = "'.$password.'";
$db_name = "'.$database.'";
$db_host = "'.$host.'";

?>');
									fclose($config);
									if(is_file('php/db_config.php')){
										header('location:index.php');
									}
								}
								if(!$error)
									$error = '<p style="color:red;">Operazione interrotta: Il database &egrave; stato creato, ma &egrave; stato impossibile creare il file di configurazione.</p>';
							}
							else{
								$error = '<p style="color:red;">Operazione interrotta: '.mysql_error().'<br> Controllare il file di dump e rieseguire l\'installazione con l\'opzione "Svuota il database" selezionata</p>';
							}
						}
					}
				}
			}
			if(!$error)
				$error = '<p style="color:red;">Operazione non eseguita: '.mysql_error().'</p>';
		}
		else{
			$error = '<p style="color:red;">Operazione non eseguita: campi incompleti.</p>';
		}
	}
	else{
		$error = '<p style="color:red;">Operazione non eseguita: non trovo il file di dump.</p>';
	}
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Installazione Database</title>
</head>

<body>
<h1>Installazione iniziale del Database.</h1>
<h3>Attenzione!</h3>
<p>
	Questa procedura sar&agrave; eseguita una tantum.<br>
    Una volta completata l'operazione questa funzionalit&agrave; sar&agrave; disattivata per motivi di sicurezza.
</p>
<p>
	Tenere presente che:<br>
	Il database verr&agrave; creato se non dovesse esistere.<br>
	L'installazione funziona correttamente se il file di dump non contiene commenti.
</p>
<?php if($error) echo $error; ?>
<form action="install.php" method="post">
<p>
	<label for="host">Host</label>
    <input type="text" id="host" name="host" value=""><br>
	<label for="database">Nome del database</label>
    <input type="text" id="database" name="database" value=""><br>
	<label for="username">Username</label>
    <input type="text" id="username" name="username" value=""><br>
    <label for="password">Password</label>
    <input type="password" id="password" name="password" value=""><br>
    <label for="svuota">Svuota il database prima di caricare i dati</label>
    <input type="checkbox" id="svuota" name="svuota" value="1"><br>
    <input type="submit" name="submit" value="Installa">
</p>
</form>
</body>
</html>
