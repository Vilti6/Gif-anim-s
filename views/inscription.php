<?php
if (basename($_SERVER["PHP_SELF"]) != "index.php") {
	header("Location:../index.php?view=Accueil");
	die("");
}
?>
<div id="boite-inscription" class="boite">
    <h1 id="titre-inscription">Créer un compte</h1>

    <?php 
		if ($msg = valider("msg")) { 
        echo "<p class=\"message-erreur\">$msg</p>"; 
		} 

		mkForm("controleur.php", "POST");
    
    	mkInput("hidden", "action", "Inscription");
    
    	echo "<label class=\"etiquette\">Pseudo : </label>";
    	mkInput("text", "pseudo");
    	echo "<br />";
    	echo "<br />";
    
    	echo "<label class=\"etiquette\">Mot de passe : </label>";
    	mkInput("password", "passe");
    	echo "<br />";
    	echo "<br />";
    
    	mkInput("submit", "bouton_inscription", "S'inscrire");
    
    	endForm();
    ?>
	<br />
    <p class="texte-lien">Déjà un compte ? <?php mkLien("index.php", "Se connecter ici", "view=Connexion"); ?>.</p>
</div>