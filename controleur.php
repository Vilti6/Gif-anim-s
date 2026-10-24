<?php

/*
Fichier : controleur.php

*/

session_start();

include_once "libs/maLibUtils.php";
include_once "libs/maLibGif.php";
include_once "libs/modele.php";
include_once "libs/maLibSecurisation.php";

if ($action = valider ("action"))
{
	
	switch ($action)
	{
		case 'Générer':
			if (!utilisateurCourant()) 
			{
				rediriger("index.php", "view=Connexion&msg=" . urlencode("Vous devez être connecté pour générer un gif."));
    }
		// vérification si il y a un texte
		$texte = valider("texte_gif");

		if (!$texte)
		{
			rediriger("index.php", "view=Formulaire&msg=" . urlencode("Saisissez un texte."));
		}
		
		//vérification si il y a une image de fond
		if (isset($_FILES["image_fond"]) && is_uploaded_file($_FILES["image_fond"]["tmp_name"]))
		{
			// vérification de l'extension
			$extension = extensionImage($_FILES["image_fond"]["tmp_name"]);
			if (!$extension)
			{
				rediriger("index.php", "view=Formulaire&msg=" . urlencode("L'image n'est pas du bon type"));
			}
			// si ok alors on met l'image dans le dossier uploads
			$fichier = "image_fond" . uniqid() . "." . $extension;
			copy($_FILES["image_fond"]["tmp_name"], "uploads/" . $fichier);
		}
		else
		{
			// pas de nouvelle image : on prend celle du projet repris (s'il appartient bien a l'utilisateur)
			$projet = valider("projet");
			$projet_repris = chargerProjet($projet, utilisateurCourant());
			$fichier = parametre($projet_repris, "fond", "");
			if ($fichier == "" || !file_exists("uploads/" . $fichier))
			{
				rediriger("index.php", "view=Formulaire&msg=" . urlencode("Choisissez une image de fond."));
			}
		}
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
		

		//rediriger("index.php", "view=Formulaire&msg=" . urlencode("Texte et image bien reçu."));
		$largeur = getValue("largeur", 0);
		$hauteur = getValue("hauteur", 0);
		$nb_frames = getValue("nb_frames", 3);
		$duree = getValue("duree", 100);
		$apparition = getValue("apparition", "coup");
		$defilement = getValue("defilement", "aucun");
		
		$resultat = genererGif("uploads/" .$fichier, $texte, $logo, $police, $couleur, $position_texte, $position_logo, $largeur, $hauteur, $nb_frames, $duree, $apparition, $defilement);
		if (!$resultat)
		{
			rediriger("index.php", "view=Formulaire&msg=" . urlencode("La génération a échoué."));
		}
		
		// sauvegarde du projet dans la base : même identifiant que le GIF, rattache a l'utilisateur
		$nom_projet = getValue("nom_projet", "Sans titre");
		enregistrerProjet($resultat, utilisateurCourant(), $nom_projet, $texte, $fichier, $logo, $police, $couleur, $position_texte, $position_logo, $apparition, $defilement, $largeur, $hauteur, $nb_frames, $duree);

		// si on a bien genere le gif :
		rediriger("index.php", "view=Gif&id=" . $resultat);
		break;
		
		case 'Inscription':
            $pseudo = valider("pseudo");
            $passe = valider("passe");
            
            if ($pseudo && $passe) {
                $utilisateurExistant = getUtilisateurParPseudo($pseudo);
                
                if (!$utilisateurExistant) {
                    $idNouvelUser = creerUtilisateur($pseudo, $passe);
                    
                    if ($idNouvelUser) {
                        rediriger("index.php", "view=Connexion&msg=" . urlencode("Compte créé avec succès ! Connectez-vous."));
                    } else {
                        rediriger("index.php", "view=Inscription&msg=" . urlencode("Erreur lors de la création du compte."));
                    }
                } else {
                    rediriger("index.php", "view=Inscription&msg=" . urlencode("Ce pseudo est déjà utilisé."));
                }
            } else {
                rediriger("index.php", "view=Inscription&msg=" . urlencode("Veuillez remplir tous les champs."));
            }
        break;
        
        case 'Connexion':
            $pseudo = valider("pseudo");
            $passe = valider("passe");
            
            if ($pseudo && $passe) {
                $utilisateur = getUtilisateurParPseudo($pseudo);
                
                if ($utilisateur) {
                    if (password_verify($passe, $utilisateur['mot_de_passe'])) {
                        $_SESSION['idUser'] = $utilisateur['id'];
                        $_SESSION['pseudo'] = $utilisateur['pseudo'];
                        rediriger("index.php", "view=Accueil&msg=" . urlencode("Bienvenue " . $utilisateur['pseudo'] . " !"));
                    } else {
                        rediriger("index.php", "view=Connexion&msg=" . urlencode("Identifiants incorrects."));
                    }
                } else {
                    rediriger("index.php", "view=Connexion&msg=" . urlencode("Identifiants incorrects."));
                }
            } else {
                rediriger("index.php", "view=Connexion&msg=" . urlencode("Veuillez remplir tous les champs."));
            }
        break;
        
        case 'Deconnexion':
            session_destroy();
            rediriger("index.php", "view=Accueil");
        break;
        
        case 'Telecharger':
            $id = valider("id");
            $projet = chargerProjet($id, utilisateurCourant());
            $chemin_fichier = "gifs/" . $id . ".gif";
            
            if ($projet && file_exists($chemin_fichier))
            {
                // on indique que c'est un fichier gif et on force le telechargement avec attachment
                header("Content-Type: image/gif");
                header("Content-Disposition: attachment; filename=\"" . $id . ".gif\"");
                readfile($chemin_fichier);
                die("");
            }
            else
            {
                rediriger("index.php", "view=Projets&msg=" . urlencode("Le GIF n'existe pas."));
            }
        break;
        		
		
		
	}
}

rediriger("index.php");
?>
