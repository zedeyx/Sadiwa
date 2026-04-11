<?php
require_once '../config/session.php';
require_once '../functions/church.php';
requireLogin();

$churches = getAllChurches();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Churches - SADIWA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Heritage Churches</h2>
            <?php if (hasRole('Admin')): ?>
            <a href="manage_churches.php" class="btn btn-primary">Manage Churches</a>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="churchesTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Municipality</th>
                                <th>Year Built</th>
                                <th>Architectural Style</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($churches as $church): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($church['name']); ?></td>
                                <td><?php echo htmlspecialchars($church['municipality']); ?></td>
                                <td><?php echo htmlspecialchars($church['year_built'] ?? 'Unknown'); ?></td>
                                <td><?php echo htmlspecialchars($church['architectural_style'] ?? 'Unknown'); ?></td>
                                <td>
                                    <a href="church_details.php?id=<?php echo $church['church_id']; ?>" class="btn btn-sm btn-info">View Details</a>
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
                pageLength: 10,
                order: [[0, 'asc']]
            });
        });
    </script>
</body>
</html>