<?php 

//inizializzazione della sessione
session_start();

//verifico l'autentificazione dell'utente
if ($_SESSION['permesso']!=1) header('Location: ../login.php');

//verifico lo stato dell'account dell'utente
if($_SESSION['ban'] == 1){
    unset($_SESSION);
    //session_destroy() permette di distruggere i dati della sessione memorizzati nella memoria della sessione
    session_destroy();
    header('Location: ../ban.php');
    exit();
}

?>