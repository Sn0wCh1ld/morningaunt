<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <style>            
            div 
            {
                height: 200px;
                width: 400px;

                position: fixed;
                top: 50%;
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
                0% { background-color: #f99; }
                33% { background-color: #9f9; }  
                67% { background-color: #99f; }
                100% { background-color: #f99; }
            }
        </style>
        <br><br>
        <br>
        <br>
        <div class="contenant" style="text-align:center">
            
            <form method="post"  action="?action">
                <label><b>Nom d'utilisateur: </b></label>
                <input type="text" placeholder="Nom d'utilisateur" name="nom" required><br><br>

                <label><b>Mot de passe: </b></label>
                <input type="password" placeholder="Mot de passe" name="mdp" required><br><br>
                <input type="hidden" name="act" value="run">
                <button type="submit" name="login">Se Connecter</button>
            </form>
            
        <br><br><br>
        
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
                    echo "Une base de donnée fut créée." . "<br>";
                } 
                else 
                {
                    echo "Erreur de création de base de données: " . $connection->error;
                }
                
                // Créer une table de données si elle n'existe pas déjà
                $createTable = "CREATE TABLE IF NOT EXISTS userTable"
                             . "("
                             . "id INT(4) UNSIGNED AUTO_INCREMENT PRIMARY KEY,"
                             . "username VARCHAR(30) NOT NULL,"
                             . "password VARCHAR(1024) NOT NULL,"
                             . "reg_date TIMESTAMP"
                             . ")";
        
                if (mysqli_query($connection, $createTable))
                {
                    echo "Table created or already exists";
                }
                else
                {
                    echo "Error creating table: " . mysqli_error($connection);
                }
                
                //submission
                if(isset($_GET['action']))
                {
                    $un = $_POST['nom'];
                    $pw = $_POST['mdp'];
                    
                    if (empty($pw))
                    {
                        echo "<br>" . "Veuillez inscrire un nom d'utilisateur";
                    }
                    else
                    {
                        $hashedpw = hash('sha256', $pw);
                    
                        $sql = "select * from 'userTable' where name = $un";
                    
                        if($connection->query($sql) === TRUE)
                        {
                            //username exists
                            vérifierLogin();
                        } 
                        else 
                        {
                            //username does not exist
                            nouvelUtilisateur($connection, $un, $hashedpw);
                        }
                    }
                }
                
                function vérifierLogin()
                {
                    $sql = "SELECT username FROM userTable";
                }
                
                function nouvelUtilisateur($connection, $un, $hashedpw)
                {
                    $sql = "INSERT INTO userTable (username, password)
                            VALUES ('$un', '$hashedpw')";
                    
                    if ($connection->query($sql) === TRUE)
                    {
                        echo "<br>" . "DICKHEAD";
                    }
                    else
                    {
                        echo "<br>" . "ERROR: " . $connection->error;
                    }
                    
                    //your code
                    echo "<br>" . 'LOGGED IN';
                    }
            ?>
        </div>
    </body>
</html>
