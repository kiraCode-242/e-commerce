<?php
session_start();
include 'bd.php';

// Vérifiez si la connexion est réussie
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => "Échec de la connexion : " . $conn->connect_error]));
}

if (isset($_POST['produit_id']) && isset($_SESSION['user_id'])) {
    $produit_id = intval($_POST['produit_id']);
    $utilisateur_id = intval($_SESSION['user_id']);

    // Vérifie si l'annonce est déjà likée
    $check_sql = "SELECT * FROM favoris WHERE utilisateurs_id = ? AND produits_id = ?";
    $stmt = $conn->prepare($check_sql);
    if (!$stmt) {
        die(json_encode(['success' => false, 'message' => "Erreur de préparation : " . $conn->error]));
    }
    $stmt->bind_param("ii", $utilisateur_id, $produit_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Ajouter à la table favoris
        $sql = "INSERT INTO favoris (utilisateurs_id, produits_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die(json_encode(['success' => false, 'message' => "Erreur de préparation : " . $conn->error]));
        }
        $stmt->bind_param("ii", $utilisateur_id, $produit_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'liked' => true, 'message' => "Like ajouté"]);
        } else {
            echo json_encode(['success' => false, 'message' => "Erreur : " . $stmt->error]);
        }
    } else {
        // Si déjà liké, on supprime 
        $sql = "DELETE FROM favoris WHERE utilisateurs_id = ? AND produits_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die(json_encode(['success' => false, 'message' => "Erreur de préparation : " . $conn->error]));
        }
        $stmt->bind_param("ii", $utilisateur_id, $produit_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'liked' => false, 'message' => "Like retiré"]);
        } else {
            echo json_encode(['success' => false, 'message' => "Erreur : " . $stmt->error]);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => "Erreur : Connectez-vous pour réaliser cette action."]);
}

$conn->close();
?>
