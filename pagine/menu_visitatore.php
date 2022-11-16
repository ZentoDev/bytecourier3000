<ul id="menu">
    <?php
    /*dato che questo menu verrà utilizzato da pagine con path differente, i link dovranno adattarsi al posizionamento del file eseguito.
      In particolare i file in questione sono index.php, localizzato nella root, e i file relative alle pagine del visitatore, localizzate nella cartella pagine (root/pagine)*/
       if($_SERVER['PHP_SELF'] == '/localhtdocs/bytecourier3000/index.php')   
            echo 
            '<li><a href="index.php">Home</a></li>
            <li><a href="pagine/informazioni.php">Informazioni</a></li>
            <li><a href="pagine/catalogo.php">Tipologia spedizioni</a></li>
            <li><a href="pagine/faq.php">FAQ</a></li>
            <li><a href="pagine/login.php">Login / Sign up</a></li>'; 
        else 
            echo 
            '<li><a href="../index.php">Home</a></li>
            <li><a href="informazioni.php">Informazioni</a></li>
            <li><a href="catalogo.php">Tipologia spedizioni</a></li>
            <li><a href="faq.php">FAQ</a></li>
            <li><a href="login.php">Login / Sign up</a></li>';
        ?>
</ul>