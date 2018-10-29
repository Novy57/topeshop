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
            <img class="card-img-top img-thumbnail rounded mx-auto d-block" src="<?=URL?>/assets/uploads/user/default.png" alt="Card image cap" style="width:25%;">
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
