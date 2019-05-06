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

 
?>
  <!-- Division principale -->
 <div id="contenu" class="panel bg_dark" align="center">
    <h2>Suivre le paiement des fiches de frais</h2>  
    <?php 
      //Recuperation d'un tableau contenant toutes les fiches de frais validé
      $tabFicheFrais = obtenirFicheFraisValide($idConnexion);
      $lgTabFicheFrais = mysql_fetch_assoc($tabFicheFrais);
    ?> 
    <table class="table">
             <tr>
                <th></th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Mois</th>
                <th></th>
                <th>Nombres justificatifs</th> 
                <th>Montant validé</th> 
                <th>Date de modification</th> 
                <th><span class="glyphicon glyphicon-send" aria-hidden="true"></span></th> 
                <th></th>             
             </tr>
<?php          
            
            // parcours des éléments hors forfait 
            while ( is_array($lgTabFicheFrais) ) {
            ?>
                <tr>
                  <form action="cMiseEnPaiementFicheFrais.php" method="post">
                    <th><input type="hidden" name="lstVisiteur" value="<?php echo $lgTabFicheFrais['idVisiteur'] ;?>"></th>
                    <?php 
                      //Recuperation du nom et prénom en fonction de l'id 
                      $tabNomPrenom = obtenirDetailVisiteur($idConnexion, $lgTabFicheFrais['idVisiteur']);
                    ?> 
                    <th><?php echo $tabNomPrenom['nom'] ;?></th>
                    <th><?php echo $tabNomPrenom['prenom'] ;?></th>
                    <th><?php echo $lgTabFicheFrais['mois'] ;?></th>
                    <th><input type="hidden" name="lstMoisVisiteur" value="<?php echo $lgTabFicheFrais['mois'] ;?>"></th>
                    <th><?php echo $lgTabFicheFrais['nbJustificatifs'] ;?></th> 
                    <th><?php echo $lgTabFicheFrais['montantValide'] ;?></th> 
                    <th><?php echo $lgTabFicheFrais['dateModif'] ;?></th> 
                    <th><input type="submit" value="Suivre" class="btn btn-primary"></th>  
                    <th><input type="hidden" name="etape" value="validerConsult"></th>   
                  </form>
                </tr>
            <?php
              $lgTabFicheFrais = mysql_fetch_assoc($tabFicheFrais);    
            }
  ?>
    </table>
 </div>

 <?php        
  require($repInclude . "_pied.inc.html");
  require($repInclude . "_fin.inc.php");
?> 