<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
session_start();
require_once("../../dati/lib_xmlaccess.php");


$docRec = openXML("../../dati/xml/recensioni.xml");
$rootRec = $docRec->documentElement;
$listaRec = $rootRec->childNodes;

$docOrd = openXML("../../dati/xml/ordini.xml");
$rootOrd = $docOrd->documentElement;
$listaOrd = $rootOrd->childNodes;

//variabile per selezionare l'ordine con cui le recensioni vengono mostrate
// 1 = più recenti; 2 = migliori; 3 = peggiori;
if( isset($_POST['modifica_ordine']) )   $_SESSION['ordine_visualizzazione'] = $_POST['ordine_selezionato'];


if( isset($_POST['valutazione']) ){

    foreach( $listaRec as $rec ){

        if( $_POST['id_rec_valutazione'] == $rec->getAttribute('id_recensione') ){

            $aggiornato = 0;
            foreach( $rec->getElementsByTagName("valutazione_utente") as $valutazione ){

                //Se l'utente ha già valutato precedentemente la recensione, la aggiorno con l'ultimo gradimento espresso 
                if( $valutazione->getAttribute('id_user') == $_SESSION['username'] ){

                    $valutazione->setAttribute('tipologia', $_POST['valutazione']);
                    $aggiornato = 1;
                }
            }
            
            if( $aggiornato == 0 ){
            
                $new_like = $docRec->createElement('valutazione_utente');
                $rec->appendChild($new_like);

                $new_like->setAttribute('id_user', $_SESSION['username']);
                $new_like->setAttribute('tipologia', $_POST['valutazione']);
            }

            printFileXML("../../dati/xml/recensioni.xml", $docRec); 
        }
    }
    
}

//stampa le recensioni
function stampaRecensioni($listOrd, $listRecensioni, $tipologia, $visualizzazione){

    $find = 0;
    $array_review = array();
	$tabNote = '
    <h2>Recensioni della tipologia "'.$_SESSION['id_tipo'].'"</h2>
    <div style="float: right; margin-right: 200px;">
    <form action="recensioni_tipologia.php" method="post">
    <label for="ordine">Ordine recensioni:</label>
    '.stampaType($_SESSION['ordine_visualizzazione']).'
    <button type="submit" name="modifica_ordine" value="1">visualizza</button>
    </form>
    </div>
    <br /><br />';

    //ricerco gli ordini della tipologia selezionata e verifico che sia presente una recensione, 
    //in tal caso la aggiungo ad un vettore che successivamente verrà ordinato secondo la modalità di visualizzazione scelta
    //e stampato di conseguenza
	for ( $c=0; $c < $listOrd->length; $c++ ) {
        $ordine = $listOrd->item($c);
        
        if( $ordine->getAttribute('tipologia_spedizione') == $tipologia ){
            
            //verifico che esista una recensione per l'ordine selezionato
            for( $i=0; $i < $listRecensioni->length; $i++ ) {
                $recensione = $listRecensioni->item($i);            
                
                if( $recensione->getAttribute('id_ordine') == $ordine->getAttribute('id_richiesta') ){
                    //carico il vettore con le recensioni del tipo selezionato
                    $array_review[] = $recensione;
                
                    $find = 1;
                }
            }
        }
	}
    
    if( $find == 0)  return 'Non sono presenti al momento valutazioni per la tipologia selezionata';

    else{
        ordinaReview($array_review, $visualizzazione);
        
        foreach($array_review as $recensione){
                
            $author = $ordine->getAttribute('username');
            $id_review = $recensione->getAttribute('id_recensione');
            $voto = $recensione->getAttribute('voto');
            $data = $recensione->getAttribute('data');
            $text = $recensione->textContent;

            $tabNote .="<table id=\"table_commenti\">
                        <tr>
                        <td><strong>Autore:</strong> $author<strong>  Valutazione:</strong> $voto stelle<strong><br />  Data:</strong> $data</td>
                        <td rowspan=\"2\"></td>
                        </tr>
                        <tr class=\"tr_bordo\">
                         <td>$text</td>
                        </tr>
                        </table>";

            
            //Bottoni like-dislike
            $find_like = 0;
            $count_like = 0;
            $count_dislike = 0;
            foreach( $recensione->getElementsByTagName("valutazione_utente") as $valutazione ){
            

                if( $valutazione->getAttribute('tipologia') == 'like' ){
                    $count_like++;
                }

                if( $valutazione->getAttribute('tipologia') == 'dislike' ){
                    $count_dislike++;
                }
            }
            
            if( $find_like == 1 ){
                $buttons = '
                             <button disabled class="btn-outline">'.$count_like.' Like</button>
                             <button type="submit" name="valutazione" value="dislike" class="btn-primary">'.$count_dislike.' Dislike</button>';
            }

            else if( $find_like == -1 ){
                $buttons = '
                             <button type="submit" name="valutazione" value="like" class="btn-primary">'.$count_like.' Like</button>
                             <button disabled class="btn-outline">'.$count_dislike.' dislike</button>';
            }

            else {
                $buttons = '                
                             <button type="submit" name="valutazione" value="like" class="btn-primary">'.$count_like.' Like</button>
                             <button type="submit" name="valutazione" value="dislike" class="btn-primary">'.$count_dislike.' Dislike</button>';
            }

            $tabNote .= 
                    '<form action="recensioni_tipologia.php" method="post" >
                    <input type="hidden" name="id_rec_valutazione" value="'.$id_review.'">
                    <button disabled class="btn-outline">'.$count_like.' Like</button>
                    <button disabled class="btn-outline">'.$count_dislike.' dislike</button> 
                   </form>';
        }
    }
    $tabNote .= "</table>";
    return $tabNote;
}


