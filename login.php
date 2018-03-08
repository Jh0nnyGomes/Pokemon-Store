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
                <li><a href="index.html">Home</a></li>
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
            
                $username = $_POST["username"];
                $psw = $_POST["password"];
                
                include('dbhelper.php');
                LoginSession(Login(Connect(), $username, $psw));
            
                                    
            ?>
        </div>
    </body>
</html>