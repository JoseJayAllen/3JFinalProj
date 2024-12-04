<?php
require 'database.php';

$services = [];
$sql = "SELECT * FROM services"; // Fetch all services
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row; // Populate the $services array
    }
}

// Fetch reviews with appointment details
$reviews = [];
$sql = "SELECT r.rating, r.comment, u.full_name AS user, a.service_id, s.service_name 
        FROM reviews r
        JOIN users u ON r.user_id = u.user_id
        JOIN appointments a ON r.appointment_id = a.appointment_id
        JOIN services s ON a.service_id = s.service_id
        ORDER BY r.created_at DESC LIMIT 6";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row; // Populate the $reviews array
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
    <style>
        /* Custom CSS for color palette and typography */
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f0f8f1; /* Light greenish background for wellness feel */
        }

        .hero-section {
            color: white;
            background-color: #2e7d32; /* Calming green background */
        }

        .hero-section h1 {
            font-family: 'Roboto', sans-serif;
            font-size: 3rem;
        }

        .hero-section p {
            font-size: 1.25rem;
        }

        .service-card {
            background-color: #fff9f1; /* Soft earth tone background */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .service-card h3 {
            color: #2e7d32; /* Green for headings */
        }

        .review-card {
            background-color: #f4f8f4; /* Light soft background */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .review-card h3 {
            font-size: 1.25rem;
            color: #388e3c; /* Green for user names */
        }

        .review-card p {
            font-size: 1rem;
            color: #555;
        }

        .cta-section {
            background-color: #388e3c; /* Soft green */
            color: white;
        }

        .cta-section h2 {
            font-family: 'Roboto', sans-serif;
            font-size: 2rem;
        }

        .cta-section p {
            font-size: 1.125rem;
        }

        .cta-section a {
            background-color: white;
            color: #388e3c;
            padding: 10px 20px;
            font-weight: bold;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .cta-section a:hover {
            background-color: #f4f8f4;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <header class="hero-section bg-cover bg-center h-screen flex items-center justify-center text-center" style="background-image: url('img/bg.jpg');">
        <div class="bg-black bg-opacity-50 p-8 rounded-lg max-w-3xl">
            <h1 class="text-4xl md:text-5xl font-bold">Your Wellness Journey Starts Here</h1>
            <p class="mt-4 text-lg">Relax, rejuvenate, and refresh with our premium spa services.</p>
            <div class="mt-6 flex justify-center gap-4">
                <a href="booking.php" class="bg-green-500 hover:bg-green-600 text-white py-3 px-6 rounded-lg font-medium shadow-lg">Book Now</a>
                <a href="service.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-3 px-6 rounded-lg font-medium shadow-lg">View Services</a>
            </div>
        </div>
    </header>

    <section class="services-overview py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Our Popular Services</h2>
            <div class="flex flex-wrap gap-8 justify-center">
                <?php foreach ($services as $service): ?>
                    <div class="service-card max-w-xs flex-grow">
                        <img src="img/<?php echo $service['service_name']; ?>.jpg" 
                            alt="<?php echo $service['service_name']; ?>" 
                            class="w-full h-56 object-cover">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-2"><?php echo $service['service_name']; ?></h3>
                            <p class="text-gray-600 mb-4"><?php echo $service['description']; ?></p>
                            <p class="text-gray-800 font-bold mb-4">Price: $<?php echo $service['price']; ?></p>
                            <a href="booking.php?service_id=<?php echo $service['service_id']; ?>" 
                               class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg">
                               Book Now
                            </a>
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
                    <div class="review-card">
                        <h3><?php echo htmlspecialchars($review['user']); ?></h3>
                        <p class="text-gray-500 mb-2">Service: <?php echo htmlspecialchars($review['service_name']); ?></p>
                        <p class="text-yellow-500 font-bold mb-2">Rating: <?php echo $review['rating']; ?>/5</p>
                        <p class="text-gray-600">"<?php echo htmlspecialchars($review['comment']); ?>"</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="cta-section py-16 text-center">
        <h2 class="text-3xl font-bold mb-4">Ready to Begin Your Journey?</h2>
        <p class="text-lg mb-6">Create an account or schedule your first session today!</p>
        <a href="signup.php" class="font-medium py-3 px-6 rounded-lg shadow-lg">Get Started</a>
    </section>

</body>
</html>
