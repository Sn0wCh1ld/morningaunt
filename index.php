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
            div 
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
        
        <div class="contenant" style="text-align:center">
            <h1>BLS</h1>
            <form method="post"  action="?action">
                <label><b>Nom d'utilisateur: </b></label>
                <input type="text" placeholder="Nom d'utilisateur" name="nom" required><br><br>

                <label><b>Mot de passe: </b></label>
                <input type="password" placeholder="Mot de passe" name="mdp" required><br><br>
                <input type="hidden" name="act" value="run">
                <button type="submit" name="login">Se Connecter</button>
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
                    //echo "Une base de donnée fut créée ou existe déjà.<br>";
                } 
                else 
                {
                    echo "Erreur de création de base de données: " . $connection->error;
                }
                
                // Créer une table de données si elle n'existe pas déjà
                $createTable = "CREATE TABLE IF NOT EXISTS userTable"
                             . "("
                             . "id INT(4) UNSIGNED AUTO_INCREMENT PRIMARY KEY,"
                             . "nom_utilisateur VARCHAR(30) NOT NULL,"
                             . "mot_de_passe VARCHAR(1024) NOT NULL,"
                             . "reg_date TIMESTAMP"
                             . ")";
        
                if (mysqli_query($connection, $createTable))
                {
                    //echo "Une table de données fut créée ou existe déjà.";
                }
                else
                {
                    echo "Erreur de création de table de données: " . mysqli_error($connection);
                }
                
                // Fonction du bouton de connexion
                if(isset($_GET['action']))
                {
                    $nom = $_POST['nom'];
                    $mdp = $_POST['mdp'];
                    
                    if (empty($mdp))
                    {
                        echo "<br>" . "Veuillez inscrire un mot de passe";
                    }
                    else
                    {
                        $hashedmdp = hash('sha256', $mdp);
                    
                        
                        $resultat = mysqli_query($connection, "SELECT nom_utilisateur FROM usertable WHERE nom_utilisateur = '$nom'");
                        $nombreRangées = mysqli_num_rows($resultat);
                        
                        
                        if($nombreRangées > 0)
                        {
                            //Si l'utilisateur existe
                            vérifierLogin($connection, $nom, $hashedmdp);
                        } 
                        else 
                        {
                            // Si l'utilisateur n'existe pas
                            nouvelUtilisateur($connection, $nom, $hashedmdp);
                        }
                    }
                }
                
                // Vérifier l'information de l'utilisateur
                function vérifierLogin($connection, $nom, $hashedmdp)
                {
                    $resultat = mysqli_query($connection, "SELECT nom_utilisateur, mot_de_passe FROM usertable WHERE nom_utilisateur = '$nom' AND  mot_de_passe = '$hashedmdp'");
                
                    $nombreRangées = mysqli_num_rows($resultat);
                    
                    if($nombreRangées > 0)
                    {
                        echo "CONNECTION FONCTIONNE";
                    }
                    else
                    {
                        echo "LE MOT DE PASSE EST INCORRECT";
                    }
                }
                
                // Insérer les données d'un nouvel utilisateur
                function nouvelUtilisateur($connection, $nom, $hashedmdp)
                {
                    $sql = "INSERT INTO userTable (nom_utilisateur, mot_de_passe)
                            VALUES ('$nom', '$hashedmdp')";
                    
                    if ($connection->query($sql) === TRUE)
                    {
                        echo "<br>" . "NOUVEL UTILISATEUR CRÉÉ";
                    }
                    else
                    {
                        echo "<br>" . "ERREUR: " . $connection->error;
                    }
                }
            ?>
        </div>
    </body>
</html>
