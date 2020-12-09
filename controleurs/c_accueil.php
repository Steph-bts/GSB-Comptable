<?php
/**
 * Controleur Accueil
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
 * Si l'utilisateur est connecté = on récupère le nombre de fiches à clôturer,
 * valider, et payer, pour information
 */
if($estConnecte) {
    $fichesARembourser = $pdo->getLesFichesVisiteursAPayer();
    $nbreFichesAPayer = count($fichesARembourser);

    $fichesAValider = $pdo->getLesVisiteursAValider();
    $nbreFichesAValider = count($fichesAValider);

    $mois = getMois(date('d/m/Y'));
    $moisPrecedent = getMoisPrecedent($mois); 
    $fichesACloturer = $pdo->getVisiteursNonClos($moisPrecedent);
    $nbreFichesACloturer = count($fichesACloturer);
    include 'vues/v_accueil.php';
} else {
    include 'vues/v_connexion.php';
}
