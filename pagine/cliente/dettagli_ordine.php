<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_cliente.php");
require_once("../../dati/lib_xmlaccess.php");

$docOrd = openXML("../../dati/xml/ordini.xml");  
$rootOrd = $docOrd->documentElement;
$listaOrd = $rootOrd->childNodes;

$docOp = openXML("../../dati/xml/operazioni.xml");
$rootOp = $docOp->documentElement;  
$listaOp = $rootOp->childNodes;

$docClienti = openXML("../../dati/xml/clienti.xml");
$rootClienti = $docClienti->documentElement;  
$listaClienti = $rootClienti->childNodes;

$docSetting = openXML("../../dati/xml/setting.xml"); 
$rootSetting = $docSetting->documentElement;

$docRec = openXML("../../dati/xml/recensioni.xml");
$rootRec = $docRec->documentElement;
$listaRec = $rootRec->childNodes;

$mex_pagamento = '';
if( isset($_POST['pagamento']) ){

    //ricerco l'ordine
    $find = 0;
    for ($pos = 0; $pos < $listaOrd->length && $find == 0; $pos++) {
        $ordine = $listaOrd->item($pos);

        if( $_SESSION['id_ordine'] == $ordine->getAttribute('id_richiesta') ){

          //Se l'utente annulla l'ordine, si modifica lo stato in 'rifiutato'
          if( $_POST['pagamento'] == "delete"){
            
            //aggiorno lo stato dell'ordine
            $ordine->setAttribute('stato', 'rifiutato');

            printFileXML("../../dati/xml/ordini.xml", $docOrd);
            $mex_pagamento = 'L\'ordine è stato annullato';
          }

          //Se accetta di pagare, verranno verificati i crediti residui del cliente; in caso siano sufficienti 
          //si procedere alle operazioni di pagamento
          else{
            $costo = $ordine->getAttribute('costo');

            for( $i = 0; $i < $listaClienti->length && $find == 0; $i++) {
                $cliente = $listaClienti->item($i);

                if( $_SESSION['username'] == $cliente->getAttribute('username') ){
                    $cr_client = $cliente->getAttribute('crediti');
                    $find = 1;
                }
            }
            //verifico che il cliente abbia abbastanza crediti per pagare l'ordine
            if( $cr_client <  $costo )  
                $mex_pagamento = 'Possiedi '.$cr_client.' crediti, te ne servono '.$costo.' per pagare l\'ordine '.$_POST['pagamento'];

            else{
                //aggiorno il saldo del cliente
                $cliente->setAttribute('crediti', $cr_client - $costo);

                //aggiorno lo stato dell'ordine
                $ordine->setAttribute('stato', 'accettato');

                //creo operazione associata all'ordine
                $new_id = getId($listaOp, 'id_operazione');
                $new_op = $docOp->createElement('operazione');
                $rootOp->appendChild($new_op);
                $new_note = $docOp->createElement('note');
                $new_op->appendChild($new_note);

                $new_op->setAttribute('id_operazione', $new_id);
                $new_op->setAttribute('username_bytecourier', '');
                $new_op->setAttribute('id_ordine', $_SESSION['id_ordine']);
                if( $ordine->getAttribute('ritiro') == 'in_loco' )
                    $new_op->setAttribute('stato', 1);
                else
                    $new_op->setAttribute('stato', 3);

                //verifico che sia abilitata l'autoassegnazione del bytecourier
                if( $rootSetting->getAttribute('assegnazione_automatica') == 'true' )
                    autoAlloc($docOp);


                printFileXML("../../dati/xml/clienti.xml", $docClienti);
                printFileXML("../../dati/xml/ordini.xml", $docOrd);
                printFileXML("../../dati/xml/operazioni.xml", $docOp);

                $mex_pagamento = 'L\'ordine è stato pagato, adesso hai '.$cr_client - $costo.' crediti';
            }
          }
        }
    }
}

if( isset($_POST['recensione']) ){

    $new_id = getId($listaRec, 'id_recensione');
    $new_rec = $docRec->createElement('recensione', $_POST['testo_recensione']);
    $rootRec->appendChild($new_rec);

    $new_rec->setAttribute('id_recensione', $new_id);
    $new_rec->setAttribute('id_ordine', $_SESSION['id_ordine']);
    $new_rec->setAttribute('voto', $_POST['voto']);

    printFileXML("../../dati/xml/recensioni.xml", $docRec);    
}

