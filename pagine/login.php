<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

$datimancanti=0;   //$datimancanti=1 Non sono stati inseriti tutti i dati necessari all'autentificazione dell'utente
$accessonegato=0;  //$accessonegato=1 I dati inseriti non sono validi per l'autentificazione dell'utente

//nel caso in cui si provenga dalla form
if(isset($_POST['invio'])){

    require_once("../mysql/connection.php");    //accedo al database

	//nel caso siano stati inseriti sia la password che lo username
    if ($_POST['username'] && $_POST['password']) {
		//verifico, attraverso una query, se lo username e la password corrispondono a quelle di un utente nella tabella users
		$select_query = "SELECT *
                         FROM $user_table_name
                         WHERE username = \"{$_POST['username']}\" AND password =\"{$_POST['password']}\" ";
        //se la query e' stata eseguita correttamente
		if ($res = mysqli_query($connection_mysqli, $select_query)) {
			
			$row = mysqli_fetch_array($res);
            //se $row e' diverso da null, ovvero la query precedentemente eseguita mi ha dato un risultato non nullo, allora
			//lo username e la password corrispondono effettivamente ad un utente della tabella users.
			if ($row) {  
			    //inizializzo la sessione e memorizzo una serie di informazioni nell'array $_SESSION[]
				session_start();
				$_SESSION['id_utente']=$row['user_id'];
				$_SESSION['username']=$row['username'];
                $_SESSION['permesso']=$row['permesso'];
                $_SESSION['ban']=$row['ban'];
                $_SESSION['nome']=$row['nome'];
                $_SESSION['cognome']=$row['cognome'];
                $_SESSION['data_nascita']=$row['data_nascita'];
				$_SESSION['data_login']=time();

				//indirizzo il client verso la pagina iniziale del sito
                if($_SESSION['permesso'] == 1) {   //1 = cliente
                    header('Location: cliente/home_cliente.php');
                    exit();
                }

                if($_SESSION['permesso'] == 10) {   //10 = byte courier
                    header('Location: courier/home_courier.php');
                    exit();
                }

                if($_SESSION['permesso'] == 100) {   //100 = gestore
                    header('Location: gestore/home_gestore.php');
                    exit();
                }

                if($_SESSION['permesso'] == 1000) {   //1000 = amministratore
                    header('Location: admin/home_admin.php');
                    exit();
                }

                //ruolo non trovato
                header('Location: ../index.php');    
                exit();
            }
			else {$accessonegato=1;}
		}
		
	}
	else {$datimancanti=1;}
    
    mysqli_close($connection_mysqli);
}




echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Login</title>
	<link rel="stylesheet" href="style1.css" type="text/css">
</head>

<body>

<div id="top">
    <img src="../picture/logo.png" width="120" alt="Logo" class="logo" />

	<h1 class="title">ByteCourier3000</h1>
	
</div>

<div id="content">
   <div id="center" class="colonna">

     <br />
     <div class="log">
			<h1>Accedi al tuo account</h1>
			<form action="<?php $_SERVER['PHP_SELF']?>" method="post">
			<p>Username: <input type="text" name="username" value="" size="40" /></p>
			<p>Password: <input type="password" name="password" value="" size="40" /></p>
			<p>
				<input type="reset" name="reset" value="Reset">
				<input type="submit" name="invio" value="Login">
			</p>
		    </form>
     </div>
   </div>
   
   <div id="navbar" class="colonna">
    <ul id="menu">
     <li><a href="../index.php">Home</a></li>
     <li><a href="visitatore/informazioni.php">Informazioni</a></li>
     <li><a href="visitatore/catalogo.php">Tipologia spedizioni</a></li>
     <li><a href="visitatore/faq.php">FAQ</a></li>
	 <li><a href="login.php">Login / Sign up</a></li>
    </ul>
   </div>
</div>


</body>
</html>