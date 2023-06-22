<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_cliente.php");
require_once("../../dati/lib_xmlaccess.php");

$docType = openXML("../../dati/xml/setting.xml");

$rootType = $docType->documentElement;  
$listaType = $rootType->firstChild->childNodes;

if( isset($_POST['vedi_opzioni']) ){
  
  $_SESSION['id_tipo'] = $_POST['vedi_opzioni'];
  header('Location:dettagli_tipologia.php');
  exit;  
}

if( isset($_POST['recensioni']) ){
  
  $_SESSION['id_tipo'] = $_POST['recensioni'];
  header('Location:recensioni_tipologia.php');
  exit;  
}

function stampaTipologie($lista) {

  $table="<table>"; 
  $presente = 0; //questa variabile segnalerà la presenza di tipologie disponibili

	for ($pos = 0; $pos < $lista->length; $pos++) {
		$tipologia = $lista->item($pos);

    if( $tipologia->getAttribute('abilitazione') == 'true') {
      $nome = $tipologia->getAttribute('nome');
      $durata = $tipologia->getAttribute('durata');
      $costo_unit = $tipologia->getAttribute('costo_unit');
      $costo_var = $tipologia->getAttribute('costo_var');
      $dimensioni_max = $tipologia->getAttribute('dimensioni_max');
      $dimensioni_min = $tipologia->getAttribute('dimensioni_min');

      $table.='<tr>
               <th><strong>Tipologia:</strong> '.$nome.'</th>
               <td>
               <strong>Stima durata:</strong> '.$durata.'h<br />
               </td>   
               <td>
               <td>
               <form action="catalogo.php" method="post">
               <div id="buttons">
               <button type="submit" name="vedi_opzioni" value="'.$nome.'" >Opzioni disponibili</button>
               <button type="submit" name="recensioni" value="'.$nome.'" >Recensioni</button>
               </div>
               </form>
               </td>
               </td>
               </tr>';
      $presente = 1;
    }
	}
    $table.= '</table>';
    if ($presente == 0)  return 'Al momento non &egrave; possibile ordinare una spedizione';
    return $table;
}            

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Tipologia spedizioni</title>
    <link rel="shortcut icon" href="../../picture/favicon.png"/>
	  <link rel="stylesheet" href="../style1.css" type="text/css"/>
    <link rel="stylesheet" href="../tabselezione.css" type="text/css"/>
</head>

<body>

<div id="top">
    <img src="../../picture/logo.png" width="120" alt="Logo" class="logo" />

	<h1 class="title">ByteCourier3000</h1>
	
</div>

<div id="content">
   <div id="center" class="colonna">
     <h2>Tipologia spedizioni</h2>     
     <?php echo stampaTipologie($listaType);?>
   </div>
   <div id="navbar" class="colonna">
   <?php require_once("menu_cliente.php");?>
   </div>
</div>


</body>
</html>