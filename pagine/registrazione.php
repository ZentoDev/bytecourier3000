<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

//variabili della form
$nome = "";
$cognome = "";
$data = "";
$username = "";
$pw = "";
$pw_rip = "";
$cf = "";
$tel = "";
$email = "";
$indirizzo = "";
$num_civ = "";
$citta = "";
$nazione = "";

if( isset($_POST['invio']) ){
    //Salvo il valore delle variabili inserite, ciò permette all'utente di non doverle reinserire in caso di ripetizione della form
	$nome = $_POST['nome'];
	$cognome = $_POST['cognome'];
	$data = $_POST['data'];
	$username = $_POST['username'];
    $pw = $_POST['password'];
    $pw_rip = $_POST['ripeti_pw'];
    $cf = $_POST['cf'];
    $tel = $_POST['tel'];
    $email = $_POST['email'];
    $indirizzo = $_POST['indirizzo'];
    $num_civ = $_POST['num_civ'];
    $citta = $_POST['citta'];
    $nazione = $_POST['nazione'];

    //verifica i valori inseriti nella form, 1 dati corretti, altrimenti messaggio di errore
    $ver = verifyForm();
    if( $ver == 1 ) {

        //addUser() si occupa dell'inserimento dei dati utente nel sistema, ritorna 1 se il processo avviene correttamente; -1 in caso di errori
        $add = addUser();
        if( $add == 1 )   $mex = "La registrazione è avvenuta correttamente!";
        
        else if( $add == 0 )   $mex = "Username gi&agrave; in uso, inserire un altro username";
        
        else    $mex = "Problemi interni nel processo di registrazione, si prega di contattare il supporto tecnico";    
    }    

    else $mex = $ver;
}

//verifica i valori inseriti nella form, ritorna 1 se i dati inseriti risultano corretti, altrimenti stampa il messaggio di errore relativo al campo non valido
function verifyForm(){

    if( !preg_match("/^[a-z-' ]*$/i", $_POST['nome']) )  return 'Il nome deve contenere solo caratteri alfabetici';
    if( !preg_match("/^[a-z-' ]*$/i", $_POST['cognome']) )  return 'Il cognome deve contenere solo caratteri alfabetici';
    if( !preg_match("/^[0-9]{9,15}$/", $_POST['tel']) )  return 'Il numero di telefono inserito non è valido';
    if( !filter_var( $_POST['email'], FILTER_VALIDATE_EMAIL) )  return 'L\'email non è corretta';
    if( !preg_match("/^[[:alpha:]]{6}[[:digit:]]{2}[[:alpha:]][[:digit:]]{2}[[:alpha:]][[:digit:]]{3}[[:alpha:]]$/i", $_POST['cf']) )  return 'Il codice fiscale non è corretto';
    if( !preg_match("/^[a-z-' ]*$/i", $_POST['indirizzo']) )  return 'L\'indirizzo inserito non è valido';
    if( !preg_match("/^[a-z-' ]*$/i", $_POST['citta']) )  return 'la città inserita non è valida';
    if( !preg_match("/^[a-z-' ]*$/i", $_POST['nazione']) )  return 'La nazione inserita non è valida';
    if( $_POST['ripeti_pw'] !== $_POST['password'] )  return 'Le password non coincidono';

    return 1;   //tutti i campi sono corretti
}

//si occupa dell'inserimento dei dati utente nel sistema, ritorna 1 se il processo avviene correttamente; -1 in caso di errori
function addUser(){

	require_once("../mysql/connection.php");
    if( !$connection_mysqli )   return -1;  //problemi di connessione al db, return -1

    $aggiunto = 0;
    //query per verificare l'esistenza di un utente con lo stesso username
    $select_query = "SELECT * FROM $user_table_name 
                    WHERE username = '{$_POST['username']}' ";
    
    $res = mysqli_query($connection_mysqli, $select_query);
    $row = mysqli_fetch_array($res);
    if( !$row ) {                               //se NON esiste un utente con lo stesso username, si può procedere; altrimenti return 0

	    //query per inserire il nuovo utente
	    $insert_query = "INSERT INTO $user_table_name
				        (username, password, nome, cognome, data_nascita, cf, email, tel, nazione, citta, indirizzo, num_civico, permesso, ban)
					    VALUES
					    ('{$_POST['username']}','{$_POST['password']}','{$_POST['nome']}', '{$_POST['cognome']}', '{$_POST['data']}','{$_POST['cf']}', '{$_POST['email']}', '{$_POST['tel']}', '{$_POST['nazione']}', '{$_POST['citta']}', '{$_POST['indirizzo']}', '{$_POST['num_civ']}', '1', '0' )
					    ";

	    try{
            mysqli_query($connection_mysqli, $insert_query);     //inserimento del nuovo utente, in caso di errori $aggiunto = -1
        }catch (Exception $e){
            $aggiunto = -1;
        }
         
        //se la query e la scrittura sul file xml avverranno correttamente $aggiunto = 1
        // $aggiunto != 1     -->  query di inserimento dei dati nel db andata a buon fine 
        // addCliente() == 1  -->  inserimento del cliente del file xml avvenuto correttamente
        if( $aggiunto!=-1 && addCliente() == 1 )   $aggiunto = 1;  
        
        else   $aggiunto = -1;    
	}
    
	mysqli_close($connection_mysqli);
	return $aggiunto;
}

