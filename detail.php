<?php
session_start();
include 'bd.php';

if (!isset($_GET['id'])) {
    echo "<p class='text-center'>ID d'annonce non fourni.</p>";
    exit;
}

$produit_id = intval($_GET['id']);

// Récupère les infos de l'annonce
$stmt = $conn->prepare("SELECT p.id AS produit_id, p.nom, p.description, p.prix, p.image, u.id AS vendeur_id, u.nom AS vendeur_nom 
                        FROM produits p 
                        JOIN utilisateurs u ON p.utilisateurs_id = u.id 
                        WHERE p.id = ?");
$stmt->bind_param("i", $produit_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p class='text-center'>Annonce introuvable.</p>";
    exit;
}

$produit = $result->fetch_assoc();

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && isset($_SESSION['user_id'])) {
    $message = htmlspecialchars($_POST['message'], ENT_QUOTES, 'UTF-8');
    $expediteur_id = $_SESSION['user_id'];
    $destinataire_id = $produit['vendeur_id'];

    // Insertion du message dans la table
    $stmt = $conn->prepare("INSERT INTO messages (utilisateurs_id, destinataire_id, produit_id, contenu, date_envoi, conversation_type, parent_id) 
                            VALUES (?, ?, ?, ?, NOW(), 'annonce', NULL)");
    $stmt->bind_param("iiis", $expediteur_id, $destinataire_id, $produit['produit_id'], $message);

    if ($stmt->execute()) {
        $success_message = "Message envoyé avec succès !";
    } else {
        $error_message = "Erreur lors de l'envoi du message.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($produit['nom']); ?> - LeBonDuCoin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
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

<div class="container mt-5 pt-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <img src="uploads/<?php echo htmlspecialchars($produit['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($produit['nom']); ?>">
                <div class="card-body">
                    <h1 class="card-title"><?php echo htmlspecialchars($produit['nom']); ?></h1>
                    <p class="card-text"><?php echo htmlspecialchars($produit['description']); ?></p>
                    <p class="fw-bold text-success fs-4"><?php echo htmlspecialchars($produit['prix']); ?> €</p>
                    <p class="text-muted">Vendeur: <?php echo htmlspecialchars($produit['vendeur_nom']); ?></p>

                    <!-- Bouton "J'aime" et Commander -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form class="like-form d-inline" action="like.php" method="post">
                            <input type="hidden" name="produit_id" value="<?php echo $produit['produit_id']; ?>">
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="fas fa-heart like-btn <?php 
                                    $check_sql = "SELECT * FROM favoris WHERE utilisateurs_id = ? AND produits_id = ?";
                                    $check_stmt = $conn->prepare($check_sql);
                                    $check_stmt->bind_param("ii", $_SESSION['user_id'], $produit['produit_id']);
                                    $check_stmt->execute();
                                    $check_result = $check_stmt->get_result();
                                    echo ($check_result->num_rows > 0) ? 'liked' : '';
                                ?>"></i> Ajouter aux favoris
                            </button>
                        </form>
                        
                        <!-- Bouton Commander - seulement visible si l'utilisateur n'est pas le propriétaire -->
                        <?php if ($_SESSION["user_id"] != $produit['vendeur_id']) { ?>
                            <form action="panier.php" method="POST" class="d-inline">
                                <input type="hidden" name="produit_id" value="<?php echo $produit['produit_id']; ?>">
                                <input type="hidden" name="nom" value="<?php echo htmlspecialchars($produit['nom']); ?>">
                                <input type="hidden" name="prix" value="<?php echo htmlspecialchars($produit['prix']); ?>">
                                <input type="hidden" name="description" value="<?php echo htmlspecialchars($produit['description']); ?>">
                                <input type="hidden" name="image" value="<?php echo htmlspecialchars($produit['image']); ?>">
                                <button type="submit" class="btn btn-outline-success" name="commander">Commander</button>
                            </form>
                        <?php } else { ?>
                            <button class="btn btn-outline-secondary" disabled>Votre annonce</button>
                        <?php } ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Formulaire de message -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Contacter le vendeur</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($success_message)): ?>
                        <div class="alert alert-success"><?php echo $success_message; ?></div>
                    <?php endif; ?>

                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="message" class="form-label">Votre message</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Envoyer</button>
                        </form>
                        <?php if ($_SESSION['user_id'] == $produit['vendeur_id']): ?>
                            <a href="modif.php?id=<?php echo $produit['produit_id']; ?>" class="btn btn-outline-primary mt-3 me-2">Modifier</a>
                            <a href="deleteA.php?id=<?php echo $produit['produit_id']; ?>" class="btn btn-outline-danger mt-3" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette annonce ?');">Supprimer</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-info">
                            Vous devez être <a href="connexion.php">connecté</a> pour contacter le vendeur.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>