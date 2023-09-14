<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_cliente.php");
require_once("../../dati/lib_xmlaccess.php");

$docOrd = openXML("../../dati/xml/ordini.xml");
$rootOrd = $docOrd->documentElement;
$listaOrd = $rootOrd->childNodes;

$peso             = $_SESSION['peso'];
$larghezza        = $_SESSION['larghezza'];
$altezza          = $_SESSION['altezza'];
$profondita       = $_SESSION['profondita'];
$costo            = $_SESSION['costo'];
$cod_dim          = $_SESSION['cod_dim'];
$ritiro           = $_SESSION['ritiro'];
$tipo_spedizione  = $_SESSION['tipo_spedizione'];
$nome_dest        = $_SESSION['nome_dest'];
$cognome_dest     = $_SESSION['cognome_dest'];
$via_dest         = $_SESSION['via_dest'];
$nazione_dest     = $_SESSION['nazione_dest'];
$citta_dest       = $_SESSION['citta_dest'];
$civico_dest      = $_SESSION['civico_dest'];

$mex = '';
if( isset( $_POST['invio'] )) {
    $new_id = getId($listaOrd);
    $new_ordine = $docOrd->createElement('ordine');
    $rootOrd->appendChild($new_ordine);

    $new_ordine->setAttribute('id_richiesta', $new_id);
    $new_ordine->setAttribute('username', $_SESSION['username']);
    $new_ordine->setAttribute('tipologia_spedizione', $_SESSION['tipo_spedizione']);
    $new_ordine->setAttribute('costo', $_SESSION['costo']);
    $new_ordine->setAttribute('stato', 'in_attesa');
    $new_ordine->setAttribute('ritiro', $_SESSION['ritiro']);
    $new_ordine->setAttribute('peso', $_SESSION['peso']);
    $new_ordine->setAttribute('larghezza', $_SESSION['larghezza']);
    $new_ordine->setAttribute('altezza', $_SESSION['altezza']);
    $new_ordine->setAttribute('profondita', $_SESSION['profondita']);

    if( $_SESSION['ritiro'] == 'in_loco' ) {
        $new_addrrit = $docOrd->createElement('indirizzo_ritiro');
        $new_ordine->appendChild($new_addrrit);

        $new_addrrit->setAttribute('citta', $_SESSION['citta_rit']);
        $new_addrrit->setAttribute('nazione', $_SESSION['nazione_rit']);
        $new_addrrit->setAttribute('strada', $_SESSION['via_rit']);
        $new_addrrit->setAttribute('numero', $_SESSION['civico_rit']);
    }
    $new_addrdest = $docOrd->createElement('indirizzo_destinazione');
    $new_ordine->appendChild($new_addrdest);
    
    $new_addrdest->setAttribute('citta', $_SESSION['citta_dest']);
    $new_addrdest->setAttribute('nazione', $_SESSION['nazione_dest']);
    $new_addrdest->setAttribute('strada', $_SESSION['via_dest']);
    $new_addrdest->setAttribute('numero', $_SESSION['civico_dest']);

    $new_destinatario = $docOrd->createElement('destinatario');
    $new_ordine->appendChild($new_destinatario);

    $new_destinatario->setAttribute('nome', $_SESSION['nome_dest']);
    $new_destinatario->setAttribute('cognome', $_SESSION['cognome_dest']);

    printFileXML("../../dati/xml/ordini.xml", $docOrd);
    $mex = 'richiesta ordine effettuata';

    //dopo che la richiesta ordine viene effettuata, cancello dalla sessione i dati temporanei della richiesta d'ordine
    $_SESSION['peso'] = '';
    $_SESSION['larghezza'] = '';
    $_SESSION['altezza'] = '';
    $_SESSION['profondita'] = '';
    $_SESSION['costo'] = '';
    $_SESSION['cod_dim'] = '';
    $_SESSION['ritiro'] = '';
    $_SESSION['tipo_spedizione'] = '';
    $_SESSION['nome_dest'] = '';
    $_SESSION['cognome_dest'] = '';
    $_SESSION['via_dest'] = '';
    $_SESSION['nazione_dest'] = '';
    $_SESSION['citta_dest'] = '';
    $_SESSION['civico_dest'] = '';
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
    <title>Riepilogo</title>
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
     <h2>Riepilogo</h2>
     <?php echo $mex;?>

     <h3>Informazioni pacco</h3>
	 <p>
        <strong>Larghezza:</strong> <?php echo $larghezza; ?> cm <br />
        <strong>altezza:</strong> <?php echo $altezza; ?> cm <br />
        <strong>Profondità:</strong> <?php echo $profondita; ?> cm <br />
	    <strong>Peso:</strong> <?php echo $peso; ?> kg<br />
	    <strong>Tipologia spedizione:</strong> <?php echo $tipo_spedizione; ?> <br />
	    <strong>Tipologia ritiro:</strong> <?php echo $ritiro; ?> <br />
        <strong>Costo:</strong> <?php echo $costo; ?> €<br /> 
    </p>
    <h3>Indirizzi</h3>
	 <p>
        <strong>Destinatario:</strong> <?php echo $nome_dest.' '.$cognome_dest; ?> <br />
		<strong>Indirizzo destinazione:</strong> <?php echo $via_dest.' '.$civico_dest.', '.$citta_dest.', '.$nazione_dest;?> <br />
        <?php 
        if( $ritiro == 'centro') {
            $indirizzo_rit = 'da consegnare in un centro spedizioni';
        }
        else {
            $indirizzo_rit = $_SESSION['via_rit']." ".$_SESSION['civico_rit'].", ".$_SESSION['citta_rit'].", ".$_SESSION['nazione_rit'];
        }?>
        <strong>Indirizzo ritiro:</strong> <?php echo $indirizzo_rit; ?> <br /><br />
	 </p>
	 
     <form action="ordina_spedizione_riepilogo.php" method="post">
     <?php if ( !isset( $_POST['invio'] ))  echo '<button type="submit" name="invio" value="1">Conferma ordine</button>';?>  
     </form>
   </div> 
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_cliente.php");?>
   </div>
</div>


</body>
</html>