//Leggo i valori dell'ordine
$find =0;  // =1 segnala che è stata trovato l'ordine
for ($pos = 0; $pos < $listaOrd->length && $find == 0; $pos++) {
    $ordine = $listaOrd->item($pos);
    
    if( $_SESSION['id_ordine'] == $ordine->getAttribute('id_richiesta') ) {                    
        
        $ordine_child = $ordine->firstChild; 
        $ritiro = $ordine->getAttribute('ritiro');

        if( $ritiro == 'in_loco' ) {  //indirizzo ritiro

        $indirizzo_ritiro = $ordine_child->getAttribute('strada').' ';
        $indirizzo_ritiro .= $ordine_child->getAttribute('numero').', ';
        $indirizzo_ritiro .= $ordine_child->getAttribute('citta').', ';
        $indirizzo_ritiro .= $ordine_child->getAttribute('nazione');
        $ordine_child = $ordine_child->nextSibling;  //nodo indirizzo destinazione
        }

        else $indirizzo_ritiro = 'da consegnare in un centro spedizioni';

        $destinazione = $ordine_child->getAttribute('strada').' ';
        $destinazione .= $ordine_child->getAttribute('numero').', ';
        $destinazione .= $ordine_child->getAttribute('citta').', ';
        $destinazione .= $ordine_child->getAttribute('nazione');

        $ordine_child = $ordine_child->nextSibling;  //nodo destinatario
        $nome = $ordine_child->getAttribute('nome').' ';
        $nome .= $ordine_child->getAttribute('cognome');

        $ordine_child = $ordine_child->nextSibling;  //nodo mittente
        $nome_mit = $ordine_child->getAttribute('nome').' ';
        $nome_mit .= $ordine_child->getAttribute('cognome');

        if( $ordine->getAttribute('stato') == 'modificato' ){

        $ordine_child = $ordine_child->nextSibling;  //nodo modificato (contiene valori precedenti alla modifica)
        $costo_old = $ordine_child->getAttribute('costo_old');
        $peso_old = $ordine_child->getAttribute('peso_old');
        $larghezza_old = $ordine_child->getAttribute('larghezza_old');
        $altezza_old = $ordine_child->getAttribute('altezza_old');
        $profondita_old = $ordine_child->getAttribute('profondita_old');
        }

        $tipologia = $ordine->getAttribute('tipologia_spedizione');   
        $costo = $ordine->getAttribute('costo');  
        $peso = $ordine->getAttribute('peso');  
        $larghezza = $ordine->getAttribute('larghezza');
        $altezza = $ordine->getAttribute('altezza');
        $profondita = $ordine->getAttribute('profondita'); 
        $find = 1;
    }
}
if($find == 0)  $mex = "<p>Errore nel processo di recupero dei dettagli dell'ordine, contattare il supporto tecnico</p>";


//Se è un ordine completato si abilita la possibilita di visualizzare la parte relativa alla recensione
if( $_SESSION['funzione'] == 'recensione' && $ordine->getAttribute('stato') == 'concluso'){
    
    $find = 0;
    for( $i = 0; $i <$listaRec->length && $find == 0; $i++ ){
        $recensione = $listaRec->item($i);
        
        if( $_SESSION['id_ordine'] == $recensione->getAttribute('id_ordine') ){

            $text_rec = '<h3>Recensione</h3>
                        Voto: '.$recensione->getAttribute('voto').'<br />
                        '.$recensione->textContent;
            $find = 1;
        }
    }
    if( $find == 0 ){
        $text_rec = 
                  '<br /><br />
                  <h3>Recensione</h3><br />
                  <form action="dettagli_ordine.php" method="post" >
                  Voto: 
                  <input type="number" name="voto" value="" min="1" max="5" required><br /> <br />
                  <textarea type="text" name="testo_recensione" placeholder="Recensione..." required></textarea><br /><br />
                  <button type="submit" name="recensione" value="1">Salva recensione</button>
                  </form>
                  </p>';
    }
}

