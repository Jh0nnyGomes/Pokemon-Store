<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/style.css">
    <script type='text/javascript' src='js/jquery-3.3.1.js'></script>
    <script type='text/javascript' src='js/script.js'></script>
    <?php
        if (session_status() == PHP_SESSION_NONE)
            session_start();
    ?>
</head>

<body>
    <div style="height: 60px">
        <h1>POKEMON STORE</h1>
    </div>
    <div class="main-navbar">
        <ul class="main-navbarlist">
            <li><a href="index.php">Home</a></li>
            <li><a href="catalogo.php">Catalogo</a></li>
            <li><a href="about.html">About</a></li>

            <form action="search.php" method="get">
                <input type="text" placeholder="Cerca.." name="search" class="search">
            </form>

        </ul>
        <ul class="login-navbar">
            <li><a href="login.html">Login</a></li>
            <li><a href="register.html">Registrati</a></li>
        </ul>
    </div>

    <div class="main-container">
        <?php
            
                include('dbhelper.php');
                if(isset($_SESSION['login_user'])){
                            echo '<script> UserIcon("'. $_SESSION['login_user'] .'"); </script>';
                        }
                
                if(isset($_POST['control']) && $_POST['control'] == 'add'){
                    $productId = $_POST['productId']; 
                    $productQty = $_POST['productQty']; 
                    $productPrice = $_POST['productPrice'];
                    $productName = $_POST['productName'];
                
        
                    $cart = new Cart($productId, $productName, $productPrice, $productQty);

                    $item = (serialize($cart));

                    if(!isset($_SESSION['products']))
                        $_SESSION['products'] = array($item);
                    else{
                        $exist = false;
                        
                        foreach($_SESSION['products'] as $key => $cart){
                            $checkItem = unserialize($cart);
                            
                            if($checkItem->id == $productId){
                                $checkItem->Update($checkItem->qty + $productQty, $key);
                                
                                $exist = true;
                                
                                break;
                            }
                        }
                        
                        if($exist == false){
                            array_push($_SESSION['products'], $item);
                            
                        }
                        
                    }
                }
                
                if(isset($_POST['control']) && $_POST['control'] == 'empty'){
                    $_SESSION['products'] = null;
                    
                    echo "Il carrello è vuoto";
                }
        
                if(isset($_SESSION['products'])){
                   echo "<div class='catalog'>
                    <table class='list'>
                        <thead>
                            <tr>
                              <th scope='col'>Id</th>
                              <th scope='col'></th>
                              <th scope='col'>Nome</th>
                              <th scope='col'>Prezzo</th>
                              <th scope='col'>Quantità</th>
                              <th scope='col'></th>
                            </tr>
                        </thead>
                        <tbody>";
                    
                    $subTotal = 0;
                    
                    foreach($_SESSION['products'] as $product){
                        echo "<tr>";
                        
                        $curItem = unserialize($product);
                        $curItem->itemOnTable();
                        
                        $subTotal = $subTotal + ($curItem->price * $curItem->qty);
                        
                        echo "</tr>";
                    }
                    
                    echo "<tr>" .
                            "<td colspan='6'> Prezzo Totale: " . $subTotal . "</td>" .
                         "</tr>";
                    
                }
        
                if(count($_SESSION['products']) > 0){
                    echo "<form method='post' action='cart.php'>" .
                            "<input type='submit' value='Empty' />" .
                            "<input type='hidden' name='control' value='empty' />" .
                         "</form>";

                }
            ?>
    </div>
</body>

</html>
