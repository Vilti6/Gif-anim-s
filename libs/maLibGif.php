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
	imagecopyresized($frame, $fond, 0, 0, 0, 0, $largeur, $hauteur, $largeur, $hauteur);
	return $frame;
}


function ajouterTexte($frame, $texte, $police, $couleur, $position_texte, $largeur, $hauteur)
{
	// On force un minimum de 1 pour la taille de la police
	$taille = max(1, (int)($hauteur / 10));
	
	$mesure = imagettfbbox($taille, 0, $police , $texte);
	
	$largeur_texte = $mesure[2] - $mesure[0];
	$hauteur_texte = $mesure[1] - $mesure[7];
	
	$x = (int)(($largeur - $largeur_texte) / 2);
	$y = (int)(($hauteur + $hauteur_texte) / 2);
	
	if ($position_texte == "haut")
	{
		$y = (int)($hauteur_texte + ($hauteur / 20));
	}
	else if ($position_texte == "bas")
	{
		$y = (int)($hauteur - ($hauteur / 20));
	}
	else if ($position_texte == "gauche")
	{
		$x = (int)($largeur / 20);
	}
	else if ($position_texte == "droite")
	{
		$x = (int)($largeur - $largeur_texte - ($largeur / 20));
	}
	
	$color = couleurTexte($frame, $couleur);
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
	
	// On force un minimum de 1 pixel pour éviter l'erreur de hauteur/largeur à 0
	$largeur_logo = max(1, (int)($largeur / 6));
	$hauteur_logo = max(1, (int)($largeur_logo * $specs[1] / $specs[0]));
	
	$x1 = 0;
	$y1 = 0;
	
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
function genererGif($chemin_fond, $texte, $logo, $police, $couleur, $position_texte, $position_logo, $largeur_perso, $hauteur_perso, $nb_frames, $duree)
{
	putenv('GDFONTPATH=' . realpath('fonts'));

	$fond = ouvrirImage($chemin_fond);
	if (!$fond) return false;
	
	$specs = getimagesize($chemin_fond);
	
	// Si largeur et hauteur sont à 0, on garde la taille de l'image de fond
	$largeur = ($largeur_perso > 0) ? $largeur_perso : $specs[0];
	$hauteur = ($hauteur_perso > 0) ? $hauteur_perso : $specs[1];
	
	$id = uniqid();
	$frames = array();
	$chemins_frames_pour_commande = ""; // Pour stocker "frames/id_1.gif frames/id_2.gif..."
	
	for ($i = 1; $i <= $nb_frames; $i++) {
		
		$frame = creerFrame($fond, $largeur, $hauteur);
		
		ajouterTexte($frame, $texte, $police, $couleur, $position_texte, $largeur, $hauteur);
		ajouterLogo($frame, "logos/". $logo, $position_logo, $largeur, $hauteur);
		
		$nom_fichier = "frames/" . $id . "_" . $i . ".gif";
		$frames[] = $nom_fichier; // On stocke pour pouvoir les supprimer plus tard
		
		imagegif($frame, $nom_fichier);
		imagedestroy($frame);
		
		// On ajoute ce fichier à la chaîne de texte qui servira de commande
		$chemins_frames_pour_commande .= " " . $nom_fichier;
	}
	
	imagedestroy($fond);
	
	$commande = "./makegif -d $duree gifs/" .$id. ".gif" . $chemins_frames_pour_commande;
	exec($commande, $sortie, $codeRetour);
	
	// on supprime toutes les frames du dossier
	foreach ($frames as $f) {
		if (file_exists($f)) {
			unlink($f);
		}
	}
	
	if ($codeRetour == 0) {
		return $id;
	} else {
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
function couleurTexte($frame, $couleur)
{
	if ($couleur == "noir")
	{
		return imagecolorallocate($frame, 0, 0, 0);
	}
	else if ($couleur == "rouge")
	{
		return imagecolorallocate($frame, 255, 0, 0);
	}
	else if ($couleur == "jaune")
	{
		return imagecolorallocate($frame, 255, 255, 0);
	}
	else if ($couleur == "blanc")
	{
		return imagecolorallocate($frame, 255, 255, 255);
	}
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
