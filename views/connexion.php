<?php
if (basename($_SERVER["PHP_SELF"]) != "index.php") {
	header("Location:../index.php?view=Accueil");
	die("");
}
?>
<div id="boite-connexion" class="boite">
    <h1 id="titre-connexion">Se connecter</h1>

    <?php 
    if ($msg = valider("msg")) { 
        echo "<p class=\"message-erreur\">$msg</p>"; 
    } 

    mkForm("controleur.php", "POST");
    
    mkInput("hidden", "action", "Connexion");
        
        echo "<label class=\"etiquette\">Pseudo : ";
        mkInput("text", "pseudo");
        echo "</label>";
        echo "<br />";
        echo "<br />";
        
        echo "<label class=\"etiquette\">Mot de passe : ";
        mkInput("password", "passe");
        echo "</label>";
        echo "<br />";
        echo "<br />";
        
        mkInput("submit", "bouton_connexion", "Connexion");
    endForm();
    ?>
    
    <p class="texte-lien">Pas encore de compte ? <?php mkLien("index.php", "S'inscrire ici", "view=Inscription"); ?>.</p>
</div>