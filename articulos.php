<?php
    require 'includes/app.php';
    incluirTemplate('header'); 
?>

    <main class="contenedor seccion">

        <h2>Artículos que ofrecemos con envío a domicilio</h2>

            <?php
                $limite = 20;
                include 'includes/templates/articulos.php';
            ?>



    </main>

    <?php incluirTemplate('footer');?>