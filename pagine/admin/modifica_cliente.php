<?php
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_admin.php");


if($_POST['invio'])	$mod = modifica_utenti();


function modifica_utenti(){

	require_once("../../mysql/connection.php");
    if( !$connection_mysqli )   return 'problemi di connessione al db';

	$modificato="";
    if( !$_POST['username'] ) return 'problema con la lettura dello username dell\'utente selezionato';

    //verifica che esiste nel database l'utente con il medesimo username
	$select_query="SELECT * FROM $user_table_name WHERE username = \"$_POST[username]\";";
	if (!$res = mysqli_query($connection_mysqli, $select_query)) {
		$modificato.="Non &egrave; presente alcun utente con username = \"$_POST[username]\"<br />";
		return $modificato;
	}

	//per ogni valore inserito che differisce da quello già presente avviene una query di aggiornamento
	if($_POST['nome'] != $_SESSION['nome']){
		$update_query="UPDATE $user_table_name SET nome = '{$_POST['nome']}' WHERE username = \"$_POST[username]\";";
		if ($res = mysqli_query($connection_mysqli, $update_query)) {
			$modificato.="-Il nome dell'utente &egrave; stato modificato correttamente in $_POST[nome]\n<br />";
		}
	}
    if($_POST['cognome'] != $_SESSION['cognome']){
		$update_query="UPDATE $user_table_name SET cognome = '{$_POST['cognome']}' WHERE username = \"$_POST[username]\";";
		if ($res = mysqli_query($connection_mysqli, $update_query)) {
			$modificato.="-Il cognome dell'utente &egrave; stato modificato correttamente in $_POST[cognome]\n<br />";
		}
	} 
	if($_POST['cf'] != $_SESSION['cf']){
		$update_query="UPDATE $user_table_name SET cf = '{$_POST['cf']}' WHERE username = \"$_POST[username]\";";
		if ($res = mysqli_query($connection_mysqli, $update_query)) {
			$modificato.="-Il codice fiscale dell'utente &egrave; stato aggiornato correttamente in $_POST[cf]\n<br />";
		}
	} 
	if($_POST['email'] != $_SESSION['email']){
		$update_query="UPDATE $user_table_name SET email = '{$_POST['email']}' WHERE username = \"$_POST[username]\";";
		if ($res = mysqli_query($connection_mysqli, $update_query)) {
			$modificato.="-L'email dell'utente &egrave; stata modificata correttamente in $_POST[email]\n<br />";
		}
	} 
	if($_POST['tel'] != $_SESSION['tel']){
		$update_query="UPDATE $user_table_name SET tel = '{$_POST['tel']}' WHERE username = \"$_POST[username]\";";
		if ($res = mysqli_query($connection_mysqli, $update_query)) {
			$modificato.="-Il numero di telefono dell'utente &egrave; stato modificato correttamente in $_POST[tel]\n<br />";
		}
	} 
	if($_POST['indirizzo'] != $_SESSION['indirizzo'] || 
	   $_POST['num_civico'] != $_SESSION['num_civico'] ||
	   $_POST['citta'] != $_SESSION['citta'] || 
	   $_POST['nazione'] != $_SESSION['nazione'] )  
	{
		$update_query="UPDATE $user_table_name SET indirizzo = '{$_POST['indirizzo']}' and num_civico = '{$_POST['num_civico']}' and citta = '{$_POST['citta']}' and nazione = '{$_POST['nazione']}' WHERE username = \"$_POST[username]\";";
		if ($res = mysqli_query($connection_mysqli, $update_query)) {
			$indirizzo = $_POST['indirizzo'].' '.$_POST['num_civico'].', '.$_POST['citta'].', '.$_POST['nazione'];
			$modificato.="-L'indirizzo di residenza dell'utente &egrave; stato modificato correttamente in '$indirizzo'\n<br />";
		}
	} 
    if($_POST['data'] != $_SESSION['data']){
		$update_query="UPDATE $user_table_name SET data_nascita = '{$_POST['data']}' WHERE username = \"$_POST[username]\";";
		if ($res = mysqli_query($connection_mysqli, $update_query)) {
			$modificato.="-La data di nascita dell'utente &egrave; stata modificata correttamente in $_POST[data]<br />";
		}
	}
    if($_POST['password'] != $_SESSION['pw']){
		$update_query="UPDATE $user_table_name SET password = '{$_POST['password']}' WHERE username = \"$_POST[username]\";";
		if ($res = mysqli_query($connection_mysqli, $update_query)) {
			$modificato.="-La password dell'utente &egrave; stata modificata correttamente in $_POST[password]<br />";
		}
	}
	if($_POST['ban'] != $_SESSION['ban']){	
        $update_query="UPDATE $user_table_name SET ban = '{$_POST['ban']}' WHERE username = \"$_POST[username]\";";
        if ($res = mysqli_query($connection_mysqli, $update_query)) {
			if( $_POST['ban'] == 0) $stato_ban = '"non attivo"';
			else                    $stato_ban = '"attivo"';
            $modificato.="-Lo stato del ban dell'utente &egrave; stato modificato correttamente in $stato_ban<br />";
        }	
	}
	
	if( $modificato == "") return '-Non &egrave; stato modificato alcun valore';

    return $modificato;	
}

