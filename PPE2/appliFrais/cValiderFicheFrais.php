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

  $nbJustif=lireDonneePost("nbsJustif", "");
  $montantValide=lireDonneePost("montantValide", "");
  // acquisition des données entrés, ici l'etape 2 (pour des modifications, supprimer, report etc.) et autres données lié à //l'étape 2
  $etape2=lireDonneePost("etape2", "");
    //si etape 2 est une modification 
    $fraisETP=lireDonneePost("FraisForfaitN1","");
    $fraisKM=lireDonneePost("FraisForfaitN2","");
    $fraisNUI=lireDonneePost("FraisForfaitN3","");
    $fraisREP=lireDonneePost("FraisForfaitN4","");

  if ($etape != "demanderConsult" && $etape != "validerConsult" && $etape != "validerFicheFrais") {
      // si autre valeur, on considère que c'est le début du traitement
      $etape = "demanderConsult";        
  } 

  //Si le comptable valide la fiche de frais 
  if ($etape == "validerFicheFrais")
  {
      modifierEtatFicheFrais($idConnexion, $moisSaisi, $visiteurSaisi, "VA");
      modifierNbJustifFicheFrais($idConnexion, $moisSaisi, $visiteurSaisi, $nbJustif);
      //On compte le montant total validé 
      modifierMontantValide($idConnexion, $moisSaisi, $visiteurSaisi, $montantValide);

  }
  
  if ($etape == "validerConsult") { // l'utilisateur valide ses nouvelles données
                
      // vérification de l'existence de la fiche de frais pour le mois et le visiteur demandé
      $existeFicheFrais = existeFicheFrais($idConnexion, $moisSaisi, $visiteurSaisi);
      // si elle n'existe pas, on la crée avec les élets frais forfaitisés à 0
      if ( !$existeFicheFrais ) {
          ajouterErreur($tabErreurs, "Le mois demandé est invalide");
      }
      else {
          // si le comptable souhaite modifier la fiche de frais forfaitiser 
          if ($etape2 == "modifierFicheForfait") {
            $req = obtenirReqModifierFicheForfaitETP($fraisETP, $visiteurSaisi, $moisSaisi);
            mysql_query($req, $idConnexion);  

            $req = obtenirReqModifierFicheForfaitKM($fraisKM, $visiteurSaisi, $moisSaisi);
            mysql_query($req, $idConnexion);
            
            $req = obtenirReqModifierFicheForfaitNUI($fraisNUI, $visiteurSaisi, $moisSaisi);
            mysql_query($req, $idConnexion);
            
            $req = obtenirReqModifierFicheForfaitREP($fraisREP, $visiteurSaisi, $moisSaisi);
            mysql_query($req, $idConnexion);
          }
          // récupération des données sur la fiche de frais demandée
          $tabFicheFrais = obtenirDetailFicheFrais($idConnexion, $moisSaisi, $visiteurSaisi);
      }
  }      
