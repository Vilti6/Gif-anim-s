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
	echo "<p class=\"message-info\">$msg</p>";
}
/*
// on regarde si on recoit un gif 
if ($gif = valider("gif"))
{
	echo "<p><img src=\"gifs/$gif\" alt=\"GIF animé\"/></p>";
}
*/

$logos = listerLogos();


// projet à reprendre : false si on crée un nouveau GIF, ou si le projet n'appartient pas à l'utilisateur

// liste variables pour le formulaire
$projet = valider("projet");
$projet_repris = chargerProjet($projet, utilisateurCourant());
$nom_p = parametre($projet_repris, "nom", "");
$texte_p = parametre($projet_repris, "texte", "");
$fond_p = parametre($projet_repris, "fond", "");
$logo_p = parametre($projet_repris, "logo", "");
$police_p = parametre($projet_repris, "police", "");
$couleur_p = parametre($projet_repris, "couleur", "blanc");
$position_texte_p = parametre($projet_repris, "position_texte", "centre");
$position_logo_p = parametre($projet_repris, "position_logo", "bas-droite");
$largeur_p = parametre($projet_repris, "largeur", 0);
$hauteur_p = parametre($projet_repris, "hauteur", 0);
$nb_frames_p = parametre($projet_repris, "nb_frames", 3);
$duree_p = parametre($projet_repris, "duree", 100);
$apparition_p = parametre($projet_repris, "apparition", "coup");
$defilement_p = parametre($projet_repris, "defilement", "aucun");

$tabApparition = array(
    array("val" => "coup", "label" => "D'un coup (par défaut)"),
    array("val" => "fondu", "label" => "Fondu (Fade in)"),
    array("val" => "lettre", "label" => "Lettre par lettre"),
    array("val" => "mot", "label" => "Mot par mot")
);

$tabDefilement = array(
    array("val" => "aucun", "label" => "Aucun (Fixe)"),
    array("val" => "gauche-droite", "label" => "De gauche à droite"),
    array("val" => "droite-gauche", "label" => "De droite à gauche"),
    array("val" => "bas-haut", "label" => "De bas en haut"),
    array("val" => "haut-bas", "label" => "De haut en bas")
);

$polices = listerPolices();
?>

<h1 id="titre-formulaire">Création de GIF animé</h1>


