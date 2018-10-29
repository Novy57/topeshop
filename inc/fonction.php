<?php

# La fonction utilisateur debug() nous permettra d'appeler à souhait un var_dump/print_r où l'on souhaite avec en prime les informations liées au fichier + la ligne où le debug a été appelé ! Je me permet de choisir aussi entre un print_r() et un var_dump() tout en laissant par défaut le var_dump()
function debug($var, $mode = 1)
{
    echo "<div class='alert alert-warning'>";
        
        $trace = debug_backtrace(); # la fonction debug_backtrace() nous permet de tracer l'endroit où notre fonction est appelée. Cependant, elle nous retourne un array multi-dimensionnel

        //var_dump($trace);

        $trace = array_shift($trace); # la fonction array_shift() me permet de retourner le résultat en array simple

        echo "Le debug a été appelé dans le fichier $trace[file] à la ligne $trace[line] <hr>";

        echo "<pre>";

            switch ($mode) {
                case '1':
                    var_dump($var);
                    break;
                default:
                    print_r($var);
                    break;
            }
            
        echo "</pre>";

    echo "</div>";
}

# Fonction pour vérifier que l'utilisateur est connecté
function userConnect()
{
    // if(isset($_SESSION['user']))
    // {
    //     return TRUE;
    // }
    // else 
    // {
    //     return FALSE;    
    // }

    if(isset($_SESSION['user'])) return TRUE;
    else return FALSE;
}

# Fonction pour vérifier que l'utilisateur est ADMIN
function userAdmin()
{
    if(userConnect() && $_SESSION['user']['statut'] == 1) return TRUE;
    else return FALSE;
}

# Création d'une modal de suppression
function deleteModal($id, $titre, $reference)
{
    echo "<div class='modal fade' id='deleteModal" . $id . "' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'>";
        echo '<div class="modal-dialog" role="document">';
            echo '<div class="modal-content">';
                echo '<div class="modal-header">';
                echo "<h5 class='modal-title' id='exampleModalLabel'>Suppression</h5>";
                echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                echo '<span aria-hidden="true">&times;</span>';
                echo '</button>';
                echo '</div>';
                echo '<div class="modal-body">';
                echo "Êtes-vous sûr de vouloir supprimer le produit " . $titre . " (référence: " . $reference . " ) ?";
                echo '</div>';
                echo '<div class="modal-footer">';
                echo '<button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>';
                echo '<a href="?a=delete&id=' . $id . '" class="btn btn-danger">Supprimer</a>';
                echo '</div>';
            echo '</div>';
        echo '</div>';
    echo '</div>';
}

# Création d'une fonction pour créer et ajouter au panier
function ajoutPanier($id, $quantite, $photo, $titre, $prix)
{
    if(!isset($_SESSION['panier']))
    {
        $_SESSION['panier'] = array(); // si jamais je ne trouve pas de panier, je créé un array
    }

    if(!isset($_SESSION['panier'][$id])) # Si la référence du produit n'existe pas en $_SESSION, je créée une ligne la concernant dans mon tableau
    {
        $_SESSION['panier'][$id] = array();
        $_SESSION['panier'][$id]['quantite'] = $quantite;
        $_SESSION['panier'][$id]['photo'] = $photo;
        $_SESSION['panier'][$id]['titre'] = $titre;
        $_SESSION['panier'][$id]['prix'] = $prix;
    }
    else # Le produit est déjà en panier, j'ajoute la quantité à celle existante
    {
        $_SESSION['panier'][$id]['quantite'] += $quantite;
    }
}

// Nous créons une fonction pour compter le nombre de produit dans le panier afin d'y afficher une bulle
function nombreProduit() 
{
    $quantiteProduit = 0; // Nous commençons le décompte à 0

    if (!empty($_SESSION['panier']))  // Nous regardons si le panier est créé
    {
        foreach ($_SESSION['panier'] as $produit) 
        {
            $quantiteProduit += $produit['quantite']; // nous rassemblons toutes les quantités ensemble
        }
    }

    return $quantiteProduit;
}

// Nous créons une fonction pour retourner le prix total du panier
function prixTotal() 
{
    $total = 0;
    
    if(!empty($_SESSION['panier'])) 
    {
        foreach ($_SESSION['panier'] as $produit) 
        {
            $total += $produit['prix'] * $produit['quantite'];
        }
    }
    
    return $total;
}