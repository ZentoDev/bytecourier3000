<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
session_start();
require_once("../../dati/lib_xmlaccess.php");


$docRec = openXML("../../dati/xml/recensioni.xml");
$rootRec = $docRec->documentElement;
$listaRec = $rootRec->childNodes;


//stampa le note
function stampaRecensioni($listRecensioni, $tipologia){

    $find = 0;
	$tabNote = "<table id=\"table_commenti\">";
	
	for ( $i=0; $i < $listRecensioni->length; $i++ ) {
        $recensione = $listRecensioni->item($i);

        if( $tipologia == $recensione->getAttribute('tipologia_spedizione')){

		    $author = $recensione->getAttribute('username');
            $voto = $recensione->getAttribute('voto');
		    $text = $recensione->textContent;
		
		    $tabNote .="<tr>
		              <td><strong>Autore:</strong> $author <strong>Valutazione:</strong> $voto stelle</td>
                      <td rowspan=\"2\"></td>
				    </tr>
				    <tr class=\"tr_bordo\">
				       <td>$text</td>

				    </tr>";
            $find = 1;
        }	
	}
	$tabNote .= "</table>";

    if( $find == 1)  echo $tabNote;
    else echo 'Non sono presenti al momento valutazioni per la tipologia selezionata';
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Recensioni <?php echo $_SESSION['id_tipo'];?></title>
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
     
     <?php         
        echo '<h2>Recensioni della tipologia "'.$_SESSION['id_tipo'].'"</h2>';
        echo stampaRecensioni($listaRec, $_SESSION['id_tipo']); 
        ?>
   </div>
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_visitatore.php");?>
   </div>
</div>


</body>
</html>