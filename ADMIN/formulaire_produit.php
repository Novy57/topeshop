<?php

    # Définir mon nom de page
    $page = "Ajouter un produit";

    require_once("inc/header_back.php");

    // debug($_POST);
    // debug($_FILES, 2);

    if($_POST)
    {
        # Je m'occupe du fichier envoyé : une photo !
        if(!empty($_FILES['photo']['name']))
        {

            # Nous allons donner un nom aléatoire à notre photo
            $nom_photo = $_POST['titre'] . '_' . $_POST['reference'] . '_' . time() . '-' . rand(1,999) . $_FILES['photo']['name'];
            $nom_photo = str_replace(' ', '-', $nom_photo);
            $nom_photo = str_replace(array('é','è','à','ç','ù'), 'x', $nom_photo);

            // Enregistrons le chemin de notre fichier
            $chemin_photo = RACINE . 'assets/uploads/admin/' . $nom_photo;

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

        }
        elseif(isset($_POST['photo_actuelle']))
        {
            $nom_photo = $_POST['photo_actuelle'];
        }
        else 
        {
            $nom_photo = "default.jpg";
        }

        // AUTRES VERIFICATIONS POSSIBLES

        if(empty($msg))
        {

            if(!empty($_POST['id_produit'])) # Je suis en train de modifier un produit
            {
                $result = $pdo->prepare("UPDATE produit SET reference=:reference, categorie=:categorie, titre=:titre, description=:description, couleur=:couleur, taille=:taille, public=:public, photo=:photo, prix=:prix, stock=:stock WHERE id_produit = :id_produit");

                $result->bindValue(":id_produit", $_POST['id_produit'], PDO::PARAM_INT);
            }
            else # Je suis en train d'enregistrer pour la première fois un produit
            { 
                $result = $pdo->prepare("INSERT INTO produit (reference, categorie, titre, description, couleur, taille, public, photo, prix, stock) VALUES (:reference, :categorie, :titre, :description, :couleur, :taille, :public, :photo, :prix, :stock)");
            }

            $result->bindValue(':reference', $_POST['reference'], PDO::PARAM_STR);
            $result->bindValue(':categorie', $_POST['categorie'], PDO::PARAM_STR);
            $result->bindValue(':titre', $_POST['titre'], PDO::PARAM_STR);
            $result->bindValue(':description', $_POST['description'], PDO::PARAM_STR);
            $result->bindValue(':couleur', $_POST['couleur'], PDO::PARAM_STR);
            $result->bindValue(':taille', $_POST['taille'], PDO::PARAM_STR);
            $result->bindValue(':public', $_POST['public'], PDO::PARAM_STR);
            $result->bindValue(':prix', $_POST['prix'], PDO::PARAM_STR);
            $result->bindValue(':stock', $_POST['stock'], PDO::PARAM_INT);

            $result->bindValue(':photo', $nom_photo, PDO::PARAM_STR);

            if($result->execute()) # Si j'enregistre bien en BDD
            {
                if(!empty($_FILES['photo']['name']))
                {
                    copy($_FILES['photo']['tmp_name'], $chemin_photo);
                }
                
                if(!empty($_POST['id_produit']))
                {
                    header("location:index.php?m=update");
                }
                
                $msg .= "<div class='alert alert-success'>Le produit est bien enregistré !</div>";
            }

        }

    }

    if($_GET)
    {

       if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id']))
        {
            $req = "SELECT * FROM produit WHERE id_produit = :id";

            $result = $pdo->prepare($req);
            $result->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
            $result->execute();

            if($result->rowCount() == 1)
            {
                $modif_produit = $result->fetch();

                debug($modif_produit);
            }
            else 
            {
                $msg .= "<div class='alert alert-danger'>Aucune correspondance en base de donnée.</div>";
            }
        }
        else 
        {
            $msg .= "<div class='alert alert-danger'>Aucune correspondance en base de donnée.</div>";
        } 


    }

    $reference = (isset($modif_produit)) ? $modif_produit['reference'] : "";
    $categorie = (isset($modif_produit)) ? $modif_produit['categorie'] : "";
    $titre = (isset($modif_produit)) ? $modif_produit['titre'] : "";
    $description = (isset($modif_produit)) ? $modif_produit['description'] : "";
    $couleur = (isset($modif_produit)) ? $modif_produit['couleur'] : "";
    $taille = (isset($modif_produit)) ? $modif_produit['taille'] : "";
    $public = (isset($modif_produit)) ? $modif_produit['public'] : "";
    $photo = (isset($modif_produit)) ? $modif_produit['photo'] : "";
    $prix = (isset($modif_produit)) ? $modif_produit['prix'] : "";
    $stock = (isset($modif_produit)) ? $modif_produit['stock'] : "";

    $id_produit = (isset($modif_produit)) ? $modif_produit['id_produit'] : "";

    $action = (isset($modif_produit)) ? "Modifier" : "Ajouter";
    
