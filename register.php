<?php
require_once '../config/database.php';
require_once '../config/session.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'Viewer';

    // STEP 1: Validation
    if (empty($username) || empty($password) || empty($confirmPassword)) {
        $error = 'All fields are required.';
    }
    elseif ($password !== $confirmPassword) {
    $error = 'Passwords do not match.';
    }
    elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    }
    elseif (!preg_match('/[A-Z]/', $password)) {
        $error = 'Password must include at least one uppercase letter.';
    }
    else {

        // STEP 2: Secure role (VERY IMPORTANT)
        if (!in_array($role, ['Viewer', 'Contributor'])) {
            $role = 'Viewer';
        }

        $conn = getDBConnection();

        // STEP 3: Check duplicate username
        $stmt = $conn->prepare("SELECT user_id FROM Users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = 'Username already exists.';
        } 
        else {
            // STEP 4: Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // STEP 5: Insert user
            $stmt = $conn->prepare("INSERT INTO Users (username, password_hash, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashedPassword, $role);

            if ($stmt->execute()) {
                // SUCCESS → redirect to login
                header("Location: login.php");
                exit();
            } else {
                $error = 'Registration failed.';
            }
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register - SADIWA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">

        <div class="col-md-5">
            <div class="card p-4 shadow">

                <h3 class="text-center mb-4">Sign Up</h3>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">

                    <input type="text" name="username" class="form-control mb-3" placeholder="Username" required>

                    <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>

                    <input type="password" name="confirm_password" class="form-control mb-3" placeholder="Confirm Password" required>

                    <!-- ROLE SELECT -->
                    <div class="mb-3">
                        <label>Select Role</label>
                        <select name="role" class="form-control" required>
                            <option value="Viewer">Viewer (View only)</option>
                            <option value="Contributor">Contributor (Can submit reports)</option>
                        </select>
                    </div>

                    <button class="btn btn-primary w-100">Register</button>

                </form>

                <div class="text-center mt-3">
                    <a href="login.php">Already have an account? Login</a>
                </div>

            </div>
        </div>

    </div>
</div>

</body>
</html>