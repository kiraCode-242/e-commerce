<?php
// Connexion à la base de données
include 'bd.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

$user_id = $_SESSION['user_id']; // ID de l'utilisateur connecté

// Récupérer l'ID de l'annonce si on vient d'une page détail
$produit_id = isset($_GET['id']) ? intval($_GET['id']) : null;

// Récupérer les données du formulaire pour répondre à un message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'], $_POST['conversation_id'])) {
    $message = htmlspecialchars($_POST['message'], ENT_QUOTES, 'UTF-8');
    $conversation_id = intval($_POST['conversation_id']);

    // Récupérer les informations sur la conversation
    $stmt = $conn->prepare("SELECT utilisateurs_id, destinataire_id, produit_id FROM messages WHERE id = ?");
    $stmt->bind_param("i", $conversation_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $conversation = $result->fetch_assoc();

    if ($conversation) {
        // Déterminer l'expéditeur et le destinataire pour la réponse
        $expediteur_id = $_SESSION['user_id'];
        $destinataire_id = ($conversation['utilisateurs_id'] == $_SESSION['user_id'])
                          ? $conversation['destinataire_id']
                          : $conversation['utilisateurs_id'];
        $produit_id = $conversation['produit_id'];

        // Insérer la réponse
        $stmt = $conn->prepare("INSERT INTO messages (utilisateurs_id, destinataire_id, produit_id, contenu, date_envoi, conversation_type, parent_id) VALUES (?, ?, ?, ?, NOW(), 'annonce', ?)");
        $stmt->bind_param("iiisi", $expediteur_id, $destinataire_id, $produit_id, $message, $conversation_id);

        if ($stmt->execute()) {
            $success_message = "Réponse envoyée avec succès.";
        } else {
            $error_message = "Erreur lors de l'envoi de la réponse.";
        }
    }
}

