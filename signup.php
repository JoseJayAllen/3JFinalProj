<?php
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone_number = $conn->real_escape_string($_POST['phone_number']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $conn->real_escape_string($_POST['role']);  // Get role from form input

    $check_email_query = "SELECT * FROM users WHERE email = '$email'";
    $email_result = $conn->query($check_email_query);

    if ($email_result->num_rows > 0) {
        $error = "An account with this email already exists.";
    } else {
        
        $insert_query = "INSERT INTO users (full_name, email, phone_number, password, role) 
                         VALUES ('$full_name', '$email', '$phone_number', '$password', '$role')";

        if ($conn->query($insert_query)) {
            header("Location: login.php"); 
            exit();
        } else {
            $error = "Error creating account: " . $conn->error;
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
    <title>Sign Up</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex items-center justify-center h-screen">
        <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-6">
            <h1 class="text-2xl font-bold text-center mb-6">Create an Account</h1>

            <?php if (isset($error)): ?>
                <p class="text-red-500 text-center mb-4"><?php echo $error; ?></p>
            <?php endif; ?>

            <form action="signup.php" method="POST" class="space-y-4">
                
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" id="full_name" name="full_name" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" 
                           placeholder="Enter your full name" required>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" 
                           placeholder="Enter your email" required>
                </div>

                <div>
                    <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="text" id="phone_number" name="phone_number" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" 
                           placeholder="Enter your phone number" required>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" 
                           placeholder="Create a password" required>
                </div>

                <!-- Role Selection Dropdown -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Select Role</label>
                    <select id="role" name="role" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="customer">Customer</option>
                        <option value="therapist">Therapist</option>
                    </select>
                </div>

                <div class="text-center">
                    <button type="submit" 
                            class="bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600">
                        Sign Up
                    </button>
                </div>
            </form>

            <p class="text-sm text-center mt-4">
                Already have an account? 
                <a href="login.php" class="text-blue-500 hover:underline">Log In</a>
            </p>
        </div>
    </div>
</body>
</html>
