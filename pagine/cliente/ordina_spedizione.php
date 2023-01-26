<?php
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_cliente.php");
require_once("../../dati/lib_xmlaccess.php");

$docType = openXML("../../dati/xml/setting.xml");
$rootType = $docType->documentElement;  
$listaType = $rootType->firstChild->childNodes;

//variabili della form
$volume = $_SESSION['volume'];
$peso = $_SESSION['peso'];
$ritiro = $_SESSION['ritiro'];
$tipo_sp = $_SESSION['tipo_spedizione'];

if( isset($_POST['invio']) ) {
    //Salvo il valore delle variabili inserite, ciò permette all'utente di non doverle reinserire in caso di ripetizione della form
	$volume = $_POST['volume'];
	$peso = $_POST['peso'];
    $ritiro = $_POST['ritiro'];
    $tipo_sp = $_POST['tipo_spedizione'];

    //se le dimensioni sono compatibili con la tipologia di spedizione selezionata, si passa alla pagina di inserimento degli indirizzi
    $flag = 0;
    for ($pos = 0; $pos < $listaType->length && $flag == 0; $pos++ ) {
        $tipologia_spedizione = $listaType->item($pos);
        if( $tipo_sp == $tipologia_spedizione->getAttribute('nome') ) {

            if( $volume >= $tipologia_spedizione->getAttribute('dimensioni_min') &&
                $volume <= $tipologia_spedizione->getAttribute('dimensioni_max')       )  {

                $_SESSION['volume'] = $volume;
                $_SESSION['peso'] = $peso;
                $_SESSION['ritiro'] = $ritiro;
                $_SESSION['tipo_spedizione'] = $tipo_sp;
                header('Location:ordina_spedizione_indirizzi.php');
                exit;
            }
            $flag = 1;
            $min = $tipologia_spedizione->getAttribute('dimensioni_min');
            $max = $tipologia_spedizione->getAttribute('dimensioni_max');
        }
    }
    $mex = 'la dimensione inserita non &egrave; supportata dalla tipologia di spedizione scelta (min = '.$min.', max = '.$max.')';
}

function stampaType($nome, $lista) {

    $option = '';
    for ($i = 0; $i < $lista->length; $i++ ) {
        $type = $lista->item($i);
        if( $type->getAttribute('abilitazione') == 'true') {
            $sel = ''; //mantiene la memoria della scelta effettuata dall'utente
            $name_type = $type->getAttribute('nome');
            if ($nome == $name_type) $sel = 'selected';
            $option .= '<option value="'.$name_type.'" '.$sel.'>'.$name_type.'</option>';
        }
    }
    return $option;
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
	
</div>

<div id="content">
   <div id="center" class="colonna" style="text-align: center;">

        <h1>Ordina spedizione</h1>

        <form action="ordina_spedizione.php" method="post" > 
            <div class="flex-container">
                <div>
                <strong>Dimensioni</strong><br />
                <input type="number" name="volume" value="<?php echo $volume ?>" required><br />
                <strong>Tipologia spedizione</strong><br />
				<select name="tipo_spedizione" size="2">
                    <?php echo stampaType($tipo_sp ,$listaType);?>
				</select>
	            </div>
	            <div>
                <strong>Peso</strong><br />
                <input type="number" name="peso" value="<?php echo $peso ?>" required><br />
                <strong>Tipologia ritiro</strong><br />
				<select name="ritiro" size="2">
					<option value="in_loco" <?php if ($ritiro == 'in_loco') echo 'selected';?>>Domicilio</option>
					<option value="centro" <?php if ($ritiro == 'centro') echo 'selected';?>>Centro spedizioni</option>
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