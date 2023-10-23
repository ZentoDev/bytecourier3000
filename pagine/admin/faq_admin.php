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


if( isset( $_POST['delete'] )) {

	foreach( $listaFAQ as $faq ){
		if( $_POST['delete'] == $faq->getAttribute('id_intervento') ){

			$rootFAQ->removeChild($faq);
			printFileXML("../../dati/xml/faq.xml", $docFAQ); 
		}
	}
}

function stampaFAQ($listF, $listI) {

	$faq = "<table id=\"table_commenti\">";	
	for ( $pos=0; $pos < $listF->length; $pos++ ) {

		$coppia_faq = $listF->item($pos);
        $id_domanda= $coppia_faq->getAttribute('id_intervento');
        $id_risposta = $coppia_faq->getAttribute('id_risposta');

		$domanda = "";

        for( $i=0; $i < $listI->length && $domanda == ""; $i++ ) {

            $intervento = $listI->item($i);
            $id_intervento = $intervento->getAttribute('id_intervento');

            if( $id_domanda ==  $id_intervento )  {
                $domanda = $intervento->firstChild->textContent;}
        }

		$risposta = "";

        for( $i=0; $i < $listI->length && $risposta == ""; $i++ ) {

            $intervento = $listI->item($i);
            $id_intervento = $intervento->getAttribute('id_intervento');

            if( $id_risposta ==  $id_intervento ) 
                $risposta = $intervento->firstChild->textContent; 
        }

		$faq .= "<tr>
		            <td><strong>Domanda:</strong> $domanda</td>
		            <td rowspan=\"2\">
					    <form action=\"faq_admin.php\" method=\"post\">
	                    <button type=\"submit\" name=\"delete\" value=\"$id_domanda\">Elimina</button>
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


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

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
	<p><strong>&nbspUtente: <?php echo $_SESSION['username'].' ('.$_SESSION['ruolo'].')'?> </strong></p>
</div>

<div id="content">
   <div id="center" class="colonna">
     <h2>Gestione FAQ</h2>	
	 <h3>Aggiunta FAQ</h3> 

	<form action="crea_faq.php" method="post" >
        <button type="submit" name="add_faq" value="signup">Aggiungi nuova FAQ</button><br /><br />
     </form>
	<h3>FAQ attive</h3> 
	 <?php
	 echo stampaFAQ($listaFAQ, $listaInt);?>
		
   </div>
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_admin.php");?>
   </div>
</div>


</body>
</html>