?>

    <form action="" method="post" enctype="multipart/form-data">
        <?= $msg ?>
        <input type="hidden" name="id_produit" value="<?=$id_produit?>">
        <div class="form-group">
            <label for="reference">Référence du produit</label>
            <input type="text" class="form-control" id="reference" placeholder="La référence du produit ..." name="reference" value="<?=$reference?>">
        </div>
        <div class="form-group">
            <label for="categorie">Catégorie</label>
            <select class="form-control" id="categorie" name="categorie">
                <option disabled <?php if(empty($categorie)){ echo "selected"; } ?> >La catégorie du produit</option>
                <option <?php if($categorie == "tshirt"){ echo "selected";} ?> >tshirt</option>
                <option <?php if ($categorie == "pull") {echo "selected";} ?> >pull</option>
                <option <?php if ($categorie == "manteau"){echo "selected";} ?>>manteau</option>
                <option <?php if ($categorie == "chaussette") { echo "selected";} ?>>chaussette</option>
                <option <?php if ($categorie == "pantalon") {echo "selected";} ?>>pantalon</option>
                <option <?php if ($categorie == "slip") {echo "selected";} ?>>slip</option>
                <option <?php if ($categorie == "charentaise") {echo "selected";} ?>>charentaise</option>
                <option <?php if ($categorie == "lingerie") {echo "selected";} ?>>lingerie</option>
                <option <?php if ($categorie == "badass") {echo "selected";} ?>>badass</option>
            </select>
        </div>
        <div class="form-group">
            <label for="titre">Titre du produit</label>
            <input type="text" class="form-control" id="titre" placeholder="Le titre du produit ..." name="titre" value="<?= $titre ?>">
        </div>
        <div class="form-group">
            <label for="description">Description du produit</label>
            <textarea class="form-control" id="description" rows="3" name="description" placeholder="Descriptif du produit ..."><?= $description ?></textarea>
        </div>
        <div class="form-group">
            <label for="couleur">Couleur</label>
            <select class="form-control" id="couleur" name="couleur">
                <option disabled <?php if (empty($couleur)) {echo "selected";} ?>>La couleur du produit</option>
                <option <?php if ($couleur == "rouge") {echo "selected";} ?>>rouge</option>
                <option <?php if ($couleur == "bleu") {echo "selected";} ?>>bleu</option>
                <option <?php if ($couleur == "blanc") {echo "selected";} ?>>blanc</option>
                <option <?php if ($couleur == "noir") {echo "selected";} ?>>noir</option>
                <option <?php if ($couleur == "orange") {echo "selected";} ?>>orange</option>
                <option <?php if ($couleur == "jaune") {echo "selected";} ?>>jaune</option>
                <option <?php if ($couleur == "vert") {echo "selected";} ?>>vert</option>
                <option <?php if ($couleur == "prune") {echo "selected";} ?>>prune</option>
                <option <?php if ($couleur == "violet") {echo "selected";} ?>>violet</option>
                <option <?php if ($couleur == "tomato") {echo "selected";} ?>>tomato</option>
            </select>
        </div>
        <div class="form-group">
            <label for="taille">Taille</label>
            <select class="form-control" id="taille" name="taille">
                <option disabled <?php if (empty($taille)) {echo "selected";} ?>>La taille du produit</option>
                <option <?php if ($taille == "xs") {echo "selected";} ?>>xs</option>
                <option <?php if ($taille == "s") {echo "selected";} ?>>s</option>
                <option <?php if ($taille == "m") {echo "selected";} ?>>m</option>
                <option <?php if ($taille == "l") {echo "selected";} ?>>l</option>
                <option <?php if ($taille == "xl") {echo "selected";} ?>>xl</option>
            </select>
        </div>
        <div class="form-group">
            <label for="public">Public visé</label>
            <select class="form-control" id="public" name="public">
                <option disabled <?php if (empty($public)) {echo "selected";} ?>>Le public visé</option>
                <option value="m" <?php if ($public == "m") {echo "selected";} ?>>Homme</option>
                <option value="f" <?php if ($public == "f") {echo "selected";} ?>>Femme</option>
                <option value="enfant" <?php if ($public == "enfant") {echo "selected";} ?>>Enfant</option>
                <option value="mixte" <?php if ($public == "mixte") {echo "selected";} ?>>Mixte</option>
            </select>
        </div>
        <div class="form-group">
            <label for="photo">Photo du produit</label>
            <input type="file" class="form-control-file" id="photo" name="photo">

            <?php

                if(isset($modif_produit))
                {
                    echo "<input name='photo_actuelle' value='$photo' type='hidden'>";
                    echo "<img style='width:25%;' src='" . URL . "/assets/uploads/admin/$photo'>";
                }

            ?>

        </div>
        <div class="form-group">
            <label for="prix">Prix du produit</label>
            <input type="text" class="form-control" id="prix" placeholder="Le prix du produit ..." name="prix" value="<?= $prix ?>">
        </div>
        <div class="form-group">
            <label for="stock">Stock</label>
            <input type="text" class="form-control" id="stock" placeholder="Le stock ..." name="stock" value="<?= $stock ?>">
        </div>
        <input type="submit" value="<?= $action ?> le produit" class="btn btn-info btn-lg btn-block">
    </form>

<?php require_once("inc/footer_back.php"); ?>