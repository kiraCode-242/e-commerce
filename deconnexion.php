<?php
//creation de la session
session_start();
//destruction de la session
session_destroy();
echo"vous allez etre deconnecte";
header("refresh:2;url=index.php");
?>