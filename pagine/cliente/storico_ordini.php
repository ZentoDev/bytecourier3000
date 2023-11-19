<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_cliente.php");
require_once("../../dati/lib_xmlaccess.php");

$docOrd = openXML("../../dati/xml/ordini.xml");
  
$rootOrd = $docOrd->documentElement;
$listaOrd = $rootOrd->childNodes;


if( isset($_POST['dettagli']) ){
    $_SESSION['id_ordine'] = $_POST['dettagli'];
    $_SESSION['funzione'] = 'recensione';
	header('Location:dettagli_ordine.php');
    exit;
}


//Seleziona la tipologia di ordini da visualizzare ('concluso', 'rifiutato')
$stato_selezionato = 'concluso';
if( isset($_POST['tipo_ordine']) ){
    $stato_selezionato = $_POST['tipo_ordine'];
}


function stampaSpedizioni($listOrd, $tipo_stato) {
    
    $presente = 0; //questa variabile segnalerà la presenza di operazioni disponibili
    
    $table='<h2>Ordini</h2>
            <form action="storico_ordini.php" method="post">
            <div id="buttons">
            <button type="submit" name="tipo_ordine" value="concluso" >Ordini conclusi</button>
            <button type="submit" name="tipo_ordine" value="rifiutato" >Ordini rifiutati</button>&nbsp
            </div>
            </form>
            <table>';

    switch($tipo_stato) {
        case 'concluso':
            $table.= '<h3>Ordini conclusi</h3>';
            break;

        case 'rifiutato':
            $table.= '<h3>Ordini rifiutati</h3>';
            break;           
    }
    
    for ($pos = 0; $pos < $listOrd->length; $pos++) {
        $ordine = $listOrd->item($pos);
        $stato = $ordine->getAttribute('stato');

        //seleziona gli ordini conclusi dal cliente
        if( $ordine->getAttribute('username') == $_SESSION['username'] && 
            $stato == $tipo_stato ) {                  

            $id_ordine = $ordine->getAttribute('id_richiesta');
            $ordine_child = $ordine->firstChild; 

            if( $ordine->getAttribute('ritiro') == 'in_loco' ) {  //indirizzo ritiro

            $indirizzo_ritiro = $ordine_child->getAttribute('strada').' ';
            $indirizzo_ritiro .= $ordine_child->getAttribute('numero').', ';
            $indirizzo_ritiro .= $ordine_child->getAttribute('citta').', ';
            $indirizzo_ritiro .= $ordine_child->getAttribute('nazione');
            $ordine_child = $ordine_child->nextSibling;  //nodo indirizzo destinazione
            }

            else $indirizzo_ritiro = 'consegna in un centro spedizioni';

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
    
            $table.='<tr>
                      <th><strong>Id ordine:</strong> '.$id_ordine.'<br />
                      <strong>stato:</strong> '.$stato.'</th>
                     <td>   
                          <strong>ritiro:</strong> '.$indirizzo_ritiro.'<br />
                          <strong>Destinazione:</strong> '.$destinazione.'<br />
                          <strong>Destinatario:</strong> '.$nome.'<br />
                          <strong>tipologia spedizione:</strong> '.$tipologia.'<br />
                     </td>
                     <td>
                          <strong>costo:</strong> '.$costo.' €<br />
                          <strong>peso:</strong> '.$peso.' kg<br />
                          <strong>larghezza:</strong> '.$larghezza.' cm<br />
                          <strong>altezza:</strong> '.$altezza.' cm<br />
                          <strong>profondita:</strong> '.$profondita.' cm<br />
                     </td>
                     <td>
                          <form action="storico_ordini.php" method="post">
                          <div id="buttons">
                          <button type="submit" name="dettagli" value="'.$id_ordine.'" >Visualizza dettagli</button>
                          </div>
                          </form>
                          </td>
                     </tr>';
            $coin = 1;
            $presente = 1;
        }
    }
    if($presente == 0)    echo $table = "<p>Non sono presenti ordini</p>";
    
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
    <title>Storico ordini</title>
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

     <?php echo stampaSpedizioni($listaOrd, $stato_selezionato); ?>
   </div>
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_cliente.php");?>
   </div>
</div>


</body>
</html>