<?php
// Function to validate email address format
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to validate password strength
function validatePassword($password) {
    // Add your custom password validation rules here
    // Example: Check minimum length, presence of special characters, etc.
    return (strlen($password) >= 8);
}

// Function to check if email already exists in users.json
function isEmailExists($email) {
    $users = getUsersFromJson();
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            return true;
        }
    }
    return false;
}

// Function to get users array from users.json
function getUsersFromJson() {
    $usersData = file_get_contents('users.json');
    return json_decode($usersData, true);
}

// Function to save users array to users.json
function saveUsersToJson($users) {
    $usersData = json_encode($users);
    file_put_contents('users.json', $usersData);
}

// Initialize variables
$nameErr = $emailErr = $passwordErr = $confirmPasswordErr = '';
$name = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission

    // Retrieve form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Perform form validation
    if (empty($name)) {
        $nameErr = 'Name is required';
    }

    if (empty($email)) {
        $emailErr = 'Email is required';
    } elseif (!validateEmail($email)) {
        $emailErr = 'Invalid email format';
    } elseif (isEmailExists($email)) {
        $emailErr = 'Email is already registered';
    }

    if (empty($password)) {
        $passwordErr = 'Password is required';
    } elseif (!validatePassword($password)) {
        $passwordErr = 'Password must be at least 8 characters long';
    }

    if ($password !== $confirmPassword) {
        $confirmPasswordErr = 'Passwords do not match';
    }

    // If there are no errors, proceed with registration
    if (empty($nameErr) && empty($emailErr) && empty($passwordErr) && empty($confirmPasswordErr)) {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Create user array
        $user = [
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword
        ];

        // Get existing users and add the new user
        $users = getUsersFromJson();
        $users[] = $user;

        // Save updated users array to users.json
        saveUsersToJson($users);

        // Display success message
        $successMessage = 'Registration successful!';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
</head>
<body>
    <h2>User Registration</h2>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <label for="name">Name:</label>
        <input type="text" name="name" value="<?php echo $name; ?>">
        <span style="color: red;"><?php echo $nameErr; ?></span>
        <br><br>
        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo $email; ?>">
        <span style="color: red;"><?php echo $emailErr; ?></span>
        <br><br>
        <label for="password">Password:</label>
        <input type="password" name="password">
        <span style="color: red;"><?php echo $passwordErr; ?></span>
        <br><br>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" name="confirm_password">
        <span style="color: red;"><?php echo $confirmPasswordErr; ?></span>
        <br><br>
        <input type="submit" value="Register">
    </form>
    <?php if (!empty($successMessage)): ?>
        <div style="color: green;"><?php echo $successMessage; ?></div>
    <?php endif; ?>
</body>
</html>