<form id="formulaire-gif" class="boite" enctype="multipart/form-data" action="controleur.php" method="post">
	<?php

		echo "<input type=\"hidden\" name=\"projet\" value=\"" . htmlspecialchars($projet) . "\" />";

	?>
	<input type="hidden" name="MAX_FILE_SIZE" value="10000000">

	<fieldset class="groupe-champs">
		<legend class="titre-groupe">Image de fond</legend>
		<label class="etiquette" for="image_fond">Choisir une image de fond :</label>
		<br />
		<input class="champ-fichier" type="file" name="image_fond" id="image_fond"/>
		<?php
			if ($fond_p != "" && file_exists("uploads/" . $fond_p))
			{
				echo "<img class=\"image-actuelle\" src=\"uploads/$fond_p\"height=\"60\"alt=\"Image\" /> Image actuelle";
			}
		?>
	</fieldset>
	
	<br />
	
	<fieldset class="groupe-champs">
		<legend class="titre-groupe">Texte de l'animation</legend>
		<label class="etiquette" for="texte_gif">Saisir un texte :</label>
		<br />
		<?php
			echo "<input class=\"champ-texte\" type=\"text\" name=\"texte_gif\" value=\"" . htmlspecialchars($texte_p) . "\" />";
		?>
	</fieldset>
	
	<fieldset class="groupe-champs">
		<legend class="titre-groupe">Logo</legend>
		<?php	
			foreach ($logos as $next)
			{
				$actuel = "";
				if ($next == $logo_p)
				{
					$actuel = "checked";
				}
				echo "<label class=\"etiquette-radio\">";
				echo "<input class=\"champ-radio\" type=\"radio\" name=\"logo\" value=\"$next\" $actuel>";
				echo "<img class=\"logo-radio\" src=\"logos/$next\" height=\"40\"alt=\"$next\"/>";
				echo "</label> ";
				echo "<br />";
			}
		?>
		<label class="etiquette" for="nouveau_logo">Nouveau logo :</label>
		<br />
		<input class="champ-fichier" type="file" name="nouveau_logo" id="nouveau_logo"/>
	</fieldset>
	
	<br />
	
	<fieldset class="groupe-champs">
		<legend class="titre-groupe">Personnalisation</legend>
		<label class="etiquette" for="position_logo">Position du logo :</label>
		<select class="champ-select" name="position_logo" id="position_logo">
			<option value="haut-gauche"
			<?php 
				if ($position_logo_p == "haut-gauche")
				{ 
					echo "selected"; 
				} 
			?>
			>Haut gauche</option>
			<option value="haut-droite" 
			<?php 
				if ($position_logo_p =="haut-droite")
				{
					echo "selected"; 
				} 
			?>
			>Haut droite</option>
			<option value="bas-gauche" 
			<?php 
				if ($position_logo_p == "bas-gauche") 
				{ 
					echo "selected"; 
				} 
			?>
			>Bas gauche</option>
			<option value="bas-droite"
			<?php 
				if ($position_logo_p=="bas-droite") 
				{ 
					echo "selected"; 
				} 
			?>
			>Bas droite (par défaut)</option>
		</select>
		
		<br />
		<label class="etiquette" for="position_texte">Position du texte :</label>
		<select class="champ-select" name="position_texte" id="position_texte">
			<option value="centre"
			<?php 
				if ($position_texte_p == "centre")
				{ 
					echo "selected"; 
				} 
			?>
			>Centre (par défaut)</option>
			<option value="haut" 
			<?php 
				if ($position_texte_p =="haut")
				{
					echo "selected"; 
				} 
			?>
			>Haut</option>
			<option value="bas" 
			<?php 
				if ($position_texte_p == "bas") 
				{ 
					echo "selected"; 
				} 
			?>
			>Bas</option>
			<option value="gauche"
			<?php 
				if ($position_texte_p=="gauche") 
				{ 
					echo "selected"; 
				} 
			?>
			>Gauche</option>
			<option value="droite"
			<?php 
				if ($position_texte_p=="droite") 
				{ 
					echo "selected"; 
				} 
			?>
			>droite</option>
		</select>
		
		<br />
		
		<label class="etiquette" for="police">Police du texte :</label>
		<select class="champ-select" name="police" id="police">
        <?php
        	
        	foreach (listerPolices() as $nom_police)
			{
				$select = "";
				if ($nom_police == $police_p)
				{
					$select = "selected";
				}
				// on affiche chaque police disponible
				echo "<option value=\"$nom_police\" $select>$nom_police</option>";
			}
        ?>
		</select>
		
		<br />
		
		<label class="etiquette" for="couleur">Couleur du texte :</label>
		<select class="champ-select" name="couleur" id="couleur">
			<option value="blanc"
			<?php 
				if ($couleur_p == "blanc")
				{ 
					echo "selected"; 
				} 
			?>
			>Blanc (par défaut)</option>
			<option value="noir" 
			<?php 
				if ($couleur_p =="noir")
				{
					echo "selected"; 
				} 
			?>
			>Noir</option>
			<option value="jaune"
			<?php 
				if ($couleur_p=="jaune") 
				{ 
					echo "selected"; 
				} 
			?>
			>Jaune</option>
			<option value="rouge"
			<?php 
				if ($couleur_p=="rouge") 
				{ 
					echo "selected"; 
				} 
			?>
			>Rouge</option>
		</select>

	</fieldset>
	<br />
	<fieldset class="groupe-champs">
		<legend class="titre-groupe">Animation du texte</legend>
		<label class="etiquette" for="apparition">Mode d'apparition :</label>
		<?php 
			mkSelect("apparition\" id=\"apparition\" class=\"champ-select", $tabApparition, "val", "label", $apparition_p); 
		?>
		<br />
		
		<label class="etiquette" for="defilement">Sens de défilement :</label>
		<?php 
			mkSelect("defilement\" id=\"defilement\" class=\"champ-select", $tabDefilement, "val", "label", $defilement_p); 
		?>
	</fieldset>
	<br />
	<fieldset class="groupe-champs">
        <legend class="titre-groupe">Paramètres de l'animation</legend>
        <label class="etiquette" for="largeur">Largeur (0 = taille de l'image de fond) :</label>
        <?php 
        	mkInput("number", "largeur", $largeur_p, "id=\"largeur\" class=\"champ-nombre\" min=\"0\""); 
        ?>
        <br />

        <label class="etiquette" for="hauteur">Hauteur (0 = taille de l'image de fond) :</label>
        <?php 
        	mkInput("number", "hauteur", $hauteur_p, "id=\"hauteur\" class=\"champ-nombre\" min=\"0\""); 
        ?>
        <label class="etiquette" for="nb_frames">Nombre d'images (frames) :</label>
        <?php 
        	mkInput("number", "nb_frames", $nb_frames_p, "id=\"nb_frames\" class=\"champ-nombre\" min=\"2\" max=\"50\""); 
        ?>
        <br />

        <label class="etiquette" for="duree">Durée d'une frame (ms) :</label>
        <?php 
        	mkInput("number", "duree", $duree_p, "id=\"duree\" class=\"champ-nombre\" min=\"10\" step=\"10\""); 
        	?> <!--step pour des sauts de 10 en 10-->
    </fieldset>
	<br />
	<fieldset class="groupe-champs">
		<legend class="titre-groupe">Projet</legend>
			<label class="etiquette" for="nom_projet">Nom du projet :</label>
			<?php
				echo "<input class=\"champ-texte\" type=\"text\" name=\"nom_projet\" id=\"nom_projet\" value=\"" . htmlspecialchars($nom_p) . "\"/>";
				 
			?>
	</fieldset>

	<br />
	<br />
	
	<input class="bouton-valider" type="submit" name="action" value="Générer">

</form>