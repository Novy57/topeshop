<?php

    # Règles SEO
    $page = "Panier";
    $seo_description = "Un choix très large de produits assemblés en France par des travailleurs non déclarés.";

    require_once("inc/header.php");

    // debug($_POST);

    // traitement pour retirer produit du panier
    if(isset($_GET['a']) && $_GET['a'] == 'delete') {
        if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])) {
            $produit_a_retirer = $_GET['id'];
            if($_SESSION['panier'][$produit_a_retirer]){
                unset($_SESSION['panier'][$produit_a_retirer]);
            }
            else {
                header('location:panier.php');
            }
        }
        else {
            header('location:panier.php');
        }
    }

    // traitement pour vider le panier
    if(isset($_GET['a']) && $_GET['a'] == 'truncate') {
        unset($_SESSION['panier']);
        header('location:panier.php');
    }
    
    if($_POST)
    {

        # Ajout panier et modif quantité
        if(isset($_POST["ajoutPanier"]))
        {
            $result = $pdo->prepare("SELECT * FROM produit WHERE id_produit = :id_produit");
            $result->bindValue(":id_produit", $_POST['id_produit'], PDO::PARAM_INT);
            $result->execute();
    
            $produit = $result->fetch();
    
            // debug($produit);
    
            # Appel à ma fonction pour créer mon panier
            ajoutPanier($produit['id_produit'], $_POST['quantite'], $produit['photo'], $produit['titre'], $produit['prix'], $produit['stock']);

            # Modif quantité
            if ($_POST["ajoutPanier"] == "modif quantité"){
                $msg .= "<div class='alert alert-success'>La quantité de produit " . $produit['titre'] . " a bien été modifiée !</div>";
            }
            # ou ajout au panier
            else{
                $msg .= "<div class='alert alert-success'>Le produit a bien été ajouté au panier !</div>";
            }
        }

        foreach($_SESSION['panier'] as $indice => $valeur) 
        {
            // $indice = id_produit
            //$valeur = array('quantite', 'photo', 'titre', 'prix')
            extract($valeur); // nous créons automatiquement les variables liées aux valeurs $quantite, $photo, $titre, $prix
    
            $resultat = $pdo -> query("SELECT stock FROM produit WHERE id_produit = $indice");
            $produit = $resultat -> fetch();
            //$produit = array('stock');
    
            if ($produit['stock'] < $quantite) { // si stock inférieur à la quantité
                if ($produit['stock'] > 0) { // il y a du stock ... mais moins que la commande
                    $msg .= '<div class="alert alert-danger">Le stock du produit ' . $titre . ' n\'est malheureusement pas suffisant. Il ne reste que ' . $produit['stock'] . ' exemplaire(s). La quantité à été modifiée</div>';
    
                    $_SESSION['panier'][$indice]['quantite'] = $produit['stock'];
                }
                else { // plus du tout de stock
                    $msg .= '<div class="alert alert-danger">Le produit ' . $titre . ' est malheureusement en rupture de stock. Nous avons retiré le produit de votre panier, merci de vérifier votre panier avant de le valider de nouveau.</div>';
    
                    unset($_SESSION['panier'][$indice]);
                }
            }
        }

        if(empty($msg)) { // tout est ok

            $id_membre = $_SESSION['user']['id_membre'];
            $montant = prixTotal();
    
            $resultat = $pdo -> exec("INSERT INTO commande  (id_membre, montant, date_enregistrement, etat) VALUES ($id_membre, $montant, NOW(), 'en cours de traitement')");
    
            $id_commande = $pdo -> lastInsertID(); // nous retourne l'id du dernier enregistrement
    
            // enregistrer dans la table detail_commande les details pour chaque produit commandé
            foreach ($_SESSION['panier'] as $key => $value) {
                extract($value);
    
                $resultat = $pdo -> exec("INSERT INTO detail_commande (id_commande, id_produit, quantite, prix) VALUES ($id_commande, $key, $quantite, $prix)");
    
                $resultat = $pdo -> exec("UPDATE produit SET stock = (stock - $quantite) WHERE id_produit = $key");
            }
    
            $msg .= '<div class="alert alert-success">Félicitation, votre commande #' . $id_commande . ' est confirmée. Vous allez recevoir un email avec tous les détails et le suivi de votre colis !</div>';
    
            unset($_SESSION['panier']); //suppression du panier dans la session utilisateur
        }
    }
