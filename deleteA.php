<?php
include 'bd.php';
$id = $_GET['id'];
$req = "delete from produits where id = '$id'";
$res = mysqli_query($conn,$req);
if ($res){
    echo "<marquee>supprimer avec succes, vous allez etre rediriger vers la page d'iscription</marquee>";
      header("refresh:5;url=index.php");
}
?>