?>
  <!-- Division principale -->
 <div id="contenu" class="panel bg_dark" align="center">
      <?php 
        if ($etape == "validerFicheFrais")
        {
      ?> 
        <p class="label label-success">La fiche de frais d'un montant de <?php echo $montantValide ; ?>€ à bien été validé</p> 
      <?php 
        }
      ?> 
      <h2>Validation des fiches de frais</h2>

      <form action="" method="post">
      <div class="form-group">
          <input type="hidden" name="etape" value="validerConsult" />
      <p>
        <label for="lstVisiteur">Choisir un visiteur : </label>
        <select onchange="Actualise();" id="lstVisiteur" name="lstVisiteur" title="Sélectionnez le visiteur pour valider ses fiches de frais">
        	<?php
                // on propose tous les visiteurs ayant des fiches de frais à valider 
                $req = obtenirReqVisiteurFicheFrais();
                $listVisiteur = mysql_query($req, $idConnexion);
                $lgVisiteur = mysql_fetch_assoc($listVisiteur);

                while ( is_array($lgVisiteur) ) {
                    $nom = $lgVisiteur["nom"];
                    $prenom = $lgVisiteur["prenom"];
                    $idVis = $lgVisiteur["id"];
            ?>
            <option value="<?php echo ($idVis); ?>" selected="selected"><?php echo ($prenom . ' ' . $nom); ?></option>
            <?php
                $lgVisiteur = mysql_fetch_assoc($listVisiteur);        
                }
                mysql_free_result($listVisiteur);
            ?>
        </select>
        <!-- CHAMP HIDEN POUR GARDER L'ID DU VISITEUR -->
        <select id="lstVisiteurID" hidden="hidden">
        	<?php
                // on propose tous les visiteurs ayant des fiches de frais à valider 
                $req = obtenirReqVisiteurFicheFrais();
                $listVisiteur = mysql_query($req, $idConnexion);
                $lgVisiteur = mysql_fetch_assoc($listVisiteur);

                while ( is_array($lgVisiteur) ) {
                    $idVis = $lgVisiteur["id"];
            ?>
            <option value="<?php echo ($idVis); ?>" selected="selected"><?php echo ($idVis); ?></option>
            <?php
                $lgVisiteur = mysql_fetch_assoc($listVisiteur);        
                }
                mysql_free_result($listVisiteur);
            ?>
        </select>


        <br>


        <label for="lstMoisVisiteur">Mois : </label>
        <select id="lstMoisVisiteur" name="lstMoisVisiteur" disabled="disabled">
        	<?php
                // on propose tous les mois du visiteur X
                $req = obtenirReqVisiteurMoisFicheFrais();
                $listMoisVisiteur = mysql_query($req, $idConnexion);
                $lgMoisVisiteur = mysql_fetch_assoc($listMoisVisiteur);

                while ( is_array($lgMoisVisiteur) ) {
                    $Mois = $lgMoisVisiteur["mois"];
            ?>
            <option value="<?php echo ($Mois); ?>" selected="selected"><?php echo (substr($Mois, -2, 2) . "/" . substr($Mois, 0, 4)); ?></option>
            <?php
                $lgMoisVisiteur = mysql_fetch_assoc($listMoisVisiteur);        
                }
                mysql_free_result($listMoisVisiteur);
            ?>
        </select>
        <!-- CHAMP HIDEN POUR GARDER L'ID DU VISITEUR EN FONCTION DU MOIS -->
        <select id="lstMoisVisiteurID" hidden="hidden">
        	<?php
                // on propose tous les mois du visiteur X
                $req = obtenirReqVisiteurMoisFicheFrais();
                $listMoisVisiteur = mysql_query($req, $idConnexion);
                $lgMoisVisiteur = mysql_fetch_assoc($listMoisVisiteur);

                while ( is_array($lgMoisVisiteur) ) {
                    $MoisID = $lgMoisVisiteur["idVisiteur"];
            ?>
            <option value="<?php echo ($MoisID); ?>" selected="selected"><?php echo ($MoisID); ?></option>
            <?php
                $lgMoisVisiteur = mysql_fetch_assoc($listMoisVisiteur);        
                }
                mysql_free_result($listMoisVisiteur);
            ?>
        </select>
      </p>
      </div>
      <div class="form-group">
      <p>
        <input id="ok" type="submit" value="Afficher"
               title="Afficher les fiches de frais de ce visiteur au mois donné" class="btn btn-success" />
      </p> 
      </div>   
      </form>


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
    <form action="" method="post">
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
                    <td class="qteForfait"><input type="text" name="<?php echo ('FraisForfaitN' . $compteur ); ?>" value="<?php echo $uneQuantite ; ?>"/></td>

                <?php
                $compteur += 1;
                $montantValide += $uneQuantite;
                }
                ?>
                <td><?php echo $tabFicheFrais["libelleEtat"] ; ?></td>
            </tr>
      </table>
      <input type="hidden" name="lstVisiteur" value="<?php echo $visiteurSaisi; ?>"/>
      <input type="hidden" name="lstMoisVisiteur" value="<?php echo $moisSaisi; ?>"/>
      <input type="hidden" name="etape" value="validerConsult"/>
      <input type="hidden" name="etape2" value="modifierFicheForfait"/>
      <input id="ok" type="submit" value="Appliquer les modifications cette fiche" class="btn btn-warning" />
    </form>
    <table class="table">
       <caption>Descriptif des éléments hors forfait - <?php echo $tabFicheFrais["nbJustificatifs"]; ?> justificatifs reçus -
       </caption>
             <tr>
                <th class="date">Date</th>
                <th class="libelle">Libellé</th>
                <th class="montant">Montant</th>  
                <th><span class="glyphicon glyphicon-time" aria-hidden="true"></span></th> 
                <th><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></th>             
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
                   <td><input id="Reporter" type="submit" value="Reporter" class="btn btn-warning"/></td>
                   <td><input id="Supprimer" type="submit" value="Supprimer" class="btn btn-danger"/></td>
                </tr>
            <?php
                $montantValide += $lgEltHorsForfait["montant"];
                $lgEltHorsForfait = mysql_fetch_assoc($idJeuEltsHorsForfait);
            }
            mysql_free_result($idJeuEltsHorsForfait);
  ?>
    </table>
  </div>
  <form class="form-group" action="" method="post">  
    <label for="inpJustif">Justificatifs reçu(s) : </label>
    <input id="inpJustif" name="nbsJustif" type="text" value="<?php echo $tabFicheFrais["nbJustificatifs"] ;?> " />

    <hr>
    <input type="hidden" name="lstVisiteur" value="<?php echo $visiteurSaisi ; ?>"/>
    <input type="hidden" name="lstMoisVisiteur" value="<?php echo $moisSaisi ; ?>"/>
    <input type="hidden" name="montantValide" value="<?php echo $montantValide ; ?>"/>
    <input type="hidden" name="etape" value="validerFicheFrais"/>
    <input id="ok" type="submit" value="Valider cette fiche de frais" class="btn btn-success" />
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