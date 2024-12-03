<?php
require 'database.php';

$services = [];
$sql = "SELECT * FROM services";
$result = $conn->query($sql);
$stmt = $conn->prepare("SELECT * FROM services WHERE service_id = ?");
$stmt->bind_param("i", $service_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
}

$reviews = [];
$sql = "SELECT r.rating, r.comment, u.full_name AS user FROM reviews r 
        JOIN users u ON r.user_id = u.user_id ORDER BY r.created_at DESC LIMIT 6";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wellness Spa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">

    <header class="hero-section bg-cover bg-center h-screen flex items-center justify-center text-center" style="background-image: url('path-to-your-image.jpg');">
        <div class="bg-black bg-opacity-50 p-8 rounded-lg max-w-3xl">
            <h1 class="text-4xl md:text-5xl font-bold text-white">Your Wellness Journey Starts Here</h1>
            <p class="text-white mt-4 text-lg">Relax, rejuvenate, and refresh with our premium spa services.</p>
            <div class="mt-6 flex justify-center gap-4">
                <a href="booking.php" class="bg-green-500 hover:bg-green-600 text-white py-3 px-6 rounded-lg font-medium shadow-lg">Book Now</a>
                <a href="service.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-3 px-6 rounded-lg font-medium shadow-lg">View Services</a>
            </div>
        </div>
    </header>

    <section class="services-overview py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Our Popular Services</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($services as $service): ?>
                <div class="service-card bg-white shadow-lg rounded-lg overflow-hidden">
                    <img src="images/<?php echo $service['service_name']; ?>.jpg" alt="<?php echo $service['service_name']; ?>" class="w-full h-56 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2"><?php echo $service['service_name']; ?></h3>
                        <p class="text-gray-600 mb-4"><?php echo $service['description']; ?></p>
                        <p class="text-gray-800 font-bold mb-4">Price: $<?php echo $service['price']; ?></p>
                        <a href="booking.php?service_id=<?php echo $service['service_id']; ?>" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg">Book Now</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="review bg-gray-100 py-16">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">What Our Customers Say</h2>
        <div class="review-slider grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($reviews as $review): ?>
                <div class="reviews-card bg-white shadow-lg rounded-lg p-6">
                    <h3 class="font-semibold text-lg mb-2"><?php echo htmlspecialchars($review['user']); ?></h3>
                    <p class="text-yellow-500 font-bold mb-2">Rating: <?php echo $review['rating']; ?>/5</p>
                    <p class="text-gray-600">"<?php echo htmlspecialchars($review['comment']); ?>"</p>
                </div>  
            <?php endforeach; ?>
        </div>
    </div>
</section>


    <section class="call-to-action bg-green-500 text-white py-16 text-center">
        <h2 class="text-3xl font-bold mb-4">Ready to Begin Your Journey?</h2>
        <p class="text-lg mb-6">Create an account or schedule your first session today!</p>
        <a href="signup.php" class="bg-white text-green-500 hover:text-green-600 font-medium py-3 px-6 rounded-lg shadow-lg">Get Started</a>
    </section>

</body>
</html>
