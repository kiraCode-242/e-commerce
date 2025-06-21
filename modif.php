<?php
include 'bd.php';
session_start();

// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION["user_id"])){
   header("location:connexion.php");
   exit;
}

// Vérifier si l'ID de l'annonce est fourni
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    header("location:index.php");
    exit;
}

$id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Récupérer les informations de l'annonce et vérifier si l'utilisateur est le propriétaire
$sql = "SELECT * FROM produits WHERE id = ? AND utilisateurs_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0) {
    // L'annonce n'existe pas ou n'appartient pas à cet utilisateur
    header("location:index.php");
    exit;
}

$produit = $result->fetch_assoc();

// Traitement du formulaire de modification
if(isset($_POST['modifier'])) {
    $nom = $_POST['nom'];
    $prix = $_POST['prix'];
    $description = $_POST['description'];
    $categorie_id = $_POST['categories_id']; // ou categories_id selon votre structure

    // Variable pour stocker le nom de l'image
    $imageName = $produit['image']; // Par défaut, on garde l'image existante

    // Vérifier si une nouvelle image a été téléchargée
    if(!empty($_FILES['image']['name'])) {
        $image = $_FILES['image'];
        $imageName = time() . '_' . basename($image['name']);
        $targetDir = "uploads/";
        $targetFile = $targetDir . $imageName;

        // Vérification du type de fichier
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if(in_array($imageFileType, $allowedTypes)) {
            // Suppression de l'ancienne image si elle existe
            if(!empty($produit['image']) && file_exists("uploads/" . $produit['image'])) {
                unlink("uploads/" . $produit['image']);
            }
            
            if(!move_uploaded_file($image['tmp_name'], $targetFile)) {
                echo "<div class='alert alert-danger'>Erreur lors du téléchargement de l'image.</div>";
                $imageName = $produit['image']; // On garde l'ancienne image en cas d'erreur
            }
        } else {
            echo "<div class='alert alert-danger'>Type de fichier non autorisé. Veuillez télécharger une image (jpg, jpeg, png, gif).</div>";
            $imageName = $produit['image']; // On garde l'ancienne image en cas d'erreur
        }
    }

    // Mise à jour de l'annonce dans la base de données
    // Adaptez le nom de la colonne (categorie_id ou categories_id) selon votre structure
    $sql = "UPDATE produits SET nom = ?, description = ?, prix = ?, categories_id = ?, image = ? WHERE id = ? AND utilisateurs_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsiii", $nom, $description, $prix, $categorie_id, $imageName, $id, $user_id);
    
    if($stmt->execute()) {
        echo "<div class='alert alert-success text-center'>Annonce modifiée avec succès ! Redirection en cours...</div>";
        header("refresh:2;url=detail.php?id=" . $id);
    } else {
        echo "<div class='alert alert-danger'>Erreur lors de la modification : " . $stmt->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'annonce - LeBonDuCoin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>
<nav class="navbar nav nav-underline navbar-expand-lg bg-body-tertiary shadow p-3 mb-5 bg-body-tertiary rounded">
  <div class="container-fluid">
    <a class="navbar-brand" href="detail.php?id=<?php echo $id; ?>"><i class="fas fa-arrow-left"></i></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="nav justify-content-center"><P class="fs-5 fst-italic fw-bold">LeBon<span class="text-success">DuCoin</span></P></div>
  </div>
</nav>

<div class="container">
    <div class="text-center mb-4">
        <h3>Modifier l'annonce</h3>
        <p class="text-muted">Modifiez les informations de votre annonce</p>
    </div>

    <div class="container d-flex justify-content-center">
        <form action="" method="POST" enctype="multipart/form-data" style="width:50vw; min-width:300px;">
            <div class="mb-3">
                <label for="image" class="form-label">Image actuelle</label>
                <div class="mb-2">
                    <img src="uploads/<?php echo htmlspecialchars($produit['image']); ?>" alt="Image actuelle" class="img-thumbnail" style="max-height: 200px;">
                </div>
                <label for="image" class="form-label">Changer l'image (facultatif)</label>
                <input type="file" class="form-control" name="image" accept="image/*">
                <small class="text-muted">Laissez vide pour conserver l'image actuelle</small>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Libellé</label>
                <input type="text" class="form-control" name="nom" required value="<?php echo htmlspecialchars($produit['nom']); ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Prix</label>
                <input type="number" class="form-control" name="prix" min="0" step="0.01" required value="<?php echo htmlspecialchars($produit['prix']); ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="4" required><?php echo htmlspecialchars($produit['description']); ?></textarea>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Catégorie</label>
                <select class="form-select" name="categories_id" required>
                    <!-- Adaptez le nom du champ (categorie_id ou categories_id) selon votre structure -->
                    <option value="1" <?php if($produit['categories_id'] == 1) echo 'selected'; ?>>Sport</option>
                    <option value="2" <?php if($produit['categories_id'] == 2) echo 'selected'; ?>>Électronique</option>
                    <option value="3" <?php if($produit['categories_id'] == 3) echo 'selected'; ?>>Vêtement</option>
                </select>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary" name="modifier">Enregistrer les modifications</button>
                <a href="detail.php?id=<?php echo $id; ?>" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>