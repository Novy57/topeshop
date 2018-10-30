<?php

    # Règles SEO
    $page = "Mon profil";
    $seo_description = "Regardez votre profil qui est sublime, magnifique, vous êtes une star !";

    require_once("inc/header.php");

    if(!userConnect())
    {
        header("location:connexion.php");
        exit(); // die() fonctionne aussi
    }

    if(isset($_GET['a']) && isset($_GET['id']) && $_GET['a'] == "delete" && is_numeric($_GET['id'])) # la fonction is_numeric() me permet de vérifier que le paramètre rentré est bien un chiffre
    {
        $req = "SELECT * FROM membre WHERE id_membre = :id";
        $result = $pdo->prepare($req);
        $result->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
        $result->execute();
        // debug($result);

        if($result->rowCount() == 1)
        {
            $membre = $result->fetch();
            
            $delete_req = "DELETE FROM membre WHERE id_membre = $membre[id_membre]";
            
            $delete_result = $pdo->exec($delete_req);

            if($delete_result)
            {
                //$chemin_photo = RACINE . 'assets/uploads/admin/' . $produit['photo'];
                
                //if(file_exists($chemin_photo) && $produit['photo'] != "default.jpg") # la fonction fil_exists() me permet de vérifier si le fichier existe bel et bien
                //{
                    //unlink($chemin_photo); # la fonction unlink() me permet de supprimer un fichier
                //}
                
                header("location:deconnexion.php?");
            }
            else
            {
                header("location:profil.php?m=fail");  
            }
        }
        else 
        {
            header("location:profil.php?m=fail");    
        }
    }

    // Résultat supression profil
    if(isset($_GET['m']) && !empty($_GET['m']))
    {
        switch($_GET['m'])
        {
            case "fail":
            $msg .= "<div class='alert alert-danger'>Une erreur est survenue, veuillez réessayer.</div>";
            break;
            default:
            $msg .= "<div class='alert alert-warning'>C'est inattendu !!!! Pas compris !</div>";
            break;
        }
    }
   
    // Changement photo
    if($_POST)
    {
        # Je m'occupe du fichier envoyé : une photo !
        if(!empty($_FILES['photo']['name']))
        {
            # Nous allons donner un nom aléatoire à notre photo
            $nom_photo = $_SESSION['user']['prenom'] . '_' . $_SESSION['user']['nom'] . '_' . time() . '-' . rand(1,999) . $_FILES['photo']['name'];
            $nom_photo = str_replace(' ', '-', $nom_photo);
            $nom_photo = str_replace(array('é','è','à','ç','ù'), 'x', $nom_photo);
            
            // Enregistrons le chemin de notre fichier
            $chemin_photo = RACINE . 'assets/uploads/user/' . $nom_photo;
            
            $taille_max = 2*1048576; # On définit ici la taille maximale autorisée (2Mo)
            if($_FILES['photo']["size"] > $taille_max || empty($_FILES['photo']["size"]))
            {
                $msg .= "<div class='alert alert-danger'>Veuillez sélectionner un fichier de 2Mo maximum.</div>";
            }
            
            $type_photo = [
                'image/jpeg',
                'image/png',
                'image/gif'
            ];
            if (!in_array($_FILES['photo']["type"], $type_photo) || empty($_FILES['photo']["type"])) 
            {
                $msg .= "<div class='alert alert-danger'>Veuillez sélectionner un fichier JPEG/JPG, PNG ou GIF.</div>";
            }
            
            if(empty($msg))
            {
                $result = $pdo->prepare("UPDATE membre SET photo=:photo WHERE id_membre = :id_membre");
                $result->bindValue(":id_membre", $_SESSION['user']['id_membre'], PDO::PARAM_INT);
                $result->bindValue(':photo', $nom_photo, PDO::PARAM_STR);
                
                if($result->execute()) # Si j'enregistre bien en BDD
                {
                    copy($_FILES['photo']['tmp_name'], $chemin_photo);
                    $_SESSION['user']['photo'] = $nom_photo;
                    $msg .= "<div class='alert alert-success'>Votre nouvelle photo est bien enregistrée !</div>";
                }
            }
        }
    }

    // Visu du profil
    foreach($_SESSION['user'] as $key => $value)
    {
        $info[$key] = htmlspecialchars($value); # nous vérifions que les informations à afficher ne comporte pas d'injections et ne perturberont pas notre service
    }


    // Supression d'un profil par modal de supression (fonction créée dans fonction.php)
    deleteModal($info['id_membre'], "de " . $info['prenom'] . " " . $info['nom'], "", "profil");

    // debug($info);

?>

    <div class="starter-template">
        <h1><?= $page ?></h1>
        <div class="card">
            <div class="row">
                <div class="col">

                    <img class="card-img-top img-thumbnail rounded mx-auto d-block" src="<?=URL?>/assets/uploads/user/<?= $info['photo'] ?>" alt="Card image cap" style="width:50%;">
                </div>
                <div class="col align-self-center mx-4">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="photo">Pour modifier votre photo de profil, choisir un fichier, puis valider :</label>
                            <input type="file" class="form-control-file" id="photo" name="photo">
                        </div>
                        <!--<input type="submit" value="Valider" class="btn btn-info btn-lg btn-block">-->
                        <button type="submit" class="btn btn-primary btn-lg btn-block" name="photoValid">Valider</button>
                    </form>
                </div>
            </div>

            <div class="card-body">
                <h5 class="card-title">Bonjour <?= $info['pseudo'] ?></h5>
                <p class="card-text">Nous sommes râvi de vous revoir sur notre plateforme.</p>
            </div>  
            <ul class="list-group list-group-flush">
                <li class="list-group-item">Prénom: <?= $info['prenom'] ?></li>
                <li class="list-group-item">Nom: <?= $info['nom'] ?></li>
                <li class="list-group-item">Email: <?= $info['email'] ?></li>
                <li class="list-group-item">Civilité: <?php switch($info['civilite']){case "m": echo "homme"; break; case "f": echo "femme"; break; default: echo "Non défini"; break;} ?></li>
                <li class="list-group-item">Adresse: <?= $info['adresse'] ?></li>
                <li class="list-group-item">Code postal: <?= $info['code_postal'] ?></li>
                <li class="list-group-item">Ville: <?= $info['ville'] ?></li>
            </ul>
            <div class="card-body">
                <a href="inscription.php?id=<?= $info['id_membre'] ?>" class="card-link"><i class='fas fa-pen'></i></a>
                <a data-toggle='modal' data-target='#deleteModal<?= $info['id_membre'] ?>'><i class='fas fa-trash-alt'></i></a>
            </div>
        </div>
    </div>

<?php require_once("inc/footer.php"); ?>
