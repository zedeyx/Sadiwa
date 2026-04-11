<?php
require_once '../config/session.php';
require_once '../functions/church.php';
requireRole('Admin');

$churches = getAllChurches();
$success = '';
$error = '';
$editChurch = null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $municipality = trim($_POST['municipality'] ?? '');
        $latitude = $_POST['latitude'] ?? '';
        $longitude = $_POST['longitude'] ?? '';
        $yearBuilt = $_POST['year_built'] ?? null;
        $style = trim($_POST['architectural_style'] ?? '');
        
        if (empty($name) || empty($municipality) || empty($latitude) || empty($longitude)) {
            $error = 'Please fill in all required fields.';
        } else {
            if (addChurch($name, $municipality, $latitude, $longitude, $yearBuilt, $style)) {
                $success = 'Church added successfully!';
                $churches = getAllChurches();
            } else {
                $error = 'Failed to add church.';
            }
        }
    } elseif ($action === 'edit') {
        $id = $_POST['church_id'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $municipality = trim($_POST['municipality'] ?? '');
        $latitude = $_POST['latitude'] ?? '';
        $longitude = $_POST['longitude'] ?? '';
        $yearBuilt = $_POST['year_built'] ?? null;
        $style = trim($_POST['architectural_style'] ?? '');
        
        if (empty($id) || empty($name) || empty($municipality) || empty($latitude) || empty($longitude)) {
            $error = 'Please fill in all required fields.';
        } else {
            if (updateChurch($id, $name, $municipality, $latitude, $longitude, $yearBuilt, $style)) {
                $success = 'Church updated successfully!';
                $churches = getAllChurches();
            } else {
                $error = 'Failed to update church.';
            }
        }
    } elseif ($action === 'delete') {
        $id = $_POST['church_id'] ?? '';
        if ($id) {
            if (deleteChurch($id)) {
                $success = 'Church deleted successfully!';
                $churches = getAllChurches();
            } else {
                $error = 'Failed to delete church.';
            }
        }
    }
}

// Handle edit request
if (isset($_GET['edit'])) {
    $editChurch = getChurchById($_GET['edit']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Churches - SADIWA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <h2 class="mb-4">Manage Churches</h2>
        
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
        
        <!-- Add/Edit Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><?php echo $editChurch ? 'Edit Church' : 'Add New Church'; ?></h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="<?php echo $editChurch ? 'edit' : 'add'; ?>">
                    <?php if ($editChurch): ?>
                    <input type="hidden" name="church_id" value="<?php echo $editChurch['church_id']; ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Church Name *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo $editChurch ? htmlspecialchars($editChurch['name']) : ''; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="municipality" class="form-label">Municipality *</label>
                            <input type="text" class="form-control" id="municipality" name="municipality" 
                                   value="<?php echo $editChurch ? htmlspecialchars($editChurch['municipality']) : ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="latitude" class="form-label">Latitude *</label>
                            <input type="number" step="0.00000001" class="form-control" id="latitude" name="latitude" 
                                   value="<?php echo $editChurch ? $editChurch['latitude'] : ''; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="longitude" class="form-label">Longitude *</label>
                            <input type="number" step="0.00000001" class="form-control" id="longitude" name="longitude" 
                                   value="<?php echo $editChurch ? $editChurch['longitude'] : ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="year_built" class="form-label">Year Built</label>
                            <input type="number" class="form-control" id="year_built" name="year_built" 
                                   value="<?php echo $editChurch ? $editChurch['year_built'] : ''; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="architectural_style" class="form-label">Architectural Style</label>
                            <input type="text" class="form-control" id="architectural_style" name="architectural_style" 
                                   value="<?php echo $editChurch ? htmlspecialchars($editChurch['architectural_style']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><?php echo $editChurch ? 'Update' : 'Add'; ?> Church</button>
                        <?php if ($editChurch): ?>
                        <a href="manage_churches.php" class="btn btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Churches List -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">All Churches</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="churchesTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Municipality</th>
                                <th>Year Built</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($churches as $church): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($church['name']); ?></td>
                                <td><?php echo htmlspecialchars($church['municipality']); ?></td>
                                <td><?php echo htmlspecialchars($church['year_built'] ?? 'Unknown'); ?></td>
                                <td>
                                    <a href="?edit=<?php echo $church['church_id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this church?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="church_id" value="<?php echo $church['church_id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#churchesTable').DataTable({
                pageLength: 10
            });
        });
    </script>
</body>
</html>