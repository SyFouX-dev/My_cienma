<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}
?>

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
        $userIdToDelete = $_POST['delete_users'];
    
        echo "user delete: ".$userIdToDelete;
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

    if (isset($_POST['update_user'])) {
        $userIdToUpdate = $_POST['update_user_id'];
        $email = $_POST['update_email'];
        $firstname = $_POST['update_firstname'];
        $lastname = $_POST['update_lastname'];
        $birthdate = $_POST['update_birthdate'];
        $address = $_POST['update_address'];
        $zipcode = $_POST['update_zipcode'];
        $city = $_POST['update_city'];
        $country = $_POST['update_country'];

        try {
            $stmt = $conn->prepare("UPDATE user SET email = ?, firstname = ?, lastname = ?, birthdate = ?, address = ?, zipcode = ?, city = ?, country = ? WHERE id = ?");
            if ($stmt === false) {
                throw new Exception('Échec de la préparation de la requête : ' . $conn->error);
            }

            $stmt->bind_param('ssssssssi', $email, $firstname, $lastname, $birthdate, $address, $zipcode, $city, $country, $userIdToUpdate);

            if (!$stmt->execute()) {
                throw new Exception('Échec de l\'exécution de la requête : ' . $stmt->error);
            }

            echo "<p>Utilisateur mis à jour avec succès !</p>";
        } catch (Exception $e) {
            echo "<p>Erreur lors de la mise à jour de l'utilisateur : " . $e->getMessage() . "</p>";
        }
    }

    if (isset($_POST['search_user'])) {
        $userIdToSearch = $_POST['search_user_id'];

        try {
            $stmt = $conn->prepare("SELECT * FROM user WHERE id = ?");
            if ($stmt === false) {
                throw new Exception('Échec de la préparation de la requête : ' . $conn->error);
            }

            $stmt->bind_param('i', $userIdToSearch);

            if (!$stmt->execute()) {
                throw new Exception('Échec de l\'exécution de la requête : ' . $stmt->error);
            }

            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
            } else {
                echo "<p>Aucun utilisateur trouvé avec cet ID.</p>";
            }
        } catch (Exception $e) {
            echo "<p>Erreur lors de la recherche de l'utilisateur : " . $e->getMessage() . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Admin</title>
    <link rel="stylesheet" href="./Style/admin.css">
</head>
<body>

    <header>
        <h1>BIENVENUE SUR LE PANEL ADMIN !</h1>
        <button><a href="Connexion_Admin.php">Déconnexion</a></button>
    </header>

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
        <input type="text" id="delete_user" name="delete_users" required><br><br>

        <input type="submit" name="delete_user" value="Supprimer">
    </form>
</div>

<div>
    <h2>Modifier un utilisateur</h2>
    <form method="post" action="">
        <label for="search_user_id">ID de l'utilisateur à modifier :</label>
        <input type="text" id="search_user_id" name="search_user_id" required><br><br>
        <input type="submit" name="search_user" value="Rechercher">
    </form>

    <?php if (isset($user)): ?>
    <form method="post" action="">
        <input type="hidden" name="update_user_id" value="<?php echo $user['id']; ?>">
        
        <label for="update_email">Email :</label>
        <input type="email" id="update_email" name="update_email" value="<?php echo $user['email']; ?>" required><br><br>

        <label for="update_firstname">Prénom :</label>
        <input type="text" id="update_firstname" name="update_firstname" value="<?php echo $user['firstname']; ?>" required><br><br>

        <label for="update_lastname">Nom :</label>
        <input type="text" id="update_lastname" name="update_lastname" value="<?php echo $user['lastname']; ?>" required><br><br>

        <label for="update_birthdate">Date de Naissance :</label>
        <input type="date" id="update_birthdate" name="update_birthdate" value="<?php echo $user['birthdate']; ?>" required><br><br>

        <label for="update_address">Adresse :</label>
        <input type="text" id="update_address" name="update_address" value="<?php echo $user['address']; ?>" required><br><br>

        <label for="update_zipcode">Code Postal :</label>
        <input type="text" id="update_zipcode" name="update_zipcode" value="<?php echo $user['zipcode']; ?>" required><br><br>

        <label for="update_city">Ville :</label>
        <input type="text" id="update_city" name="update_city" value="<?php echo $user['city']; ?>" required><br><br>

        <label for="update_country">Pays :</label>
        <input type="text" id="update_country" name="update_country" value="<?php echo $user['country']; ?>" required><br><br>

        <input type="submit" name="update_user" value="Mettre à jour">
    </form>
    <?php endif; ?>
</div>

</body>
</html>