?>

    <div class="starter-template">
        <h1><?= $page ?></h1>
        <?= $msg ?>
    </div>

    <?php if(empty($_SESSION['panier'])) : ?>
        <div class="alert alert-danger">Votre panier est vide !</div>
        <a class="btn btn-primary" href="index.php">Visitez notre boutique</a>
    <?php else : ?>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Titre du produit</th>
                    <th scope="col">Photo</th>
                    <th scope="col">Prix Unitaire</th>
                    <th scope="col">Quantité</th>
                    <th scope="col">Prix Total</th>
                    <th scope="col">Ajouter</th>
                    <th scope="col">Retirer</th>
                    <th scope="col">Supprimer</th>
                </tr>
            </thead>
            <?php foreach($_SESSION['panier'] as $key => $value) : ?>
                <tbody>
                    <tr>
                    
                        <td><?= $value['titre'] ?></td>

                        <td><img src="assets/uploads/admin/<?=$value['photo']?>" alt="<?=$value['titre']?>" style="width:50%;"></td>

                        <td><?= $value['prix'] ?> €</td>

                        <td><?= $value['quantite'] ?></td>

                        <td><?= $value['quantite']*$value['prix'] ?> €</td>

                        <td>         
                            <?php if ($value['stock']-$value['quantite'] > 0) : ?>
                                <form action="" method="post">
                                    <input type="hidden" name="id_produit" value="<?= $key ?>">
                                    <select class="form-control" name="quantite">
                                        <option selected disabled>Quantité ...</option>
                                        <?php for($i=1; $i <= $value['stock']-$value['quantite'] ; $i++) : ?>
                                            <option><?=$i?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <input type="submit" class="btn btn-success btn-block" value="modif quantité" name="ajoutPanier">
                                </form>
                            <?php else : ?>
                                Quantité au max, il n'y a pas plus de stock
                            <?php endif ?>
                        </td>

                        <td>         
                            <?php if ($value['quantite'] > 0) : ?>
                                <form action="" method="post">
                                    <input type="hidden" name="id_produit" value="<?= $key ?>">
                                    <select class="form-control" name="quantite">
                                        <option selected disabled>Quantité ...</option>
                                        <?php for($i=1; $i <= $value['quantite'] ; $i++) : ?>
                                            <option><?=-$i?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <input type="submit" class="btn btn-success btn-block" value="modif quantité" name="ajoutPanier">
                                </form>
                            <?php else : ?>
                                La quantité est à 0, supprimer le produit ou ajouter une quantité
                            <?php endif ?>
                        </td>

                        <td><a href="?a=delete&id=<?=$key?>"><i class='fas fa-trash-alt'></i></a></td>
                    </tr>
                </tbody>
            <?php endforeach; ?>
            <tr>
                <th colspan="5">Montant total</th>
                <td><?= number_format(prixTotal("HT"), 2, ',', '.') # la fonction number_format() nous permet de retourner un montant formaté à notre convenance. Elle accepte 1 à 4 paramètres : le nombre visé + définition du nombre de décimales + le séparateur du point décimal + déparateur des milliers ?> € HT</td> 
                <td><?= number_format(prixTotal("TTC"), 2, ',', '.') ?> € TTC</td> 
            </tr>
            <tr>
                <td><a href="?a=truncate"><em>vider le panier</em></a></td>
            </tr>
        </table>
        <?php if(userConnect()) : ?>
            <?php if ( empty($_SESSION['user']['ville']) || empty($_SESSION['user']['code_postal']) || empty($_SESSION['user']['adresse']) ) : ?>
                <p>Veuillez compléter vos coordonnées dans votre profil. (adresse / ville / code postal)</p>
                <a class="btn btn-success" href="inscription.php?id=<?= $_SESSION['user']['id_membre'] ?>">Modifier votre profil</a>
            <?php else : ?>
                <form action="" method="post">
                    <input type="submit" class="btn btn-primary" value="Valider le panier" name=valider>
                </form>
            <?php endif; ?>
        <?php else : ?>
            <p>Vous n'êtes pas connecté.</p>
            <a class="btn btn-success" href="connexion.php">Se connecter</a>
        <?php endif; ?>
    <?php endif; ?>

<?php require_once("inc/footer.php"); ?>