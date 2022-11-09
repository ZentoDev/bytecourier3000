<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Homepage</title>
	<link rel="stylesheet" href="style1.css" type="text/css">
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
   <?php require_once("menu_courier.php");?>
   </div>
</div>


</body>
</html>