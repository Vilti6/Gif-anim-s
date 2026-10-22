<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Générateur de GIF Animés</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div id="header">
        <h1>Générateur de GIF Animés</h1>
        <h2>GIF Maker - IG2I</h2>
        <div id="nav">
            <?php
            mkLien("index.php", "Accueil", "view=Accueil");
            echo " | ";
            mkLien("index.php", "Créer un GIF", "view=Formulaire");
            echo " | ";
            mkLien("index.php", "Voir les projets", "view=Projets");
            echo " | ";

            if (isset($_SESSION['idUser'])) {
                echo '<span>Bonjour ' . htmlspecialchars($_SESSION['pseudo']) . ' !</span> | ';
                mkLien("controleur.php", "Se déconnecter", "action=Deconnexion");
            } else {
                mkLien("index.php", "Connexion", "view=Connexion");
                echo " | ";
                mkLien("index.php", "Inscription", "view=Inscription");
            }
            ?>
        </div>
    </div>
    
    <!-- Début du contenu principal qui sera fermé dans footer.php -->
    <div id="main-content">