<?php
/*
Fichier : index.php
*/
session_start();

include_once "libs/maLibUtils.php";
include_once "libs/maLibGif.php";
include_once "libs/modele.php";
include_once "libs/maLibSecurisation.php";
include_once "libs/maLibForms.php";

$view = getValue("view", "Accueil"); //par défaut la vue est Accueil

include "views/header.php";

switch($view){


	case "Connexion" :
		include "views/connexion.php";
		break;

	case "Inscription" :
		include "views/inscription.php";
		break;
	
	case "Accueil" : 
		include "views/accueil.php";
		break;
		
	case "Formulaire" : 
		requireConnexion();
		include "views/formulaire.php";
		break;
		
	case "Projets" :
		requireConnexion();
		include "views/projets.php";
		break;
		
	case "Gif" :
		requireConnexion();
		include "views/gif.php";
		break;
		
	default :
		include "views/accueil.php";
		break;
}

include "views/footer.php";
?>
