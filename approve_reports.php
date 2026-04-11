<?php
require_once '../config/session.php';
require_once '../functions/report.php';
requireRole('Admin');

$reports = getAllReports();
$success = '';
$error = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reportId = $_POST['report_id'] ?? '';
    $status = $_POST['status'] ?? '';
    
    if ($reportId && in_array($status, ['Approved', 'Rejected', 'Pending'])) {
        if (updateReportStatus($reportId, $status)) {
            $success = 'Report status updated successfully!';
            $reports = getAllReports();
        } else {
            $error = 'Failed to update report status.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Reports - SADIWA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <h2 class="mb-4">Approve/Reject Damage Reports</h2>
        
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
                <div class="table-responsive">
                    <table id="reportsTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Church</th>
                                <th>Contributor</th>
                                <th>Risk Level</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reports as $report): ?>
                            <tr>
                                <td><?php echo $report['report_id']; ?></td>
                                <td><?php echo htmlspecialchars($report['report_date']); ?></td>
                                <td><?php echo htmlspecialchars($report['church_name']); ?></td>
                                <td><?php echo htmlspecialchars($report['contributor_name']); ?></td>
                                <td>
                                    <span class="badge" style="background-color: <?php echo htmlspecialchars($report['color_code']); ?>">
                                        <?php echo htmlspecialchars($report['risk_name']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars(substr($report['damage_description'], 0, 50)) . '...'; ?></td>
                                <td>
                                    <?php 
                                    $statusClass = $report['status'] === 'Approved' ? 'success' : ($report['status'] === 'Pending' ? 'warning' : 'danger');
                                    ?>
                                    <span class="badge bg-<?php echo $statusClass; ?>"><?php echo htmlspecialchars($report['status']); ?></span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="report_id" value="<?php echo $report['report_id']; ?>">
                                            <input type="hidden" name="status" value="Approved">
                                            <button type="submit" class="btn btn-sm btn-success" 
                                                    <?php echo $report['status'] === 'Approved' ? 'disabled' : ''; ?>>
                                                Approve
                                            </button>
                                        </form>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="report_id" value="<?php echo $report['report_id']; ?>">
                                            <input type="hidden" name="status" value="Rejected">
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    <?php echo $report['status'] === 'Rejected' ? 'disabled' : ''; ?>>
                                                Reject
                                            </button>
                                        </form>
                                    </div>
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
            $('#reportsTable').DataTable({
                pageLength: 10,
                order: [[1, 'desc']]
            });
        });
    </script>
</body>
</html>