//Si verifica se l'ordine sia stato modificato, in tal caso si stampano anche le eventuali modifiche
if( $ordine->getAttribute('stato') == 'modificato' ){
    $print = '<p>
    <strong>Larghezza:</strong> '.$larghezza.' cm ';
    if( $larghezza != $larghezza_old ){
        $print .= '<span style="color:red">(precedente valore: '.$larghezza_old.'cm)</span>';
    }
    $print .= '<br />
    <strong>Altezza:</strong> '.$altezza.' cm ';
    if( $altezza != $altezza_old ){
        $print .= '<span style="color:red">(precedente valore: '.$altezza_old.'cm)</span>';
    }
    $print .= '<br />
    <strong>Profondità:</strong> '.$profondita.' cm ';
    if( $profondita != $profondita_old ){
        $print .= '<span style="color:red">(precedente valore: '.$profondita_old.'cm)</span>';
    }
    $print .= '<br />
    <strong>Peso:</strong> '.$peso.' kg ';
    if( $peso != $peso_old ){
        $print .= '<span style="color:red">(precedente valore: '.$peso_old.'kg)</span>';
    }
    $print .= '
    <br />
	<strong>Tipologia spedizione:</strong> '.$tipologia.' <br />
	<strong>Tipologia ritiro:</strong> '.$ritiro.' <br />
    <strong>Costo:</strong> '.$costo.' € ';
    if( $costo != $costo_old ){
        $print .= '<span style="color:red">(precedente valore: '.$costo_old.'€)</span>';
    }
}
else{
    $print = '
    <p>
    <strong>Larghezza:</strong> '.$larghezza.' cm <br />
    <strong>Altezza:</strong> '.$altezza.' cm <br />
    <strong>Profondita:</strong> '.$profondita.' cm <br />
    <strong>Peso:</strong> '.$peso.' kg<br />
	<strong>Tipologia spedizione:</strong> '.$tipologia.' <br />
	<strong>Tipologia ritiro:</strong> '.$ritiro.' <br />
    <strong>Costo:</strong> '.$costo.' €<br /> 
    </p>';
}

//associa automaticamente le operazioni non assegnate ai bytecuorier
//Per ogni operazione libera si sceglie il bt con meno operazioni in carico in quel momento 
function autoAlloc($docOperazioni) {

	require_once("../../mysql/connection.php");
    if( !$connection_mysqli )   return -1;  //problemi di connessione al db, return -1

	$rootOp = $docOperazioni->documentElement;  
    $listOp = $rootOp->childNodes;

    //query per ottenere gli utenti che sono byte courier
	$select_query = "SELECT username FROM $user_table_name 
	WHERE permesso = 10 ";

    //creo un array associativo che contiene le coppie (bytecourier => numOp)  
    $res = mysqli_query($connection_mysqli, $select_query);
    while ($row = mysqli_fetch_assoc($res)) 
        $arrByte[$row['username']] = countOp($row['username'], $listOp);

	//scorro tutte le operazioni
	for ($pos = 0; $pos < $listOp->length; $pos++) {
		$operazione = $listOp->item($pos);   

		//verifico se l'op non è assegnata
		if( $operazione->getAttribute('username_bytecourier') == "" ) {
            
            //ricerco il bytecourier con meno operazioni a carico
			$min = 999;  //numero fittizio per ricercare il bt con il minimo numero di operazioni prese in carico

			foreach($arrByte as $nome => $numOp ){
				if( $min >  $numOp) {
					$min = $numOp;
					$byte_min = $nome;
				}
			}

			$arrByte[$byte_min]++;    
			$operazione->setAttribute('username_bytecourier', $byte_min);
		}
	}
	return 0;
} 

//conta il numero di operazione attualmente prese in carico da un bytecourier
function countOp($byte, $listOp) {

    $count = 0;
    for ($pos = 0; $pos < $listOp->length; $pos++) {
		$operazione = $listOp->item($pos);
        
	    if( $byte == $operazione->getAttribute('username_bytecourier') ) {    
            $count++;
        }
    }
    return $count;
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
    <title>Riepilogo ordine</title>
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
     <h2>Riepilogo ordine</h2>
     <?php echo $mex_pagamento;?>

     <h3>Informazioni pacco</h3>
     <?php echo $print; ?>

    <h3>Indirizzi</h3>
	 <p>
        <strong>Mittente:</strong> <?php echo $nome_mit; ?> <br />
        <strong>Destinatario:</strong> <?php echo $nome; ?> <br />
		<strong>Indirizzo destinazione:</strong> <?php echo $destinazione; ?> <br />
        <strong>Indirizzo ritiro:</strong> <?php echo $indirizzo_ritiro; ?> <br />
	 
     <form action="dettagli_ordine.php" method="post">
     <?php  
        //bottone pagamento
        if ( $_SESSION['funzione'] == 'pagamento' && !isset($_POST['pagamento']) )  
            echo '
            <button type="submit" name="pagamento" value="pay">Paga</button>
            <button type="submit" name="pagamento" value="delete">Annulla ordine</button>';
        
        if( $_SESSION['funzione'] == 'recensione' && $ordine->getAttribute('stato') == 'concluso'){
            echo $text_rec;
        }
        ?>
     </form>
   </div> 
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_cliente.php");?>
   </div>
</div>


</body>
</html>