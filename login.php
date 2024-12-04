<?php
require 'database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input fields
    if (empty($_POST['email']) || empty($_POST['password'])) {
        $error = "Please fill in all fields.";
    } else {
        $email = $conn->real_escape_string($_POST['email']);
        $password = $_POST['password'];

        // Check if the user exists
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = $conn->query($query);

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];

                // Redirect to dashboard
                header("Location: userDashboard.php");
                exit();
            } else {
                $error = "Invalid password. Please try again.";
            }
        } else {
            $error = "No account found with this email.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex items-center justify-center min-h-screen bg-gradient-to-r from-green-50 via-blue-50 to-white">
        <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-8">
            <h1 class="text-3xl font-semibold text-center text-green-600 mb-8">Log In</h1>

            <?php if (isset($error)): ?>
                <p class="text-red-500 text-center mb-4"><?php echo $error; ?></p>
            <?php endif; ?>

            <form action="login.php" method="POST" class="space-y-6">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" 
                           placeholder="Enter your email" required>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" 
                           placeholder="Enter your password" required>
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" class="bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition duration-300">Log In</button>
                </div>
            </form>

            <p class="text-sm text-center mt-6">
                Don't have an account? 
                <a href="signup.php" class="text-green-500 hover:underline">Sign Up</a>
            </p>
        </div>
    </div>

    <script>
        document.querySelector("form").addEventListener("submit", function(event) {
            var email = document.getElementById("email").value;
            var password = document.getElementById("password").value;

            if (!email || !password) {
                event.preventDefault();  // Prevent form submission
                alert("Please fill in all fields.");
            }
        });
    </script>
</body>
</html>

