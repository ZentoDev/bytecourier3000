<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_cliente.php");
require_once("../../dati/lib_xmlaccess.php");

$docOrd = openXML("../../dati/xml/ordini.xml");
$rootOrd = $docOrd->documentElement;
$listaOrd = $rootOrd->childNodes;

//variabili della form
$volume = $_SESSION['volume'];
$peso = $_SESSION['peso'];
$ritiro = $_SESSION['ritiro'];
$tipo_sp = $_SESSION['tipo_spedizione'];
$volume = $_SESSION['volume'];
$peso = $_SESSION['peso'];
$ritiro = $_SESSION['ritiro'];
$tipo_sp = $_SESSION['tipo_spedizione'];
$nome_dest = $_SESSION['nome_dest'];
$cognome_dest = $_SESSION['cognome_dest'];
$via_dest = $_SESSION['via_dest'];
$nazione_dest = $_SESSION['nazione_dest'];
$citta_dest = $_SESSION['citta_dest'];
$civico_dest = $_SESSION['civico_dest'];
$via_rit = $_SESSION['via_rit'];
$nazione_rit = $_SESSION['nazione_rit'];
$citta_rit = $_SESSION['citta_rit'];

$crediti = crediti($_SESSION['tipo_spedizione'], $_SESSION['volume']);
$mex = '';
if( isset( $_POST['paga'] )) {
    $new_id = getId($listaOrd);
    $new_ordine = $docOrd->createElement('ordine');
    $rootOrd->appendChild($new_ordine);

    $new_ordine->setAttribute('id_richiesta', $new_id);
    $new_ordine->setAttribute('username', $_SESSION['username']);
    $new_ordine->setAttribute('tipologia_spedizione', $_SESSION['tipo_spedizione']);
    $new_ordine->setAttribute('costo', $crediti);
    $new_ordine->setAttribute('stato', 'in_attesa');
    $new_ordine->setAttribute('ritiro', $_SESSION['ritiro']);
    $new_ordine->setAttribute('peso', $_SESSION['peso']);
    $new_ordine->setAttribute('volume', $_SESSION['volume']);

    if( $_SESSION['ritiro'] == 'in_loco' ) {
        $new_addrrit = $docOrd->createElement('indirizzo_ritiro');
        $new_ordine->appendChild($new_addrrit);

        $new_addrrit->setAttribute('citta', $_SESSION['citta_dest']);
        $new_addrrit->setAttribute('nazione', $_SESSION['nazione_dest']);
        $new_addrrit->setAttribute('strada', $_SESSION['via_dest']);
        $new_addrrit->setAttribute('numero', $_SESSION['civico_dest']);
    }
    $new_addrdest = $docOrd->createElement('indirizzo_destinazione');
    $new_ordine->appendChild($new_addrdest);
    
    $new_addrdest->setAttribute('citta', $_SESSION['citta_rit']);
    $new_addrdest->setAttribute('nazione', $_SESSION['nazione_rit']);
    $new_addrdest->setAttribute('strada', $_SESSION['via_rit']);
    $new_addrdest->setAttribute('numero', $_SESSION['civico_rit']);

    $new_destinatario = $docOrd->createElement('destinatario');
    $new_ordine->appendChild($new_destinatario);

    $new_destinatario->setAttribute('nome', $_SESSION['nome_dest']);
    $new_destinatario->setAttribute('cognome', $_SESSION['cognome_dest']);

    printFileXML("../../dati/xml/ordini.xml", $docOrd);
    $mex = 'richiesta ordine effettuata';

    //dopo che la richiesta ordine viene effettuata, cancello i dati della richiesta dalla sessione
    $_SESSION['volume'] = '';
    $_SESSION['peso'] = '';
    $_SESSION['ritiro'] = '';
    $_SESSION['tipo_spedizione'] = '';
    $_SESSION['volume'] = '';
    $_SESSION['peso'] = '';
    $_SESSION['ritiro'] = '';
    $_SESSION['tipo_spedizione'] = '';
    $_SESSION['nome_dest'] = '';
    $_SESSION['cognome_dest'] = '';
    $_SESSION['via_dest'] = '';
    $_SESSION['nazione_dest'] = '';
    $_SESSION['citta_dest'] = '';
    $_SESSION['civico_dest'] = '';
    $_SESSION['via_rit'] = '';
    $_SESSION['nazione_rit'] = '';
    $_SESSION['citta_rit'] = '';
}

//calcola il costo stimato della spedizione
function crediti( $tipo_sped, $dim ) {
    $docType = openXML("../../dati/xml/setting.xml");
    $rootType = $docType->documentElement;  
    $listaType = $rootType->firstChild->childNodes;

    for ($pos = 0; $pos < $listaType->length; $pos++) {
		$tipologia = $listaType->item($pos);

        if( $tipologia->getAttribute('nome') == $tipo_sped ) {
            return $tipologia->getAttribute('costo_unit') + $dim * $tipologia->getAttribute('costo_var');
        }
    }
    return NULL;
}

//ottiene un id disponibile 
function getId($lista) {

    $pos = $lista->length;
    if( $pos >= 1)
        $last_id = $lista->item(--$pos)->getAttribute('id_richiesta') + 1;

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
	
</div>

<div id="content">
   <div id="center" class="colonna">
     <h2>Riepilogo ordine</h2>
     <?php echo $mex;?>

     <h3>Informazioni pacco</h3>
	 <p>
        <strong>Volume:</strong> <?php echo $volume; ?> m^3 <br />
	    <strong>Peso:</strong> <?php echo $peso; ?> kg<br />
	    <strong>Tipologia spedizione:</strong> <?php echo $tipo_sp; ?> <br />
	    <strong>Tipologia ritiro:</strong> <?php echo $ritiro; ?> <br />
        <strong>Costo:</strong> <?php echo $crediti; ?> <br /> 
    </p>
    <h3>Indirizzi</h3>
	 <p>
        <strong>Destinatario:</strong> <?php echo $nome_dest.' '.$cognome_dest; ?> <br />
		<strong>Indirizzo destinazione:</strong> <?php echo $via_dest.' '.$civico_dest.', '.$citta_dest.', '.$nazione_dest; ?> <br />
        <?php 
        if( $ritiro == 'centro') {
            $indirizzo_rit = 'da consegnare in un centro spedizioni';
        }
        else {
            $indirizzo_rit = $via_rit." ".$civico_rit.", ".$citta_rit.", ".$nazione_rit;
        }?>
        <strong>Indirizzo ritiro:</strong> <?php echo $indirizzo_rit; ?> <br /><br />
	 </p>
	 
     <form action="dettagli_ordine.php" method="post">
     <?php if ( !isset( $_POST['paga'] ))  echo '<button type="submit" name="invio" value="1">Paga</button>';?>  
     </form>
   </div> 
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_cliente.php");?>
   </div>
</div>


</body>
</html>