<?php
/**
 * Controleur Paiement des Fiches de Frais
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
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

switch($action) {
    case 'suiviPaiement' :
        // correction des éléments du jeu test (montant valide + nb justificatifs)
        $pdo->correctionAutomatiqueMontantValide();
        $pdo->correctionAutomatiqueNbJustificatifs();

        $fichesARembourser = $pdo->getLesFichesVisiteursAPayer();
        
        // s'il n'y a pas de fiche à payer : envoi msq erreur
        if(count($fichesARembourser) === 0) {
            ajouterErreur("Il n'y a aucune fiche visiteur en attente de paiement !");
            include 'vues/v_erreurs.php';
        } else {
            include 'vues/v_suivrePaiement.php';
        }        
        break;
    case 'voirFiche' : 
        // récupération dans la bb de tous les éléments de la fiche de frais
        $idVisiteur = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
        $leMois = filter_input(INPUT_GET, 'mois', FILTER_SANITIZE_STRING);

        $numAnnee = substr($leMois,0,4);
        $numMois = substr($leMois,-2);

        $nomPrenom = $pdo->getNomPrenomVisiteur($idVisiteur);

        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
        $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
        $etatFicheVisiteur = $pdo->getEtatFicheFrais($idVisiteur,$leMois);

        $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
        $montantForfait = $pdo->calculFraisForfait($idVisiteur, $leMois);
        $montantHorsForfait = $pdo->calculFraisHorsForfait($idVisiteur, $leMois);
        $montantValide = $lesInfosFicheFrais['montantValide'];
        $libEtat = $lesInfosFicheFrais['libEtat'];
        $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
        $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
        include 'vues/v_voirFiche.php';
        break;
    case 'payer' :
        // récupération des cases cochées => maj bdd des fiches payées
        $fichesARembourser = $pdo->getLesFichesVisiteursAPayer();
        $montantVirement = 0; 
        foreach($_POST['aPayer'] as $virement) {
            $posId = stripos($virement, '&');
            $idVisiteur = substr($virement, 0, $posId);
            $moisPaye = substr($virement, $posId + 1);        
            $montantAPayer = getLeMontantAPayer($fichesARembourser, $idVisiteur, $moisPaye);
            $montantVirement += $montantAPayer;
            $pdo->majEtatFicheFrais($idVisiteur, $moisPaye, 'RB');
        }
        include 'vues/v_succesPaiement.php';
        break;
    default : 
        include 'vues/v_accueil.php';
}