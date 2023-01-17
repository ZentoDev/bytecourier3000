<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_admin.php");
require_once("../../dati/lib_xmlaccess.php");


$docFAQ = openXML("../../dati/xml/faq.xml");
$rootFAQ = $docFAQ->documentElement;
$listaFAQ = $rootFAQ->childNodes;

$docInt = openXML("../../dati/xml/interventi.xml");
$rootInt = $docInt->documentElement;
$listaInt = $rootInt->childNodes;


if( isset( $_POST['up'] )) {
	
	$prima_dom = $lista->item(0);
	
	for ($i=1; $i<$listaFAQ->length; $i++){
		$domanda = $lista->item($i);
		$id_dom = $domanda->getAttribute('id_domanda');
		
		if($_POST['id_domanda'] == $id_dom){
			
			$prima_dom->parentNode->insertBefore($domanda,$prima_dom);
			$docum->save('xml/sezione_faq.xml');
			$message = "<p class=\"messaggio\">La domanda $id_dom &egrave; stata elevata.<p>";
			break;
		}
    }	
}


if( $_POST['invio_domanda'] ) 	$aggiunta = aggiungiFAQ($docum);


function stampaFAQ($listF, $listI) {

	$faq = "<table id=\"table_commenti\">";	
	for ( $pos=0; $pos < $listF->length; $pos++ ) {

		$coppia_faq = $listF->item($pos);
        $id_domanda= $coppia_faq->getAttribute('id_intervento');
        $id_risposta = $coppia_faq->getAttribute('id_risposta');

        for( $i=0; $i < $listI->length && !$domanda ; $i++ ) {

            $intervento = $listI->item($i);
            $id_intervento = $intervento->getAttribute('id_intervento');

            if( $id_domanda ==  $id_intervento )  {
                $domanda = $intervento->firstChild->textContent;}
        }

        for( $i=0; $i < $listI->length && !$risposta ; $i++ ) {

            $intervento = $listI->item($i);
            $id_intervento = $intervento->getAttribute('id_intervento');

            if( $id_risposta ==  $id_intervento ) 
                $risposta = $intervento->firstChild->textContent; 
        }

		$faq .= "<tr>
		            <td><strong>Domanda:</strong> $domanda</td>
		            <td rowspan=\"2\">
					    <form action=\"faq_admin.php\" method=\"post\">
						<input type=\"hidden\" name=\"id_domanda\" value=\"$id_domanda\">
	                    <button type=\"submit\" name=\"up\" value=\"1\">Eleva</button>
						</form>
					</td>
				 </tr>
				 <tr class=\"tr_bordo\">
				    <td><strong>Risposta:</strong> $risposta</td>
				</tr>";
        
        $risposta = $domanda = '';
	}
	$faq .= "</table>";
	return $faq;
}

function aggiungiFAQ($doc){
	
	$root = $doc->documentElement;
	$lista = $root->childNodes;
	
	$newDom = $doc->createElement("domanda");
	$root->appendChild($newDom);
	
	$newTesto = $doc->createElement("testo", $_POST['domanda']);
	$newDom->appendChild($newTesto);
	
	$newRisp = $doc->createElement("risposta", $_POST['risposta']);
	$newDom->appendChild($newRisp);
	
	$idDom = ottieniIdDomanda($lista);
	$newDom->setAttribute("id_domanda", $idDom);
	
	$doc->save('xml/sezione_faq.xml');
	
	$add = "<p class=\"messaggio\">La domanda &egrave; stata aggiunta.<p>";
	return $add;
}

function ottieniIdDomanda($listaFaq){
	
	$id_confronto = "0";
	for($i=0; $i<$listaFaq->length; $i++){
		$domanda = $listaFaq->item($i);
		$id_domanda = $domanda->getAttribute('id_domanda');
		
		if($id_domanda>$id_confronto){
			$id_confronto = $id_domanda;
		}
	}
	
	$newID = $id_confronto +1;
	return $newID;
}


echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Gestione FAQ</title>
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
     <h2>Gestione FAQ</h2>
	 
     <?php if($_POST['up']){
		 echo $message;
	 }
	 if($_POST['invio_domanda']){
		 echo $aggiunta;
	 }
	 ?>
	
	<h3>Aggiunta FAQ</h3> 
	<form action="gestione_faq.php" method="post">
		<p>Inserisci una faq:</p>
	    <textarea type="text" name="domanda" placeholder="Scrivi la domanda..." required></textarea>
		<textarea type="text" name="risposta" placeholder="Scrivi la risposta..." required></textarea>
		<input type="submit" name="invio_domanda" value="Invia">
	</form>
	<h3>Eleva FAQ</h3> 
	 <?php
	 echo stampaFAQ($listaFAQ, $listaInt);?>
		
   </div>
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_admin.php");?>
   </div>
</div>


</body>
</html>