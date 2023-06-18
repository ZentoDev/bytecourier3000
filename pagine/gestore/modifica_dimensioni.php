<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_gestore.php");

require_once("../../dati/lib_xmlaccess.php");

$docType = openXML("../../dati/xml/setting.xml"); 
$rootType = $docType->documentElement;
$listaType = $rootType->firstChild->childNodes;

if( isset($_POST['modifica']) ){

    $find = 0;
    for( $pos = 0; $pos < $listaType->length && $find == 0; $pos++ ){
        
        $tipologia = $listaType->item($pos);
        if( $_SESSION['nome_tipo'] == $tipologia->getAttribute('nome') ){

            $lista_dim = $tipologia->childNodes;
            for( $i = 0; $i < $lista_dim->length && $find == 0; $i++) {

                $dim = $lista_dim->item($i);
                if( $_SESSION['cod'] == $dim->getAttribute('cod') ){

                    $_SESSION['larghezza'] = $_POST['larghezza'];
                    $dim->setAttribute('larghezza', $_SESSION['larghezza']);
                    $_SESSION['altezza'] = $_POST['altezza'];
                    $dim->setAttribute('altezza', $_SESSION['altezza']);
                    $_SESSION['profondita'] = $_POST['profondita'];
                    $dim->setAttribute('profondita', $_SESSION['profondita']);
                    $_SESSION['peso'] = $_POST['peso'];
                    $dim->setAttribute('peso_max', $_SESSION['peso']);
                    $_SESSION['costo'] = $_POST['costo'];
                    $dim->setAttribute('costo', $_SESSION['costo']);

                    printFileXml("../../dati/xml/setting.xml", $docType);
                    
                    $find = 1;
                    $mod = 'Modifica effettuata';
                } 
            }
        }
    }
    if( $find == 0)    $mod = 'Modifica non effettuata, contattare l\'assistenza';
}


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Modifica tipologia spedizione</title>
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

    <h2 style="margin-left:50px; text-align: center;">Modifica dimensioni dell'opzione di "<?php echo $_SESSION['nome_tipo'].'" codice: '. $_SESSION['cod'];?> </h2>
	    <?php if( isset($_POST['modifica']) )   echo "<p><strong>$mod</strong></p>"; ?>
        <form action="modifica_dimensioni.php" method="post" > 
            <div class="flex-container">
                <div>
                <strong>larghezza: </strong><br />
                <input type="number" name="larghezza" value="<?php echo $_SESSION['larghezza']?>" min="0" required><br />
                <strong>Profondità: </strong><br />
                <input type="number" name="profondita" value="<?php echo $_SESSION['profondita']?>" min="0" required><br />
				<strong>Costo: </strong><br />
                <input type="number" step="0.1" name="costo" value="<?php echo $_SESSION['costo']?>" min="0" required><br /><br />
	            </div>
	            <div>
                <strong>Altezza: </strong><br />
                <input type="number" name="altezza" value="<?php echo $_SESSION['altezza']?>" min="0" required><br />
                <strong>Peso Massimo: </strong><br />
                <input type="number" step="0.1" name="peso" value="<?php echo $_SESSION['peso']?>" min="0" required><br /><br />
	            </div>
            </div>
        
	        <div style="margin-bottom:10px; text-align: center;">
                <button type="submit" name="modifica" value="signup">Modifica valori</button>
            </div>
        </form>

        <div style="margin-bottom:10px; text-align: center;">
        <form action="dettagli_tipologia.php" method="post" > 
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