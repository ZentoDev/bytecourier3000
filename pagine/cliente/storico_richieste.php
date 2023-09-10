<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_cliente.php");
require_once("../../dati/lib_xmlaccess.php");

$docCr = openXML("../../dati/xml/crediti.xml");

$rootCr = $docCr->documentElement;  
$listaCr = $rootCr->childNodes;

function stampaRichieste($lista) {

    $table="<table>"; 
    $presente = 0; //questa variabile segnalerà la presenza di operazioni disponibili

	for ($pos = 0; $pos < $lista->length; $pos++) {
		$richiesta = $lista->item($pos);
        $stato = $richiesta->getAttribute('stato');
		if( ($stato == "accettata" || $stato == 'rifiutata') &&
            $richiesta->getAttribute('username') == $_SESSION['username'] ) {   //si selezionano le richieste "in attesa"

            $id_richiesta = $richiesta->getAttribute('id_richiesta');
			$crediti = $richiesta->getAttribute('crediti');

			$table.='<tr>
			         <th><strong>Id richiesta:</strong> '.$id_richiesta.'</th>
					 <td>
			         <strong>Crediti richiesti:</strong> '.$crediti.'<br />
			         </td>   
                     <td>
                     <strong>Stato:</strong> '.$stato.'<br />
                     </td>
			         </tr>';	
			$presente = 1;			
		}
	}
    $table.= '</table>';
    if ($presente == 0)  return 'Non ci sono richieste crediti concluse';
    return $table;
}            

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Storico richieste crediti</title>
    <link rel="shortcut icon" href="../../picture/favicon.png"/>
	<link rel="stylesheet" href="../style1.css" type="text/css"/>
    <link rel="stylesheet" href="../tabselezione.css" type="text/css"/>
</head>

<body>

<div id="top">
    <img src="../../picture/logo.png" width="120" alt="Logo" class="logo" />

	<h1 class="title">ByteCourier3000</h1>
    <p><strong>&nbspUtente: <?php echo $_SESSION['username'].' ('.$_SESSION['ruolo'].')'?> </strong></p>
</div>

<div id="content">
   <div id="center" class="colonna">
     <h2>Storico richieste crediti</h2>     
     <?php echo stampaRichieste($listaCr);?>
   </div>
   <div id="navbar" class="colonna">
   <?php require_once("menu_cliente.php");?>
   </div>
</div>


</body>
</html>