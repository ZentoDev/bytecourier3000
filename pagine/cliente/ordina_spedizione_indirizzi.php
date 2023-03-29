<?php
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE);
require_once("login_cliente.php");
require_once("../../dati/lib_xmlaccess.php");

//variabili della form
$nome_dest = $_SESSION['nome_dest'];
$cognome_dest = $_SESSION['cognome_dest'];
$via_dest = $_SESSION['via_dest'];
$nazione_dest = $_SESSION['nazione_dest'];
$citta_dest = $_SESSION['citta_dest'];
$civico_dest = $_SESSION['civico_dest'];
$via_rit = $_SESSION['via_rit'];
$nazione_rit = $_SESSION['nazione_rit'];
$citta_rit = $_SESSION['citta_rit'];
$civico_rit = $_SESSION['civico_rit'];

if( isset($_POST['invio']) ) {
    //Salvo il valore delle variabili inserite, ciò permette all'utente di non doverle reinserire in caso di ripetizione della form

    $_SESSION['nome_dest'] = $_POST['nome_dest'];
    $_SESSION['cognome_dest'] = $_POST['cognome_dest'];
	$_SESSION['nazione_dest'] = $_POST['nazione_dest'];
	$_SESSION['citta_dest'] = $_POST['citta_dest'];
    $_SESSION['via_dest'] = $_POST['via_dest'];
    $_SESSION['civico_dest'] = $_POST['civico_dest'];

    if( $_SESSION['ritiro'] == 'in_loco' ) {
        $_SESSION['nazione_rit'] = $_POST['nazione_rit'];
        $_SESSION['citta_rit'] = $_POST['citta_rit'];
        $_SESSION['via_rit'] = $_POST['via_rit'];
        $_SESSION['civico_rit'] = $_POST['civico_rit'];
    }

    //verifico che le dimensioni del pacco non siano cambiate, 
    //in caso di modifiche leggo i nuovi valori delle dimensioni e le salvo come variabili di sessione 
    if( $_SESSION['cod_dim'] != $_POST['cod_dim'] ) {
        $docType = openXML("../../dati/xml/setting.xml");
        $rootType = $docType->documentElement;  
        $lista = $rootType->firstChild->childNodes;
    
        $find = 0;
        $find_d = 0;
        
        for ($i = 0; $i < $lista->length && $find == 0; $i++ ) {
    
            $type = $lista->item($i);
            //cerco il tipo spedizione selezionato
            if( $_SESSION['tipo_spedizione'] == $type->getAttribute('nome') ){
                $find = 1; //tipologia spedizione trovata, interrompe i successivi cicli del for
                
                $lista_dim = $type->childNodes;
                for ($c = 0; $c < $lista_dim->length && $find_d == 0; $c++ ) {

                    $voce = $lista_dim->item($c); 
    
                    if( $voce->getAttribute('cod') == $_POST['cod_dim'] ) {

                        $_SESSION['larghezza'] = $voce->getAttribute('larghezza');
                        $_SESSION['altezza'] = $voce->getAttribute('altezza');
                        $_SESSION['profondita'] = $voce->getAttribute('profondita');
                        $_SESSION['peso'] = $voce->getAttribute('peso_max');
                        $_SESSION['costo'] = $voce->getAttribute('costo');
                        $find_d = 1;
                    }          
                }
                if( $find_d == 0)    $_SESSION['larghezza'] = 'Problemi interni, contattare un gestore';
            } 
        }
    }//conclusione aggiornamento delle var di sessione delle dimesioni del pacco

    $_SESSION['cod_dim'] = $_POST['cod_dim'];

    header('Location:ordina_spedizione_riepilogo.php');
    exit;
}

