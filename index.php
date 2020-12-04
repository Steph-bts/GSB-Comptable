<?php
/**
 * Index du projet GSB
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

require_once 'includes/fct.inc.php';
require_once 'includes/class.pdogsb.inc.php';

session_start();

$pdo = PdoGsb::getPdoGsb();

$estConnecte = estConnecte();
require 'vues/v_entete.php';

$uc = filter_input(INPUT_GET, 'uc', FILTER_SANITIZE_STRING);

if($uc && !$estConnecte) {
    $uc = 'connexion';
} elseif(empty($uc)) {
    $uc = 'accueil';
}

switch($uc) {
    case 'connexion' : 
        include 'controleurs/c_connexion.php';
        break;
    case 'accueil' : 
        include 'controleurs/c_accueil.php';
        break;
    case 'cloture' :
        include 'controleurs/c_cloture.php';
        break;
    case 'validation' : 
        include 'controleurs/c_validation.php';
        break;
    case 'paiement' : 
        break;
    case 'deconnexion' : 
        break;
}
require 'vues/v_pied.php';