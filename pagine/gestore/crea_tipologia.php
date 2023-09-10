<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_gestore.php");

require_once("../../dati/lib_xmlaccess.php");

$docType = openXML("../../dati/xml/setting.xml"); 
$rootType = $docType->documentElement;
$listaType = $rootType->firstChild->childNodes;

$mod = "";

if( isset($_POST['invio']) ){

    $find = 0;
    for( $pos = 0; $pos < $listaType->length && $find == 0; $pos++ ){
        
        $tipologia = $listaType->item($pos);
        if( $_POST['nome'] == $tipologia->getAttribute('nome') ){

            $mod = "Esiste già una tipologia con lo stesso nome inserito";
            $find = 1;
        }
    }

    if( $find == 0 ){

        $new_type = $docType->createElement('tipologia_spedizione');
        $rootType->firstChild->appendChild($new_type);    //$rootType->firstChild rappresenta il nodo setting_spedizioni

        $new_type->setAttribute('nome', $_POST['nome']);
        $new_type->setAttribute('durata', $_POST['durata']);
        $new_type->setAttribute('abilitazione', 'false');

        printFileXml("../../dati/xml/setting.xml", $docType);

        $_SESSION['nome_tipo'] = $_POST['nome'];
        header('Location:dettagli_tipologia.php');
    }
}

//ottiene un id disponibile 
function getId($lista) {

    $pos = $lista->length;
    if( $pos >= 1)
        $last_id = $lista->item(--$pos)->getAttribute('cod') + 1;

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
    <title>Crea nuova opzione</title>
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

   <h2 style="margin-left:50px; text-align: center;">Crea una nuova tipologia</h2>

    <p><strong><?php echo $mod;?></strong></p><br />
    <form action="crea_tipologia.php" method="post" > 
        <div class="flex-container">
            <div>
                <strong>Nome: </strong><br />
                <input type="text" name="nome" required><br />
	            </div>
	            <div>
                <strong>Durata stimata: </strong><br />
                <input type="number" name="durata" min="0" required><br />
	            </div>
            </div>
        
	        <div style="margin-bottom:10px; text-align: center;">
                <button type="submit" name="invio" value="signup">Inserisci valori</button>
            </div>
        </form>

        <div style="margin-bottom:10px; text-align: center;">
        <form action="tipologia_spedizioni.php" method="post" > 
            <button type="submit" name="indietro" value="signup">Indietro</button>
        </form>
        </div>'     
   </div>
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_gestore.php");?>
   </div>
</div>


</body>
</html>