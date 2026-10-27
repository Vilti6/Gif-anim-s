<?php

// fichier projets.php

// empêche l'accès direct au fichier sans passer par index.php

if (basename($_SERVER["PHP_SELF"]) != "index.php")
{
	header("Location:../index.php?view=accueil");
	die("");
}

echo "<h1 id=\"titre-projets\">Les projets</h1>";

// pour le nombre de projets
$aucun = true;

foreach (listerProjets(utilisateurCourant()) as $p)
{
	// on affiche à quoi ressemble le gif si il existe encore
	$id = $p['id'];
	
	$aucun = false;
	
	if (file_exists("gifs/" . $id. ".gif"))
	{
		echo "<img class=\"miniature-gif\" src=\"gifs/$id.gif\" height=\"50\" alt= \"GIF\"/>";
	}
	
	echo "<p class=\"nom-projet\">" . htmlspecialchars($p['nom']) . "</p>";
	echo "<a class=\"bouton-lien\" href=\"index.php?view=Gif&id=".$id ."\">Voir</a>";
	echo "<br />";
	echo "<a class=\"bouton-lien\" href=\"index.php?view=Formulaire&projet=" . $id."\">Reprendre</a>";
	echo "<br />";
	echo "<br />";

}


if ($aucun)
{
	echo "<p class=\"message-info\">Aucun projet pour le moment.</p>";
}
?>