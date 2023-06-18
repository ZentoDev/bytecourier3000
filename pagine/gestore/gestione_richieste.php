<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_gestore.php");

require_once("../../dati/lib_xmlaccess.php");
$docOrd = openXML("../../dati/xml/ordini.xml");
  
$rootOrd = $docOrd->documentElement;
$listaOrd = $rootOrd->childNodes;


if( isset($_POST['accetta']) ){

  $find = 0;
  for ($pos = 0; $pos < $listaOrd->length && $find == 0; $pos++) {
    $ordine = $listaOrd->item($pos);

    if( $_POST['accetta'] == $ordine->getAttribute('id_richiesta') ) { 
      $ordine->setAttribute('stato', 'in_attesa_pagamento');
      printFileXml("../../dati/xml/ordini.xml", $docOrd);
      $find = 1;
    }     
  }     
}

if( isset($_POST['modifica']) ){

  $_SESSION['id_ord'] = $_POST['modifica'];
  header('Location:modifica_richiesta.php');
  exit;   
}

function stampaRichieste($listOrd) {
    
    $presente = 0; //questa variabile segnalerà la presenza di operazioni disponibili
    
    $table="<table>";  
    
    for ($pos = 0; $pos < $listOrd->length; $pos++) {
        $ordine = $listOrd->item($pos);
        $stato = $ordine->getAttribute('stato');

        //seleziona gli ordini in attesa di accettazione
        if( $stato  == 'in_attesa' ) {                    

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
                     <strong>costo:</strong> '.$costo.'<br />
                     <strong>peso:</strong> '.$peso.'<br />
                     <strong>larghezza:</strong> '.$larghezza.' cm<br />
                     <strong>altezza:</strong> '.$altezza.' cm<br />
                     <strong>profondita:</strong> '.$profondita.' cm<br />
                     </td>
                     <td>
                      <form action="gestione_richieste.php" method="post">
                      <div id="buttons">
                      <button type="submit" name="accetta" value="'.$id_ordine.'" >Accetta richiesta</button>
                      <button type="submit" name="modifica" value="'.$id_ordine.'" >Modifica richiesta</button>
                      </div>
                      </form>
                      </td>
                      </tr>';
                                 
            $presente = 1;
        }
    }
    if($presente == 0)    echo $table = "<p>Non sono presenti ordini in attesa di accettazione</p>";
    
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
    <title>Gestione richieste ordini</title>
    <link rel="shortcut icon" href="../../picture/favicon.png"/>
	<link rel="stylesheet" href="../style1.css" type="text/css">
    <link rel="stylesheet" href="../tabselezione.css" type="text/css">
</head>

<body>

<div id="top">
    <img src="../../picture/logo.png" width="120" alt="Logo" class="logo" />

	<h1 class="title">ByteCourier3000</h1>
	
</div>

<div id="content">
   <div id="center" class="colonna">
   <h2 style="margin-left:50px; text-align: center;">Gestione richieste ordini</h2>
	<br />
    <?php 
    if( !isset($_POST['selezione']) ) 
        echo stampaRichieste($listaOrd);
    else echo $mex;
    ?>

   </div>
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_gestore.php");?>
   </div>
</div>


</body>
</html>