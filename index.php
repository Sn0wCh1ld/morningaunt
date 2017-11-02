<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <style>
            html
            {
                font-family: Avant Garde,Avantgarde,Century Gothic,CenturyGothic,AppleGothic,sans-serif;
            }
            
            .contenant 
            {
                height: 200px;
                width: 400px;

                position: fixed;
                top: 40%;
                left: 50%;
                margin-top: -100px;
                margin-left: -200px;
            }
    
            body 
            {
                background-image: url("http://rs529.pbsrc.com/albums/dd337/shan2802/SilverStar.gif?w=280&h=210&fit=crop");
                background-repeat: repeat-x;
                height: 100%;
                animation: background 5s cubic-bezier(1,0,0,1) infinite;
                
            }
            @keyframes background 
            {
                0% { background-color: #ccccff; }
                25% { background-color: #ccffff; }
                50% { background-color: #ffffcc; }
                75% { background-color: #ccffcc; }
                100% { background-color: #ffffff }
            }
        </style>
        
        <script src="https://code.jquery.com/jquery-3.2.1.js"></script>
        
        <script>
            $('.login').click(
                function()
                {
                // Prompt pour l'age
                var reponse = prompt("SVP entrer votre âge:");

                // If the username was entered
                if (reponse != null) 
                {
                    // Post à index.php
                    $.post("index.php", {name: reponse };
                }   
});
</script>
        
        <div class="contenant" style="text-align:center">
            <h1>BLS</h1>
            <form method="post"  action="?action">
                <label><b>Nom d'utilisateur: </b></label>
                <input type="text" placeholder="Nom d'utilisateur" name="nom" required><br><br>
                
                

                <label><b>Mot de passe: </b></label>
                <input type="password" placeholder="Mot de passe" name="mdp" required><br><br>
                
                <input type="hidden" name="act" value="run">
                
                <label><b>Age (première fois): </b></label>
                <input type="text" placeholder="Age" name="age"><br><br>
                
                <button type="submit" name="login" class="login">Se Connecter</button>
            </form>
        
            <?php
                $nomserveur = "localhost";
                $nomadmin = "root";
                $mdpadmin = "";
                $nombasededonnées = "userDB";
                
                // Se connecter au serveur
                $connection = new mysqli($nomserveur, $nomadmin, $mdpadmin, $nombasededonnées);
                
                if ($connection->connect_error) {
                    die("Échec de connection: " . $connection->connect_error);
                }
                
                // Créer une base de données si elle n'existe pas déjà
                $créerbdd = "CREATE DATABASE IF NOT EXISTS userDB";
                
                if ($connection->query($créerbdd) === TRUE)
                {
                    //echo "Une base de donnée fut créée ou existe déjà.";
                } 
                else 
                {
                    echo "Erreur de création de base de données: " . $connection->error;
                }
                
                // Créer une table de données si elle n'existe pas déjà
                $créertableau1 = "CREATE TABLE IF NOT EXISTS userTable
                            (id INT(4) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                            nom_utilisateur VARCHAR(30) NOT NULL,
                            mot_de_passe VARCHAR(1024) NOT NULL)";
        
                if (mysqli_query($connection, $créertableau1))
                {
                    //echo "La table de données userTable fut créée ou existe déjà.";
                }
                else
                {
                    echo "Erreur de création de la table de données userTable: " . mysqli_error($connection);
                }
                
                // Créer une seconde table de données si elle n'existe pas déjà
                $créertableau2 = "CREATE TABLE IF NOT EXISTS userdata
                            (nom_utilisateur VARCHAR(30) NOT NULL,
                            age INT UNSIGNED)";
        
                if (mysqli_query($connection, $créertableau2))
                {
                    //echo "La table de données userdata fut créée ou existe déjà.";
                }
                else
                {
                    echo "Erreur de création de la table de données userdata: " . mysqli_error($connection);
                }
                
                // Fonction du bouton de connexion
                if(isset($_GET['action']))
                {
                    $nom = $_POST['nom'];
                    $mdp = $_POST['mdp'];
                    $age = $_POST['age'];
                    
                    if (empty($mdp) || empty($nom))
                    {
                        // La boite de texte dit à l'utilisateur d'inscrire un nom d'utilisateur
                    }
                    else
                    {
                        $hashedmdp = hash('sha256', $mdp);
                        
                        $resultat = mysqli_query($connection, "SELECT nom_utilisateur FROM usertable WHERE nom_utilisateur = '$nom'");
                        $nombreRangées = mysqli_num_rows($resultat);
                        
                        if($nombreRangées > 0)
                        {
                            // Si l'utilisateur existe
                            vérifierLogin($connection, $nom, $hashedmdp);
                        } 
                        else 
                        {
                            // Si l'utilisateur n'existe pas
                            nouvelUtilisateur($connection, $nom, $hashedmdp, $age);
                        }
                    }
                }
                
                // Insérer les données d'un nouvel utilisateur
                function nouvelUtilisateur($connection, $nom, $hashedmdp, $age)
                {                   
                    $sql = "INSERT INTO userTable (nom_utilisateur, mot_de_passe)
                            VALUES ('$nom', '$hashedmdp')";
                    
                    if (!($age == NULL))
                    {
                        $sqlUserData = "INSERT INTO userdata (nom_utilisateur, age)
                            VALUES ('$nom', '$age')";
                        
                        if ($connection->query($sqlUserData) === TRUE)
                        {
                            echo "<br>NOUVELLES DONNÉES SAUVEGARDÉS";
                        }
                        else
                        {
                            echo "<br>ERREUR: " . $connection->error;
                        }
                    }
                    
                    
                    // Si un nouvel utilisateur est créé
                    if ($connection->query($sql) === TRUE)
                    {
                        echo "<br>NOUVELLE INFORMATION CRÉÉE";
                    }
                    else
                    {
                        echo "<br>ERREUR: " . $connection->error;
                    }
                    
                    vérifierLogin($connection, $nom, $hashedmdp);
                }
                
                // Vérifier l'information de l'utilisateur
                function vérifierLogin($connection, $nom, $hashedmdp)
                {
                    $resultat = mysqli_query($connection, "SELECT nom_utilisateur, mot_de_passe FROM usertable WHERE nom_utilisateur = '$nom' AND  mot_de_passe = '$hashedmdp'");
                
                    $nombreRangées = mysqli_num_rows($resultat);
                    
                    // Si le mot de passe correspond au nom d'utilisateur
                    if($nombreRangées > 0)
                    {
                        echo "<br>CONNECTION FONCTIONNE";
                        
                        afficherInformation($connection, $nom);
                    }
                    else
                    {
                        echo "<br>CE NOM D'UTILISATEUR EXISTE DÉJÀ OU LE MOT DE PASSE EST INCORRECT";
                    }
                }
                
                // Afficher le nom complet de l'utilisateur
                function afficherInformation($connection, $nom)
                {
                    $sql = mysqli_query($connection, "SELECT * FROM userdata WHERE nom_utilisateur = '$nom'");
                    
                    while($row = mysqli_fetch_assoc($sql)) 
                    {
                        echo "<br>Age: " . (float)$row["age"];
                    }
                }
            ?>
        </div>
    </body>
</html>
