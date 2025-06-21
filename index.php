<?php
// Connexion à la base de données
include 'bd.php';
session_start();

// Vérifier si une catégorie est sélectionnée
$categorie_filtre = isset($_GET['categorie']) ? $_GET['categorie'] : null;

// Compter le nombre d'articles dans le panier
$nombre_articles = isset($_SESSION['panier']) ? count($_SESSION['panier']) : 0;

// Requête SQL pour récupérer les annonces
if ($categorie_filtre) {
    $stmt = $conn->prepare("
        SELECT p.id, p.nom, p.description, p.prix, p.image, c.nom AS categories 
        FROM produits p
        JOIN categories c ON p.categories_id = c.categories_id
        WHERE c.nom = ?
        ORDER BY p.nom ASC
    ");
    $stmt->bind_param("s", $categorie_filtre);
} else {
    $stmt = $conn->prepare("
        SELECT p.id, p.nom, p.description, p.prix, p.image, c.nom AS categories 
        FROM produits p
        JOIN categories c ON p.categories_id = c.categories_id
        ORDER BY c.nom ASC
    ");
}

$stmt->execute();
$result = $stmt->get_result();

$annonces_par_categorie = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categorie = $row['categories'];
        $annonces_par_categorie[$categorie][] = $row;
    }
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- lien pour le css-->
    <link rel="stylesheet" href="style.css">
    <!-- lien pour le bootstrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- lien pour le fontAwesome-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <title>LeBonDuCoin</title>
    <style>
        .card {
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .like-btn {
            cursor: pointer;
            color: #ccc;
        }
        .like-btn.liked {
            color: red;
        }
        .navbar-brand, .navbar-nav .nav-link {
            font-size: 1.2rem;
        }
        .btn-group .btn {
            margin: 5px;
        }
        footer {
            background-color: #f8f9fa;
            padding: 20px 0;
            text-align: center;
        }
        footer .icon {
            margin: 0 10px;
            color: #6c757d;
        }
        footer .icon:hover {
            color: #000;
        }
    </style>
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
                    <li class="nav-item">
                        <a class="nav-link" href="panier.php">
                            <i class="fas fa-shopping-cart"></i> Panier
                            <span class="badge bg-danger"><?php echo $nombre_articles; ?></span>
                        </a>
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

    <!-- CAROUSEL -->
    <div id="carouselExampleCaptions" class="carousel slide">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="img/ins-b.jpg" class="d-block w-100" alt="Image de l'annonce 1">
                <div class="carousel-caption d-none d-md-block">
                    <div class="d-grid gap-2 col-6 mx-auto">
                        <a href="annonce.php"><button class="btn btn-success" type="button"><i class="fas fa-plus btn btn-outline-success"></i> Déposez votre annonce</button></a>
                    </div>
                    <p>C'est le moment de vendre, alors n'hésite pas.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="img/all-bg-title.jpg" class="d-block w-100" alt="Image de l'annonce 2">
                <div class="carousel-caption d-none d-md-block">
                    <div class="d-grid gap-2 col-6 mx-auto">
                        <a href="annonce.php"><button class="btn btn-success" type="button"><i class="fas fa-plus"></i> Déposez votre annonce</button></a>
                    </div>
                    <p>C'est le moment de vendre, alors n'hésite pas.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="img/baner-01.jpg" class="d-block w-100" alt="Image de l'annonce 3">
                <div class="carousel-caption d-none d-md-block">
                    <div class="d-grid gap-2 col-6 mx-auto">
                        <a href="annonce.php"><button class="btn btn-success" type="button"><i class="fas fa-plus"></i> Déposez votre annonce</button></a>
                    </div>
                    <p>C'est le moment de vendre, alors n'hésite pas.</p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Précédent</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Suivant</span>
        </button>
    </div>
    <!-- fin carrousel -->

    <br><br><br>

    <!-- barre de catégorie -->
    <div class="container my-4">
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <?php 
            $categories = ["Toutes", "Sport", "Electronique", "Vêtement", "Outil", "Véhicule", "Jeux", "Loisir", "Immobilier", "Autre"];
            foreach ($categories as $cat):
                $url = $cat === "Toutes" ? "index.php" : "index.php?categorie=" . urlencode($cat);
            ?>
                <a href="<?= $url ?>" class="btn <?= ($categorie_filtre == $cat || ($cat == 'Toutes' && !$categorie_filtre)) ? 'btn-success' : 'btn-outline-success' ?>"><?= htmlspecialchars($cat) ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <br><br>

    <!-- phrase d'accroche -->
    <section class="out">
        <div class="out_1">
            <h1 class="">EN CE MOMENT SUR <span class="fs-1 fst-italic fw-bold">LeBon<span class="text-success">DuCoin</span></span></h1>
            <br>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
        </div>
    </section>
    <!--fin-->

    <!-- petite anime img-->
     <hr>
    <div class="container">
        <div class="box">
            <img src="img/women-shoes-img.jpg">
        </div>
        <div class="box">
            <img src="img/shoes-img.jpg">
            
        </div>
        <div class="box">
            <img src="img/img-pro-04.jpg">
        </div>
        <div class="box">
            <img src="img/women-bag-img.jpg">
        </div>
        <div class="box">
            <img src="img/img-pro-03.jpg">
        </div>
        
    </div>
    <!-- fin -->
    <hr>


    <!-- Exemple d'annonce avec filtrage amélioré -->
    <h2 class="text-center">Annonces <?= $categorie_filtre ? 'de la catégorie ' . htmlspecialchars($categorie_filtre) : 'récentes' ?></h2>
    <div class="container">
 <div class="row">
        <?php
        // Préparer la requête SQL en fonction de la catégorie sélectionnée
        if ($categorie_filtre) {
            $sql = "SELECT p.id, p.nom, p.description, p.prix, p.image, 
                   (SELECT COUNT(*) FROM favoris f WHERE f.produits_id = p.id AND f.utilisateurs_id = ?) as est_favori 
                   FROM produits p
                   JOIN categories c ON p.categories_id = c.categories_id
                   WHERE c.nom = ?
                   ORDER BY p.id DESC";
            $stmt = $conn->prepare($sql);
            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
            $stmt->bind_param("is", $user_id, $categorie_filtre);
        } else {
            $sql = "SELECT p.id, p.nom, p.description, p.prix, p.image, 
                   (SELECT COUNT(*) FROM favoris f WHERE f.produits_id = p.id AND f.utilisateurs_id = ?) as est_favori 
                   FROM produits p
                   ORDER BY p.id DESC";
            $stmt = $conn->prepare($sql);
            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
            $stmt->bind_param("i", $user_id);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();


        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $est_favori = $row['est_favori'] > 0 ? 'liked' : '';
                echo '<div class="col-md-4 mb-4">';
                echo '<div class="card h-100">';
                echo '<a href="detail.php?id=' . $row['id'] . '" style="text-decoration: none; color: inherit;">';
                echo '<img src="uploads/' . htmlspecialchars($row['image']) . '" class="card-img-top" alt="Image de ' . htmlspecialchars($row['nom']) . '" style="height: 200px; object-fit: cover;">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title text-center fw-bold">' . htmlspecialchars($row['nom']) . '</h5>';
                echo '<p class="text-center text-muted">' . (strlen($row['description']) > 100 ? substr(htmlspecialchars($row['description']), 0, 100) . '...' : htmlspecialchars($row['description'])) . '</p>';
                echo '<p class="text-center fw-bold text-success">' . htmlspecialchars($row['prix']) . ' €</p>';
                echo '</a>';
                echo '<div class="d-flex justify-content-between align-items-center">';
                echo '<form class="like-form" action="like.php" method="post">';
                echo '<input type="hidden" name="produit_id" value="' . $row['id'] . '">';
                echo '<button type="submit" class="btn btn-outline-secondary"><i class="fas fa-heart like-btn ' . $est_favori . '"></i></button>';
                echo '</form>';
                echo '<a href="messages.php?id=' . $row['id'] . '#contact" class="btn btn-outline-success"><i class="fas fa-envelope"></i> Contacter</a>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo "<div class='col-12 text-center'>";
            echo "<div class='alert alert-warning'>";
            echo "<i class='fas fa-exclamation-triangle fa-2x mb-3'></i>";
            echo "<p>Aucune annonce disponible pour cette catégorie.</p>";
            echo "<p>Essayez une autre catégorie ou revenez plus tard.</p>";
            echo "</div>";
            echo "</div>";
        }
        $stmt->close();
        ?>
        </div>
    </div>
    <!-- fin -->

    <!-- pied de page -->
    <footer class="mt-5">
        <div class="container">
            <p class="fs-4 fst-italic fw-bold">LeBon<span class="text-dark">DuCoin</span></p>
            <p>&copy; 2024 <b>LeBonDuCoin</b> - Tous droits réservés.</p>
            <div class="red-social">
                <i class="fab fa-whatsapp fa-2x icon"></i>
                <i class="fab fa-instagram fa-2x icon"></i>
                <i class="fab fa-facebook fa-2x icon"></i>
            </div>
        </div>
    </footer>
    <!-- fin -->

    <!-- script java pour like et dislike -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.like-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Si l'utilisateur n'est pas connecté, rediriger vers la page de connexion
                <?php if(!isset($_SESSION['user_id'])): ?>
                    window.location.href = 'connexion.php';
                    return;
                <?php endif; ?>
                
                const formData = new FormData(this);
                const likeBtn = this.querySelector('.like-btn');
                
                fetch('like.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.liked) {
                            likeBtn.classList.add('liked');
                        } else {
                            likeBtn.classList.remove('liked');
                        }
                    } else {
                        alert('Erreur : ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                });
            });
        });
    });
    </script>
    <!-- fin -->
</body>
</html>