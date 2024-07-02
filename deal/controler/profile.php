<?php
session_start();
if(!isset($_SESSION['email']) || !isset($_SESSION['senha'])) {
    header('Location: ../login/login.html');
    exit();
}
$logado = $_SESSION['email'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="profile.css">
    <style>
        #pfpic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            position: relative;
        }
        #profilePic {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        #input-pfpic {
            display: none;
        }
    </style>
    <title>Document</title>
</head>
<body>
    <section id="container">
        <section id="leftmenu"> <!-- Conteúdo do left menu --> </section>
        
        <section id="rightmenu"> 
            <div id="rmwb">
            
            </div>
            <form action="InsertPerfil.php"  id="formzin" method="POST" enctype="multipart/form-data">

            <!-- Utilize uma div como botão -->
            <div id="pfpic" onclick="document.getElementById('input-pfpic').click()">
                <img id="profilePic" src="image-removebg-preview.png" width="150px" alt="Mudar Foto de Perfil">
            </div>
            
            <!-- Input file oculto para selecionar a imagem -->
            <input type="file" name="pfpic" id="input-pfpic" style="display: none;">

            <input type="text" name="nome" id="nome">

            <textarea name="bio" id="bio"></textarea>

            <input type="text" name="local" id="local">
            
            <input type="submit" id="submit" value="Enviar / Editar">

            </form>
        </section>
    </section>

    <script>
        document.getElementById('input-pfpic').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onload = function(event) {
                document.getElementById('profilePic').src = event.target.result;
            }

            reader.readAsDataURL(file);
        });
    </script>
</body>
</html>
