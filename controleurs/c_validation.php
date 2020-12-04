<?php
/**
 * Controleur Validation des Fiches de Frais
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
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

if(!empty($_POST['lstVisiteurs']) && !empty($_POST['lstMois'])) {
    $idVisiteur = $_POST['lstVisiteurs'];
    $leMois = $_POST['lstMois'];
}

switch($action) {
    case 'selectionVisiteurMois' : 
        // Pour remplir les listes déroulantes visiteurs et mois : 
       $lesVisiteurs = $pdo->getLesVisiteursAValider();
       if(!is_array($lesVisiteurs) || count($lesVisiteurs) === 0) {
           ajouterErreur("Il n'y a aucune fiche Visiteur à valider !");
           include 'vues/v_erreurs.php';
       } else {   
       /**
        * On récupère les mois à valider pour tous les visiteurs 
        * sélectionnés précédemment
        */
           foreach($lesVisiteurs as $unVisiteur) {
               $lesMois[] = $unVisiteur['mois'];
           }
       /**
        * Et, bien sûr, on supprime les doublons, c'est à dire les mois qui apparaissent
        * plusieurs fois, pour qu'il ne reste qu'un 'exemplaire' de chaque mois
        */
           $lesMois = array_unique($lesMois);
           $visiteurASelectionner = $lesVisiteurs[0];
           $moisASelectionner = $lesMois[0];
           $lesVisiteurs = unique_multidim_array($lesVisiteurs, 'id');
        }
        include 'vues/v_selectionVisiteurMois.php';
        break;
    case 'valider' : 
        if(!isset($idVisiteur) || !isset($leMois)) {
            $idVisiteur = filter_input(INPUT_GET, 'idVisiteur', FILTER_SANITIZE_STRING);
            $leMois = filter_input(INPUT_GET, 'leMois', FILTER_SANITIZE_STRING);
        }
        $numAnnee = substr($leMois,0,4);
        $numMois = substr($leMois,-2);
        $nomPrenom = $pdo->getNomPrenomVisiteur($idVisiteur);
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
        $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
        $etatFicheVisiteur = $pdo->getEtatFicheFrais($idVisiteur,$leMois);

        if(empty($etatFicheVisiteur)) {
            ajouterErreur("Il n'y a pas de fiche de frais pour ce visiteur pour le mois sélectionné");
            include 'vues/v_erreurs.php';
        } elseif($etatFicheVisiteur != 'CL') {
            ajouterErreur("La fiche de ce mois n'est pas à valider pour ce visiteur");
            include 'vues/v_erreurs.php';
        } else {
            $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $leMois);
            $montantForfait = $pdo->calculFraisForfait($idVisiteur, $leMois);
            $montantHorsForfait = $pdo->calculFraisHorsForfait($idVisiteur, $leMois);
            $libEtat = $lesInfosFicheFrais['libEtat'];
            $enCours = $montantForfait + $montantHorsForfait;
            $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
            $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
        }          
        if(!empty($_POST['corrige'])) {  
            $lesFrais = filter_input(
                INPUT_POST, 'lesFrais', FILTER_DEFAULT,
                FILTER_FORCE_ARRAY
            );
            $lesFraisHorsForfait = filter_input(
                INPUT_POST, 'horsForfait', FILTER_DEFAULT,
                FILTER_FORCE_ARRAY
            );
            $nbJustificatifs = filter_input(INPUT_POST, 'justifs', FILTER_SANITIZE_NUMBER_INT);

            if (lesQteFraisValides($lesFrais)) {
                $pdo->majFraisForfait($idVisiteur, $leMois, $lesFrais);
                $pdo->majFraisHorsForfait($idVisiteur, $leMois, $lesFraisHorsForfait);
                $pdo->majNbJustificatifs($idVisiteur, $leMois, $nbJustificatifs);
                $montantForfait = $pdo->calculFraisForfait($idVisiteur, $leMois);
                $montantHorsForfait = $pdo->calculFraisHorsForfait($idVisiteur, $leMois);
                $montantTotal = $montantForfait + $montantHorsForfait;
                header('Refresh:0 ; URL=index.php?uc=validation&action=valider&idVisiteur=' . $idVisiteur . '&leMois=' . $leMois);
            } else {
                ajouterErreur("Les quantités des frais forfait doivent être des nombres entiers");
                header('Refresh:5 ; URL=index.php?uc=validation&action=valider&idVisiteur=' . $idVisiteur . '&leMois=' . $leMois);
            }  
        }
        
        if(!empty($_POST['valide'])) {
            $lesFrais = filter_input(
                INPUT_POST, 'lesFrais', FILTER_DEFAULT,
                FILTER_FORCE_ARRAY
            );
            $lesFraisHorsForfait = filter_input(
                INPUT_POST, 'horsForfait', FILTER_DEFAULT,
                FILTER_FORCE_ARRAY
            );
            $nbJustificatifs = filter_input(INPUT_POST, 'justifs', FILTER_SANITIZE_NUMBER_INT);
            if (lesQteFraisValides($lesFrais)) {
                $pdo->majFraisForfait($idVisiteur, $leMois, $lesFrais);
                $pdo->majFraisHorsForfait($idVisiteur, $leMois, $lesFraisHorsForfait);
                $pdo->majNbJustificatifs($idVisiteur, $leMois, $nbJustificatifs);
                $montantForfait = $pdo->calculFraisForfait($idVisiteur, $leMois);
                $montantHorsForfait = $pdo->calculFraisHorsForfait($idVisiteur, $leMois);
                $montantTotal = $montantForfait + $montantHorsForfait;
                $pdo->validerFrais($idVisiteur, $leMois, $montantTotal);
                $pdo->majEtatFicheFrais($idVisiteur, $leMois, 'VA');
                
                header('Location:index.php?uc=validation&action=succesValidation&idVisiteur=' . $idVisiteur . '&leMois=' . $leMois);
            } else {
                ajouterErreur("Les quantités des frais forfait doivent être des nombres entiers");
                header('Refresh:5 ; URL=index.php?uc=validation&action=valider&idVisiteur=' . $idVisiteur . '&leMois=' . $leMois);
            }  
        }
        include 'vues/v_validation.php';
        break;
    case 'supprimerFrais' : 
        $idVisiteur = filter_input(INPUT_GET, 'idVisiteur', FILTER_SANITIZE_STRING);
        $leMois = filter_input(INPUT_GET, 'leMois', FILTER_SANITIZE_STRING);
        $idFrais = filter_input(INPUT_GET, 'idFrais', FILTER_SANITIZE_STRING);
        $pdo->refuserFraisHorsForfait($idFrais);
        header('Location:index.php?uc=validation&action=valider&idVisiteur=' . $idVisiteur . '&leMois=' . $leMois);
        include 'vues/v_validation.php';
        break;
    case 'reporterFrais' :
        $idVisiteur = filter_input(INPUT_GET, 'idVisiteur', FILTER_SANITIZE_STRING);
        $leMois = filter_input(INPUT_GET, 'leMois', FILTER_SANITIZE_STRING);
        $idFrais = filter_input(INPUT_GET, 'idFrais', FILTER_SANITIZE_STRING);
        $pdo->reporterFraisHorsForfait($idFrais,$leMois);
        header('Location:index.php?uc=validation&action=valider&idVisiteur=' . $idVisiteur . '&leMois=' . $leMois);
        include 'vues/v_validation.php';
        break;
    case 'succesValidation' :
        $idVisiteur = filter_input(INPUT_GET, 'idVisiteur', FILTER_SANITIZE_STRING);
        $leMois = filter_input(INPUT_GET, 'leMois', FILTER_SANITIZE_STRING);
        $numAnnee = substr($leMois,0,4);
        $numMois = substr($leMois,-2);
        $nomPrenom = $pdo->getNomPrenomVisiteur($idVisiteur);
        header('Refresh:5 ; URL=index.php?uc=validation&action=selectionVisiteurMois');
        include 'vues/v_succesValidation.php';
        break;
    default : 
        include 'vues/v_accueil.php';
}
