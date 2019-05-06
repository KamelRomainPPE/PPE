<?php
/** 
 * Script de contrôle et d'affichage du cas d'utilisation "Consulter une fiche de frais"
 * @package default
 * @todo  RAS
 */
  $repInclude = './include/';
  require($repInclude . "_init.inc.php");

  // page inaccessible si visiteur non connecté
  if ( ! estVisiteurConnecte() ) {
      header("Location: cSeConnecter.php");  
  }
  require($repInclude . "_entete.inc.html");
  require($repInclude . "_sommaire.inc.php"); 

  // acquisition des données entrées, ici le numéro de mois, le visiteur et l'étape du traitement
  $moisSaisi=lireDonneePost("lstMoisVisiteur", "");
  $visiteurSaisi=lireDonneePost("lstVisiteur", "");
  $etape=lireDonneePost("etape",""); 

  //Si le comptable valide la fiche de frais 
  if ($etape == "miseEnPaiement")
  {
      modifierEtatFicheFrais($idConnexion, $moisSaisi, $visiteurSaisi, "RB");
  }

  if ($etape == "validerConsult") { // l'utilisateur valide ses nouvelles données
                
      // vérification de l'existence de la fiche de frais pour le mois et le visiteur demandé
      $existeFicheFrais = existeFicheFrais($idConnexion, $moisSaisi, $visiteurSaisi);
      // si elle n'existe pas, on la crée avec les élets frais forfaitisés à 0
      if ( !$existeFicheFrais ) {
          ajouterErreur($tabErreurs, "Le mois demandé est invalide");
      }
      else {
          //récupération des données sur la fiche de frais demandée
          $tabFicheFrais = obtenirDetailFicheFrais($idConnexion, $moisSaisi, $visiteurSaisi);
      }
  }      
?>
  <!-- Division principale -->
 <div id="contenu" class="panel bg_dark" align="center">
  <?php 
        if ($etape == "miseEnPaiement")
        {
      ?> 
        <p class="label label-primary">La fiche de frais à bien été remboursé</p> 
      <?php 
        }
      ?>

<!-- PARTIE AFFICHAGE DES TABLEAUX -->
<?php      

// demande et affichage des différents éléments (forfaitisés et non forfaitisés)
// de la fiche de frais demandée, uniquement si pas d'erreur détecté au contrôle
    if ( $etape == "validerConsult" ) {
        if ( nbErreurs($tabErreurs) > 0 ) {
            echo toStringErreurs($tabErreurs) ;
        }
        else {
?>
    <h2>Détail et mise en paiement </h2>
    <h3>Fiche de frais du mois de <?php echo obtenirLibelleMois(intval(substr($moisSaisi,4,2))) . " " . substr($moisSaisi,0,4); ?> : 
    <em><?php echo $tabFicheFrais["libelleEtat"]; ?> </em>
    depuis le <em><?php echo $tabFicheFrais["dateModif"]; ?></em></h3>
    <div class="encadre">
    <p>Montant validé : <?php echo $tabFicheFrais["montantValide"] ; ;
        ?>    
        €          
    </p>
<?php          
            // demande de la requête pour obtenir la liste des éléments 
            // forfaitisés du visiteur connecté pour le mois demandé
            $req = obtenirReqEltsForfaitFicheFrais($moisSaisi, $visiteurSaisi);
            $idJeuEltsFraisForfait = mysql_query($req, $idConnexion);
            echo mysql_error($idConnexion);
            $lgEltForfait = mysql_fetch_assoc($idJeuEltsFraisForfait);
            // parcours des frais forfaitisés du visiteur connecté
            // le stockage intermédiaire dans un tableau est nécessaire
            // car chacune des lignes du jeu d'enregistrements doit être doit être
            // affichée au sein d'une colonne du tableau HTML
            $tabEltsFraisForfait = array();
            while ( is_array($lgEltForfait) ) {
                $tabEltsFraisForfait[$lgEltForfait["libelle"]] = $lgEltForfait["quantite"];
                $lgEltForfait = mysql_fetch_assoc($idJeuEltsFraisForfait);
            }
            mysql_free_result($idJeuEltsFraisForfait);
            ?>

      <table class="table">
         <caption>Quantités des éléments forfaitisés</caption>
          <tr>
              <?php
              // premier parcours du tableau des frais forfaitisés du visiteur connecté
              // pour afficher la ligne des libellés des frais forfaitisés
              foreach ( $tabEltsFraisForfait as $unLibelle => $uneQuantite ) {
              ?>
                  <th><?php echo $unLibelle ; ?></th>
              <?php
              }
              ?>
              <th>Situation :</th>
          </tr>

            <tr>
                <?php
                // second parcours du tableau des frais forfaitisés du visiteur connecté
                // pour afficher la ligne des quantités des frais forfaitisés
                $compteur = 1;
                $montantValide = 0;
                foreach ( $tabEltsFraisForfait as $unLibelle => $uneQuantite ) {
                ?>
                    <td class="qteForfait"><?php echo $uneQuantite ; ?></td>

                <?php
                $compteur += 1;
                $montantValide += $uneQuantite;
                }
                ?>
                <td><?php echo $tabFicheFrais["libelleEtat"] ; ?></td>
            </tr>
      </table>

    <table class="table">
       <caption>Descriptif des éléments hors forfait - <?php echo $tabFicheFrais["nbJustificatifs"]; ?> justificatifs reçus -
       </caption>
             <tr>
                <th class="date">Date</th>
                <th class="libelle">Libellé</th>
                <th class="montant">Montant</th>              
             </tr>
<?php          
            // demande de la requête pour obtenir la liste des éléments hors
            // forfait du visiteur connecté pour le mois demandé
            $req = obtenirReqEltsHorsForfaitFicheFrais($moisSaisi, $visiteurSaisi);
            $idJeuEltsHorsForfait = mysql_query($req, $idConnexion);
            $lgEltHorsForfait = mysql_fetch_assoc($idJeuEltsHorsForfait);
            
            // parcours des éléments hors forfait 
            while ( is_array($lgEltHorsForfait) ) {
            ?>
                <tr>
                   <td><?php echo $lgEltHorsForfait["date"] ; ?></td>
                   <td><?php echo filtrerChainePourNavig($lgEltHorsForfait["libelle"]) ; ?></td>
                   <td><?php echo $lgEltHorsForfait["montant"] ; ?></td>
                </tr>
            <?php
                $lgEltHorsForfait = mysql_fetch_assoc($idJeuEltsHorsForfait);
            }
            mysql_free_result($idJeuEltsHorsForfait);
  ?>
    </table>
  </div>
  <form class="form-group" action="" method="post">  
    <label for="inpJustif">Justificatifs reçu(s) : <?php echo $tabFicheFrais["nbJustificatifs"] ;?></label>
    <hr>
    <input type="hidden" name="lstVisiteur" value="<?php echo $visiteurSaisi ; ?>"/>
    <input type="hidden" name="lstMoisVisiteur" value="<?php echo $moisSaisi ; ?>"/>
    <input type="hidden" name="etape" value="miseEnPaiement"/>
    <input id="ok" type="submit" value="Mettre en paiement" class="btn btn-primary" />
  </form>

<?php
        }
    }
?>
  
 </div>

 <?php        
  require($repInclude . "_pied.inc.html");
  require($repInclude . "_fin.inc.php");
?> 