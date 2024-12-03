<?php
require 'database.php';

// Initialize variables
$filter_conditions = [];
$order_by = 'service_name'; // Default sorting
$order_dir = 'ASC';

// Handle filtering
if (!empty($_GET['price_min'])) {
    $filter_conditions[] = "price >= " . (float)$_GET['price_min'];
}
if (!empty($_GET['price_max'])) {
    $filter_conditions[] = "price <= " . (float)$_GET['price_max'];
}
if (!empty($_GET['duration_min'])) {
    $filter_conditions[] = "duration >= " . (int)$_GET['duration_min'];
}
if (!empty($_GET['duration_max'])) {
    $filter_conditions[] = "duration <= " . (int)$_GET['duration_max'];
}

// Handle sorting
if (!empty($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'price_asc':
            $order_by = 'price';
            $order_dir = 'ASC';
            break;
        case 'price_desc':
            $order_by = 'price';
            $order_dir = 'DESC';
            break;
        case 'duration_asc':
            $order_by = 'duration';
            $order_dir = 'ASC';
            break;
        case 'duration_desc':
            $order_by = 'duration';
            $order_dir = 'DESC';
            break;
    }
}

// Build query
$query = "SELECT * FROM services";
if (!empty($filter_conditions)) {
    $query .= " WHERE " . implode(" AND ", $filter_conditions);
}
$query .= " ORDER BY $order_by $order_dir";

// Execute query
$result = $conn->query($query);
$services = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service List</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-center mb-8 text-gray-800">Our Services</h1>
        <div class="flex gap-8">
            <!-- Sidebar for Filters -->
            <aside class="w-1/4 bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-bold mb-4">Filter Services</h2>
                <form method="GET" action="services.php">
                    <div class="mb-4">
                        <label for="price_min" class="block text-sm font-medium text-gray-700">Price (Min)</label>
                        <input type="number" name="price_min" id="price_min" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="mb-4">
                        <label for="price_max" class="block text-sm font-medium text-gray-700">Price (Max)</label>
                        <input type="number" name="price_max" id="price_max" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="mb-4">
                        <label for="duration_min" class="block text-sm font-medium text-gray-700">Duration (Min, mins)</label>
                        <input type="number" name="duration_min" id="duration_min" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="mb-4">
                        <label for="duration_max" class="block text-sm font-medium text-gray-700">Duration (Max, mins)</label>
                        <input type="number" name="duration_max" id="duration_max" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg w-full">Apply Filters</button>
                </form>
            </aside>
            
            <!-- Services List -->
            <div class="w-3/4">
                <!-- Sorting Options -->
                <div class="mb-6 flex justify-end">
                    <form method="GET" action="services.php" class="flex gap-4 items-center">
                        <label for="sort" class="text-sm font-medium text-gray-700">Sort By:</label>
                        <select name="sort" id="sort" class="border-gray-300 rounded-md shadow-sm">
                            <option value="price_asc">Price (Low to High)</option>
                            <option value="price_desc">Price (High to Low)</option>
                            <option value="duration_asc">Duration (Short to Long)</option>
                            <option value="duration_desc">Duration (Long to Short)</option>
                        </select>
                        <button type="submit" class="bg-green-500 text-white py-2 px-4 rounded-lg">Sort</button>
                    </form>
                </div>

                <!-- Service Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($services as $service): ?>
                        <div class="bg-white shadow rounded-lg overflow-hidden">
                            <img src="images/<?php echo $service['service_name']; ?>.jpg" alt="<?php echo $service['service_name']; ?>" class="w-full h-48 object-cover">
                            <div class="p-6">
                                <h3 class="text-xl font-bold mb-2"><?php echo $service['service_name']; ?></h3>
                                <p class="text-gray-600 mb-4"><?php echo $service['description']; ?></p>
                                <p class="text-gray-800 font-bold">Price: $<?php echo $service['price']; ?></p>
                                <p class="text-gray-500">Duration: <?php echo $service['duration']; ?> mins</p>
                                <a href="booking.php?service_id=<?php echo $service['service_id']; ?>" class="mt-4 inline-block bg-blue-500 text-white py-2 px-4 rounded-lg">Book Now</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
