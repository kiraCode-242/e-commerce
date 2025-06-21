<?php
include 'bd.php';

if (isset($_GET['categorie'])) {
    $categorie = $_GET['categorie'];
    $stmt = $conn->prepare("SELECT id, nom, description, prix, image FROM produits WHERE categorie_id = ?");
    $stmt->bind_param("s", $categorie);
    $stmt->execute();
    $result = $stmt->get_result();

    $annonces = [];
    while ($row = $result->fetch_assoc()) {
        $annonces[] = $row;
    }
    echo json_encode($annonces);
}
?>