function ordinaReview(array &$recensioni, $tipo_visualizzazione){

    //ordinamento per data
    if( $tipo_visualizzazione == 1 ){
        $scambio = true;
        $c = 0;
        while( $c < sizeof($recensioni) - 1 && $scambio ){
            $scambio = false;
            for( $i = 1; $i < sizeof($recensioni) - $c; $i++ ){
                if( $recensioni[$i - 1]->getAttribute('data') < $recensioni[$i]->getAttribute('data') ){

                    $rev_appoggio = $recensioni[$i - 1];
                    $recensioni[$i - 1] = $recensioni[$i];
                    $recensioni[$i] = $rev_appoggio;
                    $scambio = true;
                }
            }
            $c++;
        }
    }

    //ordinamento per recensioni migliori (miglior rapporto like/dislike)
    if( $tipo_visualizzazione == 2 ){
        $scambio = true;
        $c = 0;
        while( $c < sizeof($recensioni) - 1 && $scambio ){
            $scambio = false;
            $count = 0;
            for( $i = 1; $i < sizeof($recensioni) - $c; $i++ ){

                //conto il numero di like per recensione e li confronto con il numero di valutazioni complessive per ottenere il rapporto di gradimento della recensione
                for( $k = 0; $k < $recensioni[$i - 1]->getElementsByTagName("valutazione_utente")->length && $i == 1; $k++ ){
                    
                    $valutazione = $recensioni[$i - 1]->getElementsByTagName("valutazione_utente")->item($k);
                    $rap1 = 0;
                    if( $valutazione->getAttribute('tipologia') == 'like' ){
                        $count++;
                    }
                    //% like
                    $rap1 = ($count * 100) / $recensioni[$i - 1]->getElementsByTagName("valutazione_utente")->length;
                }

                $count = 0;
                foreach( $recensioni[$i]->getElementsByTagName("valutazione_utente") as $valutazione ){

                    $rap2 = 0;
                    if( $valutazione->getAttribute('tipologia') == 'like' ){
                        $count++;
                    }
                    //% like
                    $rap2 = ($count * 100) / $recensioni[$i]->getElementsByTagName("valutazione_utente")->length;
                }
                
                if( $rap1 < $rap2 ){

                    $rev_appoggio = $recensioni[$i - 1];
                    $recensioni[$i - 1] = $recensioni[$i];
                    $recensioni[$i] = $rev_appoggio;
                    $scambio = true;

                    $rap1 = $rap2;
                }
            }
            $c++;
        }
    }

    //ordinamento per recensioni peggiori (peggior rapporto like/dislike)
    if( $tipo_visualizzazione == 3 ){
        $scambio = true;
        $c = 0;
        while( $c < sizeof($recensioni) - 1 && $scambio ){
            $scambio = false;
            $count = 0;
            for( $i = 1; $i < sizeof($recensioni) - $c; $i++ ){

                //conto il numero di like per recensione e li confronto con il numero di valutazioni complessive per ottenere il rapporto di gradimento della recensione
                for( $k = 0; $k < $recensioni[$i - 1]->getElementsByTagName("valutazione_utente")->length && $i == 1; $k++ ){
                    
                    $valutazione = $recensioni[$i - 1]->getElementsByTagName("valutazione_utente")->item($k);
                    $rap1 = 0;
                    if( $valutazione->getAttribute('tipologia') == 'like' ){
                        $count++;
                    }
                    //% like
                    $rap1 = ($count * 100) / $recensioni[$i - 1]->getElementsByTagName("valutazione_utente")->length;
                }

                $count = 0;
                foreach( $recensioni[$i]->getElementsByTagName("valutazione_utente") as $valutazione ){

                    $rap2 = 0;
                    if( $valutazione->getAttribute('tipologia') == 'like' ){
                        $count++;
                    }
                    //% like
                    $rap2 = ($count * 100) / $recensioni[$i]->getElementsByTagName("valutazione_utente")->length;
                }
                
                if( $rap1 > $rap2 ){

                    $rev_appoggio = $recensioni[$i - 1];
                    $recensioni[$i - 1] = $recensioni[$i];
                    $recensioni[$i] = $rev_appoggio;
                    $scambio = true;

                    $rap1 = $rap2;
                }
            }
            $c++;
        }
    }
}

//genera il menu di selezione per la modalità di visualizzazione delle recensioni
function stampaType($visualizzazione) {

    $select = '<select name="ordine_selezionato">';
    $num_elem = 0;
    $option = array('Più recenti', 'Migliori', 'Peggiori');
    for ($i = 0; $i < 3; $i++ ) {

        $sel = ''; //serve a selezionare di default la precedente scelta dell'utente
        if ($visualizzazione == ++$i) $sel = 'selected';
        
        $select .= '<option value="'.$i.'" '.$sel.'>'.$option[--$i].'</option>';
        $num_elem++;            
    }
    
    $select .= '</select>';
    return $select;
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
    <link rel="stylesheet" href="../buttons.css" type="text/css">
</head>

<body>

<div id="top">
    <img src="../../picture/logo.png" width="120" alt="Logo" class="logo" />

	<h1 class="title">ByteCourier3000</h1>
    <p><strong>&nbspUtente: visitatore</strong></p>
</div>

<div id="content">
   <div id="center" class="colonna">
     
     <?php         
        echo stampaRecensioni($listaOrd, $listaRec, $_SESSION['id_tipo'], $_SESSION['ordine_visualizzazione']); 
        ?>
   </div>
   <div id="navbar" class="colonna">
   <?php require_once("menu_visitatore.php");?>
   </div>
</div>


</body>
</html>