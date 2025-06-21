<?php
session_start();
// Initialiser le panier s'il n'existe pas
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Connexion à la base de données
include 'bd.php';

// Ajouter un article au panier et l'enregistrer dans la BDD
if (isset($_POST['commander'])) {
    $article = [
        'produit_id' => htmlspecialchars($_POST['produit_id']),
        'nom' => htmlspecialchars($_POST['nom']),
        'prix' => htmlspecialchars($_POST['prix']),
        'description' => htmlspecialchars($_POST['description']),
        'image' => htmlspecialchars($_POST['image']),
    ];
    
    // Ajouter au panier de session
    $_SESSION['panier'][] = $article;
    
    // Insérer dans la table commandes
    $sql = "INSERT INTO commandes (produit_id, utilisateurs_id, date_commande, statut) VALUES (?, ?, NOW(), 'En attente')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $article['produit_id'], $_SESSION["user_id"]);
    $stmt->execute();
    
    header("Location: panier.php"); // Rediriger vers le panier
    exit;
}

// Supprimer un article du panier
if (isset($_GET['action']) && $_GET['action'] === 'supprimer' && isset($_GET['index'])) {
    $index = (int)$_GET['index'];
    unset($_SESSION['panier'][$index]);
    $_SESSION['panier'] = array_values($_SESSION['panier']); // Réindexer le tableau
    header("Location: panier.php");
    exit;
}

// Vider le panier
if (isset($_GET['action']) && $_GET['action'] === 'vider') {
    unset($_SESSION['panier']);
    $_SESSION['panier'] = []; // Réinitialiser comme un tableau vide au lieu de supprimer complètement
    header("Location: panier.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre Panier</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .cart-box {
            margin-top: 50px;
        }
        .btn-vider {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container cart-box">
        <?php
        if (empty($_SESSION['panier'])) {
            echo "<div class='alert alert-warning text-center'>Votre panier est vide.</div>";
        } else {
            echo "<div class='container mt-5'>";
            echo "<h2 class='text-center'>Votre Panier</h2>";
            echo "<ul class='list-group'>";
            foreach ($_SESSION['panier'] as $index => $article) {
                echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
                echo "<div>";
                echo "<img src='uploads/" . htmlspecialchars($article['image']) . "' alt='" . htmlspecialchars($article['nom']) . "' style='width: 50px; height: 50px; object-fit: cover; margin-right: 10px;'>";
                echo htmlspecialchars($article['nom']) . " - " . htmlspecialchars($article['prix']) . " €";
                echo "</div>";
                echo "<a href='panier.php?action=supprimer&index=$index' class='btn btn-danger btn-sm'>Annuler</a>";
                echo "</li>";
            }
            echo "</ul>";
            echo "</div>";
        }
        ?>
        <!-- Bouton pour vider le panier -->
        <div class="text-center btn-vider">
            <a href="panier.php?action=vider" class="btn btn-danger"><i class="fas fa-trash"></i> Vider le panier</a>
        </div>
        
        <!-- Bouton pour revenir à l'accueil -->
        <div class="text-center mt-3">
            <a href="index.php" class="btn btn-primary"><i class="fas fa-home"></i> Retour à l'accueil</a>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>

<?php
$conn->close();
?>