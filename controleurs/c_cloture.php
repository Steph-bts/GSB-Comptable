<?php
/**
 * Controleur Clôture Automatique
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
$mois = getMois(date('d/m/Y'));
$moisPrecedent = getMoisPrecedent($mois); 
$visiteurs = $pdo->getVisiteursNonClos($moisPrecedent);
$nbreFichesACloturer = count($visiteurs);

$numMoisPrec = substr($moisPrecedent,4,2);
$numAnnee = substr($moisPrecedent,0,4); 


$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

switch($action) {
    case 'cloturer' : 
        if($nbreFichesACloturer === 0) {
            ajouterErreur("Il n'y a aucune fiche Visiteurs en attente de clôture ! ");
            include 'vues/v_erreurs.php';
        } else {
            include 'vues/v_clotureAutomatique.php';
        }        
        break;
    case 'succesCloture' :    
        $nbreVisiteursClos = count($visiteurs);

        foreach($visiteurs as $visiteur) { 
            $pdo->majEtatFicheFrais($visiteur['idvisiteur'], $moisPrecedent ,'CL');
            $pdo->creeNouvellesLignesFrais($visiteur['idvisiteur'], $mois);
        }
        include 'vues/v_succesCloture.php';
        break;
    default :
        include 'vues/v_accueil.php';
}