<?php

    # Règles SEO
    $page = "Page produit";
    $seo_description = "Un choix très large de produits assemblés en France par des travailleurs non déclarés.";

    require_once("inc/header.php");

    if(isset($_GET['id']) && is_numeric($_GET['id']))
    {
        $result = $pdo->prepare("SELECT * FROM produit WHERE id_produit = :id_produit");
        $result->bindValue(':id_produit', $_GET['id'], PDO::PARAM_INT);
        $result->execute();

        $produit = $result->fetch();
    }
    else 
    {
        header("location:index.php");
    }

    // debug($produit);

?>
    <div class="starter-template">
        <h1><?= $page ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?=URL?>">Accueil</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="index.php?cat=<?= $produit['categorie'] ?>"><?=$produit['categorie']?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?=$produit['titre']?></li>
            </ol>
        </nav>
    </div>

    <div class="card text-center">
        <div class="card-header">
            <img src="assets/uploads/admin/<?=$produit['photo']?>" alt="<?= $produit['titre'] ?>" class="card-img-top img-fluid" style='width:50%;'>
        </div>
        <div class="card-body">
            <h5 class="card-title"><?= $produit['titre'] ?></h5>
            <p class="card-text"><?= $produit['description'] ?></p>
            <div class="card">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Categorie: <a href="index.php?cat=<?= $produit['categorie'] ?>"><?= $produit['categorie'] ?></a></li>
                    <li class="list-group-item">Couleur: <?= $produit['couleur'] ?></li>
                    <li class="list-group-item">Taille: <?= $produit['taille'] ?></li>
                    <li class="list-group-item">Public: <?= $produit['public'] ?></li>
                </ul>
            </div>
            <div class="btn btn-danger btn-lg btn-block">
                Prix <span class="badge badge-warning"><?= $produit['prix'] ?> €</span>
            </div>
            <?php if($produit['stock'] > 0) : ?>
                <form method="post" action="panier.php">
                    <input type="hidden" name="id_produit" value="<?= $produit['id_produit'] ?>">
                    <div class="form-group row">
                        <div class='form-group col-sm-8'>
                            <select class="form-control" name="quantite">
                                <option selected disabled>Quantité ...</option>
                                <?php for($i=1; $i <= $produit['stock']; $i++) : ?>
                                    <option><?=$i?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="form-group col-sm-4">
                            <input type="submit" class="btn btn-success btn-block" value="Ajouter au panier" name="ajoutPanier">
                        </div>
                    </div>
                </form>
            <?php else : ?>
                <p>Nous sommes malheureusement en rupture de stock.</p>
            <?php endif; ?>
        </div>
        <div class="card-footer text-muted">
            <p>Stock disponible: <?= $produit['stock'] ?> </p>
        </div>
    </div>

<?php require_once("inc/footer.php"); ?>