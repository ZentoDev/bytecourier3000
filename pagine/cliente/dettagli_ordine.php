<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_cliente.php");

require_once("../../dati/lib_xmlaccess.php");
$docOp = openXML("../../dati/xml/operazioni.xml");
$docOrd = openXML("../../dati/xml/ordini.xml");
$docClienti = openXML("../../dati/xml/clienti.xml");
$docSetting = openXML("../../dati/xml/setting.xml"); 
  
$rootOrd = $docOrd->documentElement;
$listaOrd = $rootOrd->childNodes;

$rootOp = $docOp->documentElement;  
$listaOp = $rootOp->childNodes;

$rootClienti = $docClienti->documentElement;  
$listaClienti = $rootClienti->childNodes;

$rootSetting = $docSetting->documentElement;

$mex_pagamento = '';
if( isset($_POST['pagamento']) ){

    $find = 0;
    for ($pos = 0; $pos < $listaOrd->length && $find == 0; $pos++) {
        $ordine = $listaOrd->item($pos);

        if( $_POST['pagamento'] == $ordine->getAttribute('id_richiesta') ){
            $costo = $ordine->getAttribute('costo');

            for( $i = 0; $i < $listaClienti->length && $find == 0; $i++) {
                $cliente = $listaClienti->item($i);

                if( $_SESSION['username'] == $cliente->getAttribute('username') ){
                    $cr_client = $cliente->getAttribute('crediti');
                    $find = 1;
                }
            }
            //verifico che il cliente abbia abbastanza crediti per pagare l'ordine
            if( $cr_client <  $costo )  
                $mex_pagamento = 'Possiedi '.$cr_client.' crediti, te ne servono '.$costo.' per pagare l\'ordine '.$_POST['pagamento'];

            else{
                //aggiorno il saldo del cliente
                $cliente->setAttribute('crediti', $cr_client - $costo);

                //aggiorno lo stato dell'ordine
                $ordine->setAttribute('stato', 'accettato');

                //creo operazione associata all'ordine
                $new_id = getId($listaOp);
                $new_op = $docOp->createElement('operazione');
                $rootOp->appendChild($new_op);
                $new_note = $docOp->createElement('note');
                $new_op->appendChild($new_note);

                $new_op->setAttribute('id_operazione', $new_id);
                $new_op->setAttribute('username_bytecourier', '');
                $new_op->setAttribute('id_ordine', $_POST['pagamento']);
                $new_op->setAttribute('stato', 1);

                //verifico che sia abilitata l'autoassegnazione del bytecourier
                if( $rootSetting->getAttribute('assegnazione_automatica') == 'true' )
                    autoAlloc($docOp);


                printFileXML("../../dati/xml/clienti.xml", $docClienti);
                printFileXML("../../dati/xml/ordini.xml", $docOrd);
                printFileXML("../../dati/xml/operazioni.xml", $docOp);

                $mex_pagamento = 'L\'ordine è stato pagato, adesso hai '.$cr_client - $costo.' crediti';
            }
        }
    }
}


$coin =0;  // =1 segnala che è stata trovato trovato l'ordine
for ($pos = 0; $pos < $listaOrd->length && $coin == 0; $pos++) {
    $ordine = $listaOrd->item($pos);
    
    if( $_SESSION['id_ordine'] == $ordine->getAttribute('id_richiesta') ) {                    
        
        $ordine_child = $ordine->firstChild; 
        $ritiro = $ordine->getAttribute('ritiro');

        if( $ritiro == 'in_loco' ) {  //indirizzo ritiro

        $indirizzo_ritiro = $ordine_child->getAttribute('strada').' ';
        $indirizzo_ritiro .= $ordine_child->getAttribute('numero').', ';
        $indirizzo_ritiro .= $ordine_child->getAttribute('citta').', ';
        $indirizzo_ritiro .= $ordine_child->getAttribute('nazione');
        $ordine_child = $ordine_child->nextSibling;  //nodo indirizzo destinazione
        }

        else $indirizzo_ritiro = 'da consegnare in un centro spedizioni';

        $destinazione = $ordine_child->getAttribute('strada').' ';
        $destinazione .= $ordine_child->getAttribute('numero').', ';
        $destinazione .= $ordine_child->getAttribute('citta').', ';
        $destinazione .= $ordine_child->getAttribute('nazione');

        $ordine_child = $ordine_child->nextSibling;  //nodo destinatario
        $nome = $ordine_child->getAttribute('nome').' ';
        $nome .= $ordine_child->getAttribute('cognome');

        $tipologia = $ordine->getAttribute('tipologia_spedizione');   
        $costo = $ordine->getAttribute('costo');  
        $peso = $ordine->getAttribute('peso');  
        $larghezza = $ordine->getAttribute('larghezza');
        $altezza = $ordine->getAttribute('altezza');
        $profondita = $ordine->getAttribute('profondita'); 
        $coin = 1;
    }
}
if($coin == 0)  $mex = "<p>Errore nel processo di recupero dei dettagli dell'ordine, contattare il supporto tecnico</p>";



