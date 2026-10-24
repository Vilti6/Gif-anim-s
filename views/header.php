<h1>Générateur de GIF Animés</h1>
<body>
	<div>
		<a href="index.php?view=Accueil">Accueil</a> 
		<a href="index.php?view=Formulaire">Créer un GIF</a>
		<a href="index.php?view=Projets">Voir les projets</a>
		<?php
			if (isset($_SESSION['idUser'])) {
                echo "<Sspan>Bonjour " . htmlspecialchars($_SESSION['pseudo']) . " !</span> | ";
                mkLien("controleur.php", "Se déconnecter", "action=Deconnexion");
            } else {
                mkLien("index.php", "Connexion", "view=Connexion");
                echo " | ";
                mkLien("index.php", "Inscription", "view=Inscription");
            }
            ?>
	</div>
	<hr />
