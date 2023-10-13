<?php 
    # On se connecte à notre base de donnée
    $connection = mysqli_connect('localhost','root','','boutique');

    # Si la connexion n'a pas aboutie, on affiche une erreur
    if(!$connection){
        die("Une erreur est survenue lors de la liason avec la base de donnée. Veuillez réessayer plus tard!");
    }

    # Vérifie si le paramètre article_id existe
    if(!empty($_GET['article_id'])){
        $article_id = (int) $_GET['article_id']; # Conversion en int

        # Si la conversion ne retourne pas de valeur on le ramène à l'accueil
        if(!$article_id){
            header('Location: ./');
        }

        # On récupère l'article en fonction de l'id
        $sql = "SELECT * FROM article WHERE id = ?";
        $stmt = mysqli_prepare($connection, $sql); # On prépare la requête (évite les injections SQL)
        $query = mysqli_stmt_bind_param($stmt, "i", $article_id); 
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if($result){
            # On récupère les données de la requête
            $article = mysqli_fetch_assoc($result);

            if(!$article){
                header('Location: ./');
            }

            # On vérifie si l'article n'existe pas dans le panier
            $sql = "SELECT * FROM panier WHERE id_article = ?";
            $stmt = mysqli_prepare($connection, $sql); # On prépare la requête (évite les injections SQL)
            $query = mysqli_stmt_bind_param($stmt, "i", $article_id); 
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if($result){
                $panier = mysqli_fetch_assoc($result);

                if($panier){
                    # Si le panier existe, on incrémente la quantité
                    $sql = "UPDATE panier SET quantite = quantite + 1 WHERE id=?";
                    $stmt = mysqli_prepare($connection, $sql);
                    $query = mysqli_stmt_bind_param($stmt, "i", $panier['id']);
                    mysqli_stmt_execute($stmt);

                    # Si l'ajout se passe bien on affiche un message de succès
                    if(mysqli_affected_rows($connection)>0){
                        header('Location: ./pannier.php');
                    }else{
                        # Sinon affiche une erreur
                        echo "Oops! Une erreur s'est produite lors de la modification des données... Veuillez réessayer plus tard";;
                        die();
                    }
                }else{
                    # Sinon on l'ajoute dans la bd
                    $sql = "INSERT INTO panier(id_article, prix_unitaire) VALUES(?, ?)";
                    $stmt = mysqli_prepare($connection, $sql);
                    $query = mysqli_stmt_bind_param($stmt, "id", $article_id, $article['prix']);
                    mysqli_stmt_execute($stmt);

                    # Si l'ajout se passe bien on affiche un message de succès
                    if(mysqli_affected_rows($connection)>0){
                        header('Location: ./pannier.php');
                    }else{
                        # Sinon affiche une erreur
                        echo "Oops! Une erreur s'est produite lors de l'insertion des données... Veuillez réessayer plus tard";;
                        die();
                    }
                }
            }

        }else{
            echo "Oops! Une erreur s'est produite lors de l'insertion des données... Veuillez réessayer plus tard";
            die();
        }

    }else{
        # Sinon on le ramène à l'accueil
        header('Location: ./');
    }
?>