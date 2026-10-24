<div>
    <h1>Bienvenue sur le Générateur de GIF Animés</h1>
    
    <p>Cette application vous permet de créer automatiquement des GIF animés à partir d'une image de fond, d'un texte et d'un logo.</p>
    
	<h2>Comment ça marche ?</h2>
	<ul>
		<li>1. Choisissez un fond : Uploadez votre propre image de fond.</li>
        <li>2. Ajoutez votre texte : Saisissez un message, choisissez sa police, sa couleur et paramétrez son animation (apparition, défilement).</li>
        <li>3. Intégrez un logo : Sélectionnez un logo existant ou uploadez le vôtre et positionnez-le où vous le souhaitez.</li>
        <li>4. Paramétrez l'animation : Définissez les dimensions, le nombre de frames et la vitesse de l'animation.</li>
    </ul>
    
    <br />

    <?php 
    	if (!isset($_SESSION['idUser']))
    	{
    	echo"<p>Pour commencer à créer et sauvegarder vos projets, veuillez vous connecter ou créer un compte.</p>";
    	} else {
    		echo "<p>Vous êtes connecté. Rendez-vous dans l'onglet \"Créer un GIF\" pour démarrer !</p>";
    	}
    ?>
		
    
</div>
