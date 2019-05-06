<?php  
/** 
 * Script de contrôle et d'affichage du cas d'utilisation "Se connecter"
 * @package default
 * @todo  RAS
 */
  $repInclude = './include/';
  require($repInclude . "_init.inc.php");
  
  // est-on au 1er appel du programme ou non ?
  $etape=(count($_POST)!=0)?'validerConnexion' : 'demanderConnexion';
  $type=(count($_POST)!=0)? $_POST['type'] : '';
  if ($etape=='validerConnexion') { // un client demande à s'authentifier
  //Determine si le client est visiteur ou comptable
      //Si visiteur : 
      if ($type == 'Visiteur') {
        // acquisition des données envoyées, ici login et mot de passe
        $login = lireDonneePost("txtLogin");
        $mdp = lireDonneePost("txtMdp");   
        $lgUser = verifierInfosConnexionVisiteur($idConnexion, $login, $mdp) ;
        // si l'id utilisateur a été trouvé, donc informations fournies sous forme de tableau
        if ( is_array($lgUser) ) { 
            affecterInfosConnecte($lgUser["id"], $lgUser["login"], $type);
        }
        else {
            ajouterErreur($tabErreurs, "Pseudo et/ou mot de passe incorrects");
        }  
      }
      //Si non si comptable :
      elseif ($type == 'Comptable') {
        // acquisition des données envoyées, ici login et mot de passe
        $login = lireDonneePost("txtLogin");
        $mdp = lireDonneePost("txtMdp");   
        $lgUser = verifierInfosConnexionComptable($idConnexion, $login, $mdp) ;
        // si l'id utilisateur a été trouvé, donc informations fournies sous forme de tableau
        if ( is_array($lgUser) ) { 
            affecterInfosConnecte($lgUser["id"], $lgUser["login"], $type);
            var_dump($_SESSION);
        }
        else {
            ajouterErreur($tabErreurs, "Pseudo et/ou mot de passe incorrects");
        }  
      }
      //Sinon erreur
      else
      {
        ajouterErreur($tabErreurs, "Pseudo et/ou mot de passe incorrects");
      }
  }
  if ( $etape == "validerConnexion" && nbErreurs($tabErreurs) == 0 ) {
        header("Location:cAccueil.php");
  }

  require($repInclude . "_entete.inc.html");
  require($repInclude . "_sommaire.inc.php");
  
?>
<!-- Division pour le contenu principal -->
    <div id="contenu" class="panel bg_dark" align="center">
      <h2>Identification utilisateur</h2>
<?php
          if ( $etape == "validerConnexion" ) 
          {
              if ( nbErreurs($tabErreurs) > 0 ) 
              {
                echo toStringErreurs($tabErreurs);
              }
          }
?>               
      <form id="frmConnexion" class="form-group" action="" method="post">
      <div>
        <input type="hidden" name="etape" id="etape" value="validerConnexion" />
      <p>
        <label for="txtLogin" accesskey="n">* Login : </label>
        <input type="text" id="txtLogin" name="txtLogin" value="" title="Entrez votre login" />
      </p>
      <p>
        <label for="txtMdp" accesskey="m">* Mot de passe : </label>
        <input type="password" id="txtMdp" name="txtMdp" value=""  title="Entrez votre mot de passe"/>
      </p>
      </div>
      <div class="piedForm" align="center">
      <p>
        <input class="btn btn-info" type="submit" name="type" value="Visiteur" />
        <input class="btn btn-warning" type="submit" name="type" value="Comptable" />
        <input class="btn btn-link" type="reset" id="annuler" value="Effacer" />
      </p> 
      </div>
      </form>
    </div>
<?php
    require($repInclude . "_pied.inc.html");
    require($repInclude . "_fin.inc.php");
?>