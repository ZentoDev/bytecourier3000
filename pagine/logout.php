<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

session_start();
//permette di cancellare le chiavi e i valori dell'array $_SESSION
unset($_SESSION);
//session_destroy() permette di distruggere i dati della sessione memorizzati nella memoria della sessione
session_destroy();

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Logout</title>
	<link rel="stylesheet" href="style1.css" type="text/css">
</head>

<body>

<div id="top">
    <img src="../picture/logo.png" width="120" alt="Logo" class="logo" />

	<h1 class="title">ByteCourier3000</h1>
	
</div>

<div id="content">
   <div id="center" class="colonna">

    <h2>Hai effettuato il logout!</h1>
    
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