<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_cliente.php");

require_once("../../dati/lib_xmlaccess.php");
$docOrd = openXML("../../dati/xml/ordini.xml");
  
$rootOrd = $docOrd->documentElement;
$listaOrd = $rootOrd->childNodes;

if( isset($_POST['paga'])){
    $_SESSION['id_ordine'] = $_POST['paga'];
    $_SESSION['funzione'] = 'pagamento';
	header('Location:dettagli_ordine.php');
    exit;
}

//Seleziona la tipologia di ordini da visualizzare ('in_attesa_pagamento', 'modificato')
$stato_selezionato = 'in_attesa_pagamento';
if( isset($_POST['tipo_ordine']) ){
    $stato_selezionato = $_POST['tipo_ordine'];
}

function stampaSpedizioni($listOrd, $tipo_stato) {
    
    $presente = 0; //questa variabile segnalerà la presenza di operazioni disponibili
    
    $intestazione='<h2>Ordini</h2>
            <form action="ordini_pagamento.php" method="post">
            <div id="buttons">
            <button type="submit" name="tipo_ordine" value="in_attesa_pagamento" >Ordini accettati</button>
            <button type="submit" name="tipo_ordine" value="modificato" >Ordini modificati</button>&nbsp
            </div>
            </form>';                  
    switch($tipo_stato) {
        case 'in_attesa_pagamento':
            $intestazione.= '<h3>Ordini in attesa di pagamento</h3>';
            break;
        
        case 'modificato':
            $intestazione.= '<h3>Ordini modificati, in attesa di pagamento</h3>';
            break;        
    }

    $table='<table>';
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
                     <td>   
                      <strong>ritiro:</strong> '.$indirizzo_ritiro.'<br />
                      <strong>Destinazione:</strong> '.$destinazione.'<br />
                      <strong>Destinatario:</strong> '.$nome.'<br />
                      <strong>tipologia spedizione:</strong> '.$tipologia.'<br />
                     </td>   
                     <td>
                     <strong>costo:</strong> '.$costo.' €<br />
                     <strong>peso:</strong> '.$peso.'<br />
                     <strong>larghezza:</strong> '.$larghezza.' cm<br />
                     <strong>altezza:</strong> '.$altezza.' cm<br />
                     <strong>profondita:</strong> '.$profondita.' cm<br />
                     </td>';

            if( $stato  == 'in_attesa_pagamento' || $stato == 'modificato') {

                $table .='<td>
                          <form action="ordini.php" method="post">
                          <div id="buttons">
                          <button type="submit" name="paga" value="'.$id_ordine.'" >Dettagli</button>
                          </div>
                          </form>
                          </td>
                          </tr>';
            }
            else if ( $stato == 'accettato' )   $table .='<td>In corso</td></tr>';
            else $table .='<td>In attesa di verifica</td></tr>';
                     
            $presente = 1;
        }
    }
    if($presente == 0)    echo $intestazione."<p>Non sono presenti ordini</p>";
    
    else{
        $table.="</table>";
        echo $intestazione.''.$table;
    }        
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Ordini in corso</title>
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