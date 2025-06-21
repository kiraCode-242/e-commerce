<?php
//creation de la session
session_start();
include 'bd.php';

if (isset($_POST["bout"])) {
    $email = $_POST['email'];
    $mdp = $_POST['mdp'];

    $sql = "SELECT * FROM utilisateurs WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($mdp, $row['mdp'])) {
            $_SESSION['user_id'] = $row['id'];
            echo "<marquee>Connexion réussie !</marquee>";
            // Rediriger vers la page d'accueil
            header("refresh:5;url=index.php");
        } else {
            echo "Mot de passe incorrect.";
        }
    } else {
        echo "Aucun utilisateur trouvé.";
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
    <a class="navbar-brand" href="index.php"><i class="fas fa-arrow-left"></i></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="nav justify-content-center"><P class="fs-5 fst-italic fw-bold">LeBon<span class="text-success">DuCoin</span></P></div>
  </div>
</nav>

<div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div class="card shadow p-4" style="width: 400px;">
      <h3 class="text-center mb-2">Connexion</h3><hr>
      <form action="" method="POST">
        <div class="form-group">
          <label for="identifiant">Mail</label>
          <input type="email" class="form-control" id="identifiant" name="email" placeholder="Votre mail..." required>
        </div>
        <div class="form-group">
          <label for="password">Mot de passe</label>
          <input type="password" class="form-control" id="mdp" name="mdp" placeholder="Votre mot de passe..." required>
        </div>
        <br>
        <div class="d-grid gap-2 col-6 mx-auto">
            <button class="btn btn-primary" type="submit" name="bout">Se connecter</button>
        </div>

      </form><hr>
      <div class="links">
    	Vous n'avez pas de compte? <a href="inscri.php" class="se">S'inscrire</a>
</div>
    </div>
  </div>

</body>
</html>