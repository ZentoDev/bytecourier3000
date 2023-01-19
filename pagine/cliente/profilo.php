<?php
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_cliente.php");

//se mancano dati utente, vengono letti dal db
if( !isset( $_SESSION['nome'], $_SESSION['cognome'],  $_SESSION['conome'], $_SESSION['pw'], $_SESSION['data'] )  ) {

    $err = 'errore interno, contattare un amministratore';
    require_once("../../mysql/connection.php");
    if( $connection_mysqli ) {
        $err = '';
        //query per accedere ai dati dell'utente
        $select_query = "SELECT * FROM $user_table_name
        WHERE username = \"$_SESSION[username]\"";

        if ( $res = mysqli_query($connection_mysqli, $select_query) ) {
 
            $row = mysqli_fetch_array($res);  

            $_SESSION['nome'] = $row['nome'];
            $_SESSION['cognome'] = $row['cognome'];
            $_SESSION['pw'] = $row['password'];
            $_SESSION['data'] = $row['data_nascita'];
        }
        mysqli_close($connection_mysqli);
    }
}

//viene eseguita in caso di attivazione della form
if($_POST['invio'])	{
    if ( $_POST['password'] == $_POST['ripeti_pw'] )  $mod = modifica_password();

    else $mod = 'Le password inserite non coincidono';
}


function modifica_password() {

	require_once("../../mysql/connection.php");
    if( !$connection_mysqli )   return 'problemi interni al sistema, contattare un amministratore';

	$modificato="";
    if( !$_POST['username'] ) return 'problemi interni al sistema, contattare un amministratore';

    //verifica che esiste nel database l'utente con il medesimo username
	$select_query="SELECT * FROM $user_table_name WHERE username = \"$_POST[username]\";";
	if (!$res = mysqli_query($connection_mysqli, $select_query)) {
		$modificato.="problemi interni al sistema,  contattare un amministratore<br />";
		return $modificato;
	}

	//per ogni valore inserito che differisce da quello già presente avviene una query di aggiornamento

    if($_POST['password'] != $_SESSION['pw']){
		$update_query="UPDATE $user_table_name SET password = '{$_POST['password']}' WHERE username = \"$_POST[username]\";";
		if ($res = mysqli_query($connection_mysqli, $update_query)) {
			$modificato.="-La password &egrave; stata modificata correttamente in $_POST[password]<br />";
		}
	}
	
	if( $modificato == "") return '-Non &egrave; stato modificato alcun valore';

    return $modificato;	
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Profilo</title>
    <link rel="shortcut icon" href="../../picture/favicon.png"/>
	  <link rel="stylesheet" href="../style1.css" type="text/css">
	  <link rel="stylesheet" href="../tabselezione.css" type="text/css">
</head>

<body>

<div id="top">
    <img src="../../picture/logo.png" width="120" alt="Logo" class="logo" />

	<h1 class="title">ByteCourier3000</h1>
	
</div>

<div id="content">
   <div id="center" class="colonna">

     <h2 style="margin-left:50px; text-align: center;">Profilo di <?php echo $_SESSION['username'];?> </h2>
	 <?php if( $_POST['invio'])   echo "<p><strong>$mod</strong></p>"; ?>
     <form action="profilo.php" method="post" > 
            <div class="flex-container">
                <div>
                <strong>Nome: </strong><?php echo $_SESSION['nome']?> <br />
                <strong>Cognome: </strong><?php echo $_SESSION['cognome']?> <br /> <br /><br /><br /><br />
                <strong>Nuova password</strong><br />
	            <input type="password" name="password" value="">
	            </div>
	            <div>
                <strong>Data di nascita: </strong><?php echo $_SESSION['data']?> <br /><br /><br /><br /><br /><br />
	            <strong>Conferma password</strong><br />
	            <input type="password" name="ripeti_pw" value="" required>
	            </div>
            </div>
        
	        <div style="margin-bottom:10px; text-align: center;">
                <button type="submit" name="invio" value="signup">Modifica password</button>
            </div>

        </form>
   <?php echo $err;?>
   </div>
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_cliente.php");?>
   </div>
</div>


</body>
</html>