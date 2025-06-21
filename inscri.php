<?php
include 'bd.php';

if (isset($_POST["bout"])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);

    $sql = "select * from utilisateurs where email='$email'";
    $res = mysqli_query($conn, $sql);
    if(mysqli_num_rows($res) != 0){
        echo "Le mail : $email est déjà présent dans la base, inscription impossible.";
    }else{

      $sql = "INSERT INTO utilisateurs (nom, prenom, email, mdp) VALUES ('$nom', '$prenom', '$email', '$mdp')";

        
        $res = mysqli_query($conn, $sql);
        echo "Inscription réussie, Vous allez être redirigé pour vous connecter....";
        header("refresh:3;url=connexion.php");
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <title>Document</title>
</head>
<body>
<nav class="navbar nav nav-underline navbar-expand-lg bg-body-tertiary shadow p-3 mb-1 bg-body-tertiary rounded">
  <div class="container-fluid">
    <a class="navbar-brand" href="connexion.php"><i class="fas fa-arrow-left"></i></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="nav justify-content-center"><P class="fs-5 fst-italic fw-bold">LeBon<span class="text-success">DuCoin</span></P></div>
  </div>
</nav>

<div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div class="card shadow p-4" style="width: 400px;">
      <h3 class="text-center mb-2">Inscription</h3><hr>
    <form action="" method="post">
    
    <div class="form-group">
          <label for="nom">Nom</label>
          <input type="text" class="form-control" id="nom" name="nom" placeholder="Votre nom...">
        </div>
        <div class="form-group">
          <label for="prenom">Prénom</label>
          <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Votre prénom">
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" class="form-control" id="email" name="email" placeholder="Votre email">
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" class="form-control" id="mdp" name="mdp" placeholder="Votre mot de passe">
        </div>
        <br>
        <div class="d-grid gap-2 col-6 mx-auto">
            <button class="btn btn-primary" type="submit" name="bout">S'inscrire</button>
        </div>
      </form>
    </div>
  </div>
    </form>
</body>
</html>