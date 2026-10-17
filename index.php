<?php
/*
Fichier : index.php
*/

include_once "libs/maLibUtils.php";
include_once "libs/maLibGif.php";

$view = getValue("view", "Accueil"); //par défaut la vue est Accueil

include "views/header.php";

switch($view){

	case "Accueil" : 
		include "views/accueil.php";
		break;
		
	case "Formulaire" : 
		include "views/formulaire.php";
		break;
		
	default :
		include "views/accueil.php";
		break;
}

include "views/footer.php";
?>
