<?php
require 'db.php';

// Fetch services
function getServices($pdo) {
    $stmt = $pdo->query("SELECT service_name, price, description, image FROM services LIMIT 6");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch testimonials
function getTestimonials($pdo) {
    $stmt = $pdo->query("SELECT customer_name, rating, comment, photo FROM reviews LIMIT 5");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
