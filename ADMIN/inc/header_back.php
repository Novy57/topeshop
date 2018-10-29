<?php

    require_once("../inc/init.php");
    require_once(RACINE . "/inc/fonction.php");

    if (!userAdmin()) {
        header("location:" . URL);
        exit();
    }

    if (isset($_GET['a']) && $_GET['a'] == "deconnect") {
        unset($_SESSION['user']);
        header("location:" . URL);
        die();
    }


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta name="author" content="TeamKeepers">

    <title>Administration | Eshop.com</title>

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

    <nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
      <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="<?= URL ?>ADMIN/">Eshop.com</a>
      <div class="btn-group btn-group-toggle">
        <label class="btn btn-secondary active">
            <a class="btn btn-light" href="<?= URL ?>">Retour au site</a>
        </label>
        <label class="btn btn-secondary">
            <a class="btn btn-warning" href="?a=deconnect" data-toggle="tooltip" data-placement="bottom" title="Déconnexion"><i class="fas fa-power-off"></i></a>
        </label>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
          <div class="sidebar-sticky">
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
              <span>GESTION DES PRODUITS</span>
              <a class="d-flex align-items-center text-muted" href="#">
                <span data-feather="plus-circle"></span>
              </a>
            </h6>
            <ul class="nav flex-column">
              <li class="nav-item">
                <a class="nav-link active" href="<?= URL ?>ADMIN/">
                  <span data-feather="home"></span>
                  Liste des produits <span class="sr-only">(current)</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="formulaire_produit.php">
                  <span data-feather="file"></span>
                  Ajouter un produit
                </a>
              </li>
            </ul>

            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
              <span>GESTION DES UTILISATEURS</span>
              <a class="d-flex align-items-center text-muted" href="#">
                <span data-feather="plus-circle"></span>
              </a>
            </h6>
            <ul class="nav flex-column mb-2">
              <li class="nav-item">
                <a class="nav-link" href="#">
                  <span data-feather="file-text"></span>
                  Liste des utilisateurs
                </a>
              </li>
            </ul>
          </div>
        </nav>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">

        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2"><?= $page ?></h1>
        </div>