<?php

/*
Fichier : formulaire.php
*/

// empêche l'accès direct au fichier sans passer par index.php



if (basename($_SERVER["PHP_SELF"]) != "index.php")
{
	header("Location:../index.php?view=accueil");
	die("");
}


if ($msg = valider ("msg"))
{
	echo "<p>$msg</p>";
}
// on regarde si on recoit un gif 
if ($gif = valider("gif"))
{
	echo "<p><img src=\"gifs/$gif\" alt=\"GIF animé\"/></p>";
}

$logos = listerLogos();

$polices = listerPolices();
?>

<h1>Création de GIF animé</h1>

<!-- d'après la diapo n°34 du cours RIA S1 Multimédia, il est nécessaire de mettre : enctype="multipart/form-data" pour specifier la presence d'un fichier et aussi, il est necessaire de specifier la taille maximale du fichier-->
<form enctype="multipart/form-data" action="controleur.php" method="post">
	<input type="hidden" name="MAX_FILE_SIZE" value="10000000">

	<fieldset>
		<legend>Image de fond</legend>
		<label for="image_fond">Choisir une image de fond :</label>
		<br />
		<input type="file" name="image_fond" id="image_fond"/>
	</fieldset>
	
	<br />
	
	<fieldset>
		<legend>Texte de l'animation</legend>
		<label for="texte_gif">Saisir un texte :</label>
		<br />
		<input type="text" name="texte_gif" id="texte_gif"/>
	</fieldset>
	
	<fieldset>
		<legend>Logo</legend>
		<?php
			foreach ($logos as $next)
			{
				echo "<label>";
				echo "<input type=\"radio\" name=\"logo\" value=\"$next\">";
				echo "<img src=\"logos/$next\" height=\"40\" alt=\"$next\" />";
				echo "</label> ";
				echo "<br />";
			}
		?>
		<label for="nouveau_logo">Nouveau logo :</label>
		<br />
		<input type="file" name="nouveau_logo" id="nouveau_logo"/>
	</fieldset>
	
	<br />
	
	<fieldset>
		<legend>Personnalisation</legend>
		<label for="position_logo">Position du logo :</label>
		<select name="position_logo" id="position_logo">
			<option value="haut-gauche">Haut gauche</option>
			<option value="haut-droite">Haut droite</option>
			<option value="bas-gauche">Bas gauche</option>
			<option value="bas-droite" selected>Bas droite (par défaut)</option>
		</select>
		
		<br />
		<label for="position_texte">Position du texte :</label>
		<select name="position_texte" id="position_texte">
			<option value="centre" selected>Centre (par défaut)</option>
			<option value="haut">Haut</option>
        	<option value="bas">Bas</option>
        	<option value="gauche">Gauche</option>
        	<option value="droite">Droite</option>
		</select>
		
		<br />
		
		<label for="police">Police du texte :</label>
		<select name="police" id="police">
        <?php
			foreach ($polices as $nom_police)
			{
            	// on affiche chaque police disponible sous forme d'option
				echo "<option value=\"$nom_police\">$nom_police</option>";
        	}
        ?>
		</select>
		
		<br />
		
		<label for="couleur">Couleur du texte :</label>
		<select name="couleur" id="couleur">
			<option value="blanc" selected>Blanc</option>
			<option value="noir">Noir</option>
			<option value="jaune">Jaune</option>
			<option value="rouge">Rouge</option>
		</select>

	</fieldset>
	
	<br />
	<br />
	
	<input type="submit" name="action" value="Generer">

</form>

		
