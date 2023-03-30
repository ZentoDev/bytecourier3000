<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_cliente.php");
require_once("../../dati/lib_xmlaccess.php");

$docOrd = openXML("../../dati/xml/ordini.xml");
$rootOrd = $docOrd->documentElement;
$listaOrd = $rootOrd->childNodes;

$pagato = 0;
$mex = '';
$coin =0;  // =1 segnala che è stata trovato trovato l'ordine
for ($pos = 0; $pos < $listaOrd->length && $coin == 0; $pos++) {
    $ordine = $listaOrd->item($pos);
    
    if( $_POST['id_ordine'] == $ordine->getAttribute('id_richiesta') ) {                    
        
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



if( isset( $_POST['paga'] )) {

    $docCliente = openXML("../../dati/xml/clienti.xml");
    $rootCliente = $docCliente->documentElement;
    $listaCliente = $rootCliente->childNodes;
    
    $coin = 0;
    for ( $pos = 0; $pos <= $listaCliente->length && $coin == 0; $pos++) {
        $cliente = $listaCliente->item($pos);
        
        if( $cliente->getAttribute('username') == $_SESSION['username'] ) {
            //Si verifica che il cliente possa permettersi il pagamento
            if ( $cliente->getAttribute('crediti') >= $costo ) {
                //aggiornamento file clienti.xml
                $newCr = $cliente->getAttribute('crediti') - $costo;
                $cliente->setAttribute('crediti', $newCr);
                printFileXML("../../dati/xml/clienti.xml", $docCliente);
                //aggiornamento file ordini.xml
                $ordine->setAttribute('stato', 'accettato');
                printFileXML("../../dati/xml/ordini.xml", $docOrd);

                $mex = "<p>L'ordine è stato pagato</p>";
                $pagato = 1;
            }
            else $mex = "<p>Non hai abbastanza crediti per pagare l'ordine (disponi di ".$cliente->getAttribute('crediti')." crediti)</p>"; 

            $coin = 1;
        }
    }
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
     <?php echo $mex;?>

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
     <?php if ( $pagato == 0 )  
       echo '<input type="hidden" name="id_ordine" value="'.$_POST['id_ordine'].'"/>
             <button type="submit" name="paga" value="1">Paga</button>';?>  
     </form>
   </div> 
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_cliente.php");?>
   </div>
</div>


</body>
</html>