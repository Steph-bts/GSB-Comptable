<?php
/**
 * Fonctions pour l'application GSB
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    Stéphanie Otto <contact@lencodage.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

/**
 * Teste si un quelconque comptable est connecté
 *
 * @return vrai ou faux
 */
function estConnecte()
{
    return isset($_SESSION['idComptable']);
}

/**
 * Enregistre dans une variable session les infos d'un comptable
 *
 * @param String $idComptable ID du comptable
 * @param String $nom        Nom du comptable
 * @param String $prenom     Prénom du comptable
 *
 * @return null
 */
function connecter($idComptable, $nom, $prenom)
{
    $_SESSION['idComptable'] = $idComptable;
    $_SESSION['nom'] = $nom;
    $_SESSION['prenom'] = $prenom;
}

function verifMdp($idComptable, $mdp) {
    $
    
    $mdpOk = password_verify($mdp, $resultat['mdp']);
}

/**
 * Détruit la session active
 *
 * @return null
 */
function deconnecter()
{
    session_destroy();
}

/**
 * Transforme une date au format français jj/mm/aaaa vers le format anglais
 * aaaa-mm-jj
 *
 * @param String $maDate au format  jj/mm/aaaa
 *
 * @return Date au format anglais aaaa-mm-jj
 */
function dateFrancaisVersAnglais($maDate)
{
    @list($jour, $mois, $annee) = explode('/', $maDate);
    return date('Y-m-d', mktime(0, 0, 0, $mois, $jour, $annee));
}

/**
 * Transforme une date au format format anglais aaaa-mm-jj vers le format
 * français jj/mm/aaaa
 *
 * @param String $maDate au format  aaaa-mm-jj
 *
 * @return Date au format format français jj/mm/aaaa
 */
function dateAnglaisVersFrancais($maDate)
{
    @list($annee, $mois, $jour) = explode('-', $maDate);
    $date = $jour . '/' . $mois . '/' . $annee;
    return $date;
}

/**
 * Retourne le mois au format aaaamm selon le jour dans le mois
 *
 * @param String $date au format  jj/mm/aaaa
 *
 * @return String Mois au format aaaamm
 */
function getMois($date)
{
    @list($jour, $mois, $annee) = explode('/', $date);
    unset($jour);
    if (strlen($mois) == 1) {
        $mois = '0' . $mois;
    }
    return $annee . $mois;
}

/* gestion des erreurs */

/**
 * Indique si une valeur est un entier positif ou nul
 *
 * @param Integer $valeur Valeur
 *
 * @return Boolean vrai ou faux
 */
function estEntierPositif($valeur)
{
    return preg_match('/[^0-9]/', $valeur) == 0;
}

/**
 * Indique si un tableau de valeurs est constitué d'entiers positifs ou nuls
 *
 * @param Array $tabEntiers Un tableau d'entier
 *
 * @return Boolean vrai ou faux
 */
function estTableauEntiers($tabEntiers)
{
    $boolReturn = true;
    foreach ($tabEntiers as $unEntier) {
        if (!estEntierPositif($unEntier)) {
            $boolReturn = false;
        }
    }
    return $boolReturn;
}

/**
 * Vérifie si une date est inférieure d'un an à la date actuelle
 *
 * @param String $dateTestee Date à tester
 *
 * @return Boolean vrai ou faux
 */
function estDateDepassee($dateTestee)
{
    $dateActuelle = date('d/m/Y');
    @list($jour, $mois, $annee) = explode('/', $dateActuelle);
    $annee--;
    $anPasse = $annee . $mois . $jour;
    @list($jourTeste, $moisTeste, $anneeTeste) = explode('/', $dateTestee);
    return ($anneeTeste . $moisTeste . $jourTeste < $anPasse);
}

/**
 * Vérifie la validité du format d'une date française jj/mm/aaaa
 *
 * @param String $date Date à tester
 *
 * @return Boolean vrai ou faux
 */
function estDateValide($date)
{
    $tabDate = explode('/', $date);
    $dateOK = true;
    if (count($tabDate) != 3) {
        $dateOK = false;
    } else {
        if (!estTableauEntiers($tabDate)) {
            $dateOK = false;
        } else {
            if (!checkdate($tabDate[1], $tabDate[0], $tabDate[2])) {
                $dateOK = false;
            }
        }
    }
    return $dateOK;
}

/**
 * Vérifie que le tableau de frais ne contient que des valeurs numériques
 *
 * @param Array $lesFrais Tableau d'entier
 *
 * @return Boolean vrai ou faux
 */
function lesQteFraisValides($lesFrais)
{
    return estTableauEntiers($lesFrais);
}

/**
 * Vérifie la validité des trois arguments : la date, le libellé du frais
 * et le montant
 *
 * Des message d'erreurs sont ajoutés au tableau des erreurs
 *
 * @param String $dateFrais Date des frais
 * @param String $libelle   Libellé des frais
 * @param Float  $montant   Montant des frais
 *
 * @return null
 */
