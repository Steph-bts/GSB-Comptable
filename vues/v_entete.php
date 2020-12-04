<?php
/**
 * Vue Entête
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
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta charset="UTF-8">
        <title>Intranet du Laboratoire Galaxy-Swiss Bourdin</title> 
        <meta name="description" content="Appli comptable GSB">
        <meta name="author" content="Stéphanie Otto">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="./styles/bootstrap/bootstrap.css" rel="stylesheet">
        <link href="./styles/style.css" rel="stylesheet">
        <script type="text/javascript" src="./includes/jsfunctions.inc.js"></script>
           
    </head>
    <body>
        <div class="container">
            <?php
            $uc = filter_input(INPUT_GET, 'uc', FILTER_SANITIZE_STRING);
            if($estConnecte) {
            ?>
            <div class="header">
                <div class="row vertical-align">
                    <div class="col-md-4">
                        <h1>
                            <img src="./images/logo.jpg" class="img-responsive" 
                                 alt="Laboratoire Galaxy-Swiss Bourdin" 
                                 title="Laboratoire Galaxy-Swiss Bourdin">
                        </h1>
                    </div>
                    <div class="col-md-8">
                        <ul class="nav nav-pills pull-right orange" role="tablist">
                            <li <?php if(!$uc || $uc == 'accueil') {?>
                                class="active"
                            <?php } ?>>
                                <a href="index.php">
                                    <span class="glyphicon glyphicon-home"></span>
                                     Accueil
                                </a>
                            </li>
                            <li <?php if($uc == 'cloture') {?>
                                class="active"
                            <?php } ?>>
                                <a href="index.php?uc=cloture&action=cloturer">
                                    <span class="glyphicon glyphicon-folder-close"></span>
                                     Clôture Automatique
                                </a>
                            </li>
                            <li <?php if($uc == 'validation') {?>
                                class="active"
                            <?php } ?>>
                                <a href="index.php?uc=validation&action=selectionVisiteurMois">
                                    <span class="glyphicon glyphicon-hand-right">
                                        
                                    </span>
                                     Validation
                                </a>
                            </li>
                            <li <?php if($uc == 'paiement') {?>
                                class="active"
                            <?php } ?>>
                                <a href="index.php?uc=paiement&action=suiviPaiement">
                                    <span class="glyphicon glyphicon-euro">
                                        
                                    </span>
                                     Paiement
                                </a>
                            </li>
                            <li <?php if($uc == 'deconnexion') {?>
                                class="active"
                            <?php } ?>>
                                <a href="index.php?uc=deconnexion&action=deconnecter">
                                    <span class="glyphicon glyphicon-log-out"></span>
                                     Déconnexion
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <?php
            } else {
            ?>
                <h1>
                    <img src="./images/logo.jpg" class="img-responsive" 
                         alt="Laboratoire Galaxy-Swiss Bourdin" 
                         title="Laboratoire Galaxy-Swiss Bourdin">
                </h1>
            <?php }


