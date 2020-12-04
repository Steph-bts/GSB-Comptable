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
        break;
    default : 
        break;
}