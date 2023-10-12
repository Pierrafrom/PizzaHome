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
DB_Connection::connect();

try {
    $stmt = DB_Connection::getPDO()->query("SELECT * FROM CLIENT");

    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo '<table>';
    echo '<tr><th>ID</th><th>Nom</th><th>Email</th></tr>';
    foreach ($clients as $client) {
        echo '<tr>';
        echo '<td>' . $client['id'] . '</td>';
        echo '<td>' . $client['first_name'] . '</td>';
        echo '<td>' . $client['last_name'] . '</td>';
        echo '</tr>';
    }
    echo '</table>';
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>