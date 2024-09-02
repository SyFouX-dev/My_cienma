<?php
include 'db_connection.php';

$message_movies = "";
$message_members = "";
$message_projections = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['search_movies'])) {
        $name = $_POST['name'];
        $genre = $_POST['genre'];
        $distributor = $_POST['distributor'];

        if (empty($name) && empty($genre) && empty($distributor)) {
            $message_movies = "Merci de spécifier au moins un titre, un genre ou un distributeur.";
        } else {
            $sql = "SELECT m.title
                    FROM movie m
                    INNER JOIN distributor d ON m.id_distributor = d.id
                    INNER JOIN movie_genre mg ON m.id = mg.id_movie
                    INNER JOIN genre g ON mg.id_genre = g.id
                    WHERE 1=1";

            if (!empty($name)) {
                $sql .= " AND m.title LIKE '%$name%'";
            }
            if (!empty($genre)) {
                $sql .= " AND g.name LIKE '%$genre%'";
            }
            if (!empty($distributor)) {
                $sql .= " AND d.name LIKE '%$distributor%'";
            }

            $result_movies = $conn->query($sql);
        }
    } elseif (isset($_POST['search_members'])) {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];

        if (empty($first_name) && empty($last_name)) {
            $message_members = "Merci de spécifier au moins un prénom ou un nom de famille.";
        } else {
            $sql = "SELECT *, membership.id AS membershipId, user.id AS userId
                    FROM user
                    INNER JOIN membership ON user.id = membership.id_user
                    INNER JOIN subscription ON membership.id_subscription = subscription.id
                    WHERE 1=1";

            if (!empty($first_name)) {
                $sql .= " AND user.firstname LIKE '%$first_name%'";
            }
            if (!empty($last_name)) {
                $sql .= " AND user.lastname LIKE '%$last_name%'";
            }

            $result_members = $conn->query($sql);
        }
    } elseif (isset($_POST['search_projections'])) {
        $projection_date = $_POST['projection_date'];

        if (empty($projection_date)) {
            $message_projections = "Merci de spécifier une date de projection.";
        } else {
            $sql = 'SELECT movie.title, movie_schedule.date_begin 
                    FROM movie 
                    INNER JOIN movie_schedule ON movie.id=movie_schedule.id_movie 
                    WHERE DATE(movie_schedule.date_begin) = DATE("' . $projection_date . '")';

            $result_projections = $conn->query($sql);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search</title>
    <link rel="stylesheet" href="./Style/search.css">
</head>
<body>
    <header>
        <div class="navbar">
            <div class="brand">Bienvenue sur My Cinema</div>
            <nav>
                <ul>
                    <li><a href="#">Accueil</a></li>
                    <li><a href="#">Films</a></li>
                    <li><a href="#">Membres</a></li>
                    <li><a href="Connexion_Admin.php">Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <h1>Search</h1>
    <form method="POST" action="">
        <h2>Search Movies</h2>
        <input type="text" name="name" placeholder="Movie Name">
        <select name="genre" class="form-select">
            <option value="">Tous les genres</option>
            <option value="Action">Action</option>
            <option value="Animation">Animation</option>
            <option value="Adventure">Adventure</option>
            <option value="Drama">Drama</option>
            <option value="Comedy">Comedy</option>
            <option value="Mystery">Mystery</option>
            <option value="Biography">Biography</option>
            <option value="Crime">Crime</option>
            <option value="Fantasy">Fantasy</option>
            <option value="Horror">Horror</option>
            <option value="Sci-Fi">Sci-Fi</option>
            <option value="Romance">Romance</option>
            <option value="Family">Family</option>
            <option value="Thriller">Thriller</option>       
        </select>
        <input type="text" name="distributor" placeholder="Distributor">
        <button type="submit" name="search_movies">Search Movies</button>
    </form>

    <?php if (!empty($message_movies)): ?>
        <p><?php echo $message_movies; ?></p>
    <?php elseif (isset($result_movies) && $result_movies->num_rows > 0): ?>
        <h3>Search Results - Movies:</h3>
        <ul>
            <?php while($row = $result_movies->fetch_assoc()): ?>
                <li>
                    <?php echo $row['title']; ?>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php elseif(isset($result_movies)): ?>
        <p>No movies found.</p>
    <?php endif; ?>

    <form method="POST" action="">
        <h2>Search Members</h2>
        <input type="text" name="first_name" placeholder="First Name">
        <input type="text" name="last_name" placeholder="Last Name">
        <button type="submit" name="search_members">Search Members</button>
    </form>

    <?php if (!empty($message_members)): ?>
        <p><?php echo $message_members; ?></p>
    <?php elseif (isset($result_members) && $result_members->num_rows > 0): ?>
        <h3>Search Results - Members:</h3>
        <ul>
            <?php while($row = $result_members->fetch_assoc()): ?>
                <li><?php echo $row['firstname'] . ' ' . $row['lastname']; ?></li>
            <?php endwhile; ?>
        </ul>
    <?php elseif(isset($result_members)): ?>
        <p>No members found.</p>
    <?php endif; ?>

    <form method="POST" action="">
        <h2>Search Projections</h2>
        <input type="date" name="projection_date" placeholder="Projection Date">
        <button type="submit" name="search_projections">Search Projections</button>
    </form>

    <?php if (!empty($message_projections)): ?>
        <p><?php echo $message_projections; ?></p>
    <?php elseif (isset($result_projections) && $result_projections->num_rows > 0): ?>
        <h3>Search Results - Projections:</h3>
        <ul>
            <?php while($row = $result_projections->fetch_assoc()): ?>
                <li><?php echo $row['title'] . ' - ' . $row['date_begin']; ?></li>
            <?php endwhile; ?>
        </ul>
    <?php elseif(isset($result_projections)): ?>
        <p>No projections found.</p>
    <?php endif; ?>

</body>
</html>
