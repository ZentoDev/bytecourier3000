<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_gestore.php");

require_once("../../dati/lib_xmlaccess.php");

$docType = openXML("../../dati/xml/setting.xml"); 
$rootType = $docType->documentElement;
$listaType = $rootType->firstChild->childNodes;

$find = 0;
if( isset( $_POST['enter'] )) {
    for ($pos = 0; $pos < $listaType->length && $find == 0; $pos++) {
        $tipologia = $listaType->item($pos);
        if( $tipologia->getAttribute('nome') == $_POST['enter'] ) {
            if( $tipologia->getAttribute('abilitazione') == 'true')  $newValue = 'false';
            else  $newValue = 'true';
            
            $tipologia->setAttribute('abilitazione', $newValue);
            PrintFileXML("../../dati/xml/setting.xml", $docType);

            $find = 1;
        }
    }
} 

function stampaTipologie($listType) {
    
    $table="<table>";  
    
    for ($pos = 0; $pos < $listType->length; $pos++) {
        $tipologia = $listType->item($pos);
                           
        $nome = $tipologia->getAttribute('nome');
        $durata = $tipologia->getAttribute('durata');
        $abilitazione = $tipologia->getAttribute('abilitazione');
        
        if( $abilitazione == 'true') $tasto = 'disattiva';
        else  $tasto = 'attiva';
        $table.='<tr>
                  <th><strong>Tipologia:</strong> '.$nome.'<br />
                 <td>   
                  <strong>Durata:</strong> '.$durata.' h<br />
                  <strong>Abilitato:</strong> '.$abilitazione.'<br />
                 </td>   
                 <td>
                  <form action="tipologia_spedizioni.php" method="post">
                  <div id="buttons">
                  <button type="submit" name="enter" value="'.$nome.'" >'.$tasto.'</button>
                  </div>
                 </td>
                 <td>
                  </form>
                  <form action="dettagli_tipologia.php" method="post">
                  <div id="buttons">
                  <button type="submit" name="nome" value="'.$nome.'" >modifica dettagli</button>
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
    <title>Gestione tipologia spedizioni</title>
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
     <h2>Gestione tipologia spedizioni</h2>

     <?php echo stampaTipologie($listaType); ?>
   </div>
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_gestore.php");?>
   </div>
</div>


</body>
</html>