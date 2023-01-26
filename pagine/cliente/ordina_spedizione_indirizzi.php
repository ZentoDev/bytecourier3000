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
    header('Location:ordina_spedizione_riepilogo.php');
    exit;
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
                <input type="number" name="civico_dest" value="<?php echo $civico_dest;?>" required><br />
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