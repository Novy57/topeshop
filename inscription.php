<?php

    # Règles SEO
    $page = "Inscription";
    $seo_description = "Rejoignez le club des meilleures affaires en ligne: jusqu'à -80%";

    require_once("inc/header.php");

    if($_POST)
    {

        // debug($_POST, 2);

        # Je vérifie le pseudo
        if(!empty($_POST['pseudo']))
        {
            $pseudo_verif = preg_match("#^[a-zA-Z0-9-._]{3,20}$#", $_POST['pseudo']);
            # Ici, nous allons utiliser une expression régulière (REGEX). Une REGEX nous permet de vérifier une condition.
            # la fonction preg_match() nous permet de vérifier si une variable respecte la REGEX rentrée. Elle prend 2 arguments : REGEX + le résultat à vérifier. Elle nous retourne un TRUE/FALSE

            if(!$pseudo_verif) # équivaut à dire $pseudo_verif est FALSE
            {
                $msg .= "<div class='alert alert-danger'>Votre pseudo doit contenir des lettres (minuscules ou majuscules), un chiffre et doit posséder entre 3 et 20 caractères. Vous pouvez utiliser un caractère spécial ('-', '.', '_'). Veuillez réessayer !</div>";
            }

        }
        else 
        {
            $msg .= "<div class='alert alert-danger'>Veuillez rentrer un pseudo.</div>";
        }

        # Je vérifie le password
        if(!empty($_POST['password']))
        {
            $password_verif = preg_match('#^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[-+!*\'\?$@%_])([-+!*\?$\'@%_\w]{6,15})$#', $_POST['password']);

            if(!$password_verif)
            {
                $msg .= "<div class='alert alert-danger'>Votre mot de passe doit contenir entre 6 et 15 caractères avec au moins une majuscule, une minuscule, un nombre et un symbole. Veuillez réessayer !</div>";
            }
        }
        else 
        {
            $msg .= "<div class='alert alert-danger'>Veuillez rentrer un mot de passe.</div>";
        }

        # Je vérifie l'email
        if(!empty($_POST['email']))
        {
            $email_verif = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
            # la fonction filter_var() me permet de vérifier un résultat (email, URL ...). Elle prend 2 arguments : le résultat à vérifier + la méthode. Nous avons un retour un BOOL (TRUE/FALSE)

            $email_interdits = [
                'mailinator.com',
                'yopmail.com',
                'mail.com'
            ];

            $email_domain = explode('@', $_POST['email']); # On utilise la function explode() pour exploser un résultat en 2 partie selon le caractère choisit. Elle prend 2 arguments : le caractère ciblé, le résultat à analyser 

            // debug($email_domain);
            
            if(!$email_verif || in_array($email_domain[1], $email_interdits))
            # la fonction in_array() nous permet de vérifier que le résultat ciblé fait bien partie de l'ARRAY ciblé. Elle prends 2 arguments: le résultat à vérifier + le tableau ciblé
            {
                $msg .= "<div class='alert alert-danger'>Veuillez rentrer un email valide.</div>";
            }

        }
        else 
        {
            $msg .= "<div class='alert alert-danger'>Veuillez rentrer un email.</div>";
        }

        # Je vérifie que la civilité est valide
        if(!isset($_POST['civilite']) || ($_POST['civilite'] != "m" && $_POST['civilite'] != "f" && $_POST['civilite'] != "o"))
        {
            $msg .= "<div class='alert alert-danger'>Veuillez rentrer votre civilité.</div>";
        }

        // PLACER LES AUTRES VERIFICATIONS ICI

        if(empty($msg))
        {
            // check si le pseudo est dispo
            $result = $pdo->prepare("SELECT pseudo FROM membre WHERE pseudo = :pseudo");
            $result->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
            $result->execute();

            if($result->rowCount() == 1)
            {
                $msg .= "<div class='alert alert-danger'>Le pseudo $_POST[pseudo] est déjà pris, veuillez en choisir un autre.</div>";
            }
            else 
            {
                $result = $pdo->prepare("INSERT INTO membre (pseudo, mdp, nom, prenom, email, civilite, ville, code_postal, adresse, statut) VALUES (:pseudo, :mdp, :nom, :prenom, :email, :civilite, :ville, :code_postal, :adresse, 0)");

                $password_hash = password_hash($_POST['password'], PASSWORD_BCRYPT); 
                # La fonction password_hash() va nous permettre de crypter sérieusement un mot de passe. Elle prend 2 arguments: le résultat ciblé + la méthode à utiliser

                $result->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
                $result->bindValue(':mdp', $password_hash, PDO::PARAM_STR);
                $result->bindValue(':nom', $_POST['nom'], PDO::PARAM_STR);
                $result->bindValue(':prenom', $_POST['prenom'], PDO::PARAM_STR);
                $result->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
                $result->bindValue(':civilite', $_POST['civilite'], PDO::PARAM_STR);
                $result->bindValue(':ville', $_POST['ville'], PDO::PARAM_STR);
                $result->bindValue(':adresse', $_POST['adresse'], PDO::PARAM_STR);
                
                $result->bindValue(':code_postal', $_POST['code_postal'], PDO::PARAM_INT);

                if($result->execute())
                {
                    // $msg .= "<div class='alert alert-success'>Vous êtes bien enregistré.</div>";

                    header("location:connexion.php?m=success");
                }


            }
        }

    }

    # Je souhaite conserver les valeurs rentrées par l'utilisateur durant le processus de rechargement de la page
    $pseudo = (isset($_POST['pseudo'])) ? $_POST['pseudo'] : '';
    $prenom = (isset($_POST['prenom'])) ? $_POST['prenom'] : '';
    $nom = (isset($_POST['nom'])) ? $_POST['nom'] : '';
    $email = (isset($_POST['email'])) ? $_POST['email'] : '';
    $adresse = (isset($_POST['adresse'])) ? $_POST['adresse'] : '';
    $code_postal = (isset($_POST['code_postal'])) ? $_POST['code_postal'] : '';
    $ville = (isset($_POST['ville'])) ? $_POST['ville'] : '';
    $civilite = (isset($_POST['civilite'])) ? $_POST['civilite'] : '';

?>

    <div class="starter-template">
    <h1><?= $page ?></h1>
        <form action="" method="post">
            <small class="form-text text-muted">Vos données ne seront revendues à des services tiers.</small>
            <?= $msg ?>
            <div class="form-group">
                <label for="pseudo">Pseudo</label>
                <input type="text" class="form-control" id="pseudo" placeholder="Choisissez votre pseudo ..." name="pseudo" required value="<?= $pseudo ?>">
                
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" class="form-control" id="password" placeholder="Choisissez votre mot de passe ..." name="password" required>
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
                    <option value="f" <?php if($civilite == 'f'){echo 'selected';} ?> >Femme</option>
                    <option value="m" <?php if ($civilite == 'm') {echo 'selected';} ?> >Homme</option>
                    <option value="o" <?php if ($civilite == 'o') {echo 'selected';} ?> >Je ne souhaite pas le préciser</option>
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
            <button type="submit" class="btn btn-primary btn-lg btn-block">Inscription</button>
        </form>
    </div>

<?php require_once("inc/footer.php"); ?>