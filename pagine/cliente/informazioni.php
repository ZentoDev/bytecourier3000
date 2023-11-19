<?php
ini_set('display_errors', 1);
session_start();
require_once("../../dati/lib_xmlaccess.php");

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Informazioni</title>
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
   <div id="center" class="colonna">

     <h2>Informazioni</h2>
     <br />
     <h3 class="colorato">Chi siamo</h3>
	 <p>Dal 1989 siamo leader del mercato nel mondo delle spedizioni. 
        Bytecourier3000 offre soluzioni affidabili per la spedizione delle merci.
     </p>
	 <hr />
	 <h3 class="colorato">Dove siamo</h3>
	 <p>in tutta Italia!</p>
	 <hr />
	 <h3 class="colorato">Contatti</h3>
	 <p>Email: <a href="mailto:info@bytecourier3000.com">info@bytecourier3000.com</a> <br />
	 Telefono: 800 6655 42
	 </p>
   </div>
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_cliente.php");?>
   </div>
</div>


</body>
</html>