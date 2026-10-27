<?php

// fichier gif.php

// empêche l'accès direct au fichier sans passer par index.php

if (basename($_SERVER["PHP_SELF"]) != "index.php")
{
	header("Location:../index.php?view=accueil");
	die("");
}


$id = valider("id");
$projet = chargerProjet($id, utilisateurCourant());

if (!$projet || !file_exists("gifs/" . $id . ".gif"))
{
	echo"<p class=\"message-erreur\">Le gif n'existe pas</p>";
}
else
{
	echo "<h1 id=\"titre-gif\">" . htmlspecialchars($projet['nom']) . "</h1>";
	echo "<p id=\"conteneur-image-gif\"><img id=\"image-gif-genere\" src=\"gifs/$id.gif\" alt=\"GIF animé\" /></p>";
	echo "<a id=\"bouton-telecharger\" class=\"bouton-lien\" href=\"controleur.php?action=Telecharger&id=$id\">Télécharger le GIF</a>";
}

?>