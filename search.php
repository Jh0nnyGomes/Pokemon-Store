<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <div style="height: 60px">
            <h1>POKEMON STORE</h1>
        </div>
        <div class="main-navbar">
            <ul class="main-navbarlist">
                <li><a href="index.php">Home</a></li>
                <li><a href="catalogo.php">Catalogo</a></li>
                <li><a href="cart.php">Carrello</a></li>
                
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
                
                $find = $_GET["search"];
                
                include('dbhelper.php');
                
                $db_handler = new DBHelper();
                $db_handler->FindPokemon($find);
                
            ?>
        </div>
    </body>
</html>