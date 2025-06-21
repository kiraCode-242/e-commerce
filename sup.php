<?php
session_start();
include 'bd.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

if (isset($_GET['id'])) {
    $produit_id = intval($_GET['id']);
    $utilisateur_id = intval($_SESSION['user_id']);
    
    $sql = "DELETE FROM favoris WHERE utilisateurs_id = ? AND produits_id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("ii", $utilisateur_id, $produit_id);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            header("Location: favoris.php?success=1");
        } else {
            header("Location: favoris.php?error=1");
        }
    } else {
        header("Location: favoris.php?error=2");
    }
} else {
    header("Location: favoris.php");
}

$conn->close();
?>