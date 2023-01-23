<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_cliente.php");
require_once("../../dati/lib_xmlaccess.php");

$docCr = openXML("../../dati/xml/crediti.xml");
$docClienti = openXML("../../dati/xml/clienti.xml");

$rootCr = $docCr->documentElement;  
$listaCr = $rootCr->childNodes;

$rootClienti = $docClienti->documentElement;  
$listaClienti = $rootClienti->childNodes;

if( isset($_POST['invio'] ) ) {
    $new_id = getId($listaCr);
    $new_richiesta = $docCr->createElement('richiesta_crediti');
    $rootCr->appendChild($new_richiesta);

    $new_richiesta->setAttribute('id_richiesta', $new_id);
    $new_richiesta->setAttribute('username', $_SESSION['username']);
    $new_richiesta->setAttribute('crediti', $_POST['new_crediti']);
    $new_richiesta->setAttribute('stato', 'in_attesa');

    printFileXML("../../dati/xml/crediti.xml", $docCr);
}

//stampa il numero di crediti possediti dal cliente
function crediti($username, $lista) {

    for( $pos = 0; $pos < $lista->length; $pos++ ) {
        if ( $username == $lista->item($pos)->getAttribute('username') ) {
            return $lista->item($pos)->getAttribute('crediti');
        }
    }
    return '---';
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

function stampaRichiesteAttesa($lista) {

    $table="<table>"; 
    $presente = 0; //questa variabile segnalerà la presenza di operazioni disponibili

	for ($pos = 0; $pos < $lista->length; $pos++) {
		$richiesta = $lista->item($pos);
 
		if( $richiesta->getAttribute('stato') == "in_attesa" &&
            $richiesta->getAttribute('username') == $_SESSION['username'] ) {   //si selezionano le richieste "in attesa"

            $id_richiesta = $richiesta->getAttribute('id_richiesta');
			$crediti = $richiesta->getAttribute('crediti');

			$table.='<tr>
			         <th><strong>Id richiesta:</strong> '.$id_richiesta.'</th>
					 <td>
			         <strong>Crediti richiesti:</strong> '.$crediti.'<br />
			         </td>   
			         </tr>';	
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
    <title>Richiesta crediti</title>
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
     <h2>Richiesta crediti</h2>
	 <p> Possiedi <?php echo crediti($_SESSION['username'], $listaClienti);?> crediti</p><br />
     <form action="richiesta_crediti.php" method="post">
        <input type="number" name="new_crediti">
        <button type="submit" name="invio" value="invio">Invia richiesta</button>
     </form>
     <a href="storico_richieste.php">Storico richieste crediti</a><br />
     <h3>Richieste in attesa</h3>
     
     <?php echo stampaRichiesteAttesa($listaCr);?>

   </div>
   <div id="navbar" class="colonna">
   <?php require_once("menu_cliente.php");?>
   </div>
</div>


</body>
</html>