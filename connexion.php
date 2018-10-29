<?php

    # Règles SEO
    $page = "Connexion";
    $seo_description = "Infiltrez le club le plus privé des meilleures affaires en ligne en France";

    require_once("inc/header.php");

    if(isset($_GET['m']) && $_GET['m'] == "success")
    {
        $msg .= "<div class='alert alert-success'>Vous êtes bien inscrit. Veuillez désormais vous connecter.</div>";
    }

    // debug($_POST);

    if($_POST)
    {

        $req = "SELECT * FROM membre WHERE pseudo = :pseudo";

        $result = $pdo->prepare($req);
        $result->bindValue(":pseudo", $_POST['pseudo'], PDO::PARAM_STR);

        $result->execute();

        if($result->rowCount() == 1) # Nous avons sélectionner le résultat en BDD correspondant à un pseudo
        {
            $user = $result->fetch();

            // debug($user);

            if(password_verify($_POST['password'], $user['mdp'])) # la fonction password_verify() est en lien avec password_hash(). Elle me permet de vérifier la correspondance entre une donnée rentrée et un hash. Elle prend 2 arguments : valeur rentrée + le hash à matcher
            {
                // $_SESSION['user']['pseudo'] = $user['pseudo'];
                // $_SESSION['user']['prenom'] = $user['prenom'];

                foreach ($user as $key => $value) 
                {
                    if($key != "mdp")
                    {
                        $_SESSION['user'][$key] = $value;

                        header("location:profil.php");
                    }
                }
            }
            else 
            {
                $msg .= "<div class='alert alert-danger'>Erreur d'identification, veuillez réessayer.</div>";
            }
        }
        else 
        {
            $msg .= "<div class='alert alert-danger'>Erreur d'identification, veuillez réessayer.</div>";
        }

    }

?>

    <div class="starter-template">
        <h1><?= $page ?></h1>

        <form action="" method="post">
            <?= $msg ?>
            <div class="form-group">
                <label for="pseudo">Pseudo</label>
                <input type="text" class="form-control" id="pseudo" placeholder="Quel est votre pseudo ..." name="pseudo" required>
                
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" class="form-control" id="password" placeholder="Quel est votre mot de passe ..." name="password" required>
            </div>
            <button type="submit" class="btn btn-success btn-lg btn-block">Connexion</button>
        </form>

    </div>

<?php require_once("inc/footer.php"); ?>