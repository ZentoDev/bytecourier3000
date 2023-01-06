<?php 

//inizializzazione della sessione
session_start();

//verifico l'autentificazione dell'utente
if ($_SESSION['permesso']!=1000) header('Location: ../login.php');

?>