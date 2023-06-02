<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_gestore.php");

require_once("../../dati/lib_xmlaccess.php");

$docType = openXML("../../dati/xml/setting.xml"); 
$rootType = $docType->documentElement;
$listaType = $rootType->firstChild->childNodes;

if( isset($_POST['invio']) ){

    $find = 0;
    for( $pos = 0; $pos < $listaType->length && $find == 0; $pos++ ){
        
        $tipologia = $listaType->item($pos);
        if( $_SESSION['nome_tipo'] == $tipologia->getAttribute('nome') ){

            $lista_dim = $tipologia->childNodes;
            $new_id = getId($lista_dim);
            $new_dim = $docType->createElement('tipo_pacco');
            $tipologia->appendChild($new_dim);
            
            $new_dim->setAttribute('cod', $new_id);
            $new_dim->setAttribute('larghezza', $_POST['larghezza']);
            $new_dim->setAttribute('altezza', $_POST['larghezza']);
            $new_dim->setAttribute('profondita', $_POST['larghezza']);
            $new_dim->setAttribute('peso_max', $_POST['larghezza']);
            $new_dim->setAttribute('costo', $_POST['larghezza']);

            printFileXml("../../dati/xml/setting.xml", $docType);
            $find = 1;

            $mod = 'Opzione inserita, cod '.$new_id;
        }
    }
    if( $find == 0)    $mod = 'Inserimento non effettuato, contattare l\'assistenza';
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
	
</div>

<div id="content">
   <div id="center" class="colonna">

   <h2 style="margin-left:50px; text-align: center;">Crea nuova opzione per la tipologia "<?php echo $_SESSION['nome_tipo'];?>" </h2>
	 <?php 
     if( !isset($_POST['invio']) )   
     {
        echo 
        '<form action="crea_opzione_dimensioni.php" method="post" > 
            <div class="flex-container">
                <div>
                <strong>larghezza: </strong><br />
                <input type="number" name="larghezza" min="0" required><br />
                <strong>Profondità: </strong><br />
                <input type="number" name="profondita" min="0" required><br />
				<strong>Costo: </strong><br />
                <input type="number" step="0.1" name="costo" min="0" required><br /><br />
	            </div>
	            <div>
                <strong>Altezza: </strong><br />
                <input type="number" name="altezza" min="0" required><br />
                <strong>Peso Massimo: </strong><br />
                <input type="number" step="0.1"  name="peso" min="0" required><br /><br />
	            </div>
            </div>
        
	        <div style="margin-bottom:10px; text-align: center;">
                <button type="submit" name="invio" value="signup">Inserisci valori</button>
            </div>
        </form>
        <div style="margin-bottom:10px; text-align: center;">
        <form action="dettagli_tipologia.php" method="post" > 
                <button type="submit" name="indietro" value="signup">Indietro</button>
        </form>
        </div>';
     }
     else 
     {
        echo '
        <div style="margin-bottom:10px; text-align: center;">
            <p><strong>'.$mod.'</strong></p><br /><br /><br /><br /><br /><br /><br /><br />

            <form action="dettagli_tipologia.php" method="post" > 
            <button type="submit" name="indietro" value="signup">Indietro</button>
            </form>
        </div>';
     }
     ?>
   </div>
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_gestore.php");?>
   </div>
</div>


</body>
</html>