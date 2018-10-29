<?php

    require_once("init.php");
    require_once("fonction.php");

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta name="author" content="TeamKeepers">

    <title>Eshop.com | Des promos, du délire ... c'est la folie</title>
    <meta name="description" content="<?= $seo_description ?>">

    <!-- appel favicon -->
    <link rel="shortcut icon" href="assets/img/favicon.ico" type="image/x-icon">

    <!-- Import du css font awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
    <!-- Import du css bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <!-- Feuille CSS perso -->
    <link rel="stylesheet" href="css/style.css">

</head>
<body>

    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
      <a class="navbar-brand" href="index.php">Eshop.com</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <a class="nav-link" href="index.php">Accueil <span class="sr-only">(current)</span></a>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="https://example.com" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Mon compte</a>
            <div class="dropdown-menu" aria-labelledby="dropdown01">

              <?php if(!userConnect()) : ?>
                <a class="dropdown-item" href="inscription.php">Inscription</a>
                <a class="dropdown-item" href="connexion.php">Connexion</a>
              <?php else : ?>
                <a class="dropdown-item" href="profil.php">Mon profil</a>
                <a class="dropdown-item" href="deconnexion.php">Déconnexion</a>
              <?php endif; ?>

            </div>
          </li>
          </li>

          <?php if(userAdmin()) : ?>
            <li class="nav-item">
              <a class="nav-link" href="ADMIN/">Admin</a>
            </li>
          <?php endif; ?>
          
        </ul>
        <a href="panier.php" class="btn btn-outline-success my-2"><i class="fas fa-shopping-cart"></i><?php if(nombreProduit()){echo'<span class="badge badge-primary badge-pill">' . nombreProduit() . '</span>';} ?></a>
      </div>
    </nav>

    <main role="main" class="container">