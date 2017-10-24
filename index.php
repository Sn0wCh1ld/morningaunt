<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <br>
        <br>
        <div class="contenant" style="text-align:center">
            
            <form method="post"  action="?action">
                <label><b>Nom d'utilisateur: </b></label>
                <input type="text" placeholder="Nom d'utilisateur" name="uname" required><br><br>

                <label><b>Mot de passe: </b></label>
                <input type="password" placeholder="Mot de passe" name="pword" required><br><br>
                <input type="hidden" name="act" value="run">
                <button type="submit" name="login">Se Connecter</button>
            </form>
        
            <?php
                // put your code here
                
                echo "<br><br><br>";
            
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "userDB";
        
                // Create connection
                $connection = new mysqli($servername, $username, $password, $dbname);
        
                // Check connection
                if ($connection->connect_error) {
                    die("Connection failed: " . $connection->connect_error);
                }
        
                // Create database
                $createDB = "CREATE DATABASE IF NOT EXISTS userDB";
                $createTable = "CREATE TABLE IF NOT EXISTS userTable"
                             . "("
                             . "id INT(4) UNSIGNED AUTO_INCREMENT PRIMARY KEY,"
                             . "username VARCHAR(30) NOT NULL,"
                             . "password VARCHAR(256) NOT NULL,"
                             . "reg_date TIMESTAMP"
                             . ")";
        
                if ($connection->query($createDB) === TRUE)
                {
                    echo "Database created successfully, or already exists" . "<br>";
                } 
                else 
                {
                    echo "Error creating database: " . $connection->error;
                }
        
                // Create table
        
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
                    $un = $_POST['uname'];
                    $pw = $_POST['pword'];
                    
                    $hashedpw = hash('sha256', $pw);
                    
                    $sql = "select * from 'userTable' where name = $un";
                    
                    if($connection->query($sql) === TRUE)
                    {
                         //username exists
                    } 
                    else 
                    {
                        //username does not exist
                        nouvelUtilisateur($connection, $username, $hashedpw);
                    }
                    
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
