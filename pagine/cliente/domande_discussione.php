<?php
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_cliente.php");
require_once("../../dati/lib_xmlaccess.php");

$docInt = openXML("../../dati/xml/interventi.xml");
$rootInt = $docInt->documentElement;
$listaInt = $rootInt->childNodes;


if( isset($_POST['risposta']) ){

    $_SESSION['id_intervento'] = $_POST['risposta'];
    header('Location:domande_discussione.php');
    exit;
}

if( isset($_POST['nuova_risposta']) ){

    $new_id = getId($listaInt, 'id_intervento');
    $new_intervento = $docInt->createElement('intervento', $_POST['testo_risposta']);
    $rootInt->appendChild($new_intervento);

    $new_intervento->setAttribute('id_intervento', $new_id);
    $new_intervento->setAttribute('id_risposta', $_SESSION['id_intervento']);
    $new_intervento->setAttribute('username', $_SESSION['username']);

    printFileXML("../../dati/xml/interventi.xml", $docInt);    
}

function stampaInterventi($listI, $id_domanda) {

    for( $i=0; $i < $listI->length; $i++ ) {
        $intervento = $listI->item($i);

        //Verifico che l'intervento sia una risposta alla domanda selezionata nella pagina precedente
        if( $intervento->getAttribute('id_intervento') == $id_domanda ){
                        
            $id_intervento = $intervento->getAttribute('id_intervento');
            $testo = $intervento->firstChild->textContent; 
            $autore = $intervento->getAttribute('username');
		    $stampa .= 
                "<table id=\"table_commenti\">
                  <tr>
                  <td>
                  <strong>Autore:</strong> $autore<br /><br />
                  $testo
                  </td>
                 </tr></table>
                        
                 <form action=\"domande_discussione.php\" method=\"post\" >
                  <textarea type=\"text\" name=\"testo_risposta\" placeholder=\"Scrivi risposta\" required></textarea><br />
                  <button type=\"submit\" name=\"nuova_risposta\" value=\"$id_intervento\">Rispondi</button>
                 </form>  ";
        }
    }

    $stampa .= "<h2>Risposte</h2>";

    for( $i=0; $i < $listI->length; $i++ ) {
        $intervento = $listI->item($i);

        //Verifico che l'intervento sia una risposta alla domanda selezionata nella pagina precedente
        if( $intervento->getAttribute('id_risposta') == $id_domanda ){
            
            $id_intervento = $intervento->getAttribute('id_intervento');
            $testo = $intervento->firstChild->textContent; 
            $autore = $intervento->getAttribute('username');
		    $stampa .= 
                "<table id=\"table_commenti\">
                  <tr>
                  <td>
                  <strong>Autore:</strong> $autore<br /><br />
                  $testo
                  </td>
                 </tr></table>
                        
                 <form action=\"domande_discussione.php\" method=\"post\" >
                  <button type=\"submit\" name=\"risposta\" value=\"$id_intervento\">Discussione</button>
                 </form>  ";
        }
    }
	
	return $stampa;
}

//ottiene un id disponibile (da chiamare prima che si appenda un nuovo elemento al doc xml)
function getId($lista, $nome_attr) {

    $pos = $lista->length;
    if( $pos >= 1)
        $last_id = $lista->item(--$pos)->getAttribute($nome_attr) + 1;

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
    <title>Risposte</title>
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
     
	 <?php echo stampaInterventi($listaInt, $_SESSION['id_intervento']);?>
		
   </div>
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_cliente.php");?>
   </div>
</div>


</body>
</html>