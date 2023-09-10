<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_cliente.php");
require_once("../../dati/lib_xmlaccess.php");

$docOrd = openXML("../../dati/xml/ordini.xml");
$rootOrd = $docOrd->documentElement;
$listaOrd = $rootOrd->childNodes;


function stampaOption($tipo) {

    $docType = openXML("../../dati/xml/setting.xml");
    $rootType = $docType->documentElement;  
    $lista = $rootType->firstChild->childNodes;

    $find = 0;
    $num_elem = 0;
    
    for ( $i = 0; $i < $lista->length && $find == 0; $i++ ) {

        $type = $lista->item($i);
        //cerco il tipo di spedizione selezionato
        if( $tipo == $type->getAttribute('nome') ){
            $find = 1; //tipologia spedizione trovata, interrompe i successivi cicli del for
            
            $lista_dim = $type->childNodes;
            $opzione = '<ul>';
            for ( $c = 0; $c < $lista_dim->length; $c++ ) {

                $voce = $lista_dim->item($c); 
                $larghezza = $voce->getAttribute('larghezza');
                $altezza = $voce->getAttribute('altezza');
                $profondita = $voce->getAttribute('profondita');
                $peso = $voce->getAttribute('peso_max');
                $costo = $voce->getAttribute('costo');

                $opzione .= '<li>larghezza: '.$larghezza.'cm; altezza: '.$altezza.'cm; profondità: '.$profondita.'cm; peso: '.$peso.'kg; costo: '.$costo.' €</li>';
                $num_elem++;            
            }
            $opzione .= '</ul>';
            if( $num_elem == 0)    $opzione = '<br />Non sono disponibili opzioni al momento';
        } 
    }
    return $opzione;
}


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Opzioni</title>
    <link rel="shortcut icon" href="../../picture/favicon.png"/>
	<link rel="stylesheet" href="../style1.css" type="text/css">
    <link rel="stylesheet" href="../tabcommenti.css" type="text/css">
</head>

<body>

<div id="top">
    <img src="../../picture/logo.png" width="120" alt="Logo" class="logo" />

	<h1 class="title">ByteCourier3000</h1>
    <p><strong>&nbspUtente: <?php echo $_SESSION['username'].' ('.$_SESSION['ruolo'].')'?> </strong></p>
</div>

<div id="content">
   <div id="center" class="colonna">
     <h2>Opzioni della tipologia pacco '<?php echo $_SESSION['id_tipo'];?>'</h2>
     <p>Queste sono le opzioni disponibili per la tipologia scelta: </p><br />
     <?php echo stampaOption($_SESSION['id_tipo']);?>
   </div> 
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_cliente.php");?>
   </div>
</div>


</body>
</html>