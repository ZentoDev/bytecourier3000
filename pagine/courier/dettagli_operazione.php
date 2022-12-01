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

$mex = '';
$coin =0;  // =1 segnala che è stata trovato trovato l'operazione 
for ($pos = 0; $pos < $listaOp->length && $coin == 0; $pos++) {
	
    $operazione = $listaOp->item($pos);
    if( $_POST['id_operazione'] == $operazione->getAttribute('id_operazione') )   $coin = 1;

}

$coin =0;
for ( $i = 0; $i < $listaOrd->length && $coin == 0; $i++ ) {
	$ordine = $listaOrd->item($i);
    $id_ordine = $ordine->getAttribute('id_richiesta');

    if( $id_ordine ==  $operazione->getAttribute('id_ordine')) {
		$destinatario = $ordine->firstChild;  //nodo destinatario
        $nome = $destinatario->firstChild->textContent;  //nome
        $cognome = $destinatario->lastChild->textContent;  //cognome
	    $indirizzo = $destinatario->nextSibling->textContent;  //via
		$destinatario = $ordine->lastChild; //nodo destinazione
		$citta = $destinatario->getAttribute('citta');
        $stato = $operazione->getAttribute('stato');   

        $coin = 1;
	    $presente = 1;					
	}
}

if($coin == 0)  $mex = "<p>Errore nel processo di recupero dei dettagli dell'operazione, contattare il supporto tecnico</p>";


if( $_POST['next_stat'] == 1) {

    $fail = 0;
    switch ($stato) {
        case 1:
            $stato += 1;
            $operazione->setAttribute('stato', $stato );
            $mex_stat = "Stato aggiornato";
            break;
        case 2:
            $stato += 1;
            $operazione->setAttribute('stato', $stato );
            $mex_stat = "Ritiro completato";
            break;
        case 3:
            $stato += 1;
            $operazione->setAttribute('stato', $stato );
            $mex_stat = "Stato aggiornato";
            break;
        case 4:
            $stato += 1;
            $operazione->setAttribute('stato', $stato );
            $mex_stat = "";
            break;

        default: 
            $mex_stat = "errore nel riconoscimento dello stato, contattare il supporto tecnico";
            $fail = 1;
    }

    if( $fail == 0)   printFileXML("../../dati/xml/operazioni.xml", $docOp);

}

function statoOperazione($stat) {
    switch ($stat) {
        case 1:
            return "transito verso il cliente per il ritiro";
        case 2:
            return "transito verso il centro byte courier";
        case 3:
            return "pacco al centro byte courier";
        case 4:
            return "transito verso il destinatario";
        case 5:
            return "consegna effettuata";

        default: return "errore nel riconoscimento dello stato, contattare il supporto tecnico";
    }
}

function aggiornaStato($stat) {

}


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Dettagli operazione</title>
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
     <h2>Dettagli operazione</h2>
     <?php echo $mex;?>

     <h3>Operazione <?php echo $_POST['id_operazione']; ?></h3>
	 <p>
	    <strong>Nome:</strong> <?php echo $nome; ?> <br />
	    <strong>Cognome:</strong> <?php echo $cognome; ?> <br />
		<strong>Citt&agrave:</strong> <?php echo $citta; ?> <br />
		<strong>Indirizzo:</strong> <?php echo $indirizzo; ?> <br /><br />

		<strong>Stato dell'operazione:</strong> <?php echo statoOperazione($stato); ?> <br />
        <?php
        if( $stato != 5 ) 
            echo '
                Quando la seguente fase dell\'operazione viene completata, aggiorna lo stato premendo sul seguente pulsante <br />
                <form action="dettagli_operazione.php" method="post">
                    <input type="hidden" name="id_operazione" value=" '. $_POST["id_operazione"] .' " >
                    <button type="submit" name="next_stat" value="1" >Aggiorna stato</button>        
                </form>
                <br />';
        
        echo $mex_stat;
        ?>

	 </p>
	 
	 
   </div> 
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_courier.php");?>
   </div>
</div>


</body>
</html>