function valideInfosFrais($dateFrais, $libelle, $montant)
{
    if ($dateFrais == '') {
        ajouterErreur('Le champ date ne doit pas être vide');
    } else {
        if (!estDatevalide($dateFrais)) {
            ajouterErreur('Date invalide');
        } else {
            if (estDateDepassee($dateFrais)) {
                ajouterErreur(
                    "date d'enregistrement du frais dépassé, plus de 1 an"
                );
            }
        }
    }
    if ($libelle == '') {
        ajouterErreur('Le champ description ne peut pas être vide');
    }
    if ($montant == '') {
        ajouterErreur('Le champ montant ne peut pas être vide');
    } elseif (!is_numeric($montant)) {
        ajouterErreur('Le champ montant doit être numérique');
    }
}

/**
 * Ajoute le libellé d'une erreur au tableau des erreurs
 *
 * @param String $msg Libellé de l'erreur
 *
 * @return null
 */
function ajouterErreur($msg)
{
    if (!isset($_REQUEST['erreurs'])) {
        $_REQUEST['erreurs'] = array();
    }
    $_REQUEST['erreurs'][] = $msg;
}

/**
 * Retoune le nombre de lignes du tableau des erreurs
 *
 * @return Integer le nombre d'erreurs
 */
function nbErreurs()
{
    if (!isset($_REQUEST['erreurs'])) {
        return 0;
    } else {
        return count($_REQUEST['erreurs']);
    }
}

/**
 * Retourne le mois précédent, au format aaaamm
 * 
 * @param String $mois aaaamm
 * 
 * @return String $mois précédent aaaamm
 */
function getMoisPrecedent($mois) {
    $numAnnee = substr($mois, 0, 4);
    $numMois = substr($mois, 4, 2);
    $numMoisPrecedent = intval($numMois) - 1;
    $numMoisPrecedent = strval($numMoisPrecedent);
    if(strlen($numMoisPrecedent) == 1) {
        $numMoisPrecedent = '0' . $numMoisPrecedent;    
    }
    return $numAnnee . $numMoisPrecedent;
}

/**
 * Retourne un tableau contenant les 12 derniers mois
 * 
 * @return Array $les12Mois             les 12 derniers mois au format aaaamm
 */
function getLesDouzeDerniersMois() {
    $mois = getMois(date('d/m/Y'));
    $numAnnee = substr($mois, 0, 4);    
    $numMois = substr($mois, 4, 2);    
    $listeMois = array();
    for($i = 1; $i <= 12; $i++) {
        $numAnnee = intval($numAnnee);
        $numMois = intval($numMois);
        if(($numMois - 1) === 0) {
            $numMois = 12;
            $numAnnee = $numAnnee - 1;
        } else {
            $numMois = $numMois - 1;
        }
        $numMois = strval($numMois);
        $numAnnee = strval($numAnnee);
        if (strlen($numMois) == 1) {
            $listeMois[] = $numAnnee . '0' . $numMois;
        } else {
            $listeMois[] = $numAnnee . $numMois;
        }        
    }
    return $listeMois;
}
/* Fonction qui contrôle les entrées de l'utilisateur, et les "nettoie" si besoin */
function verifInput($var) {
    $var = trim($var);
    $var = stripslashes($var);
    $var = htmlspecialchars($var);
    return $var;
}
/**
 * Fonction qui prend en paramètre un mois au format aaaamm et qui retourne le
 * mois suivant au même format
 * 
 * @param type String         $mois
 * 
 * @return type String        $mois
 */
function getLeMoisSuivant($mois) {
    $numAnnee = substr($mois, 0, 4);    
    $numMois = substr($mois, 4, 2); 
    $numAnnee = intval($numAnnee);
    $numMois = intval($numMois);
    if($numMois === 12) {
        $numMois = 1;
        $numAnnee += 1;
    } else {
        $numMois += 1;
    }
    $numMois = strval($numMois);
    $numAnnee = strval($numAnnee);
    if(strlen($numMois === 1)) {
        return $numAnnee . '0' . $numMois;
    } else {
        return $numAnnee . $numMois;
    }
}

/**
 * Fonction qui parcours un tableau avec les fiches de frais des visiteurs, 
 * et qui extrait le montant d'une fiche pour un visiteur et un mois passé
 * en paramètres
 * 
 * @param array()       $ficheAPayer
 * @param String        $idVisiteur
 * @param String        $mois au format aaaamm
 * 
 * @return float
 */
function getLeMontantAPayer($ficheAPayer,$idVisiteur, $mois) {
    foreach($ficheAPayer as $fiche) {
        if(($fiche['id'] === $idVisiteur) && ($fiche['mois'] === $mois)) {
            return floatval($fiche['montant']);
        }
    }
}

/**
 * Fonction qui prend en paramètre un array multidimensionnel, et qui en 
 * supprime les doublons
 * 
 * @param Array          $array : tableau duquel on veut supp les doublons
 * @param String         $key : clé sur laquelle on veut que se fasse la
 *                          sélection
 * @return Array
 */
function unique_multidim_array($array, $key) {
    $temp_array = array();
    $i = 0;
    $key_array = array();
   
    foreach($array as $val) {
        if (!in_array($val[$key], $key_array)) {
            $key_array[$i] = $val[$key];
            $temp_array[$i] = $val;
        }
        $i++;
    }
    return $temp_array;
}

