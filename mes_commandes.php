<?php
# Définir mon nom de page
    $page = "Mes commandes";

    require_once("inc/header.php");

    if(!userConnect())
    {
        header("location:connexion.php");
        exit();
    }

    # Je sélectionne toutes mes commandes en BDD pour la table des commandes
    if( ($_GET) && isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id']) )
    {   
        $result = $pdo->prepare('SELECT * FROM commande WHERE id_membre = :id_membre');
        $result->bindValue(':id_membre', $_GET['id'], PDO::PARAM_INT);
        if($result->execute()) # Si j'enregistre bien en BDD
        { 
            $commandes = $result->fetchAll();
        }
    }
    else
    {
        $msg .= "<div class='alert alert-dnager'>Qui êtes vous ?</div>";
    }

    if (empty($commandes)){
        $msg .= "<div class='alert alert-warning'>Vous avez aucune commande enregistrée.</div>";
    }
    else
    {
        // Table des commandes
        $contenu .= "<div class='table-responsive'>";
        $contenu .= "<table class='table table-striped table-sm'>";
        $contenu .= "<thead class='thead-dark'><tr>";
        for($i= 0; $i < $result->columnCount(); $i++)
        {   $colonne = $result->getColumnMeta($i);
            if ($colonne['name'] != "id_membre")
            {
                $contenu .= "<th scope='col'>" . ucfirst(str_replace('_', ' ', $colonne['name'])) . "</th>";
            }
        }
        $contenu .= "</tr></thead><tbody>";
        
        foreach($commandes as $commande)
        {
            $contenu .= "<tr>";
            foreach ($commande as $key => $value) 
            {
                if ($key != "id_membre")
                {
                    $contenu .= "<td>" . $value . "</td>";  
                }
            }
            $contenu .= "</tr>";
        }
        
        $contenu .= "</tbody></table>";
        $contenu .= "</div>";

        // Tables détail des commandes
        foreach($commandes as $commande)
        {
            $result = $pdo->query("SELECT * FROM detail_commande WHERE id_commande = $commande[id_commande]");
            $detail_commandes = $result->fetchAll();

            $contenu .= "<p>Contenu de ma commande " . $commande['id_commande'] . "</p>";

            $contenu .= "<div class='table-responsive'>";
            $contenu .= "<table class='table table-striped table-sm'>";
            $contenu .= "<thead class='thead-dark'><tr>";
            for($i= 0; $i < $result->columnCount(); $i++)
            {   $colonne = $result->getColumnMeta($i);
                if ( ($colonne['name'] != "id_commande") && ($colonne['name'] != "id_detail_commande") )
                {
                    $contenu .= "<th scope='col'>" . ucfirst(str_replace('_', ' ', $colonne['name'])) . "</th>";
                }
            }
            $contenu .= "</tr></thead><tbody>";

            foreach($detail_commandes as $detail_commande)
            {
                $contenu .= "<tr>";
                foreach ($detail_commande as $key => $value)
                {
                    if ( ($key != "id_commande") && ($key != "id_detail_commande") )
                    {
                        $contenu .= "<td>" . $value . "</td>";  
                    }
                }
                $contenu .= "</tr>";
            }
            $contenu .= "</tbody></table>";
            $contenu .= "</div>";
        }
    }







?>

<div class="starter-template">
    <h1><?= $page ?></h1>
</div>

<?php if (!empty($commandes)) : ?>
    <?= $contenu ?>
<?php else : ?>
    <?= $msg ?>
<?php endif; ?>


<?php require_once("inc/footer.php"); ?>