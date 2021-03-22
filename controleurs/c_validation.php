<?php

/**
 * Controleur Validation des Fiches de Frais
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

/**
 * récupération de l'id visiteur, soit via la sélection dans les listes
 * déroulantes, soit via l'URL
 */
if (!empty($_POST['lstVisiteurs']) && !empty($_POST['lstMois'])) {
    $idVisiteur = $_POST['lstVisiteurs'];
    $leMois = $_POST['lstMois'];
} else {
    $idVisiteur = filter_input(INPUT_GET, 'idVisiteur', FILTER_SANITIZE_STRING);
    $leMois = filter_input(INPUT_GET, 'leMois', FILTER_SANITIZE_STRING);
}

/**
 *  Si on a en effet un visiteur et un mois, formatage de numAnnee et numMois
 *  + récupération dans la bdd des noms et prénoms du visiteur concerné
 */
    
if (isset($idVisiteur) && isset($leMois)) {
    $numAnnee = substr($leMois, 0, 4);
    $numMois = substr($leMois, -2);
    $nomPrenom = $pdo->getNomPrenomVisiteur($idVisiteur);
}

switch ($action) {
case 'selectionVisiteurMois':
    // Pour remplir les listes déroulantes visiteurs et mois :
    $lesVisiteurs = $pdo->getLesVisiteursAValider();
    if (!is_array($lesVisiteurs) || count($lesVisiteurs) === 0) {
        ajouterErreur("Il n'y a aucune fiche Visiteur à valider !");
        include 'vues/v_erreurs.php';
    } else {
    /**
    * On récupère les mois à valider pour tous les visiteurs
    * sélectionnés précédemment
    */
        foreach ($lesVisiteurs as $unVisiteur) {
            $lesMois[] = $unVisiteur['mois'];
        }
   /**
    * Et, bien sûr, on supprime les doublons, c'est à dire les mois qui apparaissent
    * plusieurs fois, pour qu'il ne reste qu'un 'exemplaire' de chaque mois
    * et de chaque visiteur
    */
        $lesMois = array_unique($lesMois);
        $visiteurASelectionner = $lesVisiteurs[0];
        $moisASelectionner = $lesMois[0];
        $lesVisiteurs = unique_multidim_array($lesVisiteurs, 'id');
    }
    include 'vues/v_selectionVisiteurMois.php';
    break;
case 'valider':
    // Récupération des informations pour remplissage de la fiche de frais
    $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $leMois);
    $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $leMois);
    $etatFicheVisiteur = $pdo->getEtatFicheFrais($idVisiteur, $leMois);

    if (empty($etatFicheVisiteur)) {
        ajouterErreur("Il n'y a pas de fiche de frais pour ce visiteur pour le mois sélectionné");
        include 'vues/v_erreurs.php';
    } elseif ($etatFicheVisiteur != 'CL') {
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

    include 'vues/v_validationEntete.php';
    include 'vues/v_validationForfait.php';
    include 'vues/v_validationHorsForfait.php';
    include 'vues/v_validationFiche.php';
    break;
case 'corrigerForfait':
    /* pour correction des frais forfait, on récupère l'id du frais dans
     * l'URL, et on met à jour la bdd avant de revenir à la fiche */
    $lesFrais = filter_input(
        INPUT_POST,
        'lesFrais',
        FILTER_DEFAULT,
        FILTER_FORCE_ARRAY
    );
    $pdo->majFraisForfait($idVisiteur, $leMois, $lesFrais);
    header('Location:index.php?uc=validation&action=valider&idVisiteur=' . $idVisiteur . '&leMois=' . $leMois);
    break;
case 'corrigerHorsForfait':
    /* pour correction des frais hors forfait, on récupère l'id du frais dans
     * l'URL, et on met à jour la bdd avant de revenir à la fiche */
    $lesFraisHorsForfait = filter_input(
        INPUT_POST,
        'horsForfait',
        FILTER_DEFAULT,
        FILTER_FORCE_ARRAY
    );
    $nbJustificatifs = filter_input(INPUT_POST, 'justifs', FILTER_SANITIZE_NUMBER_INT);
    $pdo->majFraisHorsForfait($idVisiteur, $leMois, $lesFraisHorsForfait);
    $pdo->majNbJustificatifs($idVisiteur, $leMois, $nbJustificatifs);
    header('Location:index.php?uc=validation&action=valider&idVisiteur=' . $idVisiteur . '&leMois=' . $leMois);
    break;
case 'supprimerFrais':
    // idem que ci-dessus, simpelement cette fois on ajoute REFUSE au libellé
    $idFrais = filter_input(INPUT_GET, 'idFrais', FILTER_SANITIZE_STRING);
    $pdo->refuserFraisHorsForfait($idFrais);
    header('Location:index.php?uc=validation&action=valider&idVisiteur=' . $idVisiteur . '&leMois=' . $leMois);
    break;
case 'reporterFrais':
    /* cette fois le frais disparaît de la fiche en cours, et se reporte le
     * mois suivant */
    $idFrais = filter_input(INPUT_GET, 'idFrais', FILTER_SANITIZE_STRING);
    $pdo->reporterFraisHorsForfait($idFrais, $leMois);
    header('Location:index.php?uc=validation&action=valider&idVisiteur=' . $idVisiteur . '&leMois=' . $leMois);
    break;
case 'succesValidation':
    /* maintenant que tout est OK, on valide, donc :
 * - on met à jour le montant validé dans la fiche de frais
 * - on passe l'état de la fiche de frais à VA pour validée
     */
    $montantForfait = $pdo->calculFraisForfait($idVisiteur, $leMois);
    $montantHorsForfait = $pdo->calculFraisHorsForfait($idVisiteur, $leMois);
    $enCours = $montantForfait + $montantHorsForfait;
    $pdo->validerFrais($idVisiteur, $leMois, $enCours);
    $pdo->majEtatFicheFrais($idVisiteur, $leMois, 'VA');
    include 'vues/v_succesValidation.php';
    header('Refresh:5 ; URL=index.php?uc=validation&action=selectionVisiteurMois');
    break;
default:
    include 'vues/v_accueil.php';
}
