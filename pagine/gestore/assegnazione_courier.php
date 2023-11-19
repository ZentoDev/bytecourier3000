<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_gestore.php");

require_once("../../dati/lib_xmlaccess.php");

$docOp = openXML("../../dati/xml/operazioni.xml");
$docOrd = openXML("../../dati/xml/ordini.xml");
$docType = openXML("../../dati/xml/setting.xml"); 

$rootOp = $docOp->documentElement;  
$listaOp = $rootOp->childNodes;

$rootOrd = $docOrd->documentElement;
$listaOrd = $rootOrd->childNodes;

$rootType = $docType->documentElement;

if( isset( $_POST['modifica_assegnazione'] )) {
    $rootType->setAttribute('assegnazione_automatica', $_POST['assegnazione']);
    printFileXml("../../dati/xml/setting.xml", $docType);
    
	if( $_POST['assegnazione'] == "true" ){
		//associa automaticamente le operazioni non assegnate ai bytecuorier
		autoAlloc($docOp);
	}
}

if( isset( $_POST['accetta'] )) {
	$_SESSION['cod_op'] = $_POST['id_operazione'];
	header('Location:seleziona_courier.php');
    exit;
}

$assegnazione = $rootType->getAttribute('assegnazione_automatica');


function stampaOperazioni($listOp, $listOrd){

	$presente = 0; //questa variabile segnalerà la presenza di operazioni disponibili

	$table="<table>";  

	for ($pos = 0; $pos < $listOp->length; $pos++) {
		$operazione = $listOp->item($pos);

		$stato = $operazione->getAttribute('stato');   

		if( $operazione->getAttribute('username_bytecourier') == "" ) {    //operazione già presa in carico? in tal caso non mostrarla

			$id_operazione = $operazione->getAttribute('id_operazione');
			$id_ordine = $operazione->getAttribute('id_ordine');

			$coin =0;  // =1 segnala che è stata trovato trovato l'ordine associato all'operazione
		    for ( $i = 0; $i < $listOrd->length && $coin == 0; $i++ ) {
				$ordine = $listOrd->item($i);

			    if( $id_ordine ==  $id_ordine = $ordine->getAttribute('id_richiesta') ) {

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
				              <form action="assegnazione_courier.php" method="post">
				              <div id="buttons">
				               <input type="hidden" name="id_operazione" value="'.$id_operazione.'">
				               <button type="submit" name="accetta" value="accetta" >Assegna a byte courier</button>
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

function tipologiaOperazione($stat) {
    switch ($stat) {
        case 1:
            return "ritiro a domicilio";
		case 2:
			return "transito verso il centro byte courier";
        case 3:
            return "consegna";
		case 4:
			return "transito verso il destinatario";

        default: return "errore nel riconoscimento dello stato, contattare il supporto tecnico";
    }
}

//associa automaticamente le operazioni non assegnate ai bytecourier
//Per ogni operazione libera si sceglie il bt con meno operazioni in carico in quel momento 
function autoAlloc($docOperazioni) {

	require_once("../../mysql/connection.php");
    if( !$connection_mysqli )   return -1;  //problemi di connessione al db, return -1

	$rootOp = $docOperazioni->documentElement;  
    $listOp = $rootOp->childNodes;

	//query per ottenere gli utenti che sono byte courier
	$select_query = "SELECT username FROM $user_table_name 
	WHERE permesso = 10 ";

    //creo un array associativo che contiene le coppie (bytecourier => numOp)  
    $res = mysqli_query($connection_mysqli, $select_query);
    while ($row = mysqli_fetch_assoc($res)) 
        $arrByte[$row['username']] = countOp($row['username'], $listOp);

	//scorro tutte le operazioni
	for ($pos = 0; $pos < $listOp->length; $pos++) {
		$operazione = $listOp->item($pos);   

		//verifico se l'op non è assegnata
		if( $operazione->getAttribute('username_bytecourier') == "" ) {

            //ricerco il bytecourier con meno operazioni a carico
			$min = 999;  //numero fittizio per ricercare il bt con il minimo numero di operazioni prese in carico

			foreach($arrByte as $nome => $numOp ){
				if( $min >  $numOp) {
					$min = $numOp;
					$byte_min = $nome;
				}
			}

			$arrByte[$byte_min]++;            
			$operazione->setAttribute('username_bytecourier', $byte_min);
		}
	}
	printFileXml("../../dati/xml/operazioni.xml", $docOperazioni);
	return 0;
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
   <h2 style="margin-left:50px; text-align: center;">Assegnazione byte courier</h2>
    <form action="assegnazione_courier.php" method="post" > 
		<strong>Assegnazione automatica: </strong><br /><br />
        <input type="radio" name="assegnazione" value="true" <?php if ($assegnazione == "true") echo 'checked';?> />Attivata<br />
        <input type="radio" name="assegnazione" value="false" <?php if ($assegnazione == "false") echo 'checked';?> />Disattivata<br />
        <button type="submit" name="modifica_assegnazione" value="signup">Modifica assegnazione</button>
    </form>
	<br />
    <?php echo stampaOperazioni($listaOp, $listaOrd); ?>

   </div>
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_gestore.php");?>
   </div>
</div>


</body>
</html>