<?php
if (basename($_SERVER["PHP_SELF"]) != "index.php") {
	header("Location:../index.php?view=Accueil");
	die("");
}
?>
<div>
    <h1>Créer un compte</h1>

    <?php 
		if ($msg = valider("msg")) { 
        echo "<p>$msg</p>"; 
		} 

		mkForm("controleur.php", "POST");
    
    	mkInput("hidden", "action", "Inscription");
    
    	echo "<label>Pseudo : </label>";
    	mkInput("text", "pseudo");
    	echo "<br />";
    	echo "<br />";
    
    	echo "<label>Mot de passe : </label>";
    	mkInput("password", "passe");
    	echo "<br />";
    	echo "<br />";
    
    	mkInput("submit", "bouton_inscription", "S'inscrire");
    
    	endForm();
    ?>
	<br />
    <p>Déjà un compte ? <?php mkLien("index.php", "Se connecter ici", "view=Connexion"); ?>.</p>
</div>
