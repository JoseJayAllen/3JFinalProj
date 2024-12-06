<?php
session_start();
include('database.php');

// Check if user_id is set in the session
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit();
}

// Get the logged-in user's ID from the session
$user_id = $_SESSION['user_id'];

// Sanitize the user_id to prevent SQL injection
$user_id = (int) $user_id;  // Cast to integer to ensure it is safe

// Query for user information using prepared statement to avoid SQL injection
$query = $conn->prepare("SELECT full_name, email, phone_number FROM users WHERE user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

// Check if user data is found
if ($user) {
    $username = $user['full_name'];
    $email = $user['email'];
    $phone_number = $user['phone_number'];
} else {
    // Redirect to login page if user data is not found
    header("Location: login.php");
    exit();
}

// Query for upcoming appointments
$upcomingAppointmentsQuery = $conn->prepare("SELECT * FROM appointments WHERE user_id = ? AND appointment_date >= CURDATE() ORDER BY appointment_date ASC");
$upcomingAppointmentsQuery->bind_param("i", $user_id);
$upcomingAppointmentsQuery->execute();
$upcomingAppointmentsResult = $upcomingAppointmentsQuery->get_result();

// Query for past appointments
$pastAppointmentsQuery = $conn->prepare("SELECT * FROM appointments WHERE user_id = ? AND appointment_date < CURDATE() ORDER BY appointment_date DESC");
$pastAppointmentsQuery->bind_param("i", $user_id);
$pastAppointmentsQuery->execute();
$pastAppointmentsResult = $pastAppointmentsQuery->get_result();

// Handle review submission
if (isset($_POST['submit_review'])) {
    $appointment_id = $_POST['appointment_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    // Insert the review into the database using prepared statements
    $reviewQuery = $conn->prepare("INSERT INTO reviews (appointment_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
    $reviewQuery->bind_param("iiis", $appointment_id, $user_id, $rating, $comment);
    
    if ($reviewQuery->execute()) {
        echo "Review submitted successfully!";
    } else {
        echo "Error: " . $reviewQuery->error;
    }
}
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
                            
                            <!-- Add the review form -->
                            <?php if (empty($appointment['review_id'])): ?>
                                <form action="userDashboard.php" method="POST">
                                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['appointment_id']; ?>">
                                    <label for="rating_<?php echo $appointment['appointment_id']; ?>">Rating:</label>
                                    <input type="number" id="rating_<?php echo $appointment['appointment_id']; ?>" name="rating" min="1" max="5" required>
                                    <label for="comment_<?php echo $appointment['appointment_id']; ?>">Comment:</label>
                                    <textarea id="comment_<?php echo $appointment['appointment_id']; ?>" name="comment" required></textarea>
                                    <button type="submit" name="submit_review">Submit Review</button>
                                </form>
                            <?php else: ?>
                                <p>You already submitted a review for this appointment.</p>
                            <?php endif; ?>
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

    <script>
    function openReviewPopup(appointmentId) {
        document.getElementById('appointment_id').value = appointmentId;
        document.getElementById('reviewPopup').style.display = 'block';
    }

    function closeReviewPopup() {
        document.getElementById('reviewPopup').style.display = 'none';
    }
    </script>

</body>
</html>

