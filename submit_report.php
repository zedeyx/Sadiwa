<?php
require_once '../config/session.php';
require_once '../functions/church.php';
require_once '../functions/report.php';
requireRole('Contributor');

$churches = getAllChurches();
$riskLevels = getRiskLevels();
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $churchId = $_POST['church_id'] ?? '';
    $reportDate = $_POST['report_date'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $riskLevel = $_POST['risk_level'] ?? '';
    
    // Validation
    if (empty($churchId) || empty($reportDate) || empty($description) || empty($riskLevel)) {
        $error = 'Please fill in all required fields.';
    } else {
        // Handle file upload
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/';
            $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
            $targetPath = $uploadDir . $fileName;
            
            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (in_array($_FILES['image']['type'], $allowedTypes)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $imagePath = $fileName;
                }
            } else {
                $error = 'Only JPG and PNG images are allowed.';
            }
        }
        
        if (!$error) {
            if (addReport($churchId, $_SESSION['user_id'], $reportDate, $description, $riskLevel, $imagePath)) {
                $success = 'Report submitted successfully! It will be reviewed by an administrator.';
            } else {
                $error = 'Failed to submit report. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Report - SADIWA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <h2 class="mb-4">Submit Damage Report</h2>
        
        <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($success); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="church_id" class="form-label">Church *</label>
                        <select class="form-select" id="church_id" name="church_id" required>
                            <option value="">Select a church...</option>
                            <?php foreach ($churches as $church): ?>
                            <option value="<?php echo $church['church_id']; ?>">
                                <?php echo htmlspecialchars($church['name']) . ' - ' . htmlspecialchars($church['municipality']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="report_date" class="form-label">Report Date *</label>
                        <input type="date" class="form-control" id="report_date" name="report_date" 
                               value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="risk_level" class="form-label">Risk Level *</label>
                        <select class="form-select" id="risk_level" name="risk_level" required>
                            <option value="">Select risk level...</option>
                            <?php foreach ($riskLevels as $level): ?>
                            <option value="<?php echo $level['risk_id']; ?>">
                                <?php echo htmlspecialchars($level['level_name']); ?> - <?php echo htmlspecialchars($level['description']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Damage Description *</label>
                        <textarea class="form-control" id="description" name="description" rows="5" 
                                  placeholder="Describe the damage observed..." required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Upload Image (Optional)</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/jpeg,image/png,image/jpg">
                        <small class="text-muted">Accepted formats: JPG, PNG (Max 5MB)</small>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Submit Report</button>
                        <a href="reports.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>