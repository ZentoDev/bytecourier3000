<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_courier.php");

require_once("../../dati/lib_xmlaccess.php");
$docOp = openXML("../../dati/xml/operazioni.xml");
$docOrd = openXML("../../dati/xml/ordini.xml");

$rootOp = $docOp->documentElement;  
$listaOp = $rootOp->childNodes;

$rootOrd = $docOrd->documentElement;
$listaOrd = $rootOrd->childNodes;

if( isset($_POST['dettagli']) )  $mess = mostraDettagli($docOp);


function stampaOperazioni($listOp, $listOrd){

	$presente = 0; //questa variabile segnalerà la presenza di operazioni disponibili

	$table="<table>";  

	for ($pos = 0; $pos < $listOp->length; $pos++) {
		$operazione = $listOp->item($pos);

		if( $operazione->getAttribute('username_bytecourier') == $_SESSION['username'] && 
            $operazione->getAttribute('stato') != 5 )      
            { //seleziona le operazioni in carico all'operatore non concluse

			$id_operazione = $operazione->getAttribute('id_operazione');
			$id_ordine = $operazione->getAttribute('id_ordine');

			$coin =0;  // =1 segnala che è stata trovato trovato l'ordine associato all'operazione
		    for ( $i = 0; $i < $listOrd->length && $coin == 0; $i++ ) {
				$ordine = $listOrd->item($i);

			    if( $ordine->getAttribute('id_richiesta') ==  $id_ordine) {

					$stato = $operazione->getAttribute('stato');
							
					$ordine_child = $ordine->firstChild;  //nodo indirizzo ritiro

					if( $ordine->getAttribute('ritiro') == 'in_loco' ){
						if( $stato >= 3) {
							$indirizzo_ritiro = 'centro byte courier';
						}
						else{
							$indirizzo_ritiro = $ordine_child->getAttribute('strada').' ';
							$indirizzo_ritiro .= $ordine_child->getAttribute('numero').', ';
							$indirizzo_ritiro .= $ordine_child->getAttribute('citta').', ';
							$indirizzo_ritiro .= $ordine_child->getAttribute('nazione');
						}
						$ordine_child = $ordine_child->nextSibling;  //nodo indirizzo destinazione
					}
					else  $indirizzo_ritiro = 'centro byte courier';


					$destinazione = $ordine_child->getAttribute('strada').' ';
					$destinazione .= $ordine_child->getAttribute('numero').', ';
					$destinazione .= $ordine_child->getAttribute('citta').', ';
					$destinazione .= $ordine_child->getAttribute('nazione');
					
					$ordine_child = $ordine_child->nextSibling;  //nodo destinatario
					$nome = $ordine_child->getAttribute('nome').' ';
					$nome .= $ordine_child->getAttribute('cognome');

					$ordine_child = $ordine_child->nextSibling;  //nodo mittente
					$nome_mit = $ordine_child->getAttribute('nome').' ';
					$nome_mit .= $ordine_child->getAttribute('cognome');

                    $stato = $operazione->getAttribute('stato');   

				    $table.='<tr>
				              <th><strong>Id operazione:</strong> '.$id_operazione.'</th>
				             <td>   
							  <strong>Mittente:</strong> '.$nome_mit.'<br />
							  <strong>Destinatario:</strong> '.$nome.'<br />
							  <strong>ritiro:</strong> '.$indirizzo_ritiro.'<br />
				              <strong>Destinazione:</strong> '.$destinazione.'<br />
					          <strong>Stato:</strong> '.statoOperazione($operazione->getAttribute('stato')).'<br />
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
					$presente = 1;
				}
			}
		}
            
    }

	if($presente == 0)    echo $table = "<p>Non sono presenti operazioni</p>";
    
	else{
		$table.="</table>";
		echo $table;
	}
	
}

function statoOperazione($stat) {
    switch ($stat) {
        case 1:
            return "1 - transito verso il cliente per il ritiro";
        case 2:
            return "2 - transito verso il centro byte courier";
        case 3:
            return "3 - pacco al centro byte courier";
        case 4:
            return "4 - transito verso il destinatario";
        case 5:
            return "5 - consegna effettuata";

        default: return "errore nel riconoscimento dello stato, contattare il supporto tecnico";
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
     <h2>Operazioni in corso</h2>

     <?php 
	 if( isset($_POST['dettagli']) )  echo "<p><strong>$mess</strong></p>";

	 stampaOperazioni($listaOp, $listaOrd); 
     ?>
		
   </div>
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_courier.php");?>
   </div>
</div>


</body>
</html>