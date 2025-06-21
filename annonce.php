<?php
include 'bd.php';
session_start();
if(!isset($_SESSION["user_id"])){
   header("location:connexion.php");
   exit;
}

if (isset($_POST["envoyer"])) {
    $nom = $_POST['nom'];
    $prix = $_POST['prix'];
    $description = $_POST['description'];
    $categorie_id = $_POST['categorie_id'];
    $utilisateur_id = $_SESSION['user_id'];

    // Vérification et téléchargement de l'image
    $image = $_FILES['image'];
    $imageName = time() . '_' . basename($image['name']);
    $targetDir = "uploads/";
    // Création du dossier uploads s'il n'existe pas
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    $targetFile = $targetDir . $imageName;

    // Vérification du type de fichier
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($imageFileType, $allowedTypes)) {
        if (move_uploaded_file($image['tmp_name'], $targetFile)) {
            // Utilisation de requêtes préparées pour éviter les injections SQL
            $sql = "INSERT INTO produits (nom, description, prix, utilisateurs_id, categories_id, image) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdiss", $nom, $description, $prix, $utilisateur_id, $categorie_id, $imageName);
            
            if ($stmt->execute()) {
                echo "<div class='alert alert-success text-center'>Annonce déposée avec succès ! Redirection en cours...</div>";
                header("refresh:3;url=index.php");
            } else {
                echo "<div class='alert alert-danger'>Erreur : " . $stmt->error . "</div>";
            }
            $stmt->close();
        } else {
            echo "<div class='alert alert-danger'>Erreur lors du téléchargement de l'image.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Type de fichier non autorisé. Veuillez télécharger une image (jpg, jpeg, png, gif).</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer une annonce - LeBonDuCoin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>
<nav class="navbar nav nav-underline navbar-expand-lg bg-body-tertiary shadow p-3 mb-5 bg-body-tertiary rounded">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php"><i class="fas fa-arrow-left"></i></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="nav justify-content-center"><P class="fs-5 fst-italic fw-bold">LeBon<span class="text-success">DuCoin</span></P></div>
  </div>
</nav>

    <div class="container">
        <div class="text-center mb-4">
            <h3>Créer une annonce</h3>
            <p class="text-muted">Complétez les champs pour créer votre annonce</p>
        </div>

        <div class="container d-flex justify-content-center">
            <form action="" method="POST" enctype="multipart/form-data" style="width:50vw; min-width:300px;">
                <div class="mb-3">
                    <label for="image" class="form-label">Télécharger une Image</label>
                    <input type="file" class="form-control" name="image" accept="image/*" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Libellé</label>
                    <input type="text" class="form-control" name="nom" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Prix</label>
                    <input type="number" class="form-control" name="prix" min="0" step="0.01" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="4" required></textarea>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Catégorie</label>
                    <select class="form-select" name="categorie_id" required>
                        <option value="" disabled selected>Sélectionnez une catégorie</option>
                        <option value="1">Sport</option>
                        <option value="2">Électronique</option>
                        <option value="3">Vêtement</option>
                    </select>
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-success" name="envoyer">Envoyer</button>
                    <a href="index.php" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 