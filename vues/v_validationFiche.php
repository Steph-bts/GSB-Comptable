<?php

/**
 * Vue Bouton pour validation de la Fiche
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Stéphanie Otto <contact@lencodage.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
*/

?>
<div>
    <form method="post" 
              action="index.php?uc=validation&action=succesValidation&idVisiteur=<?php echo $idVisiteur; ?>&leMois=<?php echo $leMois; ?>" 
              role="form">  
        <button name="valide" class="btn btn-success btnValide" type="submit" value="valide">
            Valider la fiche de Frais
            <br><br>
            <span class="glyphicon glyphicon-ok"></span>
        </button>
    </form>
</div>
