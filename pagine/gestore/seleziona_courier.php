<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_gestore.php");

require_once("../../dati/lib_xmlaccess.php");

$docOp = openXML("../../dati/xml/operazioni.xml");

$rootOp = $docOp->documentElement;  
$listaOp = $rootOp->childNodes;

//Associa il corriere selezionato all'operazione
if( isset($_POST['selezione']) ) {

    $mex = "associazione non avvenuta, contattare il supporto tecnico";
    $find = 0;
    for ($pos = 0; $pos < $listaOp->length && $find==0; $pos++) {
		$operazione = $listaOp->item($pos);
        
	    if( $_SESSION['cod_op'] == $operazione->getAttribute('id_operazione') ) {
            $operazione->setAttribute('username_bytecourier', $_POST['selezione']);
            printFileXml("../../dati/xml/operazioni.xml", $docOp);    

            $find = 1;
            $mex = "Il bytecourier ".$_POST['selezione']." è stato associato correttamente all'operazione ".$_SESSION['cod_op'];
        }
    }
}

function stampaCourier($listOp){

	require_once("../../mysql/connection.php");
    if( !$connection_mysqli )   return -1;  //problemi di connessione al db, return -1
    
    $aggiunto = 0;
    //query per ottenere gli utenti che sono byte courier
    $select_query = "SELECT username FROM $user_table_name 
                    WHERE permesso = 10 ";
    
    $res = mysqli_query($connection_mysqli, $select_query);
    while ($row = mysqli_fetch_assoc($res)) {
        $listByte[] = $row['username'];
    }

    $table="<table>"; 
	foreach( $listByte as $byte ) {
        
        $table.=
               '<tr>
                <th><strong>Nome:</strong> '.$byte.'<br />
                <td>   
                <strong>Numero di operazioni in corso:</strong> '.countOp($byte, $listOp).'<br />
                </td>   
                <td>
                <form action="seleziona_courier.php" method="post">
                <div id="buttons">
                <button type="submit" name="selezione" value="'.$byte.'" >Seleziona corriere</button>
                </div>
                </td>
                </form>
                </td>
                </tr>';
        $aggiunto = 1;
    }
    $table .= '</table>';
    if( $aggiunto == 0)  return 'Non sono presenti byte courier';

    return $table;
}
	

//conta il numero di operazione attualmente prese in carico da un bytecourier
function countOp($byte, $listOp) {

    $count = 0;
    for ($pos = 0; $pos < $listOp->length; $pos++) {
		$operazione = $listOp->item($pos);
        
	    if( $byte == $operazione->getAttribute('username_bytecourier') ) {    
            $count++;
        }
    }
    return $count;
}


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Assegnazione byte courier</title>
    <link rel="shortcut icon" href="../../picture/favicon.png"/>
	<link rel="stylesheet" href="../style1.css" type="text/css">
    <link rel="stylesheet" href="../tabselezione.css" type="text/css">
</head>

<body>

<div id="top">
    <img src="../../picture/logo.png" width="120" alt="Logo" class="logo" />

	<h1 class="title">ByteCourier3000</h1>
    <p><strong>&nbspUtente: <?php echo $_SESSION['username'].' ('.$_SESSION['ruolo'].')'?> </strong></p>
</div>

<div id="content">
   <div id="center" class="colonna">
   <h2 style="margin-left:50px; text-align: center;">Selezione byte courier per operazione</h2>
	<br />
    <?php 
    if( !isset($_POST['selezione']) ) 
        echo stampaCourier($listaOp);
    else echo $mex;
    ?>

   </div>
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_gestore.php");?>
   </div>
</div>


</body>
</html>