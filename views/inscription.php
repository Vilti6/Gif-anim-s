<?php
if (basename($_SERVER["PHP_SELF"]) != "index.php") {
	header("Location:../index.php?view=Accueil");
	die("");
}
?>
<div class="container form-container">
    <h1 class="text-center">Créer un compte</h1>

    <?php 
    if ($msg = valider("msg")) { 
        echo "<div class=\"msg-box\"><strong>$msg</strong></div>"; 
    } 

    mkForm("controleur.php", "POST");
    
    mkInput("hidden", "action", "Inscription");
    
    echo "<div class=\"form-group\">\n<label>Pseudo : </label>";
    mkInput("text", "pseudo");
    echo "</div>\n";
    
    echo "<div class=\"form-group\">\n<label>Mot de passe : </label>";
    mkInput("password", "passe");
    echo "</div>\n";
    
    mkInput("submit", "bouton_inscription", "S'inscrire", "class=\"btn btn-full\"");
    
    endForm();
    ?>

    <p class="text-center mt-20">Déjà un compte ? <?php mkLien("index.php", "Se connecter ici", "view=Connexion"); ?>.</p>
</div>