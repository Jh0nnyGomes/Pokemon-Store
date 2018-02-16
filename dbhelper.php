<?php
    function Connect(){
        $conn = new mysqli("localhost:3306", "root", "mysql", "Pokemon");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }

    function Execute($conn, $a){
        $conn->query($a);
    }

    function Register($conn, $username, $email, $psw, $confirmpsw){
        $checkusrname = mysqli_query($conn, "SELECT * FROM user_data WHERE username = '" . $username . "'");
        
        $checkemail = mysqli_query($conn, "SELECT * FROM user_data WHERE email = '" . $email . "'");

        $sql = "INSERT INTO  user_data (username, email, psw) VALUES ('" . $username . "', '" . $email . "', '" . $psw . "')";

        if($psw == $confirmpsw){
            if(mysqli_num_rows($checkusrname) > 0 || mysqli_num_rows($checkemail) > 0){
                print("Username o Email sono già in uso");
            } else{
                if($conn->query($sql) === TRUE) {
                    echo "Registrazione andata a buon fine.";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            }

        }

        else{
            print("Le Password non coincidono.");
        }
    }
    
    function Login($conn, $username, $psw){
        $check = mysqli_query($conn, "SELECT * FROM user_data WHERE (username = '" . $username ."' or email = '" . $username . "') and psw = '" . $psw . "'");
            
        if(mysqli_fetch_assoc($check) == null)
            print("Dati incorretti");
        else{
            $result = mysqli_query($conn, "SELECT username FROM user_data WHERE (username = '" . $username ."' or email = '" . $username . "')") or die (mysqli_error($conn));

            while($row = mysqli_fetch_assoc($result)){
                echo "Bentornato " . $row['username'];
            }
        }
        
    }

    function FindPokemon($conn, $find){
        $search = "SELECT * FROM Pokemon WHERE identifier LIKE '%$find%'";
        
        $result = mysqli_query($conn, $search);
        
        if (!$result) {
            echo "Could not successfully run query ($search) from DB: " . mysqli_error($conn);
        }

        else if (mysqli_num_rows($result) == 0) {
            echo "Nessun risultato.";
        } else {
            $cont = 1;
            echo "<div class='mainlist'>
                    <table class='list'>
                        <thead>
                            <tr>
                              <th scope='col'>#</th>
                              <th scope='col'></th>
                              <th scope='col'>Nome</th>
                              <th scope='col'>Altezza</th>
                              <th scope='col'>Peso</th>
                              <th scope='col'>N. Pokedex</th>
                            </tr>
                        </thead>
                        <tbody>";
            while($row = mysqli_fetch_assoc($result)){
                echo "  <tr>
                            <th scope='row'>" . $cont. "</th>
                                <td><img src='img/sprites/" . $row['id']. ".png'></td>
                                <td>" . $row['identifier'] . "</td>
                                <td>" . $row['height'] . "</td>
                                <td>" . $row['weight'] . "</td>
                                <td>" . $row['id'] . "</td>
                        </tr>";
                $cont++;
            }
            
            echo "</tbody> </table> </div>";
        }
    }

    function PokemonForPage($conn) {
        
    }
?>