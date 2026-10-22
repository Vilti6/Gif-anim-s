<?php
if (basename($_SERVER["PHP_SELF"]) != "index.php") {
	header("Location:../index.php?view=accueil");
	die("");
}
?>

<div class="container">
    <h1 class="text-center">Les projets</h1>

    <?php
    $aucun = true;

    foreach (listerProjets(utilisateurCourant()) as $p) {
        $id = $p['id'];
        $aucun = false;
        
        echo "<div class=\"project-item\">\n";
        
        if (file_exists("gifs/" . $id. ".gif")) {
            echo "<img src=\"gifs/$id.gif\" height=\"60\" alt=\"GIF\"/>\n";
        }
        
        echo "<div class=\"project-info\">\n";
        echo "<p><strong>" . htmlspecialchars($p['nom']) . "</strong></p>\n";
        echo "<div class=\"project-actions\">\n";
        echo "<a href=\"index.php?view=Gif&id=".$id ."\">Voir</a>\n";
        echo "<a href=\"index.php?view=Formulaire&projet=" . $id."\">Reprendre</a>\n";
        echo "</div>\n";
        echo "</div>\n";
        
        echo "</div>\n";
    }

    if ($aucun) {
        echo "<p class=\"text-center\">Aucun projet pour le moment.</p>";
    }
    ?>
</div>