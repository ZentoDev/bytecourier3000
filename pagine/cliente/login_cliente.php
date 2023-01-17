<?php 

//inizializzazione della sessione
session_start();

//verifico l'autentificazione dell'utente
if ($_SESSION['permesso']!=1) header('Location: ../login.php');

?>