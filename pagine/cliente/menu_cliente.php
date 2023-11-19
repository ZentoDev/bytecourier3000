<ul id="menu">
    <li><a href="home_cliente.php">Home</a></li>
    <li><a href="profilo.php">Profilo</a></li>
    <li><a href="ordina_spedizione.php">Ordina spedizione</a></li>
    <li><a href="ordini_attesa.php">Ordini in attesa</a></li>
    <li><a href="ordini_pagamento.php">Ordini in attesa di pagamento</a></li>
    <?php 
        $LiOrd = openXML("../../dati/xml/ordini.xml")->documentElement->childNodes;
        $flagOrd = 0;
        for( $curs = 0; $curs < $LiOrd->length && $flagOrd == 0; $curs++ ){
            
            if( $LiOrd->item($curs)->getAttribute('stato') == 'modificato'  &&  $LiOrd->item($curs)->getAttribute('username') == $_SESSION['username'] ){  
                echo '<li><a href="ordini_modificati.php">Ordini modificati <span style="color:red">●</span></a></li>';
                $flagOrd = 1;
            }
        } 
    ?>
    <li><a href="ordini.php">Ordini in corso</a></li>
    <li><a href="storico_ordini.php">Storico ordini</a></li>
    <li><a href="domande.php">Domande</a></li>
    <li><a href="richiesta_crediti.php">Richiedi crediti</a></li>
    <li><a href="informazioni.php">Informazioni</a></li>
    <li><a href="catalogo.php">Tipologia spedizioni</a></li>
    <li><a href="faq.php">FAQ</a></li>
    <li><a href="../logout.php">Logout</a></li>
</ul>