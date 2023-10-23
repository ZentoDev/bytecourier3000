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


if( isset($_POST['invio_domanda']) ){

    //inserimento domanda    
    $new_id_domanda = getId($listaInt, 'id_intervento');
    $new_intervento = $docInt->createElement('intervento');
    $rootInt->appendChild($new_intervento);
    $new_testo = $docInt->createElement('testo', $_POST['domanda']);
    $new_intervento->appendChild($new_testo);

    $new_intervento->setAttribute('id_intervento', $new_id_domanda);
    $new_intervento->setAttribute('id_risposta', "");
    $new_intervento->setAttribute('username', $_SESSION['username']);
    $new_intervento->setAttribute('admin', "true");
    
    //inserimento risposta
    $new_id_risposta = getId($listaInt, 'id_intervento');
    $new_intervento = $docInt->createElement('intervento');
    $rootInt->appendChild($new_intervento);
    $new_testo = $docInt->createElement('testo', $_POST['risposta']);
    $new_intervento->appendChild($new_testo);

    $new_intervento->setAttribute('id_intervento', $new_id_risposta);
    $new_intervento->setAttribute('id_risposta', $new_id_domanda);
    $new_intervento->setAttribute('username', $_SESSION['username']);
    $new_intervento->setAttribute('admin', "true");

    printFileXML("../../dati/xml/interventi.xml", $docInt);

    //inserimento faq
    $new_faq = $docFAQ->createElement('intervento');
    $rootFAQ->appendChild($new_faq);

    $new_faq->setAttribute('id_intervento', $new_id_domanda);
    $new_faq->setAttribute('id_risposta', $new_id_risposta);

    printFileXML("../../dati/xml/faq.xml", $docFAQ);

    $aggiunta = "Nuova faq aggiunta";
}


if( isset($_POST['new_faq_domanda']) ){
    
    //inserimento faq
    $new_faq = $docFAQ->createElement('intervento');
    $rootFAQ->appendChild($new_faq);

    $new_faq->setAttribute('id_intervento', $_POST['new_faq_domanda']);
    $new_faq->setAttribute('id_risposta', $_POST['new_faq_risposta']);

    printFileXML("../../dati/xml/faq.xml", $docFAQ);

    $aggiunta = "Nuova faq aggiunta";
}

function stampaInterventi($listF, $listI) {

    $presente = 0;
	$stampa = "<table id=\"table_commenti\">";	
	for ( $pos=0; $pos < $listI->length; $pos++ ) {

		$domanda = $listI->item($pos);
        //seleziono le domande
        if( $domanda->getAttribute('id_risposta') == "" ){

            $id_domanda= $domanda->getAttribute('id_intervento');
            $testo_domanda = $domanda->firstChild->textContent;

            //verifico che la domanda non sia già stata selezionata come faq, nel caso lo fosse la ignoro
            $faq_find = 0;
            foreach( $listF as $faq ){

                if( $id_domanda == $faq->getAttribute('id_intervento') )
                    $faq_find = 1;

            }

            if( $faq_find == 0 ){
               //verificato che è una domanda ancora non appartenente alle faq, cerco la risposta con più 'like'

               $max_like = 0;
               $count_risp = 0;
               foreach( $listI as $risposta ){
                                    
                    if( $risposta->getAttribute('id_risposta') == $id_domanda ){

                        $count_risp++;
                        
                        $like = $risposta->getElementsByTagName("valutazione_utente")->length;

                        if( $like >= $max_like ){

                            $id_risp_max_like = $risposta->getAttribute('id_intervento');
                            $testo_risposta = $risposta->firstChild->textContent;
                            $max_like = $like;
                        }
                    }
               }

               if( $count_risp > 0 ){
                    
                    $stampa .= "<tr>
                         <td><strong>Domanda:</strong> $testo_domanda</td>
                         <td rowspan=\"2\">
                          <form action=\"crea_faq.php\" method=\"post\">
                          <input type=\"hidden\" name=\"new_faq_risposta\" value=\"$id_risp_max_like\">
                          <button type=\"submit\" name=\"new_faq_domanda\" value=\"$id_domanda\">Seleziona</button>
                          </form>
                          </td>
                          </tr>
                         <tr class=\"tr_bordo\">
                         <td><strong>Risposta:</strong> $testo_risposta</td>
                         </tr>";

                    $presente = 1;

               }

            }
        }
    }
	$stampa .= "</table>";

    if( $presente == 0)  $stampa .= '<p>non sono presenti interventi</p>';
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
     <h2>Aggiungi FAQ</h2>
	 
     <?php if( isset($_POST['invio_domanda']) ){
		 echo $aggiunta;
	 }
	 ?>
	
	<h3>Inserisci FAQ manualmente</h3> 

	<form action="crea_faq.php" method="post">
	    <textarea type="text" name="domanda" placeholder="Scrivi la domanda..." required></textarea>
		<textarea type="text" name="risposta" placeholder="Scrivi la risposta..." required></textarea>
		<button type="submit" name="invio_domanda" value="Invia">Inserisci valori</button>
	</form>
	<h3>Seleziona interventi da elevare a FAQ</h3> 
	 <?php
	 echo stampaInterventi($listaFAQ, $listaInt);?>
		
   </div>
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_admin.php");?>
   </div>
</div>


</body>
</html>