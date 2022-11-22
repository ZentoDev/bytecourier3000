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
    <title>Registrazione</title>
    <link rel="shortcut icon" href="../picture/favicon.png"/>
	<link rel="stylesheet" href="style1.css" type="text/css">
</head>

<body>

<div id="top">
    <img src="../picture/logo.png" width="120" alt="Logo" class="logo" />

	<h1 class="title">ByteCourier3000</h1>
	
</div>

<div id="content">
   <div id="center" class="colonna" style="text-align: center;">

        <h1>Registrazione al sito</h1>

        <form action="registrazione.php" method="post" > 
        <div class="flex-container">
            <div>
            <strong>Nome</strong><br />
            <input type="text" name="nome" value="<?php echo '' ?>" required><br />
            <strong>Username</strong><br />
	        <input type="text" name="username" value="<?php echo '' ?>" required><br />
            <strong>Password</strong><br />
	        <input type="password" name="password" value="<?php echo '' ?>"  required><br />
	        </div>
	        <div>
            <strong>Cognome</strong><br />
            <input type="text" name="cognome" value="<?php echo '' ?>" required><br />
            <strong>Data di nascita</strong><br />
	        <input type="date" name="data" value="<?php echo''  ?>" required><br />
	        <strong>Conferma password</strong><br />
	        <input type="password" name="ripeti_pw" value="<?php echo''  ?>"  required><br />
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