//si occupa dell'inserimento di un elemento utente nel file clienti.xml; ritorna 1 se il processo avviene correttamente; -1 in caso di errori
function addCliente(){

    //Richiamo la mia libreria per la gestione degli xml e uso una funzione che crea un oggetto domDocument a partire dal corrispettivo file xml
    require_once("../dati/lib_xmlaccess.php");
    if ( !$doc = openXML("../dati/xml/clienti.xml") )  return -1;

    //il metodo documentElement() restituisce l'elemento radice del documento (in questo caso, "clienti")
	$root = $doc->documentElement;
	
	$newCliente = $doc->createElement("cliente");
	$root->appendChild($newCliente);
	
	$newCliente->setAttribute("username", $_POST['username']);
    $newCliente->setAttribute("crediti", 0);
	
    //permette di salvare il documento in un file xml
    printFileXML("../dati/xml/clienti.xml", $doc);
    return 1;			
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Registrazione</title>
    <link rel="shortcut icon" href="../picture/favicon.png"/>
	<link rel="stylesheet" href="style1.css" type="text/css">
</head>

<body>

<div id="top">
    <img src="../picture/logo.png" width="120" alt="Logo" class="logo" />

	<h1 class="title">ByteCourier3000</h1>
    <p><strong>&nbspUtente: visitatore</strong></p>
</div>

<div id="content">
   <div id="center" class="colonna" style="text-align: center;">

        <h1>Registrazione al sito</h1>

        <?php 
		if($_POST['invio']){
			echo '<h3>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;'.$mex.'</h3>';
		}
		?>

        <form action="registrazione.php" method="post" > 
            <div class="flex-container">
                <div>
                <strong>Nome</strong><br />
                <input type="text" name="nome" value="<?php echo $nome ?>" required><br />
                <strong>Username</strong><br />
	            <input type="text" name="username" value="<?php echo $username ?>" required><br />
                <strong>Email</strong><br />
	            <input type="text" name="email" value="<?php echo $email ?>" required><br />
                <strong>Codice fiscale</strong><br />
	            <input type="text" name="cf" value="<?php echo $cf ?>" required><br />
                <strong>Indirizzo residenza</strong><br />
	            <input type="text" name="indirizzo" value="<?php echo $indirizzo ?>" required><br />
                <strong>Città</strong><br />
	            <input type="text" name="citta" value="<?php echo $citta ?>" required><br />
                <strong>Conferma password</strong><br />
	            <input type="password" name="ripeti_pw" value="<?php echo $pw_rip ?>" required><br />
	            </div>

	            <div>
                <strong>Cognome</strong><br />
                <input type="text" name="cognome" value="<?php echo $cognome ?>" required><br />
                <strong>Data di nascita</strong><br />
	            <input type="date" name="data" value="<?php echo $data ?>" required><br />
                <strong>Telefono</strong><br />
	            <input type="text" name="tel" value="<?php echo $tel ?>" required><br />
                <strong>Numero civico</strong><br />
	            <input type="number" name="num_civ" value="<?php echo $num_civ ?>" min="1" required><br />
                <strong>Nazione</strong><br />
	            <input type="text" name="nazione" value="<?php echo $nazione ?>" required><br />
                <strong>Password</strong><br />
	            <input type="password" name="password" value="<?php echo $pw ?>" required><br />
	            </div>
            </div>
        
	        <div style="margin-bottom:10px">
                <button type="submit" name="invio" value="signup">Registrati al sito</button>
            </div>

        </form>


        <form action="registrazione.php" method="post">
		    <button type="submit" name="reset" value="reset" id="reset_signup" >Reset</button>
	    </form>


   </div>
   
   <div id="navbar" class="colonna">
    <ul id="menu">
     <li><a href="../index.php">Home</a></li>
     <li><a href="visitatore/informazioni.php">Informazioni</a></li>
     <li><a href="visitatore/catalogo.php">Tipologia spedizioni</a></li>
     <li><a href="visitatore/faq.php">FAQ</a></li>
     <li><a href="registrazione.php">Registrazione</a></li>  
	 <li><a href="login.php">Login</a></li>   
    </ul>
   </div>
</div>


</body>
</html>