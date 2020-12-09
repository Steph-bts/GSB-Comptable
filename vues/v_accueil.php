<?php
/**
 * Vue page d'Accueil
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
<div id="accueil">
    <h2>
        Gestion des frais<small> - Comptable : 
            <?php 
            echo $_SESSION['prenom'] . ' ' . $_SESSION['nom']
            ?></small>
    </h2><br>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <span class="glyphicon glyphicon-bookmark"></span>
                    Navigation
                </h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-4 col-md-4">
                        <a href="index.php?uc=cloture&action=cloturer"
                            class="btn btn-secondary btn-lg" role="button">
                            <span class="glyphicon glyphicon-folder-close"></span>
                            <br><p class="overflow-visible"><br>Clôture automatique </p>
                        </a>
                    </div>   
                    <div class="col-xs-4 col-md-4">
                        <a href="index.php?uc=validation&action=selectionVisiteurMois"
                            class="btn btn-primary btn-lg" role="button">
                            <span class="glyphicon glyphicon-hand-right"></span>
                            <br><p class="overflow-visible"><br>Validation<br></p>
                        </a>
                    </div>
                    <div class="col-xs-4 col-md-4">
                        <a href="index.php?uc=paiement&action=suiviPaiement"
                            class="btn btn-success btn-lg" role="button">
                            <span class="glyphicon glyphicon-euro"></span>
                            <br><p class="overflow-visible"><br>Paiement<br></p>
                        </a>                                    
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<div class="alert alert-warning" role="alert">
    <h4>Il y a : </h4>
    <h4> - <?php echo ($nbreFichesACloturer > 0)? $nbreFichesACloturer : 'aucune' ;?> fiche(s) à clôturer</h4>
    <h4> -  <?php echo ($nbreFichesAValider > 0)? $nbreFichesAValider : 'aucune' ?> fiche(s) à valider</h4>
    <h4> -  <?php echo ($nbreFichesAPayer > 0)? $nbreFichesAPayer : 'aucune' ?> fiche(s) à payer</h4>
</div>

