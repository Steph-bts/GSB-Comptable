<?php
/**
 * Controleur Connexion
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
if(!$uc) {
    $uc = 'demandeConnexion';
}

switch($action) {
    case 'demandeConnexion' :
        include 'vues/v_connexion.php';
        break;
    case 'valideConnexion' : 
         /**
         * Vérification que les mdp sont hachés. Sinon, hashage.
         */
        $motsDePasses = $pdo->getLesMotsDePasseComptables();

        foreach ($motsDePasses as $comptable) {
            $mot = $comptable['mdp'];
            $id = $comptable['id'];
            $algo = substr($mot, 0, 4);
            if($algo !== '$2y$') {
                $pdo->setMdpHashComptables($id,$mot);
            } 
        }
        
        $login = verifInput($_POST['login']);
        $mdp = verifInput($_POST['mdp']);

        $comptable = $pdo->getInfosComptable($login);

        if (!is_array($comptable)) {
            ajouterErreur('Login ou mot de passe incorrect');
            include 'vues/v_erreurs.php';
            include 'vues/v_connexion.php';
        } else {
            $mdpOk = password_verify($mdp, $comptable['mdp']);

            if($mdpOk) {
                $id = $comptable['id'];
                $nom = $comptable['nom'];
                $prenom = $comptable['prenom'];
                connecter($id, $nom, $prenom);
                header('Location: index.php');
            } else {
                ajouterErreur('Login ou mot de passe incorrect');
                include 'vues/v_erreurs.php';
                include 'vues/v_connexion.php';
            }
        }
    
        break;
    default : 
        include 'vues/v_connexion.php';
        break;
}