//associa automaticamente le operazioni non assegnate ai bytecuorier
//Per ogni operazione libera si sceglie il bt con meno operazioni in carico in quel momento 
function autoAlloc($docOperazioni) {

	require_once("../../mysql/connection.php");
    if( !$connection_mysqli )   return -1;  //problemi di connessione al db, return -1

	$rootOp = $docOperazioni->documentElement;  
    $listOp = $rootOp->childNodes;

	//scorro tutte le operazioni
	for ($pos = 0; $pos < $listOp->length; $pos++) {
		$operazione = $listOp->item($pos);   

		//verifico se l'op non è assegnata
		if( $operazione->getAttribute('username_bytecourier') == "" ) {

			$min = 999;  //numero fittizio per ricercare il bt con il minimo numero di operazioni prese in carico

		    //query per ottenere gli utenti che sono byte courier
		    $select_query = "SELECT username FROM $user_table_name 
		                     WHERE permesso = 10 ";

            $res = mysqli_query($connection_mysqli, $select_query);
            while ($row = mysqli_fetch_assoc($res)) 
	            $listByte[] = $row['username'];
        
			foreach( $listByte as $byte ) {
				$num_op = countOp($byte, $listOp);

				if( $min >  $num_op) {
					$min = $num_op;
					$byte_min = $byte;
				}
			}
            
			$operazione->setAttribute('username_bytecourier', $byte_min);
		}
	}
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

//ottiene un id disponibile (da chiamare prima che si appenda un nuovo elemento al doc xml)
function getId($lista) {

    $pos = $lista->length;
    if( $pos >= 1)
        $last_id = $lista->item(--$pos)->getAttribute('id_operazione') + 1;

    else
        $last_id = 0;

    return $last_id;
}


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Riepilogo ordine</title>
    <link rel="shortcut icon" href="../../picture/favicon.png"/>
	<link rel="stylesheet" href="../style1.css" type="text/css">
    <link rel="stylesheet" href="../tabcommenti.css" type="text/css">
</head>

<body>

<div id="top">
    <img src="../../picture/logo.png" width="120" alt="Logo" class="logo" />

	<h1 class="title">ByteCourier3000</h1>
	
</div>

<div id="content">
   <div id="center" class="colonna">
     <h2>Riepilogo ordine</h2>
     <?php echo $mex_pagamento;?>

     <h3>Informazioni pacco</h3>
	 <p>
        <strong>Larghezza:</strong> <?php echo $larghezza; ?> cm <br />
        <strong>Altezza:</strong> <?php echo $altezza; ?> cm <br />
        <strong>Profondita:</strong> <?php echo $profondita; ?> cm <br />
	    <strong>Peso:</strong> <?php echo $peso; ?> kg<br />
	    <strong>Tipologia spedizione:</strong> <?php echo $tipologia; ?> <br />
	    <strong>Tipologia ritiro:</strong> <?php echo $ritiro; ?> <br />
        <strong>Costo:</strong> <?php echo $costo; ?> €<br /> 
    </p>
    <h3>Indirizzi</h3>
	 <p>
        <strong>Destinatario:</strong> <?php echo $nome; ?> <br />
		<strong>Indirizzo destinazione:</strong> <?php echo $destinazione; ?> <br />
        <strong>Indirizzo ritiro:</strong> <?php echo $indirizzo_ritiro; ?> <br />
	 
     <form action="dettagli_ordine.php" method="post">
     <?php if ( !isset($_POST['pagamento']) )  
                echo '<button type="submit" name="pagamento" value="'.$_SESSION['id_ordine'].'">Paga</button>';?>  
     </form>
   </div> 
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_cliente.php");?>
   </div>
</div>


</body>
</html>