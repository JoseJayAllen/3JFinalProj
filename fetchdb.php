<?php
require 'database.php';

function getServices($pdo) {
    $stmt = $pdo->query("SELECT service_name, price, description");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getReviews($pdo) {
    $stmt = $pdo->query("SELECT user_id, rating, comment");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
