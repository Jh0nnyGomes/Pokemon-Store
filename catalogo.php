<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/style.css">
    <script type='text/javascript' src='js/jquery-3.3.1.js'></script>
    <script type='text/javascript' src='js/script.js'></script>
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
            
                include('dbhelper.php');
                PokemonForPage(Connect());
        
                if(isset($_SESSION['login_user'])){
                    echo "<br> Ciao: " . $_SESSION['login_user'];
                    echo '<script> UserIcon("Jhonny"); </script>';
                }
            ?>
    </div>
</body>

</html>
