<?php 

//inizializzazione della sessione
session_start();

//verifico l'autentificazione dell'utente
if ($_SESSION['permesso']!=100) header('Location: ../login.php');

?>