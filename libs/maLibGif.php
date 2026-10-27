<?php

/*
Fichier : maLibGif.php

*/

// ouvre une image png, jpeg ou gif
function ouvrirImage($chemin)
{
	$type = getimagesize($chemin);
	
	if ($type['mime'] == "image/gif")
	{
		return imagecreatefromgif($chemin);
	}
	else if ($type['mime'] == "image/jpeg")
	{
		return imagecreatefromjpeg($chemin);
	}
	else if ($type['mime'] == "image/png")
	{
		return imagecreatefrompng($chemin);
	}
	
	return false;
}

// cree une frame qui contient une copie de l'image de fond


function creerFrame($fond, $largeur, $hauteur)
{
	$frame = imagecreatetruecolor($largeur, $hauteur);
	
	// on recupere la taille de l'image de base pour la redimensionner dans la frame
	$largeur_fond = imagesx($fond); //imagesx renvoie la largeur de l'image
	$hauteur_fond = imagesy($fond); //imagesy renvoie la hauteur de l'image
	
	imagecopyresized($frame, $fond, 0, 0, 0, 0, $largeur, $hauteur, $largeur_fond, $hauteur_fond);
	return $frame;
}


// ajouter le texte sur la frame
function ajouterTexte($frame, $texte, $police, $couleur, $position_texte, $largeur, $hauteur, $decalage_x, $decalage_y, $alpha)
{
	// on calcule la taille du texte
	$taille = $hauteur/10; // imagettftext accepte les float pour la taille, pas de souci ici
	
	// imagettftext donne la taille du texte et renvoie un tableau
	// array imagettftext ( resource $image , float $size , float $angle , int $x , int $y , int $color , string $fontfile , string $text )
	
	// renvoie un tableau de 8 valeurs qui correspondent aux coordonnees des 4 coins du rectangle englobant le texte, en partant du coin inferieur gauche
	$mesure = imagettfbbox($taille, 0, $police , $texte);
	
	// on retrouve la largeur  qui correspond à la distance entre le cote gauche et le cote droit du texte
	$largeur_texte = $mesure[2] - $mesure[0];
	
	// la hauteur est coin supérieur à gauche - coin inférieur gauche
	$hauteur_texte = $mesure[1] - $mesure[7];
	
	// CORRECTION ICI : on force en entier (int)
	// on calcule les coordonnées du texte en x : largeur de l'image - largeur du texte /2
	// par defaut le texte est au centre
	$x = (int)(($largeur - $largeur_texte) /2);
	
	// meme chose en y
	$y = (int)(($hauteur + $hauteur_texte)/2);
	
	if ($position_texte == "haut")
	{
		$y = (int)($hauteur_texte + $hauteur / 20);
	}
	else if ($position_texte == "bas")
	{
		$y = (int)($hauteur - $hauteur / 20);
	}
	else if ($position_texte == "gauche")
	{
		$x = (int)($largeur / 20);
	}
	else if ($position_texte == "droite")
	{
		$x = (int)($largeur - $largeur_texte - $largeur/20);
	}
	
	//on ajoute le decalage pour le defilement 
	$x = (int)($x + $decalage_x);
	$y = (int)($y + $decalage_y);
	
	// pour la couleur du texte 
	$color = couleurTexte($frame, $couleur, $alpha);
	imagettftext($frame, $taille, 0, $x, $y, $color, $police, $texte);
}

