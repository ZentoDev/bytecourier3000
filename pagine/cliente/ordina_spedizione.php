<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_cliente.php");
require_once("../../dati/lib_xmlaccess.php");

$docType = openXML("../../dati/xml/setting.xml");
$rootType = $docType->documentElement;  
$listaType = $rootType->firstChild->childNodes; 

if( isset($_POST['invio']) ) {

    //se cambia la tipologia di spedizione le specifiche del pacco memorizzate precedentemente vengono resettate in quanto sono cambiare le condizioni 
    if( $_SESSION['tipo_spedizione'] != $_POST['tipo_spedizione']) {
        $_SESSION['cod_dim'] = '';
        $_SESSION['larghezza'] = '';
        $_SESSION['altezza'] = '';
        $_SESSION['profondita'] = '';
        $_SESSION['peso'] = '';
        $_SESSION['costo'] = '';
    }
    //Salvo il valore delle variabili inserite, ciò permette all'utente di non doverle reinserire in caso di ripetizione della form
    $_SESSION['tipo_spedizione'] = $_POST['tipo_spedizione'];
    $_SESSION['ritiro'] = $_POST['ritiro'];
    header('Location:ordina_spedizione_indirizzi.php');
    exit;
}

//genera il menu di selezione della tipologia di spedizione, le opzioni disponibili dipendono dalle tipologie abilitate (vedi setting.xml)
function stampaType($nome, $lista) {

    $select = '<br />Non sono disponibili opzioni, contattare un gestore';
    $option = '';
    $num_elem = 0;
    for ($i = 0; $i < $lista->length; $i++ ) {
        $type = $lista->item($i);
        if( $type->getAttribute('abilitazione') == 'true') {
            $name_type = $type->getAttribute('nome');

            $sel = ''; //serve a selezionare di default la precedente scelta dell'utente
            if ($nome == $name_type) $sel = 'selected';

            $option .= '<option value="'.$name_type.'" '.$sel.'>'.$name_type.'</option>';
            $num_elem++;            
        }
        if( $num_elem != 0)  $select = '<select name="tipo_spedizione" size="'.$num_elem.'">'.$option.'</select>';
    }
    return $select;
}


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Ordina spedizione</title>
    <link rel="shortcut icon" href="../../picture/favicon.png"/>
	<link rel="stylesheet" href="../style1.css" type="text/css">
</head>

<body>

<div id="top">
    <img src="../../picture/logo.png" width="120" alt="Logo" class="logo" />

	<h1 class="title">ByteCourier3000</h1>
    <p><strong>&nbspUtente: <?php echo $_SESSION['username'].' ('.$_SESSION['ruolo'].')'?> </strong></p>
</div>

<div id="content">
   <div id="center" class="colonna" style="text-align: center;">

        <h1>Ordina spedizione</h1>

        <form action="ordina_spedizione.php" method="post" > 
            <div class="flex-container">
                <div>
                <strong>Tipologia spedizione</strong><br />
                <?php echo stampaType($_SESSION['tipo_spedizione'] ,$listaType);?>
	            </div>
	            <div>
                <strong>Tipologia ritiro</strong><br />
				<select name="ritiro" size="2">
					<option value="in_loco" <?php if ($_SESSION['ritiro'] == 'in_loco') echo 'selected';?>>Domicilio</option>
					<option value="centro" <?php if ($_SESSION['ritiro'] == 'centro') echo 'selected';?>>Centro spedizioni</option>
				</select>
	            </div>
            </div>
        
	        <div style="margin-bottom:10px; text-align: center;">
                <button type="submit" name="invio" value="1">Pagina successiva</button>
            </div>
	    </form>

        <?php 
		if( isset($_POST['invio'])){
			echo '<h3>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;'.$mex.'</h3>';
		}
		?>

   </div>
   
   <div id="navbar" class="colonna">
    <?php require_once("menu_cliente.php");?>
   </div>
</div>


</body>
</html>