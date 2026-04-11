<?php
require_once '../config/session.php';
require_once '../config/database.php';

requireLogin();

$conn = getDBConnection();

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

$success = '';
$error = '';


$stmt = $conn->prepare("SELECT password_hash FROM Users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$currentHash = $userData['password_hash'];
$stmt->close();

if (isset($_POST['update'])) {

    $newUsername = trim($_POST['username']);
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmNewPassword = $_POST['confirm_new_password'];

    $error = '';

    // STEP 1: BASIC VALIDATION
    if (empty($newUsername)) {
        $error = "Username cannot be empty.";
    }

    if (empty($currentPassword)) {
        $error = "Please enter your current password.";
    }

    if (!empty($currentPassword) && !password_verify($currentPassword, $currentHash)) {
        $error = "Invalid current password.";
    }

    // STEP 2: PASSWORD VALIDATION (NO NUMBER REQUIRED)
    if (empty($error) && !empty($newPassword)) {

        if ($newPassword !== $confirmNewPassword) {
            $error = "New passwords do not match.";
        }

        if (strlen($newPassword) < 8) {
            $error = "Password must be at least 8 characters.";
        }

        if (!preg_match('/[A-Z]/', $newPassword)) {
            $error = "Password must include at least one uppercase letter.";
        }

        if (password_verify($newPassword, $currentHash)) {
            $error = "New password must be different from current password.";
        }
    }

    // STEP 3: UPDATE ONLY IF NO ERROR
    if (empty($error)) {

        $stmt = $conn->prepare("UPDATE Users SET username = ? WHERE user_id = ?");
        $stmt->bind_param("si", $newUsername, $userId);

        if ($stmt->execute()) {

            $_SESSION['username'] = $newUsername;

            if (!empty($newPassword)) {
                $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

                $stmt2 = $conn->prepare("UPDATE Users SET password_hash = ? WHERE user_id = ?");
                $stmt2->bind_param("si", $hashed, $userId);
                $stmt2->execute();
                $stmt2->close();
            }

            $success = "Profile updated successfully!";
        } else {
            $error = "Update failed.";
        }

        $stmt->close();
    }
}


if (isset($_POST['delete'])) {

    $currentPassword = $_POST['current_password_delete'];

    if (empty($currentPassword)) {
        $error = "Please enter your password.";
    }
    elseif (!password_verify($currentPassword, $currentHash)) {
        $error = "Invalid password.";
    }
    else {

        $stmt = $conn->prepare("DELETE FROM Users WHERE user_id = ?");
        $stmt->bind_param("i", $userId);

        if ($stmt->execute()) {

            session_unset();
            session_destroy();

            header("Location: login.php");
            exit();
        } else {
            $error = "Failed to delete account.";
        }

        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Profile - SADIWA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<?php include 'includes/navbar.php'; ?>

<div class="container mt-5">

    <div class="row justify-content-center">
        <div class="col-md-6">

            <!-- PROFILE CARD -->
            <div class="card shadow-lg border-0">

                <div class="card-body p-4">

                    <h3 class="text-center mb-4">My Profile</h3>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <!-- USER INFO DISPLAY -->
                    <div class="text-center mb-4">

                        <h5 class="mb-1 text-info">
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </h5>

                        <?php
                        $role = $_SESSION['role'];
                        $roleColor = 'secondary';

                        if ($role === 'Admin') $roleColor = 'danger';
                        elseif ($role === 'Contributor') $roleColor = 'warning';
                        elseif ($role === 'Viewer') $roleColor = 'info';
                        ?>

                        <span class="badge bg-<?php echo $roleColor; ?>">
                            <?php echo htmlspecialchars($role); ?>
                        </span>

                    </div>

                    <!-- UPDATE FORM -->
                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label">New Username</label>
                            <input type="text" name="username" class="form-control" 
                                   value="<?php echo htmlspecialchars($_SESSION['username']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Current Password *</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">New Password (optional)</label>
                            <input type="password" name="new_password" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="confirm_new_password" class="form-control">
                        </div>
	
                        <button name="update" class="btn btn-primary w-100">
                            Update Profile
                        </button>

                    </form>

                </div>
            </div>

            <!-- DELETE SECTION -->
            <div class="card mt-4 border-danger shadow-sm">

                <div class="card-body">

                    <h5 class="text-danger">Danger Zone</h5>
                    <p class="text-muted small">
                        Deleting your account is permanent and cannot be undone.
                    </p>

                    <form method="POST" onsubmit="return confirm('Are you sure you want to delete your account?');">

                        <div class="mb-3">
                            <input type="password" name="current_password_delete" 
                                   class="form-control" placeholder="Enter password to confirm" required>
                        </div>

                        <button name="delete" class="btn btn-danger w-100">
                            Delete Account
                        </button>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

