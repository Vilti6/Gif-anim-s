<?php

/*
Fichier : controleur.php

*/

include_once "libs/maLibUtils.php";
include_once "libs/maLibGif.php";

if ($action = valider ("action"))
{
	switch ($action)
	{
		case 'Generer':
		
		// vérification si il y a un texte
		$texte = valider("texte_gif");

		if (!$texte)
		{
			rediriger("index.php", "view=Formulaire&msg=" . urlencode("Saisissez un texte."));
		}
		
		//vérification si il y a une image de fond
		if (!isset($_FILES["image_fond"]) || !is_uploaded_file($_FILES["image_fond"]["tmp_name"]))
		{
			rediriger("index.php", "view=Formulaire&msg=". urlencode("Choisissez une image de fond."));
		}
		
		// vérification de l'extension
		$extension = extensionImage($_FILES["image_fond"]["tmp_name"]);
		if(!$extension)
		{
			rediriger("index.php", "view=Formulaire&msg=" . urlencode("L'image n'est pas du bon type"));
		}
		
		// si ok alors on met l'image dans le dossier uploads
		$fichier = "image_fond" . uniqid() . "." . "$extension";
		copy($_FILES["image_fond"]["tmp_name"], "uploads/" . $fichier);
		
		// pour le logo
		$logo = false;
		
		if (isset($_FILES["nouveau_logo"]) && is_uploaded_file($_FILES["nouveau_logo"]["tmp_name"]))
		{
			$extension_logo = extensionImage($_FILES["nouveau_logo"]["tmp_name"]);
			
			if (!$extension_logo)
			{
				rediriger("index.php", "view=Formulaire&msg=" .urlencode("le logo n'est pas du bon type"));
			}
			
			// ensuite on range le nouveau logo dans le dossier logos
			$logo = "logo_" . uniqid() . "." . $extension_logo;
			copy($_FILES["nouveau_logo"]["tmp_name"], "logos/" . $logo);
		}
		else
		{
		//si on n'ajoute pas un nouveau logo
		// on choisit dans la liste
		$choix = valider("logo");
		$logo = $choix;
		}
		
		//si logo = false alors l'utilisateur n'as pas selectionné un logo
		if(!$logo)
		{
			rediriger("index.php", "view=Formulaire&msg=" .urlencode("Un logo doit être choisi"));
		}
		
		$couleur = valider("couleur");
		//echo "couleur reçue : " . $couleur;
		//die();
		
		if ($couleur != "blanc" && $couleur != "noir" && $couleur != "rouge" && $couleur != "jaune")
		{
			$couleur = "blanc";
		}
		
		$position_texte = valider("position_texte");
		
		if ($position_texte != "haut" && $position_texte != "bas" && $position_texte != "gauche" && $position_texte != "droite" && $position_texte != "centre")
		{
			$position_texte = "centre";
		}
		
		$position_logo = valider("position_logo");
		
		if ($position_logo != "haut-gauche" && $position_logo != "haut-droite" && $position_logo != "bas-gauche" && $position_logo != "bas-droite")
		{
			$position_logo = "bas-droite";
		}
		
		$polices = listerPolices();
		$police_choisie = valider("police");
		
		//par defaut la policeest false
		$police = false;
		
		foreach($polices as $nom)
		{
			if ($police == false)
			{
				$police = $nom;
			}
			
			if ($nom == $police_choisie)
			{
				$police = $nom;
			}
		}
		
		// On récupère le chemin de l'image qu'on vient d'enregistrer
        $chemin_fond = "uploads/" . $fichier;
        
        // On utilise getimagesize pour lire sa taille réelle
        $dimensions = getimagesize($chemin_fond);
        $largeur_fond = $dimensions[0];
        $hauteur_fond = $dimensions[1];
        // On modifie l'appel pour envoyer $largeur_fond et $hauteur_fond à la fonction
        $resultat = genererGif($chemin_fond, $texte, $logo, $largeur_fond, $hauteur_fond, $police, $couleur, $position_texte, $position_logo);
		
		
		//rediriger("index.php", "view=Formulaire&msg=" . urlencode("Texte et image bien reçu."));
		
		//$resultat = genererGif("uploads/" .$fichier, $texte, $logo, $police, $couleur, $position_texte, $position_logo);
		if (!$resultat)
		{
			rediriger("index.php", "view=Formulaire&msg=" . urlencode("La génération a échoué."));
		}
		// si on a bien genere le gif :
		rediriger("index.php", "view=Formulaire&gif=" . $resultat . "&msg=" .urlencode("GIF généré."));
		break;
		
		
		
	}
}

rediriger("index.php");
?>
