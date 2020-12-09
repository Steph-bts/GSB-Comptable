<?php

/**
 * Vue Séléction du Visiteur et du Mois à Clôturer
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
<div class="row">
    <div class="col-md-6">
        <h3>Sélectionner un visiteur et un mois : </h3>
    </div>
    <div class="col-md-6">
        <form action="index.php?uc=validation&action=valider" 
              method="post" role="form">
            <div class="form-group">
                <label for="lstVisiteurs">Visiteurs : </label>
                <select id="lstVisiteurs" name="lstVisiteurs" class="form-control">
                    <?php
                    foreach ($lesVisiteurs as $unVisiteur) {
                        $id = $unVisiteur['id'];
                        $nomPrenom = $unVisiteur['nom'] . ' ' . $unVisiteur['prenom'];
                        if ($id == $visiteurASelectionner) {
                            ?>
                                <option selected value="<?php echo $id; ?>">
                            <?php echo $nomPrenom ; ?> </option>
                            <?php
                        } else {
                            ?>
                                <option value="<?php echo $id; ?>">
                            <?php echo $nomPrenom ; ?> </option>
                            <?php
                        }
                    }
                    ?>   
                </select>
            </div>
            <div class="form-group">
                <label for="lstMois">Mois : </label>
                <select id="lstMois" name="lstMois" class="form-control">
                    <?php
                    foreach ($lesMois as $unMois) {
                        $numAnnee = substr($unMois, 0, 4);
                        $numMois = substr($unMois, -2);
                        if ($unMois == $moisASelectionner) {
                            ?>
                            <option selected value="<?php echo $unMois ?>">
                                <?php echo $numMois . '/' . $numAnnee ?> </option>
                            <?php
                        } else {
                            ?>
                            <option value="<?php echo $unMois ?>">
                                <?php echo $numMois . '/' . $numAnnee ?> </option>
                            <?php
                        }
                    }
                    ?> 
                </select>
            </div>
            <input id="ok" type="submit"  value="Valider" class="btn btn-success" 
                   role="button">            
            <input id="annuler" type="reset"  value="Effacer" class="btn btn-danger" 
                   role="button">
        </form>
    </div>
</div>

