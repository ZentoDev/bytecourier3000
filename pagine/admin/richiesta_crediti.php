<?php
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_admin.php");
require_once("../../dati/lib_xmlaccess.php");

$docCr = openXML("../../dati/xml/crediti.xml");

$rootCr = $docCr->documentElement;  
$listaCr = $rootCr->childNodes;

if( isset( $_POST['scelta'] )) {

    $mex = '';
	for( $pos = 0; $pos < $listaCr->length && !$mex; $pos++ ) {
		$richiesta = $listaCr->item($pos);
		if( $_POST['id_richiesta'] == $richiesta->getAttribute('id_richiesta') ) {
			$richiesta->setAttribute('stato', $_POST['scelta']);
			
			printFileXML("../../dati/xml/crediti.xml", $docCr);
			$mex = 'operazione completata';
		}
	}
}

function stampaRichieste($listCr) {

    $table="<table>"; 
    $presente = 0; //questa variabile segnalerà la presenza di operazioni disponibili

	for ($pos = 0; $pos < $listCr->length; $pos++) {
		$richiesta = $listCr->item($pos);
 
		if( $richiesta->getAttribute('stato') == "in_attesa" ) {   //si selezionano le richieste "in attesa"

            $id_richiesta = $richiesta->getAttribute('id_richiesta');
			$user_id = $richiesta->getAttribute('username');
			$crediti = $richiesta->getAttribute('crediti');

			$table.='<tr>
			         <th><strong>Id richiesta:</strong> '.$id_richiesta.'</th>
			         <td>   
			         <strong>Username:</strong> '.$user_id.'<br />
					 </td>
					 <td>
			         <strong>Crediti richiesti:</strong> '.$crediti.'<br />
			         </td>   
			       	 <td>
			         <form action="richiesta_crediti.php" method="post">
			         <div id="buttons">
			         <input type="hidden" name="id_richiesta" value="'.$id_richiesta.'">
			         <button type="submit" name="scelta" value="accettata" >Accetta</button>
			         </div>
			         </form>
                     <form action="richiesta_crediti.php" method="post">
			         <div id="buttons">
			         <input type="hidden" name="id_richiesta" value="'.$id_richiesta.'">
			         <button type="submit" name="scelta" value="rifiutata" >Rifiuta</button>
			         </div>
			         </form>
			         </td>
			         </tr>';
			$coin = 1;		
			$presente = 1;			
		}
	}
    $table.= '</table>';
    return $table;
}            


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Gestione richiesta crediti</title>
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
     <h2>Gestione richiesta crediti</h2>

     <?php echo '<h3>'.$mex.'</h3>
				 </br>'.
				stampaRichieste($listaCr); 
	 ?>
   </div>
   
   <div id="navbar" class="colonna">
   <?php require_once("menu_admin.php");?>
   </div>
</div>


</body>
</html>