function Actualise() 
{
	//PREMIER CHAMP HIDDEN ON CHANGE LE SELECT 
	var index1 = document.getElementById("lstVisiteur").selectedIndex;
	document.getElementById("lstVisiteurID").selectedIndex = index1;

	//EN FONCTION DU NOUVEAU SELECT ON EFFACE DANS LE SELECT DES MOIS TOUTES LES OCCURENCES DIFFERENTES DE NOTRE ID VISITEUR 
	var idVisiteur = document.getElementById("lstVisiteurID").options[index1].value;

	var toutlesmois = document.getElementById("lstMoisVisiteur");
	var toutlesidvisiteur = document.getElementById("lstMoisVisiteurID");
	var taille = toutlesidvisiteur.length - 1;

	//On affiche tout 
	for (var i = 0; i <= taille; i++) {
		toutlesmois.options[i].style.display = "block";	
	}

	//On supprime les mauvais
	for (var i = 0; i <= taille; i++) {
		if (toutlesidvisiteur.options[i].value != idVisiteur)
		{
			toutlesmois.options[i].style.display = "none";
		}		
	}
	toutlesmois.disabled = false;
}