// Récupérer les conversations (premiers messages)
$stmt = $conn->prepare("
    SELECT m.id, m.contenu, m.date_envoi, m.utilisateurs_id, 
           u_exp.nom AS expediteur_nom, u_dest.nom AS destinataire_nom,
           p.nom AS produit_nom, p.image AS produit_image,
           p.id AS produit_id
    FROM messages m
    JOIN utilisateurs u_exp ON m.utilisateurs_id = u_exp.id
    JOIN utilisateurs u_dest ON m.destinataire_id = u_dest.id
    JOIN produits p ON m.produit_id = p.id
    WHERE (m.utilisateurs_id = ? OR m.destinataire_id = ?)
    AND m.parent_id IS NULL
    ORDER BY m.date_envoi DESC
");
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$conversations = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - LeBonDuCoin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .message-container {
            max-height: 400px;
            overflow-y: auto;
        }
        .message {
            border-radius: 15px;
            padding: 10px 15px;
            margin-bottom: 10px;
            max-width: 80%;
        }
        .message-sent {
            background-color: #dcf8c6;
            margin-left: auto;
        }
        .message-received {
            background-color: #f1f0f0;
        }
        .message-time {
            font-size: 0.75rem;
            color: #999;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar nav-underline fixed-top navbar-expand-lg bg-body-tertiary shadow p-3 mb-1 bg-body-tertiary rounded">
        <div class="container-fluid">
            <a class="navbar-brand" href="connexion.php"><i class="fas fa-user"></i></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarScroll">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-home"></i></a></li>
                    <li class="nav-item"><a class="nav-link" href="favoris.php"><i class="fas fa-heart"></i></a></li>
                    <li class="nav-item"><a class="nav-link active" href="messages.php"><i class="fas fa-envelope"></i></a></li>
                </ul>
                <div class="d-grid gap-2 col-3 mx-auto">
                    <p class="fs-4 fst-italic fw-bold">LeBon<span class="text-success">DuCoin</span></p>
                </div>
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="deconnexion.php"><i class="fas fa-sign-out-alt"></i></a></li>
                </ul>
                <form class="d-flex" role="search" action="search.php" method="GET">
                   <input class="form-control me-2" type="search" placeholder="Recherche..." name="search_term">
                   <button class="btn btn-outline-success" type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>
    </nav>
    <!-- FIN NAVBAR -->
    <br><br><br><br><br>
    <h2 class="text-center">Mes messages</h2>
    <hr>
    <div class="container mt-5 pt-5">

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-success text-white"><h5 class="mb-0">Conversations</h5></div>
                    <div class="list-group list-group-flush">
                        <?php if ($conversations->num_rows > 0): ?>
                            <?php while ($conversation = $conversations->fetch_assoc()): ?>
                                <a href="#conversation-<?php echo $conversation['id']; ?>" class="list-group-item list-group-item-action d-flex align-items-center" data-bs-toggle="pill">
                                    <img src="uploads/<?php echo htmlspecialchars($conversation['produit_image']); ?>" alt="<?php echo htmlspecialchars($conversation['produit_nom']); ?>" class="me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                    <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($conversation['produit_nom']); ?></h6>
                                        <small class="text-muted">
                                            <?php 
                                            echo ($conversation['utilisateurs_id'] == $user_id) 
                                                ? "Vous → " . htmlspecialchars($conversation['destinataire_nom'])
                                                : htmlspecialchars($conversation['expediteur_nom']) . " → Vous";
                                            ?>
                                        </small>
                                    </div>
                                </a>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="list-group-item">Aucune conversation pour le moment.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="tab-content">
                    <?php 
                    $conversations->data_seek(0);
                    $first = true;
                    while ($conversation = $conversations->fetch_assoc()):
                    ?>
                        <div class="tab-pane fade <?php echo $first ? 'show active' : ''; ?>" id="conversation-<?php echo $conversation['id']; ?>">
                            <div class="card">
                                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><?php echo htmlspecialchars($conversation['produit_nom']); ?></h5>
                                    <a href="detail.php?id=<?php echo $conversation['produit_id']; ?>" class="btn btn-sm btn-light">Voir l'annonce</a>
                                </div>
                                <div class="card-body">
                                    <div class="message-container">
                                        <?php
                                        $message_class = ($conversation['utilisateurs_id'] == $user_id) ? 'message-sent' : 'message-received';
                                        ?>
                                        <div class="message <?php echo $message_class; ?>">
                                            <div><?php echo nl2br(htmlspecialchars($conversation['contenu'])); ?></div>
                                            <div class="message-time"><?php echo date('d/m/Y H:i', strtotime($conversation['date_envoi'])); ?></div>
                                        </div>
                                        
                                        <?php
                                        $stmt = $conn->prepare("
                                            SELECT m.id, m.contenu, m.date_envoi, m.utilisateurs_id, u.nom as expediteur_nom
                                            FROM messages m
                                            JOIN utilisateurs u ON m.utilisateurs_id = u.id
                                            WHERE m.parent_id = ?
                                            ORDER BY m.date_envoi ASC
                                        ");
                                        $stmt->bind_param("i", $conversation['id']);
                                        $stmt->execute();
                                        $replies = $stmt->get_result();
                                        
                                        while ($reply = $replies->fetch_assoc()): 
                                            $reply_class = ($reply['utilisateurs_id'] == $user_id) ? 'message-sent' : 'message-received';
                                        ?>
                                            <div class="message <?php echo $reply_class; ?>">
                                                <div><?php echo nl2br(htmlspecialchars($reply['contenu'])); ?></div>
                                                <div class="message-time"><?php echo date('d/m/Y H:i', strtotime($reply['date_envoi'])); ?></div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>

                                    <form method="POST" action="" class="mt-3">
                                        <input type="hidden" name="conversation_id" value="<?php echo $conversation['id']; ?>">
                                        <div class="mb-3">
                                            <textarea class="form-control" name="message" rows="3" placeholder="Votre réponse..." required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success">Répondre</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php 
                        $first = false;
                    endwhile; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
