<?php
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_courier.php");

require_once("../../dati/lib_xmlaccess.php");
$docOp = openXML("../../dati/xml/operazioni.xml");
$docOrd = openXML("../../dati/xml/ordini.xml");

$rootOp = $docOp->documentElement;  
$listaOp = $rootOp->childNodes;

$rootOrd = $docOrd->documentElement;
$listaOrd = $rootOrd->childNodes;

if($_POST['dettagli']){

	$mess = mostraDettagli($docOp);
}


function stampaOperazioni($listOp, $listOrd){

    $coin =0;  // =1 segnala che è stata trovato trovato l'ordine associato all'operazione

	$table="<table>";  

	for ($pos = 0; $pos < $listOp->length; $pos++) {
		$operazione = $listOp->item($pos);

		if( $operazione->getAttribute('username_bitecourier') ) {    //mostra le operazioni prese in carico dall'operatore

			$id_operazione = $operazione->getAttribute('id_ordine');

		    for ( $i = 0; $i < $listOrd->length && $coin == 0; $i++ ) {
				$ordine = $listOrd->item($i);
			    $id_ordine = $ordine->getAttribute('id_richiesta');

			    if( $id_ordine ==  $id_operazione) {

					$destinatario = $ordine->firstChild;  //nodo destinatario
			        $nome = $destinatario->firstChild->textContent;  //nome
			        $nome .= ' '. $destinatario->lastChild->textContent;  //cognome
				    $destinazione = $destinatario->nextSibling->textContent;  //via
					$destinatario = $ordine->lastChild; //nodo destinazione
					$destinazione .= ', '. $destinatario->getAttribute('citta');
                    $stato = $operazione->getAttribute('stato');   

				    $table.='<tr>
				              <th><strong>Id operazione:</strong> '.$id_operazione.'</th>
				             <td>   
				              <strong>Destinazione:</strong> '.$destinazione.'<br />
				              <strong>Destinatario:</strong> '.$nome.'<br />
					          <strong>Stato:</strong> '.$stato.'<br />
				             </td>   
			            	 <td>
				              <form action="dettagli_operazione.php" method="post">
				              <div id="buttons">
				               <button type="submit" name="id_operazione" value="'.$id_operazione.'" >Dettagli</button>
				              </div>
				              </form>
				             </td>
				             </tr>';
				    $coin = 1;
					
				}
			}
		}
            
    }

	if($coin == 0)    echo $table = "<p>Non sono presenti operazioni</p>";
    
	else{
		$table.="</table>";
		echo $table;
	}
	
}


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Operazioni in corso</title>
    <link rel="shortcut icon" href="../../picture/favicon.png"/>
	<link rel="stylesheet" href="../style1.css" type="text/css">
</head>

<body>

<div id="top">
    <img src="../../picture/logo.png" width="120" alt="Logo" class="logo" />

	<h1 class="title">ByteCourier3000</h1>
	
</div>

<div id="content">
   <div id="center" class="colonna">
     <h2>Operazioni in corso</h2>

     <?php 
	 if( $_POST['dettagli'])   echo "<p><strong>$mess</strong></p>";

	 stampaOperazioni($listaOp, $listaOrd); 
     ?>
		
   </div>
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_courier.php");?>
   </div>
</div>


</body>
</html>