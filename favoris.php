
<?php
include 'bd.php';
session_start();
if(!isset($_SESSION["user_id"])){
   header("location:connexion.php");
 }

if (!isset($_SESSION['user_id'])) {
    echo "Veuillez vous connecter pour voir vos favoris.";
    exit;
}

$utilisateur_id = $_SESSION['user_id'];
$sql = "SELECT p.nom, p.prix, p.image, p.id 
        FROM produits p
        INNER JOIN favoris f ON p.id = f.produits_id
        WHERE f.utilisateurs_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $utilisateur_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Favoris</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="style.css">
</head>
<body>
<!-- NAVBAR -->
<nav class="navbar nav-underline fixed-top navbar-expand-lg bg-body-tertiary shadow p-3 mb-4 bg-body-tertiary rounded">
        <div class="container-fluid">
            <a class="navbar-brand" href="connexion.php"><i class="fas fa-user"></i></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarScroll">
                <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="index.php"><i class="fas fa-home"></i></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="favoris.php"><i class="fas fa-heart"></i></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="messages.php"><i class="fas fa-envelope"></i></a>
                    </li>
                </ul>
                <div class="d-grid gap-2 col-3 mx-auto">
                    <p class="fs-4 fst-italic fw-bold">LeBon<span class="text-success">DuCoin</span></p>
                </div>
                <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
                    <li class="nav-item">
                        <a class="nav-link" href="deconnexion.php"><i class="fas fa-sign-out-alt"></i></a>
                    </li>
                </ul>
                <form class="d-flex" role="search">
                    <input class="form-control me-2" type="search" placeholder="Recherche..." aria-label="Search">
                    <button class="btn btn-outline-success" type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>
    </nav>
    <!-- FIN NAVBAR -->



<div class="container">
    <div class="row">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="col-md-4">';
                echo '<div class="card">';
                echo '<a href="detail.php?id=' . $row['id'] . '" style="text-decoration: none; color: inherit;">';
                echo '<img src="uploads/' . htmlspecialchars($row['image']) . '" class="card-img-top" alt="Annonce">';
                echo '<div class="card-body">';
                echo '<hr>';
                echo '<h6 class="card-title text-center fw-bold">' . htmlspecialchars($row['nom']) . '</h6>';
                echo '<p class="text-center fw-bold">' . htmlspecialchars($row['prix']) . ' €</p>';
                echo '<a href="sup.php?id=' . $row["id"] . '" class="link-dark"><i class="fa-solid fa-trash fs-5"></i></a>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo "<p class='text-center'>Aucun favori pour l'instant !</p>";
        }
        ?>
    </div>
</div>

</body>
</html>
