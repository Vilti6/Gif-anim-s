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
/*function creerFrame($fond, $largeur, $hauteur)
{
	$frame = imagecreate($largeur, $hauteur);
	imagecopyresized($frame, $fond, 0, 0, 0, 0, $largeur, $hauteur, $largeur, $hauteur);
	return $frame;
}*/

function creerFrame($fond, $largeur, $hauteur) {
    // On utilise strictement imagecreate(), comme vu Slide 68
    $frame = imagecreate($largeur, $hauteur);
    
    // On alloue une couleur de base (par exemple blanc) pour initialiser l'image, comme Slide 70
    $blanc = imagecolorallocate($frame, 255, 255, 255);
    imagefilledrectangle($frame, 0, 0, $largeur, $hauteur, $blanc);
    
    // On récupère les dimensions d'origine de l'image de fond
    $largeur_originale = imagesx($fond);
    $hauteur_originale = imagesy($fond);
    
    // On copie le fond sur notre frame vide en utilisant imagecopyresized, comme vu Slide 71
    imagecopyresized(
        $frame,              // Destination
        $fond,               // Source
        0, 0,                // Position X,Y de destination
        0, 0,                // Position X,Y source
        $largeur, $hauteur,  // Taille de destination
        $largeur_originale, $hauteur_originale // Taille source
    );
    
    return $frame;
}

// ajouter le texte sur la frame
function ajouterTexte($frame, $texte, $police, $couleur, $position_texte, $largeur, $hauteur)
{
	// on calcule la taille du texte
	$taille = $hauteur/10;
	
	// imagettftext donne la taille du texte et renvoie un tableau
	// array imagettftext ( resource $image , float $size , float $angle , int $x , int $y , int $color , string $fontfile , string $text )
	
	// renvoie un tableau de 8 valeurs qui correspondent aux coordonnees des 4 coins du rectangle englobant le texte, en partant du coin inferieur gauche
	$mesure = imagettfbbox($taille, 0, $police , $texte);
	
	// on retrouve la largeur  qui correspond � la distance entre le cote gauche et le cote droit du texte
	$largeur_texte = $mesure[2] - $mesure[0];
	
	// la hauteur est coin sup�rieur � gauche - coin inf�rieur gauche
	$hauteur_texte = $mesure[1] - $mesure[7];
	// on calcule les coordonn�es du texte en x : largeur de l'image - largeur du texte /2
	// par defaut le texte est au centre
	$x = ($largeur - $largeur_texte) /2;
	
	// meme chose en y
	$y = ($hauteur + $hauteur_texte)/2;
	
	
	
	if ($position_texte == "haut")
	{
		$y = ($hauteur_texte + $hauteur / 20);
	}
	else if ($position_texte == "bas")
	{
		$y = ($hauteur - $hauteur / 20);
	}
	else if ($position_texte == "gauche")
	{
		$x = ($largeur / 20);
	}
	else if ($position_texte == "droite")
	{
		$x = ($largeur - $largeur_texte - $largeur/20);
	}
	
	
	// pour la couleur du texte 
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
	
	$largeur_logo = $largeur /6;
	// pour la hauteur
	$hauteur_logo = $largeur_logo * $specs[1] / $specs[0];
	
	switch ($position_logo)
	{
		case "haut-gauche":
			$x1 = 0;
            $y1 = 0;
            break;
            
        case "haut-droite":
            $x1 = $largeur - $largeur_logo;
            $y1 = 0;
            break;
            
        case "bas-gauche":
            $x1 = 0;
            $y1 = $hauteur - $hauteur_logo;
            break;
            
        case "bas-droite":
            $x1 = $largeur - $largeur_logo;
            $y1 = $hauteur - $hauteur_logo;
            break;
    }
	
	imagecopyresized($frame, $logo, $x1, $y1, 0, 0, $largeur_logo, $hauteur_logo, $specs[0], $specs[1]);
	imagedestroy($logo);
	return true;
	
}
// Produit le gif anime
function genererGif($chemin_fond, $texte, $logo, $police, $couleur, $position_texte, $position_logo)
{
	// d�finir le dossier contenant les polices
	putenv('GDFONTPATH=' . realpath('fonts'));

	
	// la taille des frames et la meme que la taille de l'imae de fond
	$specs = getimagesize($chemin_fond);
	$largeur = $specs[0];
	$hauteur = $specs[1];
	
	$fond = ouvrirImage($chemin_fond);
	
	// si le fond n'est pas du bon format on retourne faux
	if (!$fond)
	{
		return false;
	}
	
	// si le fond est du bon format, alors : 
	$id = uniqid();
	$frames = array();
	
	// Frame 1, que le fond
	$frame = creerFrame($fond, $largeur, $hauteur);
	// on range dans le tableau frames la premiere frame qui est le fond
	$frames[0] = "frames/" . $id. "_1.gif";
	imagegif($frame, $frames[0]);
	
	imagedestroy($frame);
	
	// frame 2, le fond et le texte 
	$frame = creerFrame($fond, $largeur, $hauteur);
	ajouterTexte($frame, $texte, $police, $couleur, $position_texte, $largeur, $hauteur);
	$frames[1] = "frames/" . $id. "_2.gif";
	imagegif($frame, $frames[1]);
	imagedestroy($frame);
	
	//frame 3, le fond, le texte et le logo 
	$frame = creerFrame($fond, $largeur, $hauteur);
	ajouterTexte($frame, $texte, $police, $couleur, $position_texte, $largeur, $hauteur);
	ajouterLogo($frame, "logos/". $logo, $position_logo, $largeur, $hauteur);
	$frames[2] = "frames/" . $id. "_3.gif";
	imagegif($frame, $frames[2]);
	imagedestroy($frame);
	
	imagedestroy($fond);
	
	//Appel � makegif
	$commande = "./makegif -d 100 gifs/" .$id. ".gif " . $frames[0]. " " . $frames[1]. " " . $frames[2];
	exec($commande, $sortie, $codeRetour);
	
	//supprimer les frames dans le dossier frames
	for ($i = 1; $i <= 3; $i++){
		$fichier_supprimer = "frames/".$id. "_" . $i . ".gif";
		if (file_exists($fichier_supprimer)){
			unlink($fichier_supprimer);
		}
	}
	
	//afficher
	if ($codeRetour == 0){
		return $id . ".gif";
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
		if ($nom != "." && $nom != "..") {
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
