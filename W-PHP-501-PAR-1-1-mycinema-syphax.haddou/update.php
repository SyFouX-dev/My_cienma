<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        $email = $_POST['email'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $birthdate = $_POST['birthdate'];
        $address = $_POST['address'];
        $zipcode = $_POST['zipcode'];
        $city = $_POST['city'];
        $country = $_POST['country'];

        try {
            $stmt = $conn->prepare("INSERT INTO user (email, firstname, lastname, birthdate, address, zipcode, city, country) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt === false) {
                throw new Exception('Échec de la préparation de la requête : ' . $conn->error);
            }
            
            $stmt->bind_param('ssssssss', $email, $firstname, $lastname, $birthdate, $address, $zipcode, $city, $country);

            if (!$stmt->execute()) {
                throw new Exception('Échec de l\'exécution de la requête : ' . $stmt->error);
            }

            echo "<p>Utilisateur ajouté avec succès !</p>";
        } catch (Exception $e) {
            echo "<p>Erreur lors de l'ajout de l'utilisateur : " . $e->getMessage() . "</p>";
        }
    }

    if (isset($_POST['delete_user'])) {
        $userIdToDelete = $_POST['delete_user'];
    
        try {
            $stmt = $conn->prepare("DELETE FROM user WHERE id = ?");
            if ($stmt === false) {
                throw new Exception('Échec de la préparation de la requête : ' . $conn->error);
            }
    
            $stmt->bind_param('i', $userIdToDelete);
    
            if (!$stmt->execute()) {
                throw new Exception('Échec de l\'exécution de la requête : ' . $stmt->error);
            }
    
            echo "<p>Utilisateur supprimé avec succès !</p>";
        } catch (Exception $e) {
            echo "<p>Erreur lors de la suppression de l'utilisateur : " . $e->getMessage() . "</p>";
        }
    }
    
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Utilisateurs</title>
</head>
<body>

<div>
    <h2>Ajouter un utilisateur</h2>
    <form method="post" action="">
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="firstname">Prénom :</label>
        <input type="text" id="firstname" name="firstname" required><br><br>

        <label for="lastname">Nom :</label>
        <input type="text" id="lastname" name="lastname" required><br><br>

        <label for="birthdate">Date de Naissance :</label>
        <input type="date" id="birthdate" name="birthdate" required><br><br>

        <label for="address">Adresse :</label>
        <input type="text" id="address" name="address" required><br><br>

        <label for="zipcode">Code Postal :</label>
        <input type="text" id="zipcode" name="zipcode" required><br><br>

        <label for="city">Ville :</label>
        <input type="text" id="city" name="city" required><br><br>

        <label for="country">Pays :</label>
        <input type="text" id="country" name="country" required><br><br>

        <input type="submit" name="add_user" value="Ajouter">
    </form>
</div>

<div>
    <h2>Supprimer un utilisateur</h2>
    <form method="post" action="">
        <label for="delete_user">ID de l'utilisateur à supprimer :</label>
        <input type="text" id="delete_user" name="delete_user" required><br><br>

        <input type="submit" name="delete_user" value="Supprimer">
    </form>
</div>

</body>
</html>
