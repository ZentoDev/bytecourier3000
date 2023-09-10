<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_cliente.php");

$err = '';
//se mancano dati utente, vengono letti dal db (necessario quando si carica la pagina per la prima volta durante la sessione)
if( !isset( $_SESSION['nome'], $_SESSION['cognome'],  $_SESSION['cognome'], $_SESSION['pw'], $_SESSION['data'],  $_SESSION['cf'],  $_SESSION['email'],  $_SESSION['tel']) ){
    
    require_once("../../mysql/connection.php");
    if( !$connection_mysqli )   return 'problemi interni al sistema, contattare un amministratore';
    //query per accedere ai dati dell'utente
    $select_query = "SELECT * FROM $user_table_name
    WHERE username = \"$_SESSION[username]\"";

    if ( $res = mysqli_query($connection_mysqli, $select_query) ) {
 
        $row = mysqli_fetch_array($res);  

        $_SESSION['nome'] = $row['nome'];
        $_SESSION['cognome'] = $row['cognome'];
        $_SESSION['pw'] = $row['password'];
        $_SESSION['data'] = $row['data_nascita'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['cf'] = $row['cf'];
        $_SESSION['tel'] = $row['tel'];
    }
}

//viene eseguita in caso di attivazione della form
if( isset($_POST['invio']) ) $mod = modifica_valori();


function modifica_valori(){

    require_once("../../mysql/connection.php");
    if( !$connection_mysqli )   $mod = 'problemi interni al sistema, contattare un amministratore0';

	$modificato="";

    //verifica che esiste nel database l'utente con il medesimo username
	$select_query="SELECT * FROM $user_table_name WHERE username = \"$_SESSION[username]\";";
	if (!$res = mysqli_query($connection_mysqli, $select_query)) {
		$modificato.="problemi interni al sistema,  contattare un amministratore<br />";
		return $modificato;
	}

	//per ogni valore inserito che differisce da quello già presente avviene una query di aggiornamento
	if($_POST['email'] != $_SESSION['email']){

        if( filter_var( $_POST['email'], FILTER_VALIDATE_EMAIL) ) {
            $update_query="UPDATE $user_table_name SET email = '{$_POST['email']}' WHERE username = \"$_SESSION[username]\";";
            if ($res = mysqli_query($connection_mysqli, $update_query)) {
                $modificato.="-L'email dell'utente &egrave; stata modificata correttamente in $_POST[email]\n<br />";
                $_SESSION['email'] = $_POST['email'];
            }
        }
        
        else  $modificato.= '-L\'email non è corretta';
	} 

	if($_POST['tel'] != $_SESSION['tel']){

        if( preg_match("/^[0-9]{9,15}$/", $_POST['tel']) ) {
            $update_query="UPDATE $user_table_name SET tel = '{$_POST['tel']}' WHERE username = \"$_SESSION[username]\";";
            if ($res = mysqli_query($connection_mysqli, $update_query)) {
                $modificato.="-Il numero di telefono dell'utente &egrave; stato modificato correttamente in $_POST[tel]\n<br />";
                $_SESSION['tel'] = $_POST['tel'];
            }
        }

        else  $modificato.= '-Il numero di telefono inserito non è valido';
	} 

	if($_POST['indirizzo'] != $_SESSION['indirizzo'] || $_POST['num_civico'] != $_SESSION['num_civico'] ||
	   $_POST['citta'] != $_SESSION['citta']         || $_POST['nazione'] != $_SESSION['nazione'] )  
	{
        if( preg_match("/^[a-z-' ]*$/i", $_POST['indirizzo']) && 
            preg_match("/^[a-z-' ]*$/i", $_POST['citta']) && preg_match("/^[a-z-' ]*$/i", $_POST['nazione']) ) {

            $update_query="UPDATE $user_table_name SET indirizzo = '{$_POST['indirizzo']}', num_civico = '{$_POST['num_civico']}', citta = '{$_POST['citta']}', nazione = '{$_POST['nazione']}' WHERE username = \"$_SESSION[username]\";";
            if ($res = mysqli_query($connection_mysqli, $update_query)) {
                $indirizzo = $_POST['indirizzo'].' '.$_POST['num_civico'].', '.$_POST['citta'].', '.$_POST['nazione'];
                $modificato.="-L'indirizzo di residenza dell'utente &egrave; stato modificato correttamente in '$indirizzo'\n<br />";
                $_SESSION['indirizzo'] = $_POST['indirizzo'];
                $_SESSION['num_civico'] = $_POST['num_civico'];
                $_SESSION['citta'] = $_POST['citta'];
                $_SESSION['nazione'] = $_POST['nazione'];
            }
        }

        else  $modificato.= '-L\'indirizzo, la città o la nazione inserita non è valida';
	} 
    
    if($_POST['password'] != $_SESSION['pw'] ){
        //verifico che le password inserite coincidano, in tal caso procedo con l'aggiornamento del db
        if ( $_POST['password'] == $_POST['ripeti_pw'] ) {
            $update_query="UPDATE $user_table_name SET password = '{$_POST['password']}' WHERE username = \"$_SESSION[username]\";";
            if ($res = mysqli_query($connection_mysqli, $update_query)) {
                $modificato.="-La password dell'utente &egrave; stata modificata correttamente in $_POST[password]<br />";
                $_SESSION['pw'] = $_POST['password'];
            }
        }
        else $modificato.="-Le password inserite non coincidono<br />";
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
    <p><strong>&nbspUtente: <?php echo $_SESSION['username'].' ('.$_SESSION['ruolo'].')'?> </strong></p>
</div>

<div id="content">
   <div id="center" class="colonna">

     <h2 style="margin-left:50px; text-align: center;">Profilo di <?php echo $_SESSION['username'];?> </h2>
	 <?php if( isset($_POST['invio']) )   echo "<p><strong>$mod</strong></p>"; ?>
     <form action="profilo.php" method="post" > 
            <div class="flex-container">
                <div>
                <strong>Nome: </strong><?php echo $_SESSION['nome']?> <br />
                <strong>Cognome: </strong><?php echo $_SESSION['cognome']?> <br /><br />
				<strong>Indirizzo residenza: </strong><br />
                <input type="text" name="indirizzo" value="<?php echo $_SESSION['indirizzo']?>"><br />
				<strong>citt&agrave;: </strong><br />
                <input type="text" name="citta" value="<?php echo $_SESSION['citta']?>"><br />
                <strong>Email: </strong><br />
                <input type="text" name="email" value="<?php echo $_SESSION['email']?>"><br /><br />
                <strong>Nuova password</strong><br />
	            <input type="password" name="password" value="<?php echo $_SESSION['pw']?>">
	            </div>
	            <div>
                <strong>Data di nascita: </strong><?php echo $_SESSION['data']?> <br />
                <strong>Codice fiscale: </strong><?php echo $_SESSION['cf']?> <br /><br />
                <strong>Numero civico: </strong><br />
                <input type="number" name="num_civico" value="<?php echo $_SESSION['num_civico']?>"><br />
				<strong>Nazione: </strong><br />
                <input type="text" name="nazione" value="<?php echo $_SESSION['nazione']?>"><br />
                <strong>Telefono: </strong><br />
                <input type="tel" name="tel" value="<?php echo $_SESSION['tel']?>"><br /><br />
	            <strong>Conferma password</strong><br />
	            <input type="password" name="ripeti_pw" value="">
	            </div>
            </div>
        
	        <div style="margin-bottom:10px; text-align: center;">
                <button type="submit" name="invio" value="signup">Modifica valori</button>
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