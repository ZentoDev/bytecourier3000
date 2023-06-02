<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_gestore.php");

require_once("../../dati/lib_xmlaccess.php");

$docType = openXML("../../dati/xml/setting.xml"); 
$rootType = $docType->documentElement;
$listaType = $rootType->firstChild->childNodes;

$pagato = 0;
$mex = '';
$coin = 0;  // =1 segnala che è stata trovato trovato la tipologia
for ($pos = 0; $pos < $listaType->length && $coin == 0; $pos++) {
    $tipologia = $listaType->item($pos);
    
    if( $_SESSION['nome_tipo'] == $tipologia->getAttribute('nome') ) {                    
        
        $durata = $tipologia->getAttribute('durata');

        $coin = 1;
    }
}
if($coin == 0)  $mex = "<p>Errore nel processo di recupero dei dettagli della tipologia di spedizione selezionata, contattare il supporto tecnico</p>";

//Modifica durata
if( isset( $_POST['durata']) ) {

    $durata = $_POST['durata'];
    $tipologia->setAttribute('durata', $durata);
    printFileXml("../../dati/xml/setting.xml", $docType);
}

//Elimina opzione
if( isset( $_POST['delete'] )) {

    $coin = 0;
    $list_dim = $tipologia->childNodes;
    for( $pos = 0; $pos < $list_dim->length && $coin == 0; $pos++ ) {
        $dim = $list_dim->item($pos);
        if( $_POST['delete'] == $dim->getAttribute('cod') ) {

            $tipologia->removeChild($dim);
            printFileXml("../../dati/xml/setting.xml", $docType);

            $coin = 1;
        }
    }
}

//Modifica dimensioni, salvo le variabili nella sessione prima di aprire la nuova pagina
if( isset( $_POST['modifica'] )) {

    $coin = 0;
    $list_dim = $tipologia->childNodes;
    for ($pos = 0; $pos < $list_dim->length && $coin == 0; $pos++) { 
        $voce = $list_dim->item($pos); 
    
        if( $_POST['modifica'] == $voce->getAttribute('cod') ) { 

            $_SESSION['larghezza']  = $voce->getAttribute('larghezza');
            $_SESSION['altezza']    = $voce->getAttribute('altezza');
            $_SESSION['profondita'] = $voce->getAttribute('profondita');
            $_SESSION['peso']       = $voce->getAttribute('peso_max');
            $_SESSION['costo']      = $voce->getAttribute('costo');
        
            $coin = 1;
        }
    }
    $_SESSION['cod'] = $_POST['modifica'];
    header('Location:modifica_dimensioni.php');
    exit;
}


function stampaOpzioni($type) {

    $table="<table>";  
    
    $lista_dim = $type->childNodes;
    for ($pos = 0; $pos < $lista_dim->length; $pos++) { 
        
        $voce = $lista_dim->item($pos); 
        $cod = $voce->getAttribute('cod');
        $larghezza = $voce->getAttribute('larghezza');
        $altezza = $voce->getAttribute('altezza');
        $profondita = $voce->getAttribute('profondita');
        $peso = $voce->getAttribute('peso_max');
        $costo = $voce->getAttribute('costo');  

        $table.='<tr>
                  <th><strong>Cod:</strong> '.$cod.'<br />
                 <td>   
                  <strong>Larghezza:</strong> '.$larghezza.' cm<br />
                  <strong>Altezza:</strong> '.$altezza.' cm<br />
                  <strong>Profondità:</strong> '.$profondita.' cm<br />
                 </td>   
                 <td>
                  <strong>Peso:</strong> '.$peso.' kg<br />
                  <strong>Costo:</strong> '.$costo.' €<br />
                  </div>
                 </td>
                 <td>
                 <form action="dettagli_tipologia.php" method="post">
                 <div id="buttons">
                 <button type="submit" name="delete" value="'.$cod.'" >elimina opzione</button>
                 </div>
                 </td>
                 <td>
                  <form action="dettagli_tipologia.php" method="post">
                  <div id="buttons">
                  <button type="submit" name="modifica" value="'.$cod.'" >modifica dimensioni</button>
                  </div>
                  </form>
                 </td>
                 </tr>';
    }
    $table.="</table>";
    echo $table;    
}


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Dettagli tipologia spedizione</title>
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
    <h2>Dettagli della tipologia '<?php echo $_SESSION['nome_tipo'];?>'</h2>
    <?php if( isset( $_POST['invio'] ))   echo "<p><strong>$mod</strong></p>"; ?>
    <form action="dettagli_tipologia.php" method="post" > 
		<strong>Durata stimata: </strong><br />
        <input type="text" name="durata" value="<?php echo $durata?>">        
        <button type="submit" name="durata_submit" value="signup">Modifica durata</button><br /><br />
    </form>
    <form action="crea_opzione_dimensioni.php" method="post" >
        <button type="submit" name="add_option" value="signup">Aggiungi opzione dimensioni</button><br /><br />
    </form>
    <?php echo stampaOpzioni($tipologia); ?>

   
   </div>
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_gestore.php");?>
   </div>
</div>


</body>
</html>