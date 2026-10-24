<?php
/*
Fichier : maLibSecurisation.php
*/

include_once "libs/modele.php";

// Renvoie l'ID de l'utilisateur connecté, sinon false
function utilisateurCourant()
{
	if (isset($_SESSION['idUser'])) {
        return $_SESSION['idUser'];
    }
    return false;
}

// Fonction qui bloque l'accès si on n'est pas connecté
function requireConnexion()
{
    if (!isset($_SESSION['idUser'])) {
        rediriger("index.php", "view=Connexion&msg=" . urlencode("Vous devez être connecté pour accéder à cette page."));
    }
}
?>
