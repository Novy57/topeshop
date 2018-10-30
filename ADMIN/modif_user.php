<?php

    # Règles SEO
    $page = "Modification";
    $seo_description = "Rejoignez le club des meilleures affaires en ligne: jusqu'à -80%";

    require_once("inc/header_back.php");

    if(isset($_GET["id"]) && is_numeric($_GET["id"]))
    {
        
        $req = "SELECT * FROM membre WHERE id_membre = :id";

        $result = $pdo->prepare($req);

        $result->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
        $result->execute();

        $user = $result->fetch() ;
    }

    // debug($_POST);

    if($_POST)
    {
        // VERIFICATIONS POSSIBLE ICI 

        if(empty($msg))
        {
        
            // check si le pseudo est dispo
            $result = $pdo->prepare("SELECT pseudo FROM membre WHERE pseudo = :pseudo");
            $result->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
            $result->execute();

            if($result->rowCount() == 1 && $_POST["pseudo"] != $user['pseudo'])
            {
                $msg .= "<div class='alert alert-danger'>Le pseudo $_POST[pseudo] est déjà pris, veuillez en choisir un autre.</div>";
            }
            else 
            {
                $result = $pdo->prepare("UPDATE membre SET pseudo=:pseudo, nom=:nom, prenom=:prenom, email=:email, civilite=:civilite, ville=:ville, code_postal=:code_postal, adresse=:adresse, statut=:statut WHERE id_membre = :id");

                $result->bindValue(":id", $_POST['id_membre'], PDO::PARAM_INT);

                $result->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
                $result->bindValue(':nom', $_POST['nom'], PDO::PARAM_STR);
                $result->bindValue(':prenom', $_POST['prenom'], PDO::PARAM_STR);
                $result->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
                $result->bindValue(':civilite', $_POST['civilite'], PDO::PARAM_STR);
                $result->bindValue(':ville', $_POST['ville'], PDO::PARAM_STR);
                $result->bindValue(':adresse', $_POST['adresse'], PDO::PARAM_STR);
                $result->bindValue(':code_postal', $_POST['code_postal'], PDO::PARAM_INT);
                $result->bindValue(':statut', $_POST['statut'], PDO::PARAM_INT);
                
                
                if($result->execute()) # Si j'enregistre bien en BDD
                {
                    header("location:list_user.php?m=update");
                }
                else
                {
                    echo "CA MARCHE PAS";
                }
            }
        }
    }
        
        # Je souhaite conserver les valeurs rentrées par l'utilisateur durant le processus de rechargement de la page

if(isset($user['pseudo'])){$pseudo = $user['pseudo']; } elseif(isset($_POST['pseudo'])) {$pseudo=$_POST['pseudo'];} else {$pseudo = "";}
if(isset($user['nom'])){$nom = $user['nom']; } elseif(isset($_POST['nom'])) {$nom=$_POST['nom'];} else {$nom = "";}
if(isset($user['email'])){$email = $user['email']; } elseif(isset($_POST['email'])) {$email=$_POST['email'];} else {$email = "";}
if(isset($user['prenom'])){$prenom = $user['prenom']; } elseif(isset($_POST['prenom'])) {$prenom=$_POST['prenom'];} else {$prenom = "";}
if(isset($user['id_membre'])){$id_membre = $user['id_membre']; } elseif(isset($_POST['id_membre'])) {$id_membre=$_POST['id_membre'];} else {$id_membre = "";}
if(isset($user['civilite'])){$civilite = $user['civilite']; } elseif(isset($_POST['civilite'])) {$civilite=$_POST['civilite'];} else {$civilite = "";}
if(isset($user['adresse'])){$adresse = $user['adresse']; } elseif(isset($_POST['adresse'])) {$adresse=$_POST['adresse'];} else {$adresse = "";}
if(isset($user['code_postal'])){$code_postal = $user['code_postal']; } elseif(isset($_POST['code_postal'])) {$code_postal=$_POST['code_postal'];} else {$code_postal = "";}
if(isset($user['ville'])){$ville = $user['ville']; } elseif(isset($_POST['ville'])) {$ville=$_POST['ville'];} else {$ville = "";}
if(isset($user['statut'])){$statut = $user['statut']; } elseif(isset($_POST['statut'])) {$statut=$_POST['statut'];} else {$statut = "";}

        
    ?>

    <div class="starter-template">
    <h1><?= $page ?></h1>
        <form action="" method="post">
            <?= $msg ?>
            <input type="hidden" name="id_membre" value="<?=$id_membre?>">
            <div class="form-group">
                <label for="pseudo">Pseudo</label>
                <input type="text" class="form-control" id="pseudo" placeholder="Choisissez votre pseudo ..." name="pseudo" required value="<?= $pseudo ?>">
                
            </div>
            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input type="text" class="form-control" id="prenom" placeholder="Quel est votre prénom ..." name="prenom" value="<?= $prenom ?>">
            </div>
            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" class="form-control" id="nom" placeholder="Quel est votre nom ..." name="nom" value="<?= $nom ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" placeholder="Entrez votre email ..." name="email" value="<?= $email ?>">
            </div>
            <div class="form-group">
                <label for="civilite">Civilité</label>
                <select class="form-control" id="civilite" name="civilite">
                    <option value="Homme" <?php if ($civilite == 'Homme') {echo 'selected';} ?> >Homme</option>
                    <option value="Femme" <?php if($civilite == 'Femme'){echo 'selected';} ?> >Femme</option>
                    <option value="Other" <?php if ($civilite == 'Other') {echo 'selected';} ?> >Je ne souhaite pas le préciser</option>
                </select>
            </div>
            <div class="form-group">
                <label for="adresse">Adresse</label>
                <input type="text" class="form-control" id="adresse" placeholder="Quelle est votre adresse ..." name="adresse" value="<?= $adresse ?>">
            </div>
            <div class="form-group">
                <label for="code_postal">Code postal</label>
                <input type="text" class="form-control" id="code_postal" placeholder="Quel est votre code postal ..." name="code_postal" value="<?= $code_postal ?>">
            </div>
            <div class="form-group">
                <label for="ville">Ville</label>
                <input type="text" class="form-control" id="ville" placeholder="Quelle est votre ville ..." name="ville" value="<?= $ville ?>">
            </div>
            <div class="form-group">
                <label for="statut">Statut</label>
                <select  class="form-control" id="statut" name="statut">
                    <option value="0" <?php if($statut == '0'){echo 'selected';} ?>>User</option>
                    <option value="1" <?php if($statut == '1'){echo 'selected';} ?>>Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-lg btn-block">Modifier</button>
        </form>
    </div>

<?php require_once("inc/footer_back.php"); ?>