/*l'aggiornamento delle variabili di sessione è inportante che avvenga dopo l'esecuzione della funzione di modifica (quando prevista)
in quanto i valori inseriti devono essere confrontati con i valori precedenti */
$_SESSION['nome'] = $_POST['nome'];
$_SESSION['cognome'] = $_POST['cognome'];
$_SESSION['pw'] = $_POST['password'];
$_SESSION['ban'] = $_POST['ban'];
$_SESSION['email'] = $_POST['email'];
$_SESSION['cf'] = $_POST['cf'];
$_SESSION['tel'] = $_POST['tel'];
$_SESSION['indirizzo'] = $_POST['indirizzo'];
$_SESSION['num_civico'] = $_POST['num_civico'];
$_SESSION['citta'] = $_POST['citta'];
$_SESSION['nazione'] = $_POST['nazione'];
$_SESSION['data'] = $_POST['data'];
/*modifica del formato della data da d-m-Y a Y-m-d, 
il controllo dell'esistenza della data è reso necessario dal comportamento della funzione srtotime che in caso di 
variabile vuota aggiunge una data di default (1/1/1970), dato che preferiamo un campo vuoto ad una data fasulla 
evitiamo la sua esecuzione in questo caso*/
if($_SESSION['data']){
	$timestamp = strtotime($_SESSION['data']); 
    $_SESSION['data'] = date("Y-m-d", $timestamp );
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Modifica cliente</title>
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

     <h2 style="margin-left:50px; text-align: center;">Modifica del cliente <?php echo $_POST['username'];?> </h2>
	 <?php if( $_POST['invio'])   echo "<p><strong>$mod</strong></p>"; ?>
     <form action="modifica_cliente.php" method="post" > 
            <div class="flex-container">
                <div>
                <strong>Nome</strong><br />
                <input type="text" name="nome" value="<?php echo $_SESSION['nome'] ?>"><br />
				<strong>Codice fiscale: </strong> <br />
				<input type="text" name="cf" value="<?php echo $_SESSION['cf']?>"><br />
				<strong>Email: </strong><br />
                <input type="text" name="email" value="<?php echo $_SESSION['email']?>"><br />
				<strong>Indirizzo residenza: </strong><br />
                <input type="text" name="indirizzo" value="<?php echo $_SESSION['indirizzo']?>"><br />
				<strong>citt&agrave;: </strong><br />
                <input type="text" name="citta" value="<?php echo $_SESSION['citta']?>"><br />
                <strong>Password</strong><br />
	            <input type="text" name="password" value="<?php echo $_SESSION['pw'] ?>"><br /><br />
				<label for="ban"><strong>Stato ban:</strong></label><br />
                <input type="radio" name="ban" value="0" <?php if ($_SESSION['ban'] == 0) echo 'checked';?> >ban non attivo <br />
                <input type="radio" name="ban" value="1" <?php if ($_SESSION['ban'] == 1) echo 'checked';?> >ban attivo <br />
	            </div>

	            <div>
                <strong>Cognome</strong><br />
                <input type="text" name="cognome" value="<?php echo $_SESSION['cognome'] ?>"><br />
                <strong>Data di nascita</strong><br />
	            <input type="date" name="data" value="<?php echo $_SESSION['data'] ?>"><br />
				<strong>Telefono: </strong><br />
                <input type="tel" name="tel" value="<?php echo $_SESSION['tel']?>"><br />
				<strong>Numero civico: </strong><br />
                <input type="number" name="num_civico" value="<?php echo $_SESSION['num_civico']?>"><br />
				<strong>Nazione: </strong><br />
                <input type="text" name="nazione" value="<?php echo $_SESSION['nazione']?>"><br /><br />
				<p>
	            </div>
            </div>
        
	        <div style="margin-bottom:10px; text-align: center;">
                <input type="hidden" name="username" value="<?php echo $_POST['username'] ?>">
                <button type="submit" name="invio" value="signup">Modifica valori</button>
            </div>

        </form>

   </div>
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_admin.php");?>
   </div>
</div>


</body>
</html>