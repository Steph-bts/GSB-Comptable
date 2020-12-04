<?php
/**
 * Vue Fiche Validée avec Succès
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
?>
<div class="row"><button type="button" class="btn btn-secondary" id="reSelect" ><a href="index.php?uc=validation&action=selectionVisiteurMois">Retour à la sélection</a></button></div>
<div class="row">
    <div class="panel panel-primary">
        <div class="panel-heading">Fiche de frais du mois 
            <?php echo $numMois . '-' . $numAnnee ?>  pour <?php echo $nomPrenom[0]['nom'] . ' ' . $nomPrenom[0]['prenom'] ;?> : </div>
        <div class="panel-body">
            <strong><u>Etat :</u></strong> <?php echo $libEtat ?>
            depuis le <?php echo $dateModif ?> <br> 
            <strong><u>Montant frais forfait : </u></strong> <?php echo number_format($montantForfait, 2, ',', ' '); ?> € <br>
            <strong><u>Montant frais hors forfait : </u></strong> <?php echo number_format($montantHorsForfait,2, ',', ' ') ; ?> € <br>
            <strong><u>Total en cours :</u></strong> <?php echo number_format($enCours, 2, ',', ' ') ?> €
        </div>
    </div>    

    <div class="col-md-12">
        <form method="post" 
              action="index.php?uc=validation&action=valider&idVisiteur=<?php echo $idVisiteur; ?>&leMois=<?php echo $leMois; ?>" 
              role="form">
            <fieldset>
                <h3>Eléments forfaitisés</h3>
                <?php
                foreach ($lesFraisForfait as $unFrais) {
                    $idFrais = $unFrais['idfrais'];
                    $libelle = htmlspecialchars($unFrais['libelle']);
                    $quantite = $unFrais['quantite']; ?>
                    <div class="form-group">
                        <label for="idFrais"><?php echo $libelle; ?></label>
                        <input type="text" id="idFrais" 
                               name="lesFrais[<?php echo $idFrais; ?>]"
                               size="10" maxlength="5" 
                               value="<?php echo $quantite; ?>" 
                               class="form-control">
                    </div>
                    <?php
                }
                ?>
                <button name="corrige" class="btn btn-primary" type="submit" value="corrige">Corriger</button>
            </fieldset>
            <h3>Descriptif des éléments hors forfait</h3>
            <label for="justifs">Nbre de jutificatifs reçus =</label>
            <input type="number" name="justifs" step="1" min="0" value="<?php echo $nbJustificatifs; ?>" required>
            <table class="table table-bordered table-responsive">
                <thead>
                    <tr>
                        <th class="date">Date</th>
                        <th class="libelle">Libellé</th>  
                        <th class="montant">Montant</th>  
                        <th class="action">&nbsp;</th> 
                        <th class="action">&nbsp</th>

                    </tr>
                </thead>  
                <tbody>
                <?php
                foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
                    $libelle = verifInput($unFraisHorsForfait['libelle']);
                    $date = $unFraisHorsForfait['date'];
                    $montant = $unFraisHorsForfait['montant'];
                    $id = $unFraisHorsForfait['id'];
                    ?>           
                    <tr>
                        <td> <?php echo $date ?></td>
                        <td> <?php echo $libelle ?></td>
                        <td><?php if($pdo->estRefuse($id)) { 
                            echo $montant;
                            } else { ?>                                        
                            <input type="number" step="0.01" name="horsForfait[<?php echo $id; ?>]" value="<?php echo $montant ?>">
                        </td>
                        <?php } ?>
                        <td><?php if($pdo->estRefuse($id)) {?>
                            <p>Ce frais a été rejeté</p>
                        <?php } else { ?>
                            <a href="index.php?uc=validation&action=supprimerFrais&idVisiteur=<?php echo $idVisiteur; ?>&leMois=<?php echo $leMois; ?>&idFrais=<?php echo $id;?>"
                               onclick="return confirm
                                   ('Voulez-vous vraiment refuser ce frais ?');">
                                Refuser ce frais</a>
                            </td>
                        <?php } ?>
                        <td><?php if($pdo->estRefuse($id)) {?>
                            <p>Ce frais a été rejeté</p>
                        <?php } else { ?>
                            <a href="index.php?uc=validation&action=reporterFrais&idVisiteur=<?php echo $idVisiteur; ?>&leMois=<?php echo $leMois; ?>&idFrais=<?php echo $id;?>"
                               onclick="return confirm
                                   ('Voulez-vous vraiment reporter ce frais ?');">
                                Report fiche mois suivant</a></td>
                        <?php } ?>
                    </tr>
                    <?php
                }

                ?>
                </tbody>
            </table>
            <button name="corrige" class="btn btn-primary" type="submit" value="corrige">Corriger</button>

            <hr>

            <div>

                <button name="valide" class="btn btn-success btnValide" type="submit" value="valide">
                    Valider la fiche de Frais
                    <br><br>
                    <span class="glyphicon glyphicon-ok"></span>
                </button>

            </div>
        </form>
    </div>

</div>


