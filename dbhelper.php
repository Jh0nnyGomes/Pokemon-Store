<?php
    function Connect(){
        $conn = new mysqli("localhost:3306", "root", "mysql", "Pokemon"); //Esegue la connessione tramite la classe predefinita mysqli
        if ($conn->connect_error) { //se c'è un errore mi sputa in faccia e chiude la connessione
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn; //ritorna la connessione
    }

    function Execute($conn, $a){
        $conn->query($a); //esegue una query passata come parametro --mai usata lol--
    } 

    function Register($conn, $username, $email, $psw, $confirmpsw){
        //queries per il controlllo dei dati inseriti 
        $checkusrname = mysqli_query($conn, "SELECT * FROM user_data WHERE username = '" . $username . "'");
        
        $checkemail = mysqli_query($conn, "SELECT * FROM user_data WHERE email = '" . $email . "'");
        //query per inserire i dati nel DB
        $sql = "INSERT INTO  user_data (username, email, psw) VALUES ('" . $username . "', '" . $email . "', '" . $psw . "')";

        if($psw == $confirmpsw){ //se la password corrispondono 
            if(mysqli_num_rows($checkusrname) > 0 || mysqli_num_rows($checkemail) > 0){ //se username o l'email sono già presenti nel DB mi dà errore 
                print("Username o Email sono già in uso");
            } else{
                if($conn->query($sql) === TRUE) { //se invece non sono presenti nel DB gli inserisce dentro
                    echo "Registrazione andata a buon fine.";
                } else { //se non riesce ad eseguire la query mi dà errore
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            }

        }

        else{
            print("Le Password non coincidono.");
        }
    }
    
    function Login($conn, $username, $psw){
        //query per il controllo dell'username o dell'email e della password
        $check = mysqli_query($conn, "SELECT * FROM user_data WHERE (username = '" . $username ."' or email = '" . $username . "') and psw = '" . $psw . "'");
            
        if(mysqli_fetch_assoc($check) == null){ //se non ci sono risultati mi dà errore
            print("Dati incorretti");
            
            return false;
        }
        else{
            //prende dal DB il nome utent
            $result = mysqli_query($conn, "SELECT username FROM user_data WHERE (username = '" . $username ."' or email = '" . $username . "')") or die (mysqli_error($conn));

            while($row = mysqli_fetch_assoc($result)){ //stampa il messaggio di benvenuto con il nome utente che ha recuperato dal DB
                echo "Bentornato " . $row['username'];
            }
            
            return array(true, $username); //ritorna un array contenente true e una variabile contenente l'username, mi serve per assegnare il valore alla sessione
        }
        
    }

    function FindPokemon($conn, $find){
        //query per la ricerca dei Pokemon
        $search = "SELECT * FROM Pokemon WHERE identifier LIKE '%$find%'";
        
        $result = mysqli_query($conn, $search); //esegue la query 
        
        if (!$result) { //se non riesce ad eseguire la query mi dà errore
            echo "Could not successfully run query ($search) from DB: " . mysqli_error($conn);
        }

        else if (mysqli_num_rows($result) == 0) { //se non ci sono risultati
            echo "Nessun risultato.";
        } else { //invece se ci sono risultati me li stampa
            $cont = 1; //contatore per numerare i Pokemon
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
            while($row = mysqli_fetch_assoc($result)){ //ciclo di stampa dei Pokemon in una tabella, mette dentro un array assocciativo tutti i risultati ottenuti
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
        
        $x_pag = 5;  //numero di risultati per pagina

        if (isset($_GET['pag'])) //se il metodo get contiene la pagina allora mi assegna il valore alla variabiale
        {                          
          $pag = $_GET['pag'];        
        }
        else //se invece non contiene nulla setta la variabile pag a 1
        {
         $pag  = 1;
        }


        if (!$pag || !is_numeric($pag)) //se il valore della pagina non è numerico mi setta $pag a 1
        {
          $pag = 1;
        }

        
        $sql = "SELECT * FROM pokemon"; //query che recupera tutti i Pokemon dal DB
        $res = mysqli_query($conn, $sql); //esegue la query
        $result = mysqli_num_rows($res); //conta il numero di righe del risultato della query
        $allpages = ceil($result / $x_pag); //divide il numero di Pokemon per il numero di risultati per pagina e lo arrotonda per eccesso, in modo da ottenere il numero totale di pagine
        $first = ($pag-1) * $x_pag; //il primo risultato che verrà stampato ad ogni pagina, prende il numero della pagina meno 1 e lo moltiplica per il numero di risultati per pagina
        
        $pkforpage = "SELECT * FROM pokemon LIMIT $first, $x_pag"; //query per recuperare i Pokemon, limitando i risultati da un valore ad un altro
        
        $printsql = mysqli_query($conn, $pkforpage); //esegue la query
        
        if (!$printsql) { //se non riesce ad eseguire la query mi dà errore
            echo "Could not successfully run query ($pkforpage) from DB: " . mysqli_error($conn);
        }

        else if (mysqli_num_rows($printsql) == 0) { //se non ci sono risultati
            echo "Nessun risultato.";
        } else { //se invece ci sono risultati mi crea la tabella
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
            while($row = mysqli_fetch_assoc($printsql)){ //ciclo per la "stampa" dei risultati 
                $idPoke = $row['id'];
                echo "  <tr>
                            <th scope='row'>" . $row['id'] . "</th>
                                <td>" . "<form method='get' action='details.php>
                                <input type = 'image'<img src='img/sprites/" . $row['id']. ".png'></td>
                                <td>" . $row['identifier'] . "</td>
                                <td>" . $row['height'] . "</td>
                                <td>" . $row['weight'] . "</td>
                                <td>" . $row['id'] . "</td>
                        </tr>";
            }
            
            echo "</tbody> </table> </div>";
            
            echo "<div class='pagcontainer'>";
            
            if ($allpages > 1){ //se le pagine sono maggiori di 1
              if ($pag > 1){ //se la pagina è maggiore di 1 mi crea un bottone per tornare alla pagina precendente e usando $_SERVER['PHP_SELF'] mi ritorna il link della pagina corrente, aggiungendo la stringa ?pag=x, il valore storato nel $_GET['pag'] viene aggiunto al link, mostrando così nel link il numero della pagina corrente
                  echo "<button class='pagbtn backward'><a href=\"" . $_SERVER['PHP_SELF'] . "?pag=" . ($pag - 1) . "\">";
                  echo "Pagina Indietro</a>&nbsp;</button>";
              }
                
              if($pag==$allpages){ //quando la pagina corrente è l'ultima pagina mi stampa i bottoni con le pagine precedenti
                  for ($p=$pag - 4; $p<=$pag - 1; $p++) {
                      echo "<button class='pagbtn'><a  href=\"" . $_SERVER['PHP_SELF'] . "?pag=" . $p . "\">";
                      echo $p . "</a>&nbsp;</button>";
                    }
                  //mi stampa il bottone della pagina corrente con lo stile diverso
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
                

              if ($allpages > $pag){  //se le pagine totali sono maggiori della pagina corrente mi crea un bottone per tornare alla pagina duccessiva
                  echo "<button class='pagbtn forward'><a href=\"" . $_SERVER['PHP_SELF'] . "?pag=" . ($pag + 1) . "\">";
                  echo "Pagina Avanti</a></button>";
              }
            }
            
            echo "</div>";
        } 
    }

    function LoginSession($login){
        if($login[0]){ //se il login ritorna true
            session_start(); //starta la sessione

            $_SESSION['login_user'] = $login[1]; //assegna l'username alla sessione
        }          
    }
    
    
?>
