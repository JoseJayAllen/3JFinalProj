<?php
require 'database.php';
session_start(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }

    $service_id = $_POST['service'];
    $therapist_id = $_POST['therapist'];
    $user_id = $_SESSION['user_id']; 
    $appointment_date = $_POST['date'];
    $start_time = $_POST['time'];

    $stmt = $conn->prepare("SELECT duration FROM services WHERE service_id = ?");
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $stmt->bind_result($duration);
    $stmt->fetch();
    $stmt->close();

    if (!$duration) {
        die("Service duration not found.");
    }

    $end_time = date("H:i", strtotime($start_time) + $duration * 60); 

    $stmt = $conn->prepare("INSERT INTO appointments (user_id, therapist_id, service_id, appointment_date, start_time, end_time) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiisss", $user_id, $therapist_id, $service_id, $appointment_date, $start_time, $end_time);

    if ($stmt->execute()) {
        $appointment_id = $stmt->insert_id; 

        if (isset($_POST['confirm_booking'])) {
            header("Location: booking.php?appointment_id=$appointment_id");
            exit;
        }
    } else {
        echo "Error: " . $stmt->error;
    }
}

$services_query = "SELECT service_id, service_name, description, price FROM services";
$services_result = $conn->query($services_query);
$services = $services_result->fetch_all(MYSQLI_ASSOC);

$therapists_query = "SELECT user_id, full_name FROM users WHERE role = 'therapist'";
$therapists_result = $conn->query($therapists_query);
$therapists = $therapists_result->fetch_all(MYSQLI_ASSOC);
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
<body class="bg-gray-50 text-gray-800 font-sans">

    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-3xl font-bold text-center mb-6 text-green-600">Book an Appointment</h1>
        <form id="booking-form" method="POST" action="booking.php">

            <div id="step-1" class="step">
                <h2 class="text-xl font-bold mb-4 text-gray-700">Step 1: Select Service and Therapist</h2>
                <div class="mb-4">
                    <label for="service" class="block text-sm font-medium text-gray-700">Service</label>
                    <select id="service" name="service" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required onchange="updateSummary()">
                        <option value="">Select a Service</option>
                        <?php foreach ($services as $service): ?>
                            <option value="<?php echo $service['service_id']; ?>" data-price="<?php echo $service['price']; ?>">
                                <?php echo $service['service_name']; ?> - $<?php echo $service['price']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="therapist" class="block text-sm font-medium text-gray-700">Therapist</label>
                    <select id="therapist" name="therapist" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required onchange="updateSummary()">
                        <option value="">Select a Therapist</option>
                        <?php foreach ($therapists as $therapist): ?>
                            <option value="<?php echo $therapist['user_id']; ?>">
                                <?php echo $therapist['full_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="button" class="bg-green-500 text-white py-2 px-4 rounded-lg" onclick="nextStep()">Next</button>
            </div>

            <div id="step-2" class="step hidden">
                <h2 class="text-xl font-bold mb-4 text-gray-700">Step 2: Choose Date and Time</h2>
                <div class="mb-4">
                    <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                    <input type="text" id="date" name="date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm flatpickr" placeholder="Select a date" required onchange="updateSummary()">
                </div>
                <div class="mb-4">
                    <label for="time" class="block text-sm font-medium text-gray-700">Time Slot</label>
                    <select id="time" name="time" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required onchange="updateSummary()">
                        <option value="">Select a Time Slot</option>
                    </select>
                </div>
                <button type="button" class="bg-gray-500 text-white py-2 px-4 rounded-lg" onclick="prevStep()">Back</button>
                <button type="button" class="bg-green-500 text-white py-2 px-4 rounded-lg" onclick="nextStep()">Next</button>
            </div>

            <div id="step-3" class="step hidden">
                <h2 class="text-xl font-bold mb-4 text-gray-700">Step 3: Confirmation and Payment</h2>
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
                <button type="button" class="bg-gray-500 text-white py-2 px-4 rounded-lg" onclick="prevStep()">Back</button>
                <button type="submit" name="confirm_booking" value="1" class="bg-green-500 text-white py-2 px-4 rounded-lg">Confirm Appointment</button>
            </div>
        </form>
        <a href="index.php" class="mt-6 inline-block text-blue-500">Back to Home</a>
    </div>

    <div id="confirmation-modal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full">
            <h2 class="text-xl font-bold mb-4 text-gray-700">Booking Confirmation</h2>
            <p class="mb-4 text-gray-600">Your booking has been confirmed. Do you want to book another appointment or go back home?</p>
            <div class="flex justify-between">
                <button class="bg-blue-500 text-white py-2 px-4 rounded-lg" onclick="bookAgain()">Book Again</button>
                <button class="bg-gray-500 text-white py-2 px-4 rounded-lg" onclick="goHome()">Go Home</button>
            </div>
        </div>
    </div>

    <script>
        flatpickr("#date", { minDate: "today" });
        
        document.addEventListener('DOMContentLoaded', () => {
            const timeSlots = [
                { value: "09:00", label: "9:00 AM" },
                { value: "10:00", label: "10:00 AM" },
                { value: "11:00", label: "11:00 AM" },
                { value: "13:00", label: "1:00 PM" },
                { value: "14:00", label: "2:00 PM" }
            ];

            const timeSelect = document.getElementById("time");

            timeSelect.innerHTML = "<option value=''>Select a Time Slot</option>";

            timeSlots.forEach(slot => {
                const option = document.createElement("option");
                option.value = slot.value;
                option.textContent = slot.label;
                timeSelect.appendChild(option);
            });
        });

        function nextStep() {
            const currentStep = document.querySelector(".step:not(.hidden)");
            const nextStep = currentStep.nextElementSibling;
            if (nextStep) {
                currentStep.classList.add("hidden");
                nextStep.classList.remove("hidden");
            }
        }

        function prevStep() {
            const currentStep = document.querySelector(".step:not(.hidden)");
            const prevStep = currentStep.previousElementSibling;
            if (prevStep) {
                currentStep.classList.add("hidden");
                prevStep.classList.remove("hidden");
            }
        }

        function updateSummary() {
            const service = document.getElementById("service");
            const therapist = document.getElementById("therapist");
            const date = document.getElementById("date");
            const time = document.getElementById("time");

            document.getElementById("summary-service").textContent = service.options[service.selectedIndex].text;
            document.getElementById("summary-therapist").textContent = therapist.options[therapist.selectedIndex].text;
            document.getElementById("summary-date").textContent = date.value;
            document.getElementById("summary-time").textContent = time.value;
        }

        function showConfirmationModal() {
            document.getElementById("confirmation-modal").classList.remove("hidden");
        }

        function bookAgain() {
            document.getElementById("confirmation-modal").classList.add("hidden");
            document.getElementById("booking-form").reset();
            document.getElementById("step-1").classList.remove("hidden");
        }

        function goHome() {
            window.location.href = "index.php";
        }
    </script>
</body>
</html>

