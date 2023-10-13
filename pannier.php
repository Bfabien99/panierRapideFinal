<?php
    # On se connecte à notre base de donnée
    $connection = mysqli_connect('localhost','root','','boutique');

    # Si la connexion n'a pas aboutie, on affiche une erreur
    if(!$connection){
        die("Une erreur est survenue lors de la liason avec la base de donnée. Veuillez réessayer plus tard!");
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
            padding: 10px;
            display: flex;
            flex-direction: column;
            gap: 2em;
        }

        .content h2{
            text-align: center;
        }

        .articles{
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            gap: 1.5em;
            display: flex;
            flex-direction: column;
            overflow-x: scroll;
        }

        .imgBox{
            width: 100px;
            height: 100px;
            background-color: #000000;
        }

        .imgBox img{
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        table{
            width: 100%;
            text-align: center;
            border-collapse: collapse;
        }

        table tr{
            border-bottom: 1px solid;
        }

        table thead tr th{
            max-width: 100px;
            text-transform: capitalize;
        }

        table tbody tr td.img{
            padding: 2px;
            display: flex;
            justify-content: center;
        }

        input[type="number"]{
            width: 70px;
            text-align: center;
        }

        .bilan{
            background-color: #8d8b8b30;
            padding: 10px;
            color: #444;
        }

        .bilan, .detailBox{
            display: flex;
            flex-direction: column;
            gap: 1em;
            width: 100%;
            max-width: 500px;
        }

        .detailBox{
            gap: 0.5em;
        }

        .details{
            display: flex;
            justify-content: space-between;
        }

        .details p:last-child{
            font-weight: bold;
        }

        .last{
            font-size: 1.5em;
        }

        .more{
            text-decoration: none;
            color: #fff;
            background-color: rgba(5, 149, 72, 0.841);
            padding: 5px;
            border-radius: 5px;
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
                <a href="./pannier.php"><span class="number">0</span>Pannier</a>
            </li>
        </ul>
    </header>
    <main>
        <section class="banner">
            <h3>Mon Pannier Rapide</h3>
            <img src="https://images.unsplash.com/photo-1601330862030-1e08c703ac04?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8N3x8cGFuaWVyfGVufDB8fDB8fHww&auto=format&fit=crop&w=800&q=60" alt="">
        </section>
        <div class="content">
            <h2>Mon Pannier</h2>
            <div class="articles">
               <table>
                <thead>
                    <tr>
                        <th>titre</th>
                        <th>image</th>
                        <th>quantité</th>
                        <th>prix unitaire</th>
                        <th>total</th>
                        <th>action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Tomate</td>
                        <td class="img">
                            <div class="imgBox">
                                <img src="https://plus.unsplash.com/premium_photo-1669906333449-5fc2c47cd8ec?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8dG9tYXRlfGVufDB8fDB8fHww&auto=format&fit=crop&w=800&q=60" alt="">
                            </div>
                        </td>
                        <td><input type="number" value="3"></td>
                        <td>20.00</td>
                        <td>60.00</td>
                        <td><a href=""><i class="fas fa-check" style="color: blue; margin: 0.5em;"></i></a><a href=""><i class="fas fa-trash" style="color: red; margin: 0.5em;"></i></a></td>
                    </tr>
                </tbody>
               </table>
               <div class="bilan">
                <h4>Details facture</h4>
                <div class="detailBox">
                    <div class="details">
                        <p>Tomate</p>
                        <p>3 x 20.00</p>
                        <p>60.00 FCFA</p>
                    </div>
                     <div class="details">
                        <p>Banane</p>
                        <p>1 x 40.00</p>
                        <p>40.00 FCFA</p>
                    </div>
                    <div class="details last">
                        <p>Total</p>
                        <p>100.00 FCFA</p>
                    </div>
                    <div class="details">
                        <a href="" class="more">Commander</a>
                    </div>
                </div>
               </div>
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