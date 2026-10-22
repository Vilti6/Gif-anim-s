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

$logos = listerLogos();

// projet à reprendre : false si on crée un nouveau GIF, ou si le projet n'appartient pas à l'utilisateur
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

$tabPosLogo = array(
    array("val" => "haut-gauche", "label" => "Haut gauche"),
    array("val" => "haut-droite", "label" => "Haut droite"),
    array("val" => "bas-gauche", "label" => "Bas gauche"),
    array("val" => "bas-droite", "label" => "Bas droite (par défaut)")
);

$tabPosTexte = array(
    array("val" => "centre", "label" => "Centre (par défaut)"),
    array("val" => "haut", "label" => "Haut"),
    array("val" => "bas", "label" => "Bas"),
    array("val" => "gauche", "label" => "Gauche"),
    array("val" => "droite", "label" => "Droite")
);

$tabCouleurs = array(
    array("val" => "blanc", "label" => "Blanc (par défaut)"),
    array("val" => "noir", "label" => "Noir"),
    array("val" => "jaune", "label" => "Jaune"),
    array("val" => "rouge", "label" => "Rouge")
);

// Transformation de la liste de polices en tableau associatif pour mkSelect
$tabPolices = array();
foreach (listerPolices() as $nom_police) {
    $tabPolices[] = array("val" => $nom_police, "label" => $nom_police);
}
?>

<div class="container">
    <h1 class="text-center">Création de GIF animé</h1>

    <?php
    if ($msg = valider ("msg")) {
        echo "<div class=\"msg-box\">$msg</div>";
    }
    ?>

    <!-- CORRECTION ICI : Utilisation de la balise HTML standard pour gérer l'upload -->
    <form action="controleur.php" method="POST" enctype="multipart/form-data">
    
    <?php
    mkInput("hidden", "projet", htmlspecialchars((string)$projet));
    mkInput("hidden", "MAX_FILE_SIZE", "10000000");
    ?>

    <fieldset>
        <legend>Image de fond</legend>
        <div class="form-group">
            <label for="image_fond">Choisir une image de fond :</label>
            <?php mkInput("file", "image_fond", "", "id=\"image_fond\""); ?>
        </div>
        <?php
            if ($fond_p != "" && file_exists("uploads/" . $fond_p)) {
                echo "<div class=\"mt-20\"><img src=\"uploads/$fond_p\" height=\"60\" alt=\"Image\" /> <em>Image actuelle</em></div>";
            }
        ?>
    </fieldset>
    
    <fieldset>
        <legend>Texte de l'animation</legend>
        <div class="form-group">
            <label for="texte_gif">Saisir un texte :</label>
            <?php mkInput("text", "texte_gif", htmlspecialchars($texte_p), "id=\"texte_gif\""); ?>
        </div>
    </fieldset>
    
    <fieldset>
        <legend>Logo</legend>
        <div>
            <?php	
                foreach ($logos as $next) {
                    $actuel = ($next == $logo_p); // true ou false
                    echo "<label class=\"radio-label\">";
                    mkRadioCb("radio", "logo", $next, $actuel);
                    echo "<img src=\"logos/$next\" height=\"40\" alt=\"$next\"/>";
                    echo "</label>";
                }
            ?>
        </div>
        <div class="form-group mt-20">
            <label for="nouveau_logo">Ou uploader un nouveau logo :</label>
            <?php mkInput("file", "nouveau_logo", "", "id=\"nouveau_logo\""); ?>
        </div>
    </fieldset>
    
    <fieldset>
        <legend>Personnalisation</legend>
        <div class="form-row">
            <div class="form-group half">
                <label for="position_logo">Position du logo :</label>
                <?php 
                mkSelect("position_logo\" id=\"position_logo", $tabPosLogo, "val", "label", $position_logo_p); 
                ?>
            </div>
            
            <div class="form-group half">
                <label for="position_texte">Position du texte :</label>
                <?php mkSelect("position_texte\" id=\"position_texte", $tabPosTexte, "val", "label", $position_texte_p); ?>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group half">
                <label for="police">Police du texte :</label>
                <?php mkSelect("police\" id=\"police", $tabPolices, "val", "label", $police_p); ?>
            </div>
            
            <div class="form-group half">
                <label for="couleur">Couleur du texte :</label>
                <?php mkSelect("couleur\" id=\"couleur", $tabCouleurs, "val", "label", $couleur_p); ?>
            </div>
        </div>
    </fieldset>


	<fieldset>
        <legend>Paramètres de l'animation</legend>
        <div class="form-row">
            <div class="form-group half">
                <label for="largeur">Largeur (0 = taille de l'image de fond) :</label>
                <?php mkInput("number", "largeur", $largeur_p, "id=\"largeur\" min=\"0\""); ?>
            </div>
            <div class="form-group half">
                <label for="hauteur">Hauteur (0 = taille de l'image de fond) :</label>
                <?php mkInput("number", "hauteur", $hauteur_p, "id=\"hauteur\" min=\"0\""); ?>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group half">
                <label for="nb_frames">Nombre d'images (frames) :</label>
                <?php mkInput("number", "nb_frames", $nb_frames_p, "id=\"nb_frames\" min=\"2\" max=\"50\""); ?>
            </div>
            <div class="form-group half">
                <label for="duree">Durée d'une frame (ms) :</label>
                <?php mkInput("number", "duree", $duree_p, "id=\"duree\" min=\"10\" step=\"10\""); ?>
            </div>
        </div>
    </fieldset>


    <fieldset>
        <legend>Projet</legend>
        <div class="form-group">
            <label for="nom_projet">Nom du projet :</label>
            <?php mkInput("text", "nom_projet", htmlspecialchars($nom_p), "id=\"nom_projet\""); ?>
        </div>
    </fieldset>

    <div class="text-center mt-20">
        <?php mkInput("submit", "action", "Générer", "class=\"btn btn-large\""); ?>
    </div>

    </form>
</div>