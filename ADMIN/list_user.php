<?php

    # Définir mon nom de page
    $page = "Gerer les utilisateurs";

    require_once("inc/header_back.php");

    if(isset($_GET['a']) && isset($_GET['id']) && $_GET['a'] == "delete" && is_numeric($_GET['id'])) # la fonction is_numeric() me permet de vérifier que le paramètre rentré est bien un chiffre
    {
        $req = "SELECT * FROM membre WHERE id_membre = :id";
        $result = $pdo->prepare($req);
        $result->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
        $result->execute();
        

        if($result->rowCount() == 1)
        {
            $membre = $result->fetch();
foreach ($membre as $key => $value) {
    if($key != 'mdp')
    {
        
    }
}
            
            $delete_req = "DELETE FROM membre WHERE id_membre = $membre[id_membre]";
            
            $delete_result = $pdo->exec($delete_req);

           
            
            if($delete_result)
            {
                $road_photo = RACINE . 'assets/uploads/admin/' . $membre['photo'];
                
                if(file_exists($road_photo) && $membre['photo'] != "default.jpg") # la fonction fil_exists() me permet de vérifier si le fichier existe bel et bien
                {
                    unlink($road_photo); # la fonction unlink() me permet de supprimer un fichier
                }
                
                header("location:list_user.php?m=success");
            }
            else
            {
                header("location:list_user.php?m=fail");  
            }
            
        }
        else 
        {
            header("location:list_user.php?m=fail");    
        }
    }
    
    if(isset($_GET['m']) && !empty($_GET['m']))
    {
        switch($_GET['m'])
        {
            case "success":
            $msg .= "<div class='alert alert-success'>L'utilisateur' a bien été supprimé.</div>";
            break;
            case "fail":
            $msg .= "<div class='alert alert-danger'>Une erreur est survenue, veuillez réessayer.</div>";
            break;
            case "update":
            $msg .= "<div class='alert alert-success'>L'utilisateur a bien été mis à jour.</div>";
            break;
            default:
            $msg .= "<div class='alert alert-warning'>A pas compris !</div>";
            break;
        }
    }
    
    # Je sélectionne tous mes résultats en BDD pour la table membre
    $result = $pdo->query('SELECT * FROM membre');
    $membres = $result->fetchAll();
    
    
    $contenu .= "<div class='table-responsive'>";
    $contenu .= "<table class='table table-striped table-sm'>";
    $contenu .= "<thead class='thead-dark'><tr>";
    
    for($i= 0; $i < $result->columnCount(); $i++)
    {
        if($i!=2){
        $colonne = $result->getColumnMeta($i);
        $contenu .= "<th scope='col'>" . ucfirst(str_replace('_', ' ', $colonne['name'])) . "</th>";
        }
    }
    
    $contenu .= "<th colspan='2'>Actions</th>";
    $contenu .= "</tr></thead><tbody>";
    
    
        foreach($membres as $membre)
        {
    
            $contenu .= "<tr>";
            foreach ($membre as $key => $value) 
            {
                if($key == "photo")
                {
                    $contenu .= "<td><img height='100' src='" . URL . "assets/uploads/admin/" . $value . "' alt='" . $membre['pseudo'] . "'/></td>";
                }
                else 
                {if($key != 'mdp') {

               
                    $contenu .= "<td>" . $value . "</td>"; 
                  }
                }
                
            }
    
            $contenu .= "<td><a href='modif_user.php?id=" . $membre['id_membre'] . "'><i class='fas fa-pen'></i></a></td>";
    
            $contenu .= "<td><a data-toggle='modal' data-target='#deleteModal" . $membre['id_membre'] . "'><i class='fas fa-trash-alt'></i></a></td>";
    
            # J'appelle ma modal de supression (fonction créée dans fonction.php)
            deleteModal($membre['id_membre'], $membre['pseudo'], "l'utilisateur");
    
            $contenu .= "</tr>";
        }


    
    $contenu .= "</tbody></table>";
    $contenu .= "</div>";
?>

    <?= $msg ?>
    <?= $contenu ?>







<?php require_once("inc/footer_back.php"); ?>