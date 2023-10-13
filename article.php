<?php
    # On se connecte à notre base de donnée
    $connection = mysqli_connect('localhost','root','','boutique');

    # Si la connexion n'a pas aboutie, on affiche une erreur
    if(!$connection){
        die("Une erreur est survenue lors de la liason avec la base de donnée. Veuillez réessayer plus tard!");
    }

    # On s'assure que tous les champs requis ont été postés
    if(!empty($_POST['nom']) && !empty($_POST['description']) && !empty($_POST['prix']) && !empty($_POST['stock']) && !empty($_POST['image'])){
        $nom = mb_strtolower(strip_tags(trim($_POST['nom'])));
        $description = strip_tags(trim($_POST['description']));
        $prix = (float) strip_tags(trim($_POST['prix'])); # On converti la valeur en float
        $stock = (int) strip_tags(trim($_POST['stock'])); # On converti la valeur en int
        $image = strip_tags($_POST['image']);

        $err = []; # Tableau pour contenir toutes les erreurs

        # On s'assure que le nom est compris entre 2 et 180 caractères et ne contient que des lettres alphabétiques
        if(strlen($nom) < 2 or strlen($nom) > 180 or !ctype_alpha(str_replace(' ','',$nom))){
            $err[] = "Le nom doit être compris entre 2 et 180 caractères et ne contenir que des lettres selon l'alphabet(pas d'accent)";
        }

        # On s'assure que la description est comprise entre 2 et 180 caractères
        if(strlen($description) < 10 or strlen($description) > 180 ){
            $err[] = "La description doit être compris entre 2 et 180 caractères";
        }

        # On s'assure que le prix est supérieur à 0
        if($prix <= 0 ){
            $err[] = "Le prix doit être supérieur à zéro";
        }

        # On s'assure que le stock est supérieur est supérieur ou égale à 1
        if($stock < 1){
            $err[] = "Le stock ne peut pas être inférieur à 1";
        }

        # Si aucune erreur n'a été remarquée, on commence les requête
        if(empty($err)){
            # On vérifie s'il existe un article du même nom
            $sql = "SELECT * FROM article WHERE nom = ?";
            $stmt = mysqli_prepare($connection, $sql); # On prépare la requête (évite les injections SQL)
            $query = mysqli_stmt_bind_param($stmt, "s", $nom); 
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if($result){
                # On récupère les données de la requête
                $article = mysqli_fetch_assoc($result);

                if($article){
                    # Si l'article existe on affiche une erreur
                    $err[] = "Le nom de l'article existe déja!";
                }else{
                    # Sinon on l'ajoute dans la bd
                    $sql = "INSERT INTO article(nom, description, prix, stock, image) VALUES(?, ?, ?, ?, ?)";
                    $stmt = mysqli_prepare($connection, $sql);
                    $query = mysqli_stmt_bind_param($stmt, "ssdis", $nom, $description, $prix, $stock, $image);
                    mysqli_stmt_execute($stmt);

                    # Si l'ajout se passe bien on affiche un message de succès
                    if(mysqli_affected_rows($connection)>0){
                        echo "<script>alert('Nouvel article ajoutée')</script>";
                        unset($_POST);
                    }else{
                        # Sinon affiche une erreur
                        $err[] = "Oops! Une erreur s'est produite lors de l'insertion des données... Veuillez réessayer plus tard";;
                    }
                }
            }else {
                # Si la requête se passe mal on affiche une erreur
                $err[] = "Oops! Une erreur s'est produite... Veuillez réessayer plus tard";;
            }
        }
    }

    # Selection du nombre de paniers dans la bd
    $nb_sql = "SELECT COUNT(*) AS total FROM panier";
    $nb_query = mysqli_query($connection, $nb_sql);
    if ($nb_query) {
        $nb_panier = mysqli_fetch_assoc($nb_query);
        $nb_panier = $nb_panier['total'];
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script defer src="https://kit.fontawesome.com/1f88d87af5.js" crossorigin="anonymous"></script>
    <title>MON PANIER RAPIDE</title>
    <style>
        *{
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body{
            width: 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        header{
            display: flex;
            padding: 20px;
            justify-content: space-evenly;
            position: absolute;
            width: 100%;
            z-index: 100;
            top: 0;
            align-items: center;
        }

        header a{
            text-decoration: none;
            color: #fff;
            text-shadow: 0px 0px 2px #000000;
            padding: 5px;
        }

        header a:hover{
            border: 2px solid #fff;
        }

        ul{
            list-style: none;
            display: flex;
            width: 60%;
            justify-content: space-evenly;
            max-width: 500px;
            align-items: center;
        }


        .helper{
            display: flex;
            align-items:center ;
            position: relative;
            gap: 2em;
            width: 50%;
            text-align: center;
            justify-content: space-around;
        }

        .helper .number{
            background-color: red;
            border-radius: 100%;
            font-weight: bold;
            padding: 5px;
        }

        .sticky{
            position: sticky;
            backdrop-filter: blur(5px);
            box-shadow: 0px 0px 10px 2px #58585830;
        }

        main{
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            gap: 2em;
        }

        .banner{
            max-height: 400px;
            overflow: hidden;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }


        .banner h3{
            position: absolute;
            padding: 10px;
            border: 2px solid #fff;
            color: #fff;
            text-shadow: 0px 0px 2px #000000;
        }

        .banner img{
            width: 100%;
            object-fit: cover;
        }

        .content{
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 2em;
        }

        .content h2{
            text-align: center;
        }

        .articleBox{
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
        }

        .imgBox, form{
            width: 100%;
            max-width: 500px;
        }

        .imgBox{
            width: 100%;
            box-shadow: 0px 0px 5px 1px #5757575c;
        }

        .imgBox img{
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        form{
            display: flex;
            flex-direction: column;
            gap: 1em;
            padding: 20px;
            background-color: #58585830;
            color: #444;
            font-weight: bold;
        }

        .group{
            display: flex;
            flex-direction: column;
        }

        input, textarea{
            padding: 10px;
            border-radius: 5px;
            outline: none;
            border: none;
        }

        textarea{
            resize: none;
        }

        .more{
            text-decoration: none;
            color: #ffffff;
            padding: 8px;
            border-radius: 5px;
            align-self: flex-start;
            background-color: #1639eceb;
            width: 90%;
            max-width: 200px;
            margin: 0 auto;
            cursor: pointer;
            border: none;
            outline: none;
        }

        .more:hover{
            background-color: #0f00dba1;
        }
    </style>
</head>
<body>
    <header id="header">
        <div class="brand">
            <a href="./">PanierRapide.com</a>
        </div>
        <ul class="nav">
            <li><a href="./article.php">Ajouter article</a></li>
            <li class="helper">
                <a href="./pannier.php"><span class="number"><?php echo $nb_panier ?? 0;?></span>Pannier</a>
            </li>
        </ul>
    </header>
    <main>
        <section class="banner">
            <h3>Mon Pannier Rapide</h3>
            <img src="https://images.unsplash.com/photo-1601330862030-1e08c703ac04?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8N3x8cGFuaWVyfGVufDB8fDB8fHww&auto=format&fit=crop&w=800&q=60" alt="">
        </section>
        <div class="content">
            <h2>Ajouter un nouvel article</h2>
            <div class="articleBox">
                <div class="imgBox">
                    <img src="https://images.unsplash.com/photo-1528825871115-3581a5387919?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8YmFuYW5lfGVufDB8fDB8fHww&auto=format&fit=crop&w=800&q=60" alt="">
                </div>
                <form action="" method="post">
                    <!--## On vérifie s'il y a des erreurs et on les affiche ##-->
                    <?php if(!empty($err)):?>
                        <div class="errBox">
                            <?php foreach($err as $error):?>
                                <p class="error"><?php echo $error;?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <!--##-->
                    <div class="group">
                        <label for="name">Nom de l'article</label>
                        <input type="text" name="nom" id="name" maxlength="180" value="<?php echo $_POST['nom'] ?? '';?>">
                    </div>
                    <div class="group">
                        <label for="description">Description de l'article</label>
                        <textarea name="description" id="description" maxlength="180"><?php echo $_POST['description'] ?? '';?></textarea>
                    </div>
                    <div class="group">
                        <label for="price">Prix de l'article</label>
                        <input type="text" name="prix" id="price" value="<?php echo $_POST['prix'] ?? '';?>">
                    </div>
                    <div class="group">
                        <label for="stock">Stock de l'article</label>
                        <input type="number" name="stock" id="stock" min="1" value="<?php echo $_POST['stock'] ?? '';?>">
                    </div>
                    <div class="group">
                        <label for="image">Lien de l'Image de l'article</label>
                        <input type="url" name="image" id="image" value="<?php echo $_POST['image'] ?? 'https://images.unsplash.com/photo-1557821552-17105176677c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8Y2FydHxlbnwwfHwwfHx8MA%3D%3D&auto=format&fit=crop&w=900&q=60';?>">
                    </div>
                    <button type="submit" class="more">Enregistrer</button>
                </form>
            </div>
        </div>
    </main>
</body>
<script>
    document.addEventListener('scroll',()=>{
        if(window.scrollY>=10){
            document.querySelector('#header').classList.add('sticky')
        }else{
            document.querySelector('#header').classList.remove('sticky')
        }
    })
    
</script>
</html>