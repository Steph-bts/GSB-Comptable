<?php

/**
 * Controleur Clôture Automatique
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

/**
 * On identifie le mois précédent (par rapport à la date à laquelle on se
 * connecte), pour voir s'il y a des fiches qui sont à clôturer
 */
$mois = getMois(date('d/m/Y'));
$moisPrecedent = getMoisPrecedent($mois);
$visiteurs = $pdo->getVisiteursNonClos($moisPrecedent);
$nbreFichesACloturer = count($visiteurs);

$numMoisPrec = substr($moisPrecedent, 4, 2);
$numAnnee = substr($moisPrecedent, 0, 4);

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

switch ($action) {
case 'cloturer':
    // s'il n'y a pas de fiche en attente de clôture => msq erreur approprié
    if ($nbreFichesACloturer === 0) {
        ajouterErreur("Il n'y a aucune fiche Visiteurs en attente de clôture ! ");
        include 'vues/v_erreurs.php';
    } else {
        include 'vues/v_clotureAutomatique.php';
    }
    break;
case 'succesCloture':
    // clôture des fiches du mois précédent + création de la nouvelle fiche
    $nbreVisiteursClos = count($visiteurs);
    foreach ($visiteurs as $visiteur) {
        $pdo->majEtatFicheFrais($visiteur['idvisiteur'], $moisPrecedent, 'CL');
    }
    include 'vues/v_succesCloture.php';
    break;
default:
    include 'vues/v_accueil.php';
}
