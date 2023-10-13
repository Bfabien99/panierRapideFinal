<?php
    # On se connecte à notre base de donnée
    $connection = mysqli_connect('localhost','root','','boutique');

    # Si la connexion n'a pas aboutie, on affiche une erreur
    if(!$connection){
        die("Une erreur est survenue lors de la liason avec la base de donnée. Veuillez réessayer plus tard!");
    }

    # Vérifie si les paramètres article_id et qte existe
    if(!empty($_GET['article_id']) && !empty($_GET['qte'])){
        $article_id = (int) $_GET['article_id']; # Conversion en int
        $qte = (int) $_GET['qte']; # Conversion en int

        # Si la conversion ne retourne pas de valeur on le ramène à l'accueil
        if(!$article_id or !$qte){
            header('Location: ./pannier.php');
        }

        if($qte<1){
            header('Location: ./pannier.php');
        }else{
            # On récupère le panier en fonction de l'id
            $sql = "SELECT * FROM panier WHERE id_article = ?";
            $stmt = mysqli_prepare($connection, $sql); # On prépare la requête (évite les injections SQL)
            $query = mysqli_stmt_bind_param($stmt, "i", $article_id); 
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if($result){
                # On récupère les données de la requête
                $panier = mysqli_fetch_assoc($result);

                if(!$panier){
                    header('Location: ./pannier.php');
                }

                # Si le panier existe, on incrémente la quantité
                $sql = "UPDATE panier SET quantite = ? WHERE id=?";
                $stmt = mysqli_prepare($connection, $sql);
                $query = mysqli_stmt_bind_param($stmt, "ii", $qte, $panier['id']);
                mysqli_stmt_execute($stmt);

                # Si l'ajout se passe bien on affiche un message de succès
                if(mysqli_affected_rows($connection)>0){
                    header('Location: ./pannier.php');
                }else{
                    # Sinon on le ramène au panier
                    header('Location: ./pannier.php');
                }
            }else{
                header('Location: ./pannier.php');
            }   
        }

    }else{
        # Sinon on le ramène au panier
        header('Location: ./pannier.php');
    }
?>