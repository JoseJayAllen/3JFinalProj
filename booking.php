<?php
require 'database.php';

// Fetch services
$services_query = "SELECT service_id, service_name, description, price FROM services";
$services_result = $conn->query($services_query);
$services = $services_result->fetch_all(MYSQLI_ASSOC);

// Fetch therapists
$therapists_query = "SELECT user_id, full_name FROM users WHERE role = 'therapist'";
$therapists_result = $conn->query($therapists_query);
$therapists = $therapists_result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body class="bg-gray-100">
    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-3xl font-bold text-center mb-6">Book an Appointment</h1>
        <form id="booking-form" method="POST" action="process_booking.php">
            <!-- Step 1: Select Service and Therapist -->
            <div id="step-1" class="step">
                <h2 class="text-xl font-bold mb-4">Step 1: Select Service and Therapist</h2>
                <div class="mb-4">
                    <label for="service" class="block text-sm font-medium text-gray-700">Service</label>
                    <select id="service" name="service" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select a Service</option>
                        <?php foreach ($services as $service): ?>
                            <option value="<?php echo $service['service_id']; ?>">
                                <?php echo $service['service_name']; ?> - $<?php echo $service['price']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="therapist" class="block text-sm font-medium text-gray-700">Therapist</label>
                    <select id="therapist" name="therapist" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select a Therapist</option>
                        <?php foreach ($therapists as $therapist): ?>
                            <option value="<?php echo $therapist['user_id']; ?>">
                                <?php echo $therapist['full_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="button" class="bg-blue-500 text-white py-2 px-4 rounded-lg" onclick="nextStep()">Next</button>
            </div>

            <!-- Step 2: Choose Date and Time -->
            <div id="step-2" class="step hidden">
                <h2 class="text-xl font-bold mb-4">Step 2: Choose Date and Time</h2>
                <div class="mb-4">
                    <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                    <input type="text" id="date" name="date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm flatpickr" placeholder="Select a date" required>
                </div>
                <div class="mb-4">
                    <label for="time" class="block text-sm font-medium text-gray-700">Time Slot</label>
                    <select id="time" name="time" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select a Time Slot</option>
                        <!-- Time slots will be dynamically loaded -->
                    </select>
                </div>
                <button type="button" class="bg-gray-500 text-white py-2 px-4 rounded-lg" onclick="prevStep()">Back</button>
                <button type="button" class="bg-blue-500 text-white py-2 px-4 rounded-lg" onclick="nextStep()">Next</button>
            </div>

            <!-- Step 3: Confirmation and Payment -->
            <div id="step-3" class="step hidden">
                <h2 class="text-xl font-bold mb-4">Step 3: Confirmation and Payment</h2>
                <div class="mb-4">
                    <p><strong>Service:</strong> <span id="summary-service"></span></p>
                    <p><strong>Therapist:</strong> <span id="summary-therapist"></span></p>
                    <p><strong>Date:</strong> <span id="summary-date"></span></p>
                    <p><strong>Time:</strong> <span id="summary-time"></span></p>
                </div>
                <div class="mb-4">
                    <label for="payment" class="block text-sm font-medium text-gray-700">Payment Method</label>
                    <select id="payment" name="payment" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="cash">Cash</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="paypal">PayPal</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="promo" class="block text-sm font-medium text-gray-700">Promo Code</label>
                    <input type="text" id="promo" name="promo" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <button type="button" class="bg-gray-500 text-white py-2 px-4 rounded-lg" onclick="prevStep()">Back</button>
                <button type="submit" class="bg-green-500 text-white py-2 px-4 rounded-lg">Confirm Appointment</button>
            </div>
        </form>
    </div>

    <script>
        // Multi-step form navigation
        let currentStep = 1;
        function showStep(step) {
            document.querySelectorAll('.step').forEach((el, index) => {
                el.classList.toggle('hidden', index !== step - 1);
            });
        }
        function nextStep() {
            if (currentStep < 3) currentStep++;
            showStep(currentStep);
        }
        function prevStep() {
            if (currentStep > 1) currentStep--;
            showStep(currentStep);
        }

        // Initialize Flatpickr
        flatpickr('.flatpickr', {
            minDate: 'today',
        });

        // Dynamically update summary
        document.getElementById('booking-form').addEventListener('input', function () {
            document.getElementById('summary-service').textContent = document.getElementById('service').selectedOptions[0]?.textContent || 'N/A';
            document.getElementById('summary-therapist').textContent = document.getElementById('therapist').selectedOptions[0]?.textContent || 'N/A';
            document.getElementById('summary-date').textContent = document.getElementById('date').value || 'N/A';
            document.getElementById('summary-time').textContent = document.getElementById('time').value || 'N/A';
        });
    </script>
</body>
</html>
