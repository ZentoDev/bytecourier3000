<?php
ini_set('display_errors', 0);
session_start();

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Homepage</title>
	<link rel="stylesheet" href="pagine/style1.css" type="text/css">
</head>

<body>

<div id="top">
    <img src="picture/logo.png" width="120" alt="Logo" class="logo" />

	<h1 class="title">ByteCourier3000</h1>
	
</div>

<div id="content">
  <div id="center" class="colonna">
    <h2>Benvenuto!</h2>
    <img src="picture/corriere.webp" width="50%" alt="corriere" />
	  <p>
     Oggi lo shopping online richiede massima flessibilità, velocità ed efficienza in ogni fase, anche durante la consegna. Per questo mettiamo a tua disposizione una gamma di servizi dedicati al mondo Ecommerce, per rendere la tua attività ancora più semplice, sicura e performante.
     <br /><br />
     <strong>Scopri i servizi che possono far crescere il tuo business online! </strong>
    </p>
  </div>
   
  <div id="navbar" class="colonna">
    <ul id="menu">
     <li><a href="index.php">Home</a></li>
     <li><a href="pagine/visitatore/informazioni.php">Informazioni</a></li>
     <li><a href="pagine/visitatore/catalogo.php">Tipologia spedizioni</a></li>
     <li><a href="pagine/visitatore/faq.php">FAQ</a></li>
     <li><a href="registrazione.php">Registrazione</a></li>  
	   <li><a href="pagine/login.php">Login</a></li>
    </ul>
  </div>
</div>


</body>
</html>