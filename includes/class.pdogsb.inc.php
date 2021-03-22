<?php

/**
 * Classe d'accès aux données.
 *
 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO
 * $monPdoGsb qui contiendra l'unique instance de la classe
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL - CNED <jgil@ac-nice.fr>
 * @author    Stéphanie Otto -  <contact@lencodage.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   Release: 1.0
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

class PdoGsb
{
    // Adresse du serveur : localhost en local,
    private static $serveur = 'mysql:host=localhost';
    
    // Nom de la base de données
    private static $bdd = 'dbname=gsb_frais';
    
    // Nom de l'id pour accéder à la bdd
    private static $user = 'root';
    
    //  Mdp correspondant
    private static $mdp = '';
    
    // Curseur qui sera sollicité dans la classe
    private static $monPdo;
    
    // Curseur GSB
    private static $monPdoGsb = null;
    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    private function __construct()
    {
        PdoGsb::$monPdo = new PDO(
            PdoGsb::$serveur . ';' . PdoGsb::$bdd,
            PdoGsb::$user,
            PdoGsb::$mdp
        );
        PdoGsb::$monPdo->query('SET CHARACTER SET utf8');
    }

    /**
     * Méthode destructeur appelée dès qu'il n'y a plus de référence sur un
     * objet donné, ou dans n'importe quel ordre pendant la séquence d'arrêt.
     */
    public function __destruct()
    {
        PdoGsb::$monPdo = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe
     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
     *
     * @return l'unique objet de la classe PdoGsb
     */
    public static function getPdoGsb()
    {
        if (PdoGsb::$monPdoGsb == null) {
            PdoGsb::$monPdoGsb = new PdoGsb();
        }
        return PdoGsb::$monPdoGsb;
    }

    /**
     * Retourne les informations d'un comptable
     *
     * @param String $login Login du comptable
     *
     * @return l'id, le nom, le prénom et le mdp sous la forme d'un tableau
     * associatif
     */
    public function getInfosComptable($login)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT gsb_comptable.id AS id, gsb_comptable.nom AS nom, '
            . 'gsb_comptable.prenom AS prenom, gsb_comptable.mdp AS mdp '
            . 'FROM gsb_comptable '
            . 'WHERE gsb_comptable.login = :unLogin'
        );
        
        $requetePrepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetch();
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * hors forfait concernées par les deux arguments.
     * La boucle foreach ne peut être utilisée ici car on procède
     * à une modification de la structure itérée - transformation du champ date-
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return tous les champs des lignes de frais hors forfait sous la forme
     * d'un tableau associatif
     */
    public function getLesFraisHorsForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT * FROM gsb_lignefraishorsforfait '
            . 'WHERE gsb_lignefraishorsforfait.idvisiteur = :unIdVisiteur '
            . 'AND gsb_lignefraishorsforfait.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesLignes = $requetePrepare->fetchAll();
        for ($i = 0; $i < count($lesLignes); $i++) {
            $date = $lesLignes[$i]['date'];
            $lesLignes[$i]['date'] = dateAnglaisVersFrancais($date);
        }
        return $lesLignes;
    }

    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return le nombre entier de justificatifs
     */
    public function getNbjustificatifs($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT gsb_fichefrais.nbjustificatifs as nb FROM gsb_fichefrais '
            . 'WHERE gsb_fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND gsb_fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne['nb'];
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * au forfait concernées par les deux arguments
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return l'id, le libelle et la quantité sous la forme d'un tableau
     * associatif
     */
    public function getLesFraisForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT gsb_fraisforfait.id as idfrais, '
            . 'gsb_fraisforfait.libelle as libelle, '
            . 'gsb_lignefraisforfait.quantite as quantite '
            . 'FROM gsb_lignefraisforfait '
            . 'INNER JOIN gsb_fraisforfait '
            . 'ON gsb_fraisforfait.id = gsb_lignefraisforfait.idfraisforfait '
            . 'WHERE gsb_lignefraisforfait.idvisiteur = :unIdVisiteur '
            . 'AND gsb_lignefraisforfait.mois = :unMois '
            . 'ORDER BY gsb_lignefraisforfait.idfraisforfait'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Retourne tous les id de la table FraisForfait
     *
     * @return un tableau associatif
     */
    public function getLesIdFrais()
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT gsb_fraisforfait.id as idfrais '
            . 'FROM gsb_fraisforfait ORDER BY gsb_fraisforfait.id'
        );
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Met à jour la table ligneFraisForfait
     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param Array  $lesFrais   tableau associatif de clé idFrais et
     *                           de valeur la quantité pour ce frais
     *
     * @return null
     */
    public function majFraisForfait($idVisiteur, $mois, $lesFrais)
    {
        $lesCles = array_keys($lesFrais);
        foreach ($lesCles as $unIdFrais) {
            $qte = $lesFrais[$unIdFrais];
            $requetePrepare = PdoGSB::$monPdo->prepare(
                'UPDATE gsb_lignefraisforfait '
                . 'SET gsb_lignefraisforfait.quantite = :uneQte '
                . 'WHERE gsb_lignefraisforfait.idvisiteur = :unIdVisiteur '
                . 'AND gsb_lignefraisforfait.mois = :unMois '
                . 'AND gsb_lignefraisforfait.idfraisforfait = :idFrais'
            );
            $requetePrepare->bindParam(':uneQte', $qte, PDO::PARAM_INT);
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(':idFrais', $unIdFrais, PDO::PARAM_STR);
            $requetePrepare->execute();
        }
    }

    /**
     * Met à jour le nombre de justificatifs de la table ficheFrais
     * pour le mois et le visiteur concerné
     *
     * @param String  $idVisiteur      ID du visiteur
     * @param String  $mois            Mois sous la forme aaaamm
     * @param Integer $nbJustificatifs Nombre de justificatifs
     *
     * @return null
     */
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'UPDATE gsb_fichefrais '
            . 'SET nbjustificatifs = :unNbJustificatifs '
            . 'WHERE gsb_fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND gsb_fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(
            ':unNbJustificatifs',
            $nbJustificatifs,
            PDO::PARAM_INT
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return vrai ou faux
     */
    public function estPremierFraisMois($idVisiteur, $mois)
    {
        $boolReturn = false;
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT gsb_fichefrais.mois FROM gsb_fichefrais '
            . 'WHERE gsb_fichefrais.mois = :unMois '
            . 'AND gsb_fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        if (!$requetePrepare->fetch()) {
            $boolReturn = true;
        }
        return $boolReturn;
    }

    /**
     * Retourne le dernier mois en cours d'un visiteur
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idVisiteur)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT MAX(mois) as dernierMois '
            . 'FROM gsb_fichefrais '
            . 'WHERE gsb_fichefrais.idvisiteur = :unIdVisiteur'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $dernierMois = $laLigne['dernierMois'];
        return $dernierMois;
    }

    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait
     * pour un visiteur et un mois donnés
     *
     * Récupère le dernier mois en cours de traitement, met à 'CL' son champs
     * idEtat, crée une nouvelle fiche de frais avec un idEtat à 'CR' et crée
     * les lignes de frais forfait de quantités nulles
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return null
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois)
    {
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
        if ($laDerniereFiche['idEtat'] == 'CR') {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
        }
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'INSERT INTO gsb_fichefrais (idvisiteur,mois,nbjustificatifs,'
            . 'montantvalide,datemodif,idetat) '
            . "VALUES (:unIdVisiteur,:unMois,0,0,now(),'CR')"
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $unIdFrais) {
            $requetePrepare = PdoGsb::$monPdo->prepare(
                'INSERT INTO gsb_lignefraisforfait (idvisiteur,mois,'
                . 'idfraisforfait,quantite) '
                . 'VALUES(:unIdVisiteur, :unMois, :idFrais, 0)'
            );
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(
                ':idFrais',
                $unIdFrais['idfrais'],
                PDO::PARAM_STR
            );
            $requetePrepare->execute();
        }
    }
  
    /**
     * Supprime le frais hors forfait dont l'id est passé en argument
     *
     * @param String $idFrais ID du frais
     *
     * @return null
     */
    public function supprimerFraisHorsForfait($idFrais)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'DELETE FROM gsb_lignefraishorsforfait '
            . 'WHERE gsb_lignefraishorsforfait.id = :unIdFrais'
        );
        $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
    }

    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais
     *
     * @param String $idVisiteur ID du visiteur
     *
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     */
    public function getLesMoisDisponibles($idVisiteur)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT gsb_fichefrais.mois AS mois FROM gsb_fichefrais '
            . 'WHERE gsb_fichefrais.idvisiteur = :unIdVisiteur '
            . 'ORDER BY gsb_fichefrais.mois desc'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesMois = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }
        return $lesMois;
    }

    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un
     * mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
     */
    public function getLesInfosFicheFrais($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT gsb_fichefrais.idetat as idEtat, '
            . 'gsb_fichefrais.datemodif as dateModif,'
            . 'gsb_fichefrais.nbjustificatifs as nbJustificatifs, '
            . 'gsb_fichefrais.montantvalide as montantValide, '
            . 'gsb_etat.libelle as libEtat '
            . 'FROM gsb_fichefrais '
            . 'INNER JOIN gsb_etat ON gsb_fichefrais.idetat = gsb_etat.id '
            . 'WHERE gsb_fichefrais.idvisiteur = :unIdVisiteur '
            . 'AND gsb_fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne;
    }

    /**
     * Modifie l'état et la date de modification d'une fiche de frais.
     * Modifie le champ idEtat et met la date de modif à aujourd'hui.
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $etat       Nouvel état de la fiche de frais
     *
     * @return null
     */
    public function majEtatFicheFrais($idVisiteur, $mois, $etat)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            "UPDATE gsb_fichefrais "
            . "SET idetat = :unEtat, datemodif = now() "
            . "WHERE gsb_fichefrais.idvisiteur = :unIdVisiteur "
            . "AND gsb_fichefrais.mois = :unMois"
        );
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }
    
    /**
     * Fonction qui retourne un tableau avec tous les visiteurs dont le mois
     * passé en paramètre n'est pas encore clôturé
     *
     * @param String $mois            Mois sous la forme aaaamm
     *
     * @return un tableau avec les id de tous les visiteurs concernés
     */
    public function getVisiteursNonClos($mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            "SELECT idvisiteur FROM gsb_fichefrais WHERE idetat = 'CR' "
                . "AND mois = :unMois"
        );
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }
    
    /**
     * Fonction qui retourne le nom et le prénom d'un visiteur selon l'id donné
     *
     * @param String $id        l'id du visiteur
     *
     * @return Array     nom et prénom du visiteur avec l'id sélectionnée
     */
    public function getNomPrenomVisiteur($idVisiteur)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT nom, prenom "
                . "FROM gsb_visiteur "
                . "WHERE id= :idVisiteur"
        );
        $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }
    
    /**
     * Fonction qui retourne un tableau de tous les visiteurs
     *
     * @return un tableau associatif
     * avec les id, nom et prenom de tous les visiteurs
     */
    public function getTousLesVisiteurs()
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            "SELECT id, nom, prenom FROM gsb_visiteur ORDER BY nom ASC"
        );
        $requetePrepare->execute();
        $lesVisiteurs = array();
        while ($laLigne = $requetePrepare->fetch()) {
            $lesVisiteurs[] = array(
                    $laLigne['id'] => $laLigne['nom'] . ' ' . $laLigne['prenom'],
            );
        }
        return $lesVisiteurs;
    }
    
    /**
     * Fonction qui retourne l'état d'une fiche de frais d'un certain mois pour
     * un certain visiteur
     *
     * @param String $idVisiteur        id du visiteur
     * @param String $mois              mois au format aaaamm
     *
     * @return String         'CL' ou 'CR' ou 'VA' ou 'RB' correspondant à
     * l'étape de traitement de la fiche de frais, ne retourne rien s'il n'existe
     * pas de fiches pour ce visiteurs ce mois
     */
    public function getEtatFicheFrais($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT idetat "
                . "FROM gsb_fichefrais "
                . "WHERE idvisiteur = :unVisiteur "
                . "AND mois = :unMois"
        );
        $requetePrepare->bindParam(':unVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $etat = $requetePrepare->fetch();
        return $etat['idetat'];
    }
    
    /**
     * Retourne le montant des frais forfait en cours
     *
     * @param String $idVisiteur        id du visiteur
     * @param String $mois              mois au format aaaamm
     *
     * @return Float          le calcul des frais forfait pour le mois précisé
     * et pour le visiteur concerné
     *
     */
    public function calculFraisForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT montant, quantite "
                . "FROM gsb_lignefraisforfait "
                . "JOIN gsb_fraisforfait "
                . "ON gsb_lignefraisforfait.idfraisforfait = gsb_fraisforfait.id "
                . "WHERE gsb_lignefraisforfait.idvisiteur = :unVisiteur "
                . "AND gsb_lignefraisforfait.mois = :unMois"
        );
        $requetePrepare->bindParam(':unVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $totalForfait = 0.00;
        while ($donnees = $requetePrepare->fetch()) {
            $totalForfait = $totalForfait + floatval($donnees['montant']) * floatval($donnees['quantite']);
        }
        return $totalForfait;
    }
    
    /**
     * Retourne le total des frais hors forfait pour un visiteur
     *
     * @param String $idVisiteur        id du visiteur
     * @param String $mois              mois au format aaaamm
     *
     * @return Float          le calcul des frais forfait pour le mois précisé
     * et pour le visiteur concerné
     */
    public function calculFraisHorsForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT id, montant "
                . "FROM gsb_lignefraishorsforfait "
                . "WHERE idvisiteur = :unVisiteur "
                . "AND mois = :unMois"
        );
        $requetePrepare->bindParam(':unVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $totalHorsForfait = 0.00;
        while ($donnees = $requetePrepare->fetch()) {
            if (!($this->estRefuse($donnees['id']))) {
                $totalHorsForfait = $totalHorsForfait + floatval($donnees['montant']);
            }
        }
        return $totalHorsForfait;
    }
    
    /**
     * Fonction qui modifie le libellé de la ligne de frais hors forfait
     * passée en paramètre, en ajoutant "REFUSE" au début du libellé
     *
     * @param int $idFrais
     *
     * @return null
     */
    public function refuserFraisHorsForfait($idFrais)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT libelle "
                . "FROM gsb_lignefraishorsforfait "
                . "WHERE id= :idFrais"
        );
        $requetePrepare->bindParam(':idFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
        $libelleOriginal = $requetePrepare->fetch();
        
        $libelleModifie = 'REFUSE  ' . $libelleOriginal['libelle'];
        $requeteModifie = PdoGsb::$monPdo->prepare(
            "UPDATE gsb_lignefraishorsforfait "
                . "set libelle = :nouveauLibelle "
                . "WHERE id = :idFrais"
        );
        $requeteModifie->bindParam(':nouveauLibelle', $libelleModifie, PDO::PARAM_STR);
        $requeteModifie->bindParam(':idFrais', $idFrais, PDO::PARAM_STR);
        $requeteModifie->execute();
    }
    
    /**
     * Retourne vrai si la ligne de frais hors forfait passée en paramètre a été
     * refusée
     *
     * @param INT $idFrais
     *
     * @return bool    vrai si frais refusé, faux sinon
     */
    public function estRefuse($idFrais)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT libelle "
                . "FROM gsb_lignefraishorsforfait "
                . "WHERE id= :idFrais"
        );
        $requetePrepare->bindParam(':idFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
        $libelle = $requetePrepare->fetch();
        $libelleDebut = substr($libelle['libelle'], 0, 6);
        if ($libelleDebut == 'REFUSE') {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Fonction qui valide le montant d'une fiche de frais, en mettant à
     * jour le montant donné en paramètre dans le champs montantvalide de la
     * fiche de frais
     *
     * @param type String     $idVisiteur
     * @param type String     $mois au format aaaamm
     * @param type Float      $montant
     *
     * @return null
     */
    public function validerFrais($idVisiteur, $mois, $montant)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "UPDATE gsb_fichefrais "
                . "SET montantvalide = :montant "
                . "WHERE idvisiteur = :idVisiteur "
                . "AND mois = :unMois"
        );
        $requetePrepare->bindParam(':montant', $montant, PDO::PARAM_STR);
        $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
    }
    
    /**
     * Fonction qui reporte une ligne de frais hors forfait sur le mois suivant
     *
     * @param String        $idFrais
     * @param String        $mois au format aaaamm
     *
     * @return null
     */
    public function reporterFraisHorsForfait($idFrais, $mois)
    {
        $moisSuivant = getLeMoisSuivant($mois);
        echo 'mois suivant = ' . $moisSuivant . ' ';
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "UPDATE gsb_lignefraishorsforfait "
                . "SET mois = :unMois "
                . "WHERE id = :idFrais"
        );
        $requetePrepare->bindParam(':unMois', $moisSuivant, PDO::PARAM_STR);
        $requetePrepare->bindParam(':idFrais', $idFrais, PDO::PARAM_STR);
        $requetePrepare->execute();
    }
    
    /**
     * Fonction qui retourne un tableau contenant toutes les fiches visiteurs
     * dont l'état est "validée et mise en paiement"
     *
     * @return un tableau associatif avec l'id, le nom et le prénom du visiteur,
     * ainsi que le mois, le nbre de justificatifs, le montant et la date
     * de modification de la fiche concernée
     */
    public function getLesFichesVisiteursAPayer()
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT id, nom, prenom, mois, nbjustificatifs, montantvalide, datemodif "
                . "FROM gsb_visiteur INNER JOIN gsb_fichefrais "
                . "ON (gsb_visiteur.id = gsb_fichefrais.idvisiteur) "
                . "WHERE idetat='VA' ORDER BY datemodif ASC"
        );
        $requetePrepare->execute();
        $fiche = array();
        while ($donnees = $requetePrepare->fetch()) {
            $fiche[] = array(
                'id' => $donnees['id'],
                'nom' => $donnees['nom'],
                'prenom' => $donnees['prenom'],
                'mois' => $donnees['mois'],
                'nbjustificatifs' => $donnees['nbjustificatifs'],
                'montant' => $donnees['montantvalide'],
                'date' => $donnees['datemodif']
            );
        }
        return $fiche;
    }
    
    /**
     * Fonction qui retourne un tableau contenant tous les id des visiteurs
     * dont l'état de la fiche est "CL"
     *
     * @return un tableau avec les id, nom, prenom, mois
     */
    public function getLesVisiteursAValider()
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT id, nom, prenom, mois "
                . "FROM gsb_visiteur "
                . "INNER JOIN gsb_fichefrais "
                . "ON (gsb_visiteur.id = gsb_fichefrais.idvisiteur) "
                . "WHERE idetat='CL' "
        );
        $requetePrepare->execute();
        $lesVisiteurs = array();
        while ($donnees = $requetePrepare->fetch()) {
            $lesVisiteurs[] = array(
                'id' => $donnees['id'],
                'nom' => $donnees['nom'],
                'prenom' => $donnees['prenom'],
                'mois' => $donnees['mois']
                );
        }
        return $lesVisiteurs;
    }
    
    /**
     * Fonction destinées à corriger les données du jeu test, et
     * qui contrôle que le montant dans les fiches validées est bien = au montant
     * des frais forfaits + les frais hors forfait, et qui corrige si ce n'est
     * pas le cas.
     *
     * @return null
     *
     */
    public function correctionAutomatiqueMontantValide()
    {
        $lesFichesVisiteurs = $this->getLesFichesVisiteursAPayer();
        foreach ($lesFichesVisiteurs as $uneFicheVisiteur) {
            $idVisiteur = $uneFicheVisiteur['id'];
            $mois = $uneFicheVisiteur['mois'];
            $montantValide = $uneFicheVisiteur['montant'];
            $montantForfait = $this->calculFraisForfait($idVisiteur, $mois);
            $montantHorsForfait = $this->calculFraisHorsForfait($idVisiteur, $mois);
            $montantTotal = $montantForfait + $montantHorsForfait;
            
            if ($montantValide !== $montantTotal) {
                $requetePrepare = PdoGsb::$monPdo->prepare(
                    "UPDATE gsb_fichefrais "
                    . "SET montantvalide = :montant "
                    . "WHERE idvisiteur = :idVisiteur AND mois = :unMois"
                );
                $requetePrepare->bindParam(':montant', $montantTotal, PDO::PARAM_STR);
                $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
                $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
                $requetePrepare->execute();
            }
        }
    }
    
    /**
     * Fonction destinée à corriger les données du jeu test, et
     * qui contrôle que le nbre de justificatifs dans les fiches validées
     * est bien <= au nbre de lignes de frais hors forfait, et qui corrige
     * si ce n'est pas le cas (pour des raisons de cohérence).
     *
     * @return null
     *
     */
    public function correctionAutomatiqueNbJustificatifs()
    {
        $lesFichesVisiteurs = $this->getLesFichesVisiteursAPayer();
        foreach ($lesFichesVisiteurs as $uneFicheVisiteur) {
            $idVisiteur = $uneFicheVisiteur['id'];
            $mois = $uneFicheVisiteur['mois'];
            $nbJustificatifs = $uneFicheVisiteur['nbjustificatifs'];
            $fraisHorsForfait = $this->getLesFraisHorsForfait($idVisiteur, $mois);
            
            if (count($fraisHorsForfait) < $nbJustificatifs) {
                $requetePrepare = PdoGsb::$monPdo->prepare(
                    "UPDATE gsb_fichefrais "
                    . "SET nbjustificatifs = :nbJustificatifs "
                    . "WHERE idvisiteur = :idVisiteur AND mois = :unMois"
                );
                $requetePrepare->bindParam(':nbJustificatifs', count($fraisHorsForfait), PDO::PARAM_STR);
                $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
                $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
                $requetePrepare->execute();
            }
        }
    }
       
    /**
     * Fonction qui récupère les mots de passe de tous les visiteurs
     *
     * @return Array        un tableau associatif contenant les id des
     * visiteurs associés à leur mot de passe
     */
    public function getLesMotsDePasseVisiteurs()
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT id, mdp "
                . "FROM gsb_visiteur "
        );
        $requetePrepare->execute();
        while ($donnees = $requetePrepare->fetch()) {
            $mdp[] = array(
                'id' => $donnees['id'],
                'mdp' => $donnees['mdp']
            );
        }
        return $mdp;
    }
        
    /**
     * Fonction qui modifie le mot de passe d'un visiteur, en le remplaçant
     * par son équivalent hashé
     *
     * @param String           $idVisiteur = id du visiteur
     * @param String            $mdp mot de passe non hashé
     *
     * @return null
     */
    public function setMdpHashVisiteurs($idVisiteur, $mdp)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "UPDATE gsb_visiteur "
                . "SET mdp = :motHash "
                . "WHERE id = :id "
        );
        $mdp = password_hash($mdp, PASSWORD_BCRYPT);
        $requetePrepare->bindParam(':motHash', $mdp, PDO::PARAM_STR);
        $requetePrepare->bindParam(':id', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->execute();
    }
    
    /**
     * Fonction qui récupère les mots de passe de tous les comptables
     *
     * @return Array        un tableau associatif contenant les id des
     * comptables associés à leur mot de passe
     */
    public function getLesMotsDePasseComptables()
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT id, mdp "
                . "FROM gsb_comptable "
        );
        $requetePrepare->execute();
        while ($donnees = $requetePrepare->fetch()) {
            $mdp[] = array(
                'id' => $donnees['id'],
                'mdp' => $donnees['mdp']
            );
        }
        return $mdp;
    }
        
    /**
     * Fonction qui modifie le mot de passe d'un comptable, en le remplaçant
     * par son équivalent hashé
     *
     * @param String           $idComptable = id du comptable
     * @param String            $mdp mot de passe non hashé
     *
     * @return null
     */
    public function setMdpHashComptables($idComptable, $mdp)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "UPDATE gsb_comptable "
                . "SET mdp = :motHash "
                . "WHERE id = :id "
        );
        $mdp = password_hash($mdp, PASSWORD_BCRYPT);
        $requetePrepare->bindParam(':motHash', $mdp);
        $requetePrepare->bindParam(':id', $idComptable, PDO::PARAM_STR);
        $requetePrepare->execute();
    }
    
    /**
     * Met à jour la table ligneFraisHorsForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param Array  $lesFrais   tableau associatif de clé idFrais et
     *                           de valeur la quantité pour ce frais
     *
     * @return null
     */
    public function majFraisHorsForfait($idVisiteur, $mois, $lesFrais)
    {
        $lesCles = array_keys($lesFrais);
        foreach ($lesCles as $unIdFrais) {
            $montant = $lesFrais[$unIdFrais];
            $requetePrepare = PdoGSB::$monPdo->prepare(
                'UPDATE gsb_lignefraishorsforfait '
                . 'SET gsb_lignefraishorsforfait.montant = :unMontant '
                . 'WHERE gsb_lignefraishorsforfait.idvisiteur = :unIdVisiteur '
                . 'AND gsb_lignefraishorsforfait.mois = :unMois '
                . 'AND gsb_lignefraishorsforfait.id = :idFrais'
            );
            $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(':idFrais', $unIdFrais, PDO::PARAM_STR);
            $requetePrepare->execute();
        }
    }
}
