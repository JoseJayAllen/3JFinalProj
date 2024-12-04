<?php
session_start();
include('database.php');

// Get the logged-in user's ID from the session
$user_id = $_SESSION['user_id']; 

// Query for user information
$query = "SELECT full_name, email, phone_number FROM users WHERE user_id = $user_id";
$result = $conn->query($query);
$user = $result->fetch_assoc();
$username = $user['full_name'];
$email = $user['email'];
$phone_number = $user['phone_number'];

// Query for upcoming appointments
$upcomingAppointmentsQuery = "SELECT * FROM appointments WHERE user_id = $user_id AND appointment_date >= CURDATE() ORDER BY appointment_date ASC";
$upcomingAppointmentsResult = $conn->query($upcomingAppointmentsQuery);

// Query for past appointments
$pastAppointmentsQuery = "SELECT * FROM appointments WHERE user_id = $user_id AND appointment_date < CURDATE() ORDER BY appointment_date DESC";
$pastAppointmentsResult = $conn->query($pastAppointmentsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="userDashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h1>Welcome, <?php echo htmlspecialchars($username); ?></h1>
            <button onclick="window.location.href='index.php';">Logout</button>
        </header>

        <nav class="dashboard-nav">
            <ul>
                <li><a href="#upcoming-appointments">Upcoming Appointments</a></li>
                <li><a href="#past-appointments">Past Appointments</a></li>
                <li><a href="#account-settings">Account Settings</a></li>
                <li><a href="#promotions">Promotions & Rewards</a></li>
            </ul>
        </nav>

        <main class="dashboard-main">
            <section id="upcoming-appointments" class="dashboard-section">
                <h2>Upcoming Appointments</h2>
                <div class="appointments-container">
                    <?php if ($upcomingAppointmentsResult->num_rows > 0): ?>
                        <?php while ($appointment = $upcomingAppointmentsResult->fetch_assoc()): ?>
                            <div class="appointment-card">
                                <p><strong>Date:</strong> <?php echo $appointment['appointment_date']; ?></p>
                                <p><strong>Time:</strong> <?php echo $appointment['start_time']; ?> - <?php echo $appointment['end_time']; ?></p>
                                <p><strong>Therapist:</strong> <?php echo $appointment['therapist_id']; ?></p>
                                <button>Cancel</button>
                                <button>Reschedule</button>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No upcoming appointments.</p>
                    <?php endif; ?>
                </div>
            </section>

            <section id="past-appointments" class="dashboard-section">
                <h2>Past Appointments</h2>
                <?php if ($pastAppointmentsResult->num_rows > 0): ?>
                    <?php while ($appointment = $pastAppointmentsResult->fetch_assoc()): ?>
                        <div class="appointment-card">
                            <p><strong>Date:</strong> <?php echo $appointment['appointment_date']; ?></p>
                            <p><strong>Therapist:</strong> <?php echo $appointment['therapist_id']; ?></p>
                            <button>Leave a Review</button>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No past appointments.</p>
                <?php endif; ?>
            </section>

            <section id="account-settings" class="dashboard-section">
                <h2>Account Settings</h2>
                <div class="settings-item">
                    <h3>Profile</h3>
                    <button onclick="openProfilePopup()">Edit Profile</button>
                </div>

                <div class="settings-item">
                    <h3>Change Password</h3>
                    <button onclick="openPasswordPopup()">Change Password</button>
                </div>

                <div id="profilePopup" class="popup">
                    <div class="popup-content">
                        <span class="close" onclick="closeProfilePopup()">&times;</span>
                        <h2>Edit Profile</h2>
                        <form action="updateProfile.php" method="POST">
                            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
                            <label for="full_name">Full Name:</label>
                            <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                            
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            
                            <label for="phone_number">Phone Number:</label>
                            <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
                            
                            <button type="submit">Save Changes</button>
                        </form>
                    </div>
                </div>

                <div id="passwordPopup" class="popup">
                    <div class="popup-content">
                        <span class="close" onclick="closePasswordPopup()">&times;</span>
                        <h2>Change Password</h2>
                        <form action="updatePassword.php" method="POST">
                            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
                            <label for="current_password">Current Password:</label>
                            <input type="password" id="current_password" name="current_password" required>

                            <label for="new_password">New Password:</label>
                            <input type="password" id="new_password" name="new_password" required>

                            <label for="confirm_password">Confirm New Password:</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>

                            <button type="submit">Change Password</button>
                        </form>
                    </div>
                </div>
            </section>

            <section id="promotions" class="dashboard-section">
                <h2>Promotions & Rewards</h2>
                <p>No promotions available at this time. Check back later!</p>
            </section>
        </main>
    </div>

    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: auto;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .dashboard-header {
            background-color: #4CAF50; /* Green for wellness theme */
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 15px 15px 0 0;
        }

        .dashboard-nav {
            background-color: #f0f5f4; /* Light greenish background */
            border-bottom: 1px solid #ddd;
        }

        .dashboard-nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: space-around;
        }

        .dashboard-nav li a {
            text-decoration: none;
            padding: 15px 20px;
            display: block;
            color: #333;
            font-weight: 600;
        }

        .dashboard-nav li a:hover {
            background-color: #6aa84f; /* Lighter green on hover */
            color: white;
        }

        .dashboard-main {
            padding: 20px;
        }

        .dashboard-section {
            margin-bottom: 40px;
        }

        .dashboard-section h2 {
            margin-bottom: 20px;
            color: #4CAF50;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .appointment-card {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .appointment-card p {
            margin: 5px 0;
        }

        button {
            background-color: #4CAF50; /* Green */
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .popup {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .popup-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            border-radius: 8px;
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
</body>
</html>

