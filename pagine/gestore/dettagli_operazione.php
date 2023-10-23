<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_gestore.php");

require_once("../../dati/lib_xmlaccess.php");
$docOp = openXML("../../dati/xml/operazioni.xml");
$docOrd = openXML("../../dati/xml/ordini.xml");

$rootOp = $docOp->documentElement;  
$listaOp = $rootOp->childNodes;

$rootOrd = $docOrd->documentElement;
$listaOrd = $rootOrd->childNodes;

$mex = '';
$coin =0;  // =1 segnala che è stata trovata l'operazione 
for ($pos = 0; $pos < $listaOp->length && $coin == 0; $pos++) {
	
    $operazione = $listaOp->item($pos);
    if( $_POST['id_operazione'] == $operazione->getAttribute('id_operazione') )   $coin = 1;

}

//se l'operazione viene trovata si procede alla ricerca dell'ordine associato
if($coin == 1) {

    $coin =0;
    for ( $i = 0; $i < $listaOrd->length && $coin == 0; $i++ ) {
        $ordine = $listaOrd->item($i);
        $id_ordine = $ordine->getAttribute('id_richiesta');

        if( $id_ordine ==  $operazione->getAttribute('id_ordine')) {

            $stato = $operazione->getAttribute('stato');  

            $ordine_child = $ordine->firstChild;  //nodo indirizzo ritiro

            if( $ordine->getAttribute('ritiro') == 'in_loco' ){

                $indirizzo_ritiro = $ordine_child->getAttribute('strada').' ';
                 $indirizzo_ritiro .= $ordine_child->getAttribute('numero').', ';
                $indirizzo_ritiro .= $ordine_child->getAttribute('citta').', ';
                $indirizzo_ritiro .= $ordine_child->getAttribute('nazione');
                $ordine_child = $ordine_child->nextSibling;  //nodo indirizzo destinazione
            }
            else  $indirizzo_ritiro = 'centro byte courier';

            $destinazione = $ordine_child->getAttribute('strada').' ';
            $destinazione .= $ordine_child->getAttribute('numero').', ';
            $destinazione .= $ordine_child->getAttribute('citta').', ';
            $destinazione .= $ordine_child->getAttribute('nazione');

            $ordine_child = $ordine_child->nextSibling;  //nodo destinatario
            $nome = $ordine_child->getAttribute('nome');
            $cognome = $ordine_child->getAttribute('cognome');
            $courier = $operazione->getAttribute('username_bytecourier');

            $listaNote = $operazione->firstChild->childNodes;

            $coin = 1;			
	    }
    }
}
if($coin == 0)  $mex = "<p>Errore nel processo di recupero dei dettagli dell'operazione, contattare il supporto tecnico</p>";

//restituisce il nome associato allo stato dell'operazione
function statoOperazione($stat) {

    switch ($stat) {
        case 1:
            $print = '
            <strong>Stato dell\'operazione:</strong> transito verso il cliente per il ritiro<br />
            <img src="../../picture/1.png" width="40" alt="fase 1 attiva"/>
            <img src="../../picture/2w.png" width="40" alt="fase 2"/>
            <img src="../../picture/3w.png" width="40" alt="fase 3"/>
            <img src="../../picture/4w.png" width="40" alt="fase 4"/>
            <img src="../../picture/5w.png" width="40" alt="fase 5"/>';
            return $print;
        case 2:
            $print = '
            <strong>Stato dell\'operazione:</strong> transito verso il centro byte courier<br />
            <img src="../../picture/1w.png" width="40" alt="fase 1"/>
            <img src="../../picture/2.png" width="40" alt="fase 2 attiva"/>
            <img src="../../picture/3w.png" width="40" alt="fase 3"/>
            <img src="../../picture/4w.png" width="40" alt="fase 4"/>
            <img src="../../picture/5w.png" width="40" alt="fase 5"/>';
            return $print;
        case 3:
            $print = '
            <strong>Stato dell\'operazione:</strong> pacco al centro byte courier<br />
            <img src="../../picture/1w.png" width="40" alt="fase 1"/>
            <img src="../../picture/2w.png" width="40" alt="fase 2"/>
            <img src="../../picture/3.png" width="40" alt="fase 3 attiva"/>
            <img src="../../picture/4w.png" width="40" alt="fase 4"/>
            <img src="../../picture/5w.png" width="40" alt="fase 5"/>';
            return $print;
        case 4:
            $print = '
            <strong>Stato dell\'operazione:</strong> transito verso il destinatario<br />
            <img src="../../picture/1w.png" width="40" alt="fase 1"/>
            <img src="../../picture/2w.png" width="40" alt="fase 2"/>
            <img src="../../picture/3w.png" width="40" alt="fase 3"/>
            <img src="../../picture/4.png" width="40" alt="fase 4 attiva"/>
            <img src="../../picture/5w.png" width="40" alt="fase 5"/>';
            return $print;
        case 5:
            $print = '
            <strong>Stato dell\'operazione:</strong> consegna effettuata<br />
            <img src="../../picture/1w.png" width="40" alt="fase 1"/>
            <img src="../../picture/2w.png" width="40" alt="fase 2"/>
            <img src="../../picture/3w.png" width="40" alt="fase 3"/>
            <img src="../../picture/4w.png" width="40" alt="fase 4"/>
            <img src="../../picture/5.webp" width="40" alt="fase 5 attiva"/>';
            return $print;

        default: return "errore nel riconoscimento dello stato, contattare il supporto tecnico";
    }
}

