<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_gestore.php");

require_once("../../dati/lib_xmlaccess.php");

require_once("../../dati/lib_xmlaccess.php");
$docOrd = openXML("../../dati/xml/ordini.xml");
  
$rootOrd = $docOrd->documentElement;
$listaOrd = $rootOrd->childNodes;


//Ricavo le informazioni della richiesta ordine in questione
$find = 0;
for ($pos = 0; $pos < $listaOrd->length && $find == 0; $pos++) {
    $ordine = $listaOrd->item($pos);
    
    if( $_SESSION['id_ord'] == $ordine->getAttribute('id_richiesta') ){
        $larghezza = $ordine->getAttribute('larghezza');
        $altezza = $ordine->getAttribute('altezza');
        $profondita = $ordine->getAttribute('profondita');
        $peso = $ordine->getAttribute('peso');
        $costo = $ordine->getAttribute('costo');

        $find = 1;
    }       
}

if( isset($_POST['modifica']) ){

    $ordine->setAttribute('stato', 'modificato');
    $ordine->setAttribute('larghezza', $_POST['larghezza']);
    $ordine->setAttribute('altezza', $_POST['altezza']);
    $ordine->setAttribute('profondita', $_POST['profondita']);
    $ordine->setAttribute('peso', $_POST['peso']);
    $ordine->setAttribute('costo', $_POST['costo']);

    printFileXml("../../dati/xml/ordini.xml", $docOrd);
                    
    $mod = 'Modifica effettuata';
}


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Modifica richiesta ordine</title>
    <link rel="shortcut icon" href="../../picture/favicon.png"/>
	<link rel="stylesheet" href="../style1.css" type="text/css">
    <link rel="stylesheet" href="../tabselezione.css" type="text/css">
</head>

<body>

<div id="top">
    <img src="../../picture/logo.png" width="120" alt="Logo" class="logo" />

	<h1 class="title">ByteCourier3000</h1>
    <p><strong>&nbspUtente: <?php echo $_SESSION['username'].' ('.$_SESSION['ruolo'].')'?> </strong></p>
</div>

<div id="content">
    <div id="center" class="colonna">

    <h2 style="margin-left:50px; text-align: center;">Modifica richiesta dell'ordine "<?php echo $_SESSION['id_ord'];?>" </h2>
	    <?php 
        if( isset($_POST['modifica']) )   echo "<p><strong>$mod</strong></p><br /><br /><br /><br /><br /><br />"; 
        
        else echo 
        '<form action="modifica_richiesta.php" method="post" > 
            <div class="flex-container">
                <div>
                <strong>larghezza: </strong><br />
                <input type="number" name="larghezza" value="'.$larghezza.'" min="0" required><br />
                <strong>Profondità: </strong><br />
                <input type="number" name="profondita" value="'.$profondita.'" min="0" required><br />
				<strong>Costo: </strong><br />
                <input type="number" step="0.1" name="costo" value="'.$costo.'" min="0" required><br /><br />
	            </div>
	            <div>
                <strong>Altezza: </strong><br />
                <input type="number" name="altezza" value="'.$altezza.'" min="0" required><br />
                <strong>Peso Massimo: </strong><br />
                <input type="number" step="0.1" name="peso" value="'.$peso.'" min="0" required><br /><br />
	            </div>
            </div>
        
	        <div style="margin-bottom:10px; text-align: center;">
                <button type="submit" name="modifica" value="signup">Modifica ordine</button>
            </div>
        </form>';
        ?>

        <div style="margin-bottom:10px; text-align: center;">
        <form action="gestione_richieste.php" method="post" > 
            <button type="submit" name="indietro" value="signup">Indietro</button>
        </form>
        </div>
    </div>
   
    <div id="navbar" class="colonna">
    <?php require_once("menu_gestore.php");?>
    </div>
</div>


</body>
</html>