// ajoute le logo sur une frame 
function ajouterLogo($frame, $chemin_logo, $position_logo, $largeur, $hauteur)
{
	$logo = ouvrirImage($chemin_logo);
	if (!$logo)
	{
		return false;
	}
	$specs = getimagesize($chemin_logo);
	
	// CORRECTION ICI : on force en entier (int)
	$largeur_logo = (int)($largeur /6);
	// pour la hauteur
	$hauteur_logo = (int)($largeur_logo * $specs[1] / $specs[0]);
	
	switch ($position_logo)
	{
		case "haut-gauche":
			$x1 = 0;
			$y1 = 0;
			break;
            
        case "haut-droite":
            $x1 = (int)($largeur - $largeur_logo);
            $y1 = 0;
            break;
            
        case "bas-gauche":
            $x1 = 0;
            $y1 = (int)($hauteur - $hauteur_logo);
            break;
            
        case "bas-droite":
            $x1 = (int)($largeur - $largeur_logo);
            $y1 = (int)($hauteur - $hauteur_logo);
            break;
    }
	
	imagecopyresized($frame, $logo, $x1, $y1, 0, 0, $largeur_logo, $hauteur_logo, $specs[0], $specs[1]);
	imagedestroy($logo);
	return true;
	
}
// Produit le gif anime
function genererGif($chemin_fond, $texte, $logo, $police, $couleur, $position_texte, $position_logo, $largeur_perso, $hauteur_perso, $nb_frames, $duree, $apparition, $defilement)
{
	// définir le dossier contenant les polices
	putenv('GDFONTPATH=' . realpath('fonts'));

	$fond = ouvrirImage($chemin_fond);
	
	// si le fond n'est pas du bon format on retourne faux
	if (!$fond)
	{
		return false;
	}
	
	$specs = getimagesize($chemin_fond);
	
	// si largeur ou hauteur vaut 0 on garde la taille de base de l'image
	if ($largeur_perso > 0){
		$largeur = $largeur_perso;
	} else{
		$largeur = $specs[0];
	}
	
	if ($hauteur_perso > 0) {
		$hauteur = $hauteur_perso;
	} else {
    	$hauteur = $specs[1];
	}

	// si le fond est du bon format, alors : 
	if ($nb_frames < 3)
	{
		$nb_frames = 3;
	}
	$id = uniqid();
	$frames = array();
	
	$chemins_frames = ""; // Pour stocker "frames/id_1.gif frames/id_2.gif..."
	
	// frame 1 : le fond seul
	//frames 2 à nb_frames-1 : le texte qui arrive
	//dernière frame : le texte à sa place + le logo
	$nb_texte = $nb_frames - 2;

	for ($i = 1; $i <= $nb_frames; $i++)
	{
		$frame = creerFrame($fond, $largeur, $hauteur);

		// à partir de la frame 2 : le texte
		if ($i >= 2)
		{
			// avancement de 0 à 1
			// 1 sur la dernière frame
			if ($i == $nb_frames)
			{
				$avancement = 1;
			}
			else
			{
				$avancement = ($i - 1) / $nb_texte;
			}

			// ce qui reste à parcourir : 1 au début, 0 quand le texte est à sa place
			$reste = 1 - $avancement;
			$decalage_x = 0;
			$decalage_y = 0;
			$alpha = 0;

			// sens de défilement du texte
			if ($defilement == "gauche-droite")
			{
				$decalage_x = -(int)($reste * $largeur);
			}
			else if ($defilement == "droite-gauche")
			{
				$decalage_x = (int)($reste * $largeur);
			}
			else if ($defilement == "bas-haut")
			{
				$decalage_y = (int)($reste * $hauteur);
			}
			else if ($defilement == "haut-bas")
			{
				$decalage_y = -(int)($reste * $hauteur);
			}

			// apparition en fondu (127 = transparent, 0 = opaque)
			if ($apparition == "fondu")
			{
				$alpha = (int)(127 * $reste);
			}

			// decoupage du texte selon le mode d'apparition
			$texte_a_afficher = $texte;

			if ($apparition == "lettre")
			{
				$longueur = strlen($texte);
				$nb_lettres = (int)($longueur * $avancement);
				$texte_a_afficher = substr($texte, 0, $nb_lettres);
			}
			else if ($apparition == "mot")
			{
				$longueur = strlen($texte);
				
				// on compte les espaces pour connaitre le nombre de mots
				$nb_espaces = 0;
				for ($j = 0; $j < $longueur; $j++)
				{
					if ($texte[$j] == ' ')
					{
						$nb_espaces++;
					}
				}
				$total_mots = $nb_espaces + 1;
				
				$mots_cible = (int)($total_mots * $avancement);
				
				$texte_a_afficher = "";
				$compteur_espaces = 0;
				
				if ($mots_cible > 0)
				{
					for ($j = 0; $j < $longueur; $j++)
					{
						if ($texte[$j] == ' ')
						{
							$compteur_espaces++;
						}
						
						if ($compteur_espaces == $mots_cible)
						{
							break; 
						}
						
						$texte_a_afficher = $texte_a_afficher . $texte[$j];
					}
				}
			}

			// on affiche que si on a du texte
			if ($texte_a_afficher != "")
			{
				ajouterTexte($frame, $texte_a_afficher, $police, $couleur, $position_texte, $largeur, $hauteur, $decalage_x, $decalage_y, $alpha);
			}
		}

		// le logo n'apparaît que sur la dernière frame
		if ($i == $nb_frames)
		{
			ajouterLogo($frame, "logos/" . $logo, $position_logo, $largeur, $hauteur);
		}

		$nom_fichier = "frames/" . $id . "_" . $i . ".gif";
		$frames[] = $nom_fichier;
		imagegif($frame, $nom_fichier);
		imagedestroy($frame);
		$chemins_frames = $chemins_frames . " " . $nom_fichier;
	}
			
	imagedestroy($fond);
	
	//Appel à makegif
	$commande = "./makegif -d ".$duree . " gifs/" .$id. ".gif " . $chemins_frames;
	exec($commande, $sortie, $codeRetour);
	
	//supprimer les frames dans le dossier frames
	for ($i = 1; $i <= $nb_frames; $i++){
		$fichier_supprimer = "frames/".$id. "_" . $i . ".gif";
		if (file_exists($fichier_supprimer)){
			unlink($fichier_supprimer);
		}
	}
	
	//afficher
	if ($codeRetour == 0){
		return $id;
	}
	else{
		return false;
	}
}

