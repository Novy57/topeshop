<?php

    # Définir mon nom de page
    $page = "Liste des produits";

    require_once("inc/header_back.php");

    if(isset($_GET['a']) && isset($_GET['id']) && $_GET['a'] == "delete" && is_numeric($_GET['id'])) # la fonction is_numeric() me permet de vérifier que le paramètre rentré est bien un chiffre
    {
        $req = "SELECT * FROM produit WHERE id_produit = :id";
        $result = $pdo->prepare($req);
        $result->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
        $result->execute();
        // debug($result);

        if($result->rowCount() == 1)
        {
            $produit = $result->fetch();
            
            //debug($produit);
            
            $delete_req = "DELETE FROM produit WHERE id_produit = $produit[id_produit]";
            
            $delete_result = $pdo->exec($delete_req);

            // debug($delete_result);
            
            if($delete_result)
            {
                $chemin_photo = RACINE . 'assets/uploads/admin/' . $produit['photo'];
                
                if(file_exists($chemin_photo) && $produit['photo'] != "default.jpg") # la fonction fil_exists() me permet de vérifier si le fichier existe bel et bien
                {
                    unlink($chemin_photo); # la fonction unlink() me permet de supprimer un fichier
                }
                
                header("location:index.php?m=success");
            }
            else
            {
                header("location:index.php?m=fail");  
            }
            
        }
        else 
        {
            header("location:index.php?m=fail");    
        }
    }
    
    if(isset($_GET['m']) && !empty($_GET['m']))
    {
        switch($_GET['m'])
        {
            case "success":
            $msg .= "<div class='alert alert-success'>Le produit a bien été supprimé.</div>";
            break;
            case "fail":
            $msg .= "<div class='alert alert-danger'>Une erreur est survenue, veuillez réessayer.</div>";
            break;
            case "update":
            $msg .= "<div class='alert alert-success'>Le produit a bien été mis à jour.</div>";
            break;
            default:
            $msg .= "<div class='alert alert-warning'>A pas compris !</div>";
            break;
        }
    }
    
    # Je sélectionne tous mes résultats en BDD pour la table produit
    $result = $pdo->query('SELECT * FROM produit');
    $produits = $result->fetchAll();
    
    // debug($produits);
    
    $contenu .= "<div class='table-responsive'>";
    $contenu .= "<table class='table table-striped table-sm'>";
    $contenu .= "<thead class='thead-dark'><tr>";
    
    for($i= 0; $i < $result->columnCount(); $i++)
    {
        $colonne = $result->getColumnMeta($i);
        $contenu .= "<th scope='col'>" . ucfirst(str_replace('_', ' ', $colonne['name'])) . "</th>";
    
    }
    
    $contenu .= "<th colspan='2'>Actions</th>";
    $contenu .= "</tr></thead><tbody>";
    
    //debug($produits);
    
        foreach($produits as $produit)
        {
    
            $contenu .= "<tr>";
            foreach ($produit as $key => $value) 
            {
                if($key == "photo")
                {
                    $contenu .= "<td><img height='100' src='" . URL . "assets/uploads/admin/" . $value . "' alt='" . $produit['titre'] . "'/></td>";
                }
                else 
                {
                    $contenu .= "<td>" . $value . "</td>";  
                }
                
            }
    
            $contenu .= "<td><a href='formulaire_produit.php?id=" . $produit['id_produit'] . "'><i class='fas fa-pen'></i></a></td>";
    
            $contenu .= "<td><a data-toggle='modal' data-target='#deleteModal" . $produit['id_produit'] . "'><i class='fas fa-trash-alt'></i></a></td>";
    
            # J'appelle ma modal de supression (fonction créée dans fonction.php)
            deleteModal($produit['id_produit'], $produit['titre'], $produit['reference']);
    
            $contenu .= "</tr>";
        }
    
    $contenu .= "</tbody></table>";
    $contenu .= "</div>";
?>

    <?= $msg ?>
    <?= $contenu ?>
    
    <?php require_once("inc/footer_back.php"); ?>