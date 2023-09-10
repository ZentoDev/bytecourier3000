<?php
ini_set('display_errors', 1);
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

if( isset($_POST['nuova_domanda']) ){

    $new_id = getId($listaInt, 'id_intervento');
    $new_intervento = $docInt->createElement('intervento');
    $rootInt->appendChild($new_intervento);
    $new_testo = $docInt->createElement('testo', $_POST['testo_domanda']);
    $new_intervento->appendChild($new_testo);

    $new_intervento->setAttribute('id_intervento', $new_id);
    $new_intervento->setAttribute('id_risposta', "");
    $new_intervento->setAttribute('username', $_SESSION['username']);
    $new_intervento->setAttribute('admin', "false");

    printFileXML("../../dati/xml/interventi.xml", $docInt);    
}

function stampaInterventi($listI) {

	$stampa = 
             "<h2>Scrivi nuova domanda</h2>
              <form action=\"domande.php\" method=\"post\" >
              <textarea type=\"text\" name=\"testo_domanda\" placeholder=\"Nuova domanda\" required></textarea><br />
              <button type=\"submit\" name=\"nuova_domanda\" value=\"1\">Salva</button>
              </form> ";

    $stampa .= "<h2>Domande</h2>";

    for( $i=0; $i < $listI->length; $i++ ) {
        $intervento = $listI->item($i);

        //Verifico che l'intervento non sia una risposta ad un'altro intervento o una domanda posta da un admin con lo scopo di farla apparire tra le FAQ,
        //voglio stampare solo le domande poste da clienti
        if( $intervento->getAttribute('id_risposta') == "" && $intervento->getAttribute('admin') == 'false'){
            
            $id_intervento = $intervento->getAttribute('id_intervento');
            $autore = $intervento->getAttribute('username');
            $testo = $intervento->firstChild->textContent; 

		    $stampa .= 
                "<table id=\"table_commenti\">
                  <tr>
                  <td>
                  <strong>Autore:</strong> $autore<br /><br />
                  $testo
                  </td>
                 </tr></table>
                        
                 <form action=\"domande.php\" method=\"post\" >
                  <button type=\"submit\" name=\"risposta\" value=\"$id_intervento\">Risposte</button>
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
    <title>Domande</title>
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
     
	 <?php echo stampaInterventi($listaInt);?>
   </div>
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_cliente.php");?>
   </div>
</div>


</body>
</html>