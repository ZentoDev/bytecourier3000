<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_admin.php");

//variabili della form
$nome = "";
$cognome = "";
$data = "";
$username = "";
$pw = "";
$permesso_user = "";

if( isset($_POST['invio']) ){
    //Salvo il valore delle variabili inserite, ciò permette all'utente di non doverle reinserire in caso di ripetizione della form
	$nome = $_POST['nome'];
	$cognome = $_POST['cognome'];
	$data = $_POST['data'];
	$username = $_POST['username'];
    $pw = $_POST['password'];
    $permesso_user = $_POST['permesso_user'];


    //addUser() si occupa dell'inserimento dei dati utente nel sistema, ritorna 1 se il processo avviene correttamente; -1 in caso di errori
    $add = addUser();
    if( $add == 1 )        $mex = "La creazione dell'utente &egrave; avvenuta correttamente!";
    else if( $add == 0 )   $mex = "Username gi&agrave; in uso, inserire un altro username";
    else                   $mex = "Problemi interni nel processo di registrazione, si prega di contattare il supporto tecnico";  
}

//si occupa dell'inserimento dei dati utente nel sistema, ritorna 1 se il processo avviene correttamente; -1 in caso di errori
function addUser(){

	require_once("../../mysql/connection.php");
    if( !$connection_mysqli )   return -1;  //problemi di connessione al db, return -1

    $aggiunto = 0;
    //query per verificare l'esistenza di un utente con lo stesso username
    $select_query = "SELECT * FROM $user_table_name 
                    WHERE username = '{$_POST['username']}' ";
    
    $res = mysqli_query($connection_mysqli, $select_query);
    $row = mysqli_fetch_array($res);
    if( !$row ) {                               //se NON esiste un utente con lo stesso username, si può procedere; altrimenti return 0

        $aggiunto = 1;
	    //query per inserire il nuovo utente
	    $insert_query = "INSERT INTO $user_table_name
				        (username, password, nome, cognome, data_nascita, permesso, ban)
					    VALUES
					    ('{$_POST['username']}','{$_POST['password']}','{$_POST['nome']}', '{$_POST['cognome']}', '{$_POST['data']}', '{$_POST['permesso_user']}', '0' )
					    ";
	    try{
            mysqli_query($connection_mysqli, $insert_query);     //inserimento del nuovo utente, in caso di errori $aggiunto = -1
        }catch (Exception $e){
            $aggiunto = -1;
        }
	}
    
	mysqli_close($connection_mysqli);
	return $aggiunto;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Creazione nuovo utente</title>
    <link rel="shortcut icon" href="../../picture/favicon.png"/>
	<link rel="stylesheet" href="../style1.css" type="text/css">
</head>

<body>

<div id="top">
    <img src="../../picture/logo.png" width="120" alt="Logo" class="logo" />

	<h1 class="title">ByteCourier3000</h1>
    <p><strong>&nbspUtente: <?php echo $_SESSION['username'].' ('.$_SESSION['ruolo'].')'?> </strong></p>
</div>

<div id="content">
   <div id="center" class="colonna" style="text-align: center;">

        <h1>Creazione nuovo utente avanzato</h1>

        <form action="creazione_utente.php" method="post" > 
            <div class="flex-container">
                <div>
                <strong>Nome</strong><br />
                <input type="text" name="nome" value="<?php echo $nome ?>" required><br />
                <strong>Username</strong><br />
	            <input type="text" name="username" value="<?php echo $username ?>" required><br />
                <strong>Password</strong><br />
	            <input type="text" name="password" value="<?php echo $pw ?>" required><br />
	            </div>
	            <div>
                <strong>Cognome</strong><br />
                <input type="text" name="cognome" value="<?php echo $cognome ?>" required><br />
                <strong>Data di nascita</strong><br />
	            <input type="date" name="data" value="<?php echo $data ?>" required><br />
                <strong>Tipologia utente</strong><br />
				<select name="permesso_user" size="2" required>
					<option value="10">Bite courier</option>
					<option value="100">Gestore</option>
				</select>
	            </div>
            </div>
        
	        <div style="margin-bottom:10px">
                <button type="submit" name="invio" value="signup">Crea utente</button>
            </div>

        </form>


        <form action="creazione_utente.php" method="post">
		    <button type="submit" name="reset" value="reset" id="reset_signup" >Reset</button>
	    </form>

        <?php 
		if(isset($_POST['invio'])){
			echo '<h3>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;'.$mex.'</h3>';
		}
		?>

   </div>
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_admin.php");?>
   </div>
</div>


</body>
</html>