<?php
include 'bd.php';

$searchTerm = $_GET['search_term'];

$sql = "SELECT id, nom, description, prix, image 
       FROM produits 
       WHERE nom LIKE '%$searchTerm%' 
       OR description LIKE '%$searchTerm%'";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>
<body>
      <!-- NAVBAR -->
    <nav class="navbar nav-underline fixed-top navbar-expand-lg bg-body-tertiary shadow p-3 mb-1 bg-body-tertiary rounded">
        <div class="container-fluid">
            <a class="navbar-brand" href="connexion.php"><i class="fas fa-user"></i></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarScroll">
                <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php"><i class="fas fa-home"></i></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="favoris.php"><i class="fas fa-heart"></i></a>
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
                <form class="d-flex" role="search" action="search.php" method="GET">
                   <input class="form-control me-2" type="search" placeholder="Recherche..." aria-label="Search" name="search_term">
                   <button class="btn btn-outline-success" type="submit"><i class="fas fa-search"></i></button>
                   </form>
            </div>
        </div>
    </nav>
    <!-- FIN NAVBAR -->

    <br><br><br>

    <!-- barre de catégorie -->
    <div class="btn-group d-flex justify-content-center" role="group" aria-label="Second group">
        <button type="button" class="btn btn-success">.Sport</button>
        <button type="button" class="btn btn-success">.Electronique</button>
            <button type="button" class="btn btn-success">.Vêtement</button>
        <button type="button" class="btn btn-success">.Outil</button>
        <button type="button" class="btn btn-success">.Véhicule</button>
        <button type="button" class="btn btn-success">.Jeux</button>
        <button type="button" class="btn btn-success">.Loisir</button>
        <button type="button" class="btn btn-success">.Immobilier</button>
        <button type="button" class="btn btn-success">.Autre</button>
    </div>
    <!-- fin -->

    <br><br>
    <section class="out">
        <div class="out_1">
            <h1 class="">Voici le resultat de<span class="fs-1 fst-italic fw-bold">Votre<span class="text-success">Recherche</span></span></h1>
            
        </div>
    </section>

    <div class="container">
        <div class="row">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Afficher les résultats de la recherche de la même manière que dans index.php
                    echo '<div class="col-md-4">';
                    echo '<div class="card">';
                    echo '<a href="detail.php?id=' . $row['id'] . '" style="text-decoration: none; color: inherit;">';
                    echo '<img src="uploads/' . htmlspecialchars($row['image']) . '" class="card-img-top" alt="Annonce">';
                    echo '<div class="card-body">';
                    echo '<hr>';
                    echo '<h6 class="card-title text-center fw-bold">' . htmlspecialchars($row['nom']) . '</h6>';
                    echo '<p class="text-center fw-bold">' . htmlspecialchars($row['prix']) . ' €</p>';
                    echo '<div>';
                    echo '<form method="post" class="like-form">';
                    echo '<input type="hidden" name="produit_id" value="' . $row['id'] . '">';
                    echo '<button type="submit" class="btn btn-outline-secondary">';
                    echo '<i class="fas fa-heart"></i>';
                    echo '</button>';
                    echo '</form>';
                    echo '<a class="nav-link" href="chat.php"><i class="fas fa-envelope"></i></a>';
                    echo '</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "<p class='text-center'>Aucun résultat trouvé.</p>";
            }
    
            $conn->close();
            ?>
        </div>
    </div>
    <!--fin-->
    
    <!-- pied de page -->
        <footer class="pied">
            <div class="groupe-1">
                <div class="bo">
                    <figure>
                        <div class="d-grid gap-2 col-3">
                            <p class="fs-4 fst-italic fw-bold">LeBon<span class="text-dark">DuCoin</span></p>
                        </div>
                    </figure>
                </div>
                <div class="bo">
                    <h2>Bah voilà !</h2>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod</p>
                </div>
                <div class="bo">
                    <h2>Viola</h2>
                    <div class="red-social">
                        <i class="fab fa-whatsapp fa-2x icon"></i>
                        <i class="fab fa-instagram fa-2x icon"></i>
                        <i class="fas fa-times fa-2x icon"></i>
                    </div>
                </div>
            </div>
            <div class="groupe-2">
                <small>&copy; 2024 <b>shu gu</b> - lululululu</small>
            </div>
        </footer>
        <!-- fin -->
</body>
</html>
