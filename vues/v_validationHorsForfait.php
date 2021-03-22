<?php

/**
 * Vue frais hors forfait de la fiche à valider
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

<div class="col-md-12">
    <form method="post" 
          action="index.php?uc=validation&action=corrigerHorsForfait&idVisiteur=<?php echo $idVisiteur; ?>&leMois=<?php echo $leMois; ?>" 
          role="form">        
        <h3>Descriptif des éléments hors forfait</h3>
        <label for="justifs">Nbre de jutificatifs reçus =</label>
        <input type="number" name="justifs" step="1" min="0" value="<?php echo $nbJustificatifs; ?>">
        <table class="table table-bordered table-responsive">
            <thead>
                <tr>
                    <th class="date">Date</th>
                    <th class="libelle">Libellé</th>  
                    <th class="montant">Montant</th>  
                    <th class="action">&nbsp</th> 
                    <th class="action">&nbsp</th>

                </tr>
            </thead>  
            <tbody>
            <?php
            foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
                $libelle = verifInput($unFraisHorsForfait['libelle']);
                $date = $unFraisHorsForfait['date'];
                $montant = $unFraisHorsForfait['montant'];
                $id = $unFraisHorsForfait['id'];
                $estRefuseFrais = $pdo->estRefuse($id);
                ?>           
                <tr>
                    <td> <?php echo $date ?></td>
                    <td> <?php echo $libelle ?></td>
                    <td><?php if ($estRefuseFrais) {
                        echo $montant;
                        } else { ?>                                        
                        <input type="number" step="0.01" name="horsForfait[<?php echo $id; ?>]"
                               value="<?php echo $montant ?>">
                    </td>
                            <?php
                        }
                        ?>
                    <td><?php if ($estRefuseFrais) {?>
                        <p>Ce frais a été rejeté</p>
                        <?php
                        } else {
                            ?>
                        <a href="index.php?uc=validation&action=supprimerFrais&idVisiteur=<?php echo $idVisiteur; ?>&leMois=<?php echo $leMois; ?>&idFrais=<?php echo $id;?>"
                           onclick="return confirm
                               ('Voulez-vous vraiment refuser ce frais ?');">
                            Refuser ce frais</a>
                        </td>
                        <?php } ?>
                    <td><?php if ($estRefuseFrais) {?>
                        <p>Ce frais a été rejeté</p>
                        <?php
                        } else {
                            ?>
                        <a href="index.php?uc=validation&action=reporterFrais&idVisiteur=<?php echo $idVisiteur; ?>&leMois=<?php echo $leMois; ?>&idFrais=<?php echo $id;?>"
                           onclick="return confirm
                               ('Voulez-vous vraiment reporter ce frais ?');">
                            Report fiche mois suivant</a></td>
                            <?php
                        }
                        ?>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <button name="corrigerHorsForfait" class="btn btn-primary" type="submit" value="corrige">Corriger</button>
    </form>
</div>

