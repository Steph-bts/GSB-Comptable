<?php

/**
 * Vue Page d'erreur
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @author    Stéphanie Otto <contact@lencodage.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */

?>
<div class="alert alert-danger" role="alert">
    <h5><em>Ce n'est pas un échec - 
            Mais ça n'a pas marché</em>
    </h5><br>
    <?php
    foreach ($_REQUEST['erreurs'] as $erreur) {
        echo '<p>' . verifInput($erreur) . '</p>';
    }
    ?>        
</div>
