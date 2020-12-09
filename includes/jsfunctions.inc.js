/**
 * Fonction Javascript Paiement des Frais
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
 
/**
 * Fonction qui permet, à la coche ou décoche d'une case, de tout cocher/
 * tout décocher
 * 
 * @return null
 */
function cocher_tout() {

    var chb = document.getElementById('monform').getElementsByTagName("input");

    if(chb.length>1){ // s'il y a d'autres input que "Sélectionner tout" et "Supprimer"

	// si la case "Sélectionner tout" est cochée

	if(document.getElementById('tout').checked==true){

            for(var i = 0; i < chb.length; i++){

		if(chb[i].name.substr(0,6) == "aPayer"){
                    chb[i].checked=true;
                } // si le name du checkbox commence par "aPayer"
            }
	}
	else{  // si la case "Sélectionner tout" est décochée
            for(var i = 0; i< chb.length; i++){
		if(chb[i].name.substr(0,6)=="aPayer"){
                    chb[i].checked=false;
                }
            }
	}
    } else{ // il n'y a pas de checkbox 
	return;

    }	
}



