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

if( isset($_POST['accetta']) ) $mess = accettaOperazione($docOp);

function stampaOperazioni($listOp, $listOrd){

	$presente = 0; //questa variabile segnalerà la presenza di operazioni disponibili

	$table="<table>";  

	for ($pos = 0; $pos < $listOp->length; $pos++) {
		$operazione = $listOp->item($pos);

		if( $operazione->getAttribute('username_bytecourier') == $_SESSION['username'] && 
            $operazione->getAttribute('stato') == 1 || $operazione->getAttribute('stato') ){      
            //si possono selezionare solo le operazioni i cui pacchi possono essere ritirati dal domicilio del cliente o dal centro spedizioni

				if( !$operazione->getAttribute('username_bytecourier') ) {    //operazione già presa in carico? in tal caso non mostrarla

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
	
					        $table.='<tr>
					              <th><strong>Id operazione:</strong> '.$id_operazione.'</th>
					             <td>   
								 <strong>Mittente:</strong> '.$nome_mit.'<br />
								 <strong>Destinatario:</strong> '.$nome.'<br />
								 <strong>ritiro:</strong> '.$indirizzo_ritiro.'<br />
								 <strong>Destinazione:</strong> '.$destinazione.'<br />
					             <strong>Tipologia:</strong> '.tipologiaOperazione($stato).'<br />
					             </td>   
				            	 <td>
					              <form action="seleziona_operazione.php" method="post">
					              <div id="buttons">
					               <input type="hidden" name="id_operazione" value="'.$id_operazione.'">
					               <button type="submit" name="accetta" value="accetta" >Accetta</button>
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
	}

	if($presente == 0)    echo $table = "<p>Non sono presenti operazioni</p>";
    
	else{
		$table.="</table>";
		echo $table;
	}
	
}

//associa all'operazione selezionata il bytecourier che l'ha presa in carico
function accettaOperazione($doc) {
    $list = $doc->documentElement->childNodes;
	
	//scorre l'intera lista di nodi
	for ( $pos = 0; $pos < $list->length; $pos++) {
		$operazione = $list->item($pos);
		$id_operazione = $operazione->getAttribute('id_ordine');
		
		//se id_operazione coincide con quella ricercata si aggiorna il file xml associando il bytecourier all'operazione associata
		if( $id_operazione == $_POST['id_operazione'] ) {
			$operazione->setAttribute('username_bytecourier', $_SESSION['username']);
            
			//permette di salvare il documento in un file xml
			printFileXML("../../dati/xml/operazioni.xml", $doc);
			
			return "L'operazione &egrave stata aggiunta correttamente";
		}
	}return "Problemi interni nel processo di selezione della operazione, si prega di contattare il supporto tecnico";
}

function tipologiaOperazione($stat) {
    switch ($stat) {
        case 1:
            return "ritiro a domicilio";
        case 3:
            return "consegna";

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
    <title>Selezione operazioni</title>
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
     <h2>Selezione delle operazioni</h2>

     <?php 
	 if( isset($_POST['accetta']) )   echo "<p><strong>$mess</strong></p>";

	 stampaOperazioni($listaOp, $listaOrd); 
     ?>
		
   </div>
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_courier.php");?>
   </div>
</div>


</body>
</html>