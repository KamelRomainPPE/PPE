<?php
/** 
 * Contient la division pour le sommaire, sujet à des variations suivant la 
 * connexion ou non d'un utilisateur, et dans l'avenir, suivant le type de cet utilisateur 
 * @todo  RAS
 */

?>
    <!-- Division pour le sommaire -->
    <div id="menuGauche">
     <div id="infosUtil" class="row">
    <?php      
      if (estVisiteurConnecte() ) {

          $idUser = obtenirIdUserConnecte() ;

          if ($_SESSION['type'] == "Visiteur") {
            $lgUser = obtenirDetailVisiteur($idConnexion, $idUser);
            $nom = $lgUser['nom'];
            $prenom = $lgUser['prenom'];
            $couleur = 'label-info';
          }
          elseif ($_SESSION['type'] == "Comptable") {
            $lgUser = obtenirDetailComptable($idConnexion, $idUser);
            $nom = $lgUser['nom'];
            $prenom = $lgUser['prenom'];
            $couleur = 'label-warning';
          }                
    ?>
        <h2 class="col-md-3 label label-success"><span class="glyphicon glyphicon-user" aria-hidden="true"></span>
    <?php  
            echo " " . $nom . " " . $prenom ;
    ?>
        </h2>
        <h2 class="col-md-6"></h2>
        <h3 class="col-md-3 label <?php echo $couleur ?>"> <?php echo $_SESSION['type'] ?> <span class="glyphicon glyphicon-lock" aria-hidden="true"></span></h3>        
    <?php
       }
    ?>  
      </div>  
<?php      
  if (estVisiteurConnecte() ) {
?>
        <ul id="menuList" class="row panel list-unstyled text-center bg_dark">
           <li class="col-md-3 padding5px">
              <a  class="padding5px" href="cAccueil.php" title="Page d'accueil"><span class="glyphicon glyphicon-home" aria-hidden="true"></span> Accueil</a>
           </li>
           <li class="col-md-3 padding5px">
              <a class="padding5px" href="cSeDeconnecter.php" title="Se déconnecter"><span class="glyphicon glyphicon-off" aria-hidden="true"></span> Se déconnecter</a>
           </li>
           <li class="col-md-3 padding5px">
              <?php 
              //Changement des liens du sommaire en fonction du type d'utilisateur
                if ($_SESSION['type'] == "Comptable") {
                  $href = 'cValiderFicheFrais.php';
                  $title = 'Valider les fiches de frais du mois courant';
                  $contenu = 'Valider fiche de frais';
                }
                else
                {
                  $href = 'cSaisieFicheFrais.php';
                  $title = 'Saisie fiche de frais du mois courant';
                  $contenu = 'Saisie fiche de frais';
                }
                
              ?>
              <a class="padding5px" href="<?php echo $href ?>" title="<?php echo $title ?>"> <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> <?php echo $contenu ?></a>
           </li>
           <li class="col-md-3 padding5px">
                <?php 
                //Changement des liens du sommaire en fonction du type d'utilisateur
                if ($_SESSION['type'] == "Comptable") {
                  $href = 'cSuivrePaiementFicheFrais.php';
                  $title = 'Suivre paiement des fiches de frais';
                  $contenu = 'Suivre paiement fiches';
                }
                else
                {
                  $href = 'cConsultFichesFrais.php';
                  $title = 'Consultation de mes fiches de frais';
                  $contenu = 'Mes fiches de frais';
                }
                
              ?>
              <a class="padding5px" href="<?php echo $href ?>" title="<?php echo $title ?>"><span class="glyphicon glyphicon-list" aria-hidden="true"></span> <?php echo $contenu ?></a>
           </li>
         </ul>
        <?php
          // affichage des éventuelles erreurs déjà détectées
          if ( nbErreurs($tabErreurs) > 0 ) {
              echo toStringErreurs($tabErreurs) ;
          }
  }
        ?>
    </div>
    