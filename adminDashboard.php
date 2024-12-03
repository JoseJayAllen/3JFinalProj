<?php
include('database.php');

session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php"); 
    exit();
}

$bookingsQuery = "SELECT * FROM bookings ORDER BY booking_date DESC";
$bookingsResult = $conn->query($bookingsQuery);

$servicesQuery = "SELECT * FROM services";
$servicesResult = $conn->query($servicesQuery);

$availabilityQuery = "SELECT * FROM therapist_availability";
$availabilityResult = $conn->query($availabilityQuery);

$paymentsQuery = "SELECT * FROM payments ORDER BY payment_date DESC";
$paymentsResult = $conn->query($paymentsQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="adminDashboard.css">
</head>
<body>
    <div class="admin-dashboard-container">
        <header class="admin-dashboard-header">
            <h1>Admin Dashboard</h1>
        </header>
        <nav class="admin-dashboard-nav">
            <ul>
                <li><a href="#manage-bookings">Manage Bookings</a></li>
                <li><a href="#manage-services">Manage Services</a></li>
                <li><a href="#therapist-schedule">Therapist Schedule</a></li>
                <li><a href="#payments-reports">Payments & Reports</a></li>
            </ul>
        </nav>
        <main class="admin-dashboard-main">
            <!-- Manage Bookings Section -->
            <section id="manage-bookings" class="admin-dashboard-section">
                <h2>Manage Bookings</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($booking = $bookingsResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $booking['booking_id']; ?></td>
                                <td><?php echo $booking['customer_name']; ?></td>
                                <td><?php echo $booking['service_name']; ?></td>
                                <td><?php echo $booking['status']; ?></td>
                                <td>
                                    <button>Approve</button>
                                    <button>Cancel</button>
                                    <button>Reschedule</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>

            <section id="manage-services" class="admin-dashboard-section">
                <h2>Manage Services</h2>
                <button onclick="openAddServicePopup()">Add New Service</button>
                <ul class="service-list">
                    <?php while ($service = $servicesResult->fetch_assoc()): ?>
                        <li>
                            <strong><?php echo $service['service_name']; ?></strong> - ₱<?php echo $service['price']; ?> (<?php echo $service['duration']; ?> mins)
                            <button>Edit</button>
                            <button>Delete</button>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </section>

            <section id="therapist-schedule" class="admin-dashboard-section">
                <h2>Therapist Schedule Management</h2>
                <div class="calendar">
                    <p>Calendar view goes here</p>
                </div>
                <button onclick="openAddAvailabilityPopup()">Add Therapist Availability</button>
            </section>

            <section id="payments-reports" class="admin-dashboard-section">
                <h2>Payments & Reports</h2>
                <div class="payments-table">
                    <h3>Payments</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Payment ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($payment = $paymentsResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $payment['payment_id']; ?></td>
                                    <td><?php echo $payment['customer_name']; ?></td>
                                    <td>₱<?php echo $payment['amount']; ?></td>
                                    <td><?php echo $payment['status']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <div class="reports-section">
                    <h3>Booking and Earnings Reports</h3>
                    <p>Data visualizations (charts) will go here</p>
                </div>
            </section>
        </main>
    </div>

    <div id="addServicePopup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closeAddServicePopup()">&times;</span>
            <h2>Add New Service</h2>
            <form action="addService.php" method="POST">
                <label for="service_name">Service Name:</label>
                <input type="text" name="service_name" required>

                <label for="service_description">Description:</label>
                <textarea name="service_description" required></textarea>

                <label for="price">Price (₱):</label>
                <input type="number" name="price" required>

                <label for="duration">Duration (mins):</label>
                <input type="number" name="duration" required>

                <button type="submit">Add Service</button>
            </form>
        </div>
    </div>

    <div id="addAvailabilityPopup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closeAddAvailabilityPopup()">&times;</span>
            <h2>Add Therapist Availability</h2>
            <form action="addAvailability.php" method="POST">
                <label for="therapist_id">Therapist ID:</label>
                <input type="text" name="therapist_id" required>

                <label for="availability_date">Availability Date:</label>
                <input type="date" name="availability_date" required>

                <label for="start_time">Start Time:</label>
                <input type="time" name="start_time" required>

                <label for="end_time">End Time:</label>
                <input type="time" name="end_time" required>

                <button type="submit">Add Availability</button>
            </form>
        </div>
    </div>

    <style>
        .popup {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .popup-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            width: 40%;
            max-width: 500px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>

    <script>
        function openAddServicePopup() {
            document.getElementById('addServicePopup').style.display = 'block';
        }

        function closeAddServicePopup() {
            document.getElementById('addServicePopup').style.display = 'none';
        }

        function openAddAvailabilityPopup() {
            document.getElementById('addAvailabilityPopup').style.display = 'block';
        }

        function closeAddAvailabilityPopup() {
            document.getElementById('addAvailabilityPopup').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('addServicePopup')) {
                closeAddServicePopup();
            }
            if (event.target == document.getElementById('addAvailabilityPopup')) {
                closeAddAvailabilityPopup();
            }
        }
    </script>
</body>
</html>
