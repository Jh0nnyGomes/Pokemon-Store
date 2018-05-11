<?php
    session_start();
        if(isset($_SESSION['login_user'])){
            session_unset();
            
            header('Location: login.html');
            die();
        }
?>
