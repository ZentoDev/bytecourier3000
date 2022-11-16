<ul id="menu">
    <?php 
    /*dato che questo menu verrà utilizzato da pagine con path differente, i link dovranno adattarsi al posizionamento del file eseguito.
      In particolare i file in questione sono index.php, localizzato nella root, e i file relative alle pagine del visitatore, localizzate nella cartella pagine (root/pagine)*/
      
      $pagine_cliente = array('/localhtdocs/bytecourier3000/pagine/cliente/profilo.php', 
                         '/localhtdocs/bytecourier3000/pagine/cliente/ordina_spedizione.php', 
                         '/localhtdocs/bytecourier3000/pagine/cliente/domande.php', 
                         '/localhtdocs/bytecourier3000/pagine/cliente/richiesta_crediti_cliente.php'); 

      if($_SERVER['PHP_SELF'] == '/localhtdocs/bytecourier3000/index.php')  
            echo 
            '<li><a href="index.php">Home</a></li>
            <li><a href="pagine/cliente/profilo.php">Profilo</a></li>
            <li><a href="pagine/cliente/ordina_spedizione.php">Ordina spedizione</a></li>
            <li><a href="pagine/cliente/domande.php">Domande</a></li>
            <li><a href="pagine/cliente/richiesta_crediti_cliente.php">Richiedi crediti</a></li>
            <li><a href="pagine/informazioni.php">Informazioni</a></li>
            <li><a href="pagine/catalogo.php">Tipologia spedizioni</a></li>
            <li><a href="pagine/faq.php">FAQ</a></li>
            <li><a href="pagine/logout.php">Logout</a></li>'; 

        else if( in_array($_SERVER['PHP_SELF'], $pagine_cliente) )
            echo 
            '<li><a href="../../index.php">Home</a></li>
            <li><a href="profilo.php">Profilo</a></li>
            <li><a href="ordina_spedizione.php">Ordina spedizione</a></li>
            <li><a href="domande.php">Domande</a></li>
            <li><a href="richiesta_crediti_cliente.php">Richiedi crediti</a></li>
            <li><a href="../informazioni.php">Informazioni</a></li>
            <li><a href="../catalogo.php">Tipologia spedizioni</a></li>
            <li><a href="../faq.php">FAQ</a></li>
            <li><a href="../logout.php">Logout</a></li>'; 

        else  
            echo 
            '<li><a href="../index.php">Home</a></li>
            <li><a href="cliente/profilo.php">Profilo</a></li>
            <li><a href="cliente/ordina_spedizione.php">Ordina spedizione</a></li>
            <li><a href="cliente/domande.php">Domande</a></li>
            <li><a href="cliente/richiesta_crediti_cliente.php">Richiedi crediti</a></li>
            <li><a href="informazioni.php">Informazioni</a></li>
            <li><a href="catalogo.php">Tipologia spedizioni</a></li>
            <li><a href="faq.php">FAQ</a></li>
            <li><a href="logout.php">Logout</a></li>'; 
        ?>
</ul>