//stampa le note
function stampaNote($listNote){

	$tabNote = "<table id=\"table_commenti\">";
	
	for ( $i=0; $i < $listNote->length; $i++ ) {

		$nota = $listNote->item($i);
		$author = $nota->getAttribute('username');
        $data = str_replace("T", " ", $nota->getAttribute('data_nota')); //Nel formato xsd dateTime è presente una 'T' per indicare l'inizio dell'orario, uso str_replace per sostituirla con una spazio vuoto prima di farla visualizzare all'utente
		$text = $nota->textContent;
		
		$tabNote .="<tr>
		              <td><strong>Autore:</strong> $author <strong>Data:</strong> $data</td>
                      <td rowspan=\"2\"></td>
				    </tr>
				    <tr class=\"tr_bordo\">
				       <td>$text</td>

				    </tr>";
		
	}
	$tabNote .= "</table>";
	echo $tabNote;
}


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Dettagli operazione</title>
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
     <h2>Dettagli operazione</h2>
     <?php echo $mex;?>

     <h3>Operazione <?php echo $_POST['id_operazione']; ?></h3>
	 <p>
        <strong>Courier:</strong> <?php echo $courier; ?> <br /><br />
	    <strong>Nome:</strong> <?php echo $nome; ?> <br />
	    <strong>Cognome:</strong> <?php echo $cognome; ?> <br />
		<strong>Indirizzo ritiro:</strong> <?php echo $indirizzo_ritiro; ?> <br />
		<strong>Indirizzo destinazione:</strong> <?php echo $destinazione; ?> <br /><br />

        <?php echo statoOperazione($stato);?>
        <br /><br />
        <?php /*
        if( $stato != 5 && $operazione->getAttribute('username_bytecourier') == $_SESSION['username'] ) {
            echo '
                Quando la seguente fase dell\'operazione viene completata, aggiorna lo stato premendo sul seguente pulsante <br />
                <form action="dettagli_operazione.php" method="post">
                    <input type="hidden" name="id_operazione" value="'. $_POST["id_operazione"] .'" >
                    <button type="submit" name="next_stat" value="1" >Aggiorna stato</button>        
                </form>
                <br />';
        }
        
        echo $mex_stat;
        echo '
        <br /><br />
        <form action="dettagli_operazione.php" method="post" >
		    Inserisci nota: <br />
	        <textarea type="text" name="testo" placeholder="nota..."></textarea>
			<input type="hidden" name="id_operazione" value=" '. $_POST["id_operazione"] .'">
            <input type="hidden" name="datetime" value="'. date("Y-m-d").'T'.date("H:i:s") .'">
			<button type="submit" name="invio_nota" value="1">Invia nota</button>
	    </form>
	    </p>';*/

        stampaNote($listaNote); 
        ?>
	 
    </div> 
   
    <div id="navbar" class="colonna">
    <?php require_once("menu_gestore.php");?>
    </div>
</div>


</body>
</html>