// renvoie le tableau des noms de fichiers dans le dossier logos
function listerLogos()
{
	$logos = array();
	// on lit le dossier logos
	$fp = opendir("logos");
	
	while($nom = readdir($fp))
	{
		//$logos[] = $nom;
		if ($nom != "." && $nom != "..") 
		{
			$logos[] = $nom;
		}
	}
	closedir($fp);
	return $logos;
}

// renvoie les noms des polices dans le dossier fonts
function listerPolices()
{
	$polices = array();
	$fp = opendir("fonts");
	
	while ($nom = readdir($fp))
	{
		//$polices[] = $nom;
		if ($nom != "." && $nom != "..") {
			$polices[] = $nom;
		}
	}
	closedir($fp);
	return $polices;
}

// Renvoie la couleur correspondant au nom 
function couleurTexte($frame, $couleur, $alpha = 0)
{
	if ($couleur == "noir")
	{
		return imagecolorallocatealpha($frame, 0, 0, 0, $alpha);
	}
	else if ($couleur == "rouge")
	{
		return imagecolorallocatealpha($frame, 255, 0, 0, $alpha);
	}
	else if ($couleur == "jaune")
	{
		return imagecolorallocatealpha($frame, 255, 255, 0, $alpha);
	}
	
	// par defaut  c'est blanc
	return imagecolorallocatealpha($frame, 255, 255, 255, $alpha);
}

// renvoie l'extension (gif, jpg, png) du fichier ou false si c'est pas une image acceptee
function extensionImage($chemin)
{
	$type = getimagesize($chemin);
	if ($type)
	{
		if ($type['mime'] == "image/gif")
		{
			return "gif";
		}
		else if ($type['mime'] == "image/jpeg")
		{
			return "jpg";
		}
		else if ($type['mime'] == "image/png")
		{
			return "png";
		}
	}
	return false;
}

?>