//genera le scelte disponibili in termini di dimensioni e costo (larghezza, altezza, profondità, peso, costo) per la tipologia di spedizione selezionata
function stampaType($tipo) {

    $docType = openXML("../../dati/xml/setting.xml");
    $rootType = $docType->documentElement;  
    $lista = $rootType->firstChild->childNodes;

    $find = 0;
    $num_elem = 0;
    
    for ($i = 0; $i < $lista->length && $find == 0; $i++ ) {

        $type = $lista->item($i);
        //cerco il tipo di spedizione selezionato
        if( $tipo == $type->getAttribute('nome') ){
            $find = 1; //tipologia spedizione trovata, interrompe i successivi cicli del for
            
            $lista_dim = $type->childNodes;
            for ($c = 0; $c < $lista_dim->length; $c++ ) {

                $voce = $lista_dim->item($c); 
                $cod = $voce->getAttribute('cod');
                $larghezza = $voce->getAttribute('larghezza');
                $altezza = $voce->getAttribute('altezza');
                $profondita = $voce->getAttribute('profondita');
                $peso = $voce->getAttribute('peso_max');
                $costo = $voce->getAttribute('costo');

                $sel = ''; //serve a selezionare di default la precedente scelta dell'utente
                if ($cod == $_SESSION['cod_dim'] || $cod == 1) $sel = 'checked';

                $input .= '<input type="radio" name="cod_dim" value="'.$cod.'" '.$sel.'/>larghezza: '.$larghezza.'cm; altezza: '.$altezza.'cm; profondità: '.$profondita.'cm; peso: '.$peso.'kg; costo: '.$costo.' €<br />';
                $num_elem++;            
            }
            if( $num_elem == 0)    $input = '<br />Non sono disponibili opzioni, contattare un gestore';
        } 
    }
    return $input;
}



///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>inserimento indirizzi</title>
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

        <h1>Ordina spedizione: inserimento indirizzi</h1>

        <form action="ordina_spedizione_indirizzi.php" method="post" > 
            <br />
            <strong>Seleziona dimensioni del pacco</strong><br /><br />
            <?php echo stampaType($_SESSION['tipo_spedizione']);?>
            <br />
            <h3>indirizzo di destinzazione</h3>
            <div class="flex-container" style="padding:0%; margin: -5%;">
                <div>
                <strong>Nome destinatario</strong><br />
                <input type="text" name="nome_dest" value="<?php echo $nome_dest;?>" required><br />
                <br />
                <strong>Nazione</strong><br />
                <input type="text" name="nazione_dest" value="<?php echo $nazione_dest;?>" required><br />
                <strong>Via</strong><br />
                <input type="text" name="via_dest" value="<?php echo $via_dest;?>" required><br />
	            </div>
	            <div>
                <strong>Cognome destinatario</strong><br />
                <input type="text" name="cognome_dest" value="<?php echo $cognome_dest;?>" required><br />
                <br />
                <strong>Citt&agrave;</strong><br />
                <input type="text" name="citta_dest" value="<?php echo $citta_dest;?>" required><br />
                <strong>Numero civico</strong><br />
                <input type="number" name="civico_dest" value="<?php echo $civico_dest;?>" min='1' required><br />
	            </div>
            </div>
            <br />
            <h3>indirizzo di ritiro</h3>
            <?php  
            if( $_SESSION['ritiro'] == 'centro') 
                echo '<p><strong>Hai selezionato la modalita di ritiro "in centro", consegna il pacco nel centro spedizioni più vicino</p></strong>';
            else {
                echo '
                <div class="flex-container" style="padding:0%; margin: -5%;">
                <div>
                <strong>Nazione</strong><br />
                <input type="text" name="nazione_rit" value="'.$nazione_rit.'" required><br />
                <strong>Via</strong><br />
                <input type="text" name="via_rit" value="'.$via_rit.'" required><br />
                </div>
                <div>
                <strong>Citt&agrave;</strong><br />
                <input type="text" name="citta_rit" value="'.$citta_rit.'" required><br />
                <strong>Numero civico</strong><br />
                <input type="number" name="civico_rit" value="'.$civico_rit.'" required><br />
                </div>
            </div>';
            }
            ?>
            <br />
        
	        <div style="margin-bottom:10px; text-align: center;">
                <button type="submit" name="invio" value="1">Pagina successiva</button>
            </div>
	    </form>

   </div>
   
   <div id="navbar" class="colonna">
    <?php require_once("menu_cliente.php");?>
   </div>
</div>


</body>
</html>