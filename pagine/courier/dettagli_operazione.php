<?php
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_courier.php");

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
            $indirizzo_ritiro = $ordine_child->getAttribute('strada').' ';
            $indirizzo_ritiro .= $ordine_child->getAttribute('numero').', ';
            $indirizzo_ritiro .= $ordine_child->getAttribute('citta').', ';
            $indirizzo_ritiro .= $ordine_child->getAttribute('nazione');

            $ordine_child = $ordine_child->nextSibling;  //nodo indirizzo destinazione
            $destinazione = $ordine_child->getAttribute('strada').' ';
            $destinazione .= $ordine_child->getAttribute('numero').', ';
            $destinazione .= $ordine_child->getAttribute('citta').', ';
            $destinazione .= $ordine_child->getAttribute('nazione');

            $ordine_child = $ordine_child->nextSibling;  //nodo destinatario
            $nome = $ordine_child->getAttribute('nome');
            $cognome = $ordine_child->getAttribute('cognome');

            $listaNote = $operazione->firstChild->childNodes;

            $coin = 1;			
	    }
    }
}
if($coin == 0)  $mex = "<p>Errore nel processo di recupero dei dettagli dell'operazione, contattare il supporto tecnico</p>";


//aggiornamento stato
if( $_POST['next_stat'] == 1) {

    $fail = 0;
    switch ($stato) {
        case 1:
            $stato += 1;
            $operazione->setAttribute('stato', $stato );
            $mex_stat = "Stato aggiornato";
            break;
        case 2:
            $stato += 1;
            $operazione->setAttribute('stato', $stato );
            $mex_stat = "Ritiro completato";
            break;
        case 3:
            $stato += 1;
            $operazione->setAttribute('stato', $stato );
            $mex_stat = "Stato aggiornato";
            break;
        case 4:
            $stato += 1;
            $operazione->setAttribute('stato', $stato );
            $ordine->setAttribute('stato', 'concluso');
            printFileXML("../../dati/xml/ordini.xml", $docOrd);
            $mex_stat = "";
            break;

        default: 
            $mex_stat = "errore nel riconoscimento dello stato, contattare il supporto tecnico";
            $fail = 1;
    }

    if( $fail == 0)   printFileXML("../../dati/xml/operazioni.xml", $docOp);
}

//inserimento nuova nota
if( $_POST['invio_nota'] == 1) {
    $note = $operazione->firstChild;
    
    $newNota = $docOp->createElement('nota', $_POST['testo']);
    $note->appendChild($newNota);

    $newNota->setAttribute('data_nota', $_POST['datetime']);
    $newNota->setAttribute('username', $_SESSION['username']);

    //permette di salvare il documento in un file xml
    printFileXML("../../dati/xml/operazioni.xml", $docOp);
}

//restituisce il nome associato allo stato dell'operazione
function statoOperazione($stat) {
    switch ($stat) {
        case 1:
            return "transito verso il cliente per il ritiro";
        case 2:
            return "transito verso il centro byte courier";
        case 3:
            return "pacco al centro byte courier";
        case 4:
            return "transito verso il destinatario";
        case 5:
            return "consegna effettuata";

        default: return "errore nel riconoscimento dello stato, contattare il supporto tecnico";
    }
}

//stampa le note
function stampaNote($listNote){

	$tabNote = "<table id=\"table_commenti\">";
	
	for ( $i=0; $i < $listNote->length; $i++ ) {

		$nota = $listNote->item($i);
		$author = $nota->getAttribute('username');
        $data = $nota->getAttribute('data_nota');
		$text = $nota->textContent;
		
		$tabNote .="<tr>
		              <td><strong>Autore:</strong> $author <strong>Data:</strong> $data</td>
                      <td rowspan=\"2\">llla</td>
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
	
</div>

<div id="content">
   <div id="center" class="colonna">
     <h2>Dettagli operazione</h2>
     <?php echo $mex;?>

     <h3>Operazione <?php echo $_POST['id_operazione']; ?></h3>
	 <p>
	    <strong>Nome:</strong> <?php echo $nome; ?> <br />
	    <strong>Cognome:</strong> <?php echo $cognome; ?> <br />
		<strong>Indirizzo ritiro:</strong> <?php echo $indirizzo_ritiro; ?> <br />
		<strong>Indirizzo destinazione:</strong> <?php echo $destinazione; ?> <br /><br />

		<strong>Stato dell'operazione:</strong> <?php echo statoOperazione($stato); ?> <br />
        <?php 
        if( $stato != 5 ) 
            echo '
                Quando la seguente fase dell\'operazione viene completata, aggiorna lo stato premendo sul seguente pulsante <br />
                <form action="dettagli_operazione.php" method="post">
                    <input type="hidden" name="id_operazione" value="'. $_POST["id_operazione"] .'" >
                    <button type="submit" name="next_stat" value="1" >Aggiorna stato</button>        
                </form>
                <br />';
        
        echo $mex_stat;
        ?>

<?php echo '
        <br /><br />
        <form action="dettagli_operazione.php" method="post" >
		    Inserisci nota: <br />
	        <textarea type="text" name="testo" placeholder="nota..."></textarea>
			<input type="hidden" name="id_operazione" value=" '. $_POST["id_operazione"] .'">
            <input type="hidden" name="datetime" value="'. date("Y-m-d").'T'.date("H:i:s") .'">
			<button type="submit" name="invio_nota" value="1">Invia nota</button>
	    </form>
	 </p>';

     stampaNote($listaNote); 
 ?>
	 
   </div> 
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_courier.php");?>
   </div>
</div>


</body>
</html>