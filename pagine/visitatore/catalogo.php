<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
session_start();

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>tipologia spedizioni</title>
	<link rel="stylesheet" href="../style1.css" type="text/css">
</head>

<body>

<div id="top">
    <img src="../../picture/logo.png" width="120" alt="Logo" class="logo" />

	<h1 class="title">ByteCourier3000</h1>
	
</div>

<div id="content">
   <div id="center" class="colonna">

     <h2>Tipologia spedizioni</h2>
     <br />
     
     
   </div>
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_visitatore.php");?>
   </div>
</div>


</body>
</html>