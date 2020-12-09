<?php

/**
 * Vue Succès de Paiement
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

            
            <div class="alert alert-success" role="alert">
                <h4>
                    Les virements sélectionnés
                    pour un montant total de 
                    <?php echo number_format($montantVirement, 2, ',', ' '); ?> € 
                    ont bien été transmis à la banque !
                </h4>
            </div>
