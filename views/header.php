<link rel="stylesheet" href="./css/style.css">

<h1 id="titre-principal">Générateur de GIF Animés</h1>
<body>
	<div id="menu-navigation">
		<a class="lien-navigation" href="index.php?view=Accueil">Accueil</a> 
		<a class="lien-navigation" href="index.php?view=Formulaire">Créer un GIF</a>
		<a class="lien-navigation" href="index.php?view=Projets">Voir les projets</a>
		<?php
			if (isset($_SESSION['idUser'])) {
                echo "<Sspan id=\"message-bienvenue\">Bonjour " . htmlspecialchars($_SESSION['pseudo']) . " !</span> | ";
                mkLien("controleur.php", "Se déconnecter", "action=Deconnexion");
            } else {
                mkLien("index.php", "Connexion", "view=Connexion");
                echo " | ";
                mkLien("index.php", "Inscription", "view=Inscription");
            }
            ?>
	</div>
	<hr class="separateur" />