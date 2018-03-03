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
            
            session_start();

            $_SESSION['login_user'] = $username;

            echo $_SESSION['login_user'];
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
        
        $x_pag = 5;  //[1] how many rows to display for each page

        if (isset($_GET['pag'])) //[2]
        {                          
          $pag = $_GET['pag'];        
        }
        else
        {
         $pag  = 1;
        }

        //You could do the same at th point [2] with 
        //$pag = isset($_GET['pag']) ? $_GET['pag'] : 1;

        if (!$pag || !is_numeric($pag))  //[3]
        {
          $pag = 1;
        }

        
        $sql = "SELECT * FROM pokemon";
        $res = mysqli_query($conn, $sql);
        $result = mysqli_num_rows($res);
        $allpages = ceil($result / $x_pag);
        $first = ($pag-1) * $x_pag;
        
        $pkforpage = "SELECT * FROM pokemon LIMIT $first, $x_pag";
        
        $printsql = mysqli_query($conn, $pkforpage);
        
        if (!$printsql) {
            echo "Could not successfully run query ($pkforpage) from DB: " . mysqli_error($conn);
        }

        else if (mysqli_num_rows($printsql) == 0) {
            echo "Nessun risultato.";
        } else {
            echo "<div class='catalog'>
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
            while($row = mysqli_fetch_assoc($printsql)){
                echo "  <tr>
                            <th scope='row'>" . $row['id'] . "</th>
                                <td><img src='img/sprites/" . $row['id']. ".png'></td>
                                <td>" . $row['identifier'] . "</td>
                                <td>" . $row['height'] . "</td>
                                <td>" . $row['weight'] . "</td>
                                <td>" . $row['id'] . "</td>
                        </tr>";
            }
            
            echo "</tbody> </table> </div>";
            
            echo "<div class='pagcontainer'>";
            
            if ($allpages > 1){ //[9]
              if ($pag > 1){  //[10]
                  //[11]:$_SERVER['PHP_SELF'] returns the courrent page address
                  //eg: http://localhost/PHP_TESTS/pkStore/catalog.php
                  //if we add the string ?pag=x, the value x is stored in $_GET['pag']
                  echo "<button class='pagbtn backward'><a href=\"" . $_SERVER['PHP_SELF'] . "?pag=" . ($pag - 1) . "\">";
                  echo "Pagina Indietro</a>&nbsp;</button>";
              }
                
              if($pag==$allpages){
                  for ($p=$pag - 4; $p<=$pag - 1; $p++) {
                      echo "<button class='pagbtn'><a  href=\"" . $_SERVER['PHP_SELF'] . "?pag=" . $p . "\">";
                      echo $p . "</a>&nbsp;</button>";
                    }
                  echo "<button class='pagbtn active'><a href=\"" . $_SERVER['PHP_SELF'] . "?pag=" . $pag . "\">";
                  echo $pag . "</a>&nbsp;</button>";
              } else if($pag==$allpages-1){ //se è la penultima pagina stampa fino all'ultima pagina e le pagine precedenti
                  for ($p=$pag - 3; $p<=$pag - 1; $p++) {
                      echo "<button class='pagbtn'><a href=\"" . $_SERVER['PHP_SELF'] . "?pag=" . $p . "\">";
                      echo $p . "</a>&nbsp;</button>";
                    }
                  echo "<button class='pagbtn active'><a href=\"" . $_SERVER['PHP_SELF'] . "?pag=" . $pag . "\">";
                  echo $pag . "</a>&nbsp;</button>";
                  for ($p=$pag + 1; $p<$pag + 2; $p++) { 
                      echo "<button class='pagbtn'><a href=\"" . $_SERVER['PHP_SELF'] . "?pag=" . $p . "\">";
                      echo $p . "</a>&nbsp;</button>";
                    }
                }else if($pag==$allpages-2){ //se è la terzultima pagina stampa fino all'ultima pagina e le pagine precedenti
                  for ($p=$pag - 2; $p<=$pag - 1; $p++) { 
                      echo "<button class='pagbtn'><a href=\"" . $_SERVER['PHP_SELF'] . "?pag=" . $p . "\">";
                      echo $p . "</a>&nbsp;</button>";
                    }
                  echo "<button class='pagbtn active'><a href=\"" . $_SERVER['PHP_SELF'] . "?pag=" . $pag . "\">";
                  echo $pag . "</a>&nbsp;</button>";
                  for ($p=$pag + 1; $p<$pag + 3; $p++) { 
                      echo "<button class='pagbtn'><a href=\"" . $_SERVER['PHP_SELF'] . "?pag=" . $p . "\">";
                      echo $p . "</a>&nbsp;</button>";
                    }
                }else if($pag==$allpages-3){ //se è la quartultima pagina stampa fino all'ultima pagina e le pagine precedenti
                  for ($p=$pag - 1; $p<$pag; $p++) { 
                      echo "<button class='pagbtn'><a href=\"" . $_SERVER['PHP_SELF'] . "?pag=" . $p . "\">";
                      echo $p . "</a>&nbsp;</button>";
                    }
                  echo "<button class='pagbtn active'><a href=\"" . $_SERVER['PHP_SELF'] . "?pag=" . $pag . "\">";
                  echo $pag . "</a>&nbsp;</button>";
                  for ($p=$pag + 1; $p<$pag + 4; $p++) { 
                      echo "<button class='pagbtn'><a href=\"" . $_SERVER['PHP_SELF'] . "?pag=" . $p . "\">";
                      echo $p . "</a>&nbsp;</button>";
                    }
                }else if($pag==$allpages-4){
                  echo "<button class='pagbtn active'><a href=\"" . $_SERVER['PHP_SELF'] . "?pag=" . $pag . "\">";
                  echo $pag . "</a>&nbsp;</button>";
                  for ($p=$pag + 1; $p<$pag + 5; $p++) { 
                      echo "<button class='pagbtn'><a href=\"" . $_SERVER['PHP_SELF'] . "?pag=" . $p . "\">";
                      echo $p . "</a>&nbsp;</button>";
                    }
                }else{
                  echo "<button class='pagbtn active'><a href=\"" . $_SERVER['PHP_SELF'] . "?pag=" . $pag . "\">";
                  echo $pag . "</a>&nbsp;</button>";
                  for ($p=$pag + 1; $p<$pag + 5; $p++) { 
                      echo "<button class='pagbtn'><a href=\"" . $_SERVER['PHP_SELF'] . "?pag=" . $p . "\">";
                      echo $p . "</a>&nbsp;</button>";
                    }
              }
                

              if ($allpages > $pag){  //[12]
                  echo "<button class='pagbtn forward'><a href=\"" . $_SERVER['PHP_SELF'] . "?pag=" . ($pag + 1) . "\">";
                  echo "Pagina Avanti</a></button>";
              }
            }
            
            echo "</div>";
        } 
    }

    
?>
