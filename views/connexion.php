<?php
if (basename($_SERVER["PHP_SELF"]) != "index.php") {
	header("Location:../index.php?view=Accueil");
	die("");
}
?>
<div class="container form-container">
    <h1 class="text-center">Se connecter</h1>

    <?php 
    if ($msg = valider("msg")) { 
        echo "<div class=\"msg-box\"><strong>$msg</strong></div>"; 
    } 

    mkForm("controleur.php", "POST");
    
    mkInput("hidden", "action", "Connexion");
    
    echo "<div class=\"form-group\">\n<label>Pseudo : </label>";
    mkInput("text", "pseudo");
    echo "</div>\n";
    
    echo "<div class=\"form-group\">\n<label>Mot de passe : </label>";
    mkInput("password", "passe");
    echo "</div>\n";
    
    mkInput("submit", "bouton_connexion", "Connexion", "class=\"btn btn-full\"");
    
    endForm();
    ?>
    
    <p class="text-center mt-20">Pas encore de compte ? <?php mkLien("index.php", "S'inscrire ici", "view=Inscription"); ?>.</p>
</div>