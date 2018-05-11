<?php
    class DBHelper{
        protected $servername;
        protected $port;
        protected $username;
        protected $password;
        protected $dbName;
        protected $conn;

          //default constructor
        function __construct(){
            $this->servername = 'localhost';
            $this->port = 3306;
            $this->username = 'root';
            $this->password = 'mysql';//jhonny DEBUG
            $this->dbName = 'Pokemon';
            //crea nuova connessione
            try {
              $this->conn = new PDO("mysql:host=$this->servername;dbname=$this->dbName", $this->username, $this->password);
              $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch(PDOException $e) {
              echo "Connection failed: " . $e->getMessage();
            }
          }

        function runQuery($query){
            $sth = $this->conn->prepare($query); //esegue una query passata come parametro --mai usata lol--
            $sth->execute();
            return $sth;
        } 
        
        function countRows($query){
            $sth = $this->runQuery($query);
            $sth->fetchColoumn();
            return $sth;
        }

        function Register($username, $email, $psw, $confirmpsw){
            //queries per il controlllo dei dati inseriti 
            $usr ="SELECT COUNT(*) FROM user_data WHERE username = '" . $username . "'";

            $mail = "SELECT COUNT (*) FROM user_data WHERE email = '" . $email . "'";
            //query per inserire i dati nel DB
            $sql = "INSERT INTO  user_data (username, email, psw) VALUES ('" . $username . "', '" . $email . "', '" . $psw . "')";
            
            $sth = $this->runQuery($sql);
            
            $usrRows = $this->runQuery($usr);
            
            $mailRows = $this->runQuery($mail);

            if($psw == $confirmpsw){ //se la password corrispondono 
                if($usrRows > 0 || $mailRows > 0){ //se username o l'email sono già presenti nel DB mi dà errore 
                    print("Username o Email sono già in uso");
                } 
                else{ //se invece non sono presenti nel DB gli inserisce dentro
                    if($sth === TRUE)
                        echo "Registrazione andata a buon fine.";
                }
            }
            else{
                print("Le Password non coincidono.");
            }
        }

        function Login($username, $psw){
            //query per il controllo dell'username o dell'email e della password
            $check = "SELECT * FROM user_data WHERE (username = '" . $username ."' or email = '" . $username . "') and psw = '" . $psw . "'";
            
            $checked = $this->runQuery($check);

            if($checked == null){ //se non ci sono risultati mi dà errore
                print("Dati incorretti");
            }
            else{
                //prende dal DB il nome utente
                $usr = "SELECT username FROM user_data WHERE (username = '" . $username ."' or email = '" . $username . "')";
                
                $result = $this->runQuery($usr);

                while($row = $result->fetch()){ //stampa il messaggio di benvenuto con il nome utente che ha recuperato dal DB
                    echo "Bentornato " . $row['username'];
                }
                
                    session_start();
                
                    $_SESSION['login_user'] = $username;
            }

        }

        function FindPokemon($find){
            //query per la ricerca dei Pokemon
            $search = "SELECT * FROM Pokemon WHERE identifier LIKE '%$find%'";

            $result = $this->runQuery($search); //esegue la query 

            if (!$result) { //se non riesce ad eseguire la query mi dà errore
                echo "Could not successfully run query ($search) from DB: " . $result->error;
            }
            else { //invece se ci sono risultati me li stampa
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
                while($row = $result->fetch()){ //ciclo di stampa dei Pokemon in una tabella, mette dentro un array assocciativo tutti i risultati ottenuti
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

        function PokemonForPage(){

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


            $sql = "SELECT count(*) FROM pokemon"; // [4]
            $sth = $this->runQuery($sql);
            $all_rows = $sth->fetchColumn();  //[5]
            $allpages = ceil($all_rows / $x_pag); //[6]
            $first = ($pag-1) * $x_pag;  //[7]

            $sql = "SELECT * FROM pokemon LIMIT $first, $x_pag"; //[8]
            $sth = $this->runQuery($sql);

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
            while($row = $sth->fetch()){ //ciclo per la "stampa" dei risultati 
                $idPoke = $row['id'];
                echo "  <tr>
                            <th scope='row'>" . $row['id'] . "</th>
                                <td>" . "<form method='get' action='details.php'>" .
                                "<input type = 'hidden' name = 'idPoke' value = '" . $idPoke . "'/>" .
                                "<input type = 'image' src='img/sprites/" . $row['id']. ".png'/></form></td>
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
        
        function Details(){
            
            if(isset($_GET['idPoke']))
                $idPoke = $_GET['idPoke'];
            else
                $idPoke = 1;
            
            $query = "SELECT * FROM pokemon WHERE id = '" . $idPoke . "'";
            $pokemon = $this->runQuery($query);
            $row = $pokemon->fetch();
            
            $productId = $row['id'];
            $productName = $row['identifier'];
            $productPrice = $row['base_experience'];
            
            echo "<img class = 'detail-img' src='img/sprites/" . $row['id']. ".png'>";
            
        }
    }
    
?>
