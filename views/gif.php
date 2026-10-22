<?php
if (basename($_SERVER["PHP_SELF"]) != "index.php") {
	header("Location:../index.php?view=accueil");
	die("");
}
?>

<div class="container text-center">
    <?php
    $id = valider("id");
    $projet = chargerProjet($id, utilisateurCourant());

    if (!$projet || !file_exists("gifs/" . $id . ".gif")) {
        echo "<p>Le gif n'existe pas</p>";
    } else {
        echo "<h1>" . htmlspecialchars($projet['nom']) . "</h1>";
        echo "<p><img src=\"gifs/$id.gif\" alt=\"GIF animé\" /></p>";
        echo "<br>";
        echo "<a href=\"controleur.php?action=Telecharger&id=$id\" class=\"btn\">Télécharger le GIF</a>";
    }
    ?>
</div>