<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_gestore.php");

require_once("../../dati/lib_xmlaccess.php");

$docType = openXML("../../dati/xml/setting.xml"); 
$rootType = $docType->documentElement;
$listaType = $rootType->firstChild->childNodes;

$find = 0;
if( isset( $_POST['abilitazione'] )) {
    for ($pos = 0; $pos < $listaType->length && $find == 0; $pos++) {
        $tipologia = $listaType->item($pos);
        if( $tipologia->getAttribute('nome') == $_POST['abilitazione'] ) {
            if( $tipologia->getAttribute('abilitazione') == 'true')  $newValue = 'false';
            else  $newValue = 'true';
            
            $tipologia->setAttribute('abilitazione', $newValue);
            PrintFileXML("../../dati/xml/setting.xml", $docType);

            $find = 1;
        }
    }
} 

if( isset( $_POST['nome_tipo'] )) {
    $_SESSION['nome_tipo'] = $_POST['nome_tipo'];
    header('Location:dettagli_tipologia.php');
    exit;
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
                  <button type="submit" name="abilitazione" value="'.$nome.'" >'.$tasto.'</button>
                  </div>
                 </td>
                 <td>
                  </form>
                  <form action="tipologia_spedizioni.php" method="post">
                  <div id="buttons">
                  <button type="submit" name="nome_tipo" value="'.$nome.'" >modifica dettagli</button>
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
    <p><strong>&nbspUtente: <?php echo $_SESSION['username'].' ('.$_SESSION['ruolo'].')'?> </strong></p>
</div>

<div id="content">
   <div id="center" class="colonna">
     <h2>Gestione tipologia spedizioni</h2>
     <form action="crea_tipologia.php" method="post" >
        <button type="submit" name="add_option" value="signup">Aggiungi nuova tipologia</button><br /><br />
     </form>
     <?php echo stampaTipologie($listaType); ?>
   </div>
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_gestore.php");?>
   </div>
</div>


</body>
</html>