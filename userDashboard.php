<?php
session_start();
include('database.php');

$user_id = $_SESSION['user_id']; 

$query = "SELECT full_name, email, phone_number FROM users WHERE user_id = $user_id";
$result = $conn->query($query);
$user = $result->fetch_assoc();
$username = $user['full_name'];
$email = $user['email'];
$phone_number = $user['phone_number'];

$upcomingAppointmentsQuery = "SELECT * FROM appointments WHERE user_id = $user_id AND appointment_date >= CURDATE() ORDER BY appointment_date ASC";
$upcomingAppointmentsResult = $conn->query($upcomingAppointmentsQuery);

$pastAppointmentsQuery = "SELECT * FROM appointments WHERE user_id = $user_id AND appointment_date < CURDATE() ORDER BY appointment_date DESC";
$pastAppointmentsResult = $conn->query($pastAppointmentsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
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
                <?php while ($appointment = $upcomingAppointmentsResult->fetch_assoc()): ?>
                    <div class="appointment-card">
                        <p><strong>Date:</strong> <?php echo $appointment['appointment_date']; ?></p>
                        <p><strong>Time:</strong> <?php echo $appointment['start_time']; ?> - <?php echo $appointment['end_time']; ?></p>
                        <p><strong>Therapist:</strong> <?php echo $appointment['therapist_id']; ?></p>
                        <button>Cancel</button>
                        <button>Reschedule</button>
                    </div>
                <?php endwhile; ?>
            </section>

            <section id="past-appointments" class="dashboard-section">
                <h2>Past Appointments</h2>
                <?php while ($appointment = $pastAppointmentsResult->fetch_assoc()): ?>
                    <div class="appointment-card">
                        <p><strong>Date:</strong> <?php echo $appointment['appointment_date']; ?></p>
                        <p><strong>Therapist:</strong> <?php echo $appointment['therapist_id']; ?></p>
                        <button>Leave a Review</button>
                    </div>
                <?php endwhile; ?>
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
        .popup {
            display: none; 
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0); 
            background-color: rgba(0,0,0,0.4); 
        }

        .popup-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
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

        button {
            padding: 10px;
            margin: 10px 0;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>

    <script>
        function openProfilePopup() {
            document.getElementById("profilePopup").style.display = "block";
        }

        function closeProfilePopup() {
            document.getElementById("profilePopup").style.display = "none";
        }

        function openPasswordPopup() {
            document.getElementById("passwordPopup").style.display = "block";
        }

        function closePasswordPopup() {
            document.getElementById("passwordPopup").style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById("profilePopup")) {
                closeProfilePopup();
            } else if (event.target == document.getElementById("passwordPopup")) {
                closePasswordPopup();
            }
        }
    </script>
</body>
</html>
