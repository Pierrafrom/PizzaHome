<?php

use App\DB_Connection;

$title = "Home";
$cssFiles = ["banner.css", "home.css"];
?>

<div class="banner home-banner">
    <img src="/img/homeBanner.webp" alt="An Experienced Chef Bakes Pizza with a Special Giant Spatula.">
    <h1>Pizza Home</h1>
    <p>"Where Every Bite Takes You to Italy."</p>
    <a href="#" class="btn-primary">Order Now!</a>
</div>

<?php
try {
    // Utilisation de la méthode 'query' pour récupérer tous les clients sous forme de tableau associatif
    $clients = DB_Connection::query("SELECT * FROM CLIENT");

    echo '<table>';
    echo '<tr><th>ID</th><th>First Name</th><th>Last Name</th></tr>';
    foreach ($clients as $client) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($client['id']) . '</td>';
        echo '<td>' . htmlspecialchars($client['first_name']) . '</td>';
        echo '<td>' . htmlspecialchars($client['last_name']) . '</td>';
        echo '</tr>';
    }
    echo '</table>';
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

?>