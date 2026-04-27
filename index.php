<?php
// Exemple de données utilisateur (tu peux remplacer par une base de données)
$user = [
    "nom" => "Abdelilah",
    "prenom" => "Habib",
    "email" => "abdelilah@email.com",
    "age" => 25,
    "bio" => "Passionné par le cloud et la cybersécurité."
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f4f4f4;
        }
        .profile {
            width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
        }
        p {
            font-size: 16px;
        }
    </style>
</head>
<body>

<div class="profile">
    <h2>Mon Profil</h2>
    <p><strong>Nom :</strong> <?php echo $user['nom']; ?></p>
    <p><strong>Prénom :</strong> <?php echo $user['prenom']; ?></p>
    <p><strong>Email :</strong> <?php echo $user['email']; ?></p>
    <p><strong>Âge :</strong> <?php echo $user['age']; ?> ans</p>
    <p><strong>Bio :</strong> <?php echo $user['bio']; ?></p>
</div>

</body>
</html>
