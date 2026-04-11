<?php
require_once '../config/session.php';
require_once '../functions/report.php';
requireLogin();

$reports = getAllReports();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - SADIWA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Damage Reports</h2>
            <?php if (hasRole('Contributor')): ?>
            <a href="submit_report.php" class="btn btn-primary">Submit New Report</a>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="reportsTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Church</th>
                                <th>Municipality</th>
                                <th>Description</th>
                                <th>Risk Level</th>
                                <th>Contributor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reports as $report): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($report['report_date']); ?></td>
                                <td>
                                    <a href="church_details.php?id=<?php echo $report['church_id']; ?>" class="church-link">
                                        <?php echo htmlspecialchars($report['church_name']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($report['church_name']); ?></td>
                                <td> <?php 
                                $desc = $report['damage_description'];
                                if (strlen($desc) > 50) {
                                    echo htmlspecialchars(substr($desc, 0, 50)) . '...';
                                } else {
                                    echo htmlspecialchars($desc);
                                }
                                ?></td>
                                <td>
                                    <span class="badge" style="background-color: <?php echo htmlspecialchars($report['color_code']); ?>">
                                        <?php echo htmlspecialchars($report['risk_name']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($report['contributor_name']); ?></td>
                                <td>
                                    <?php 
                                    $statusClass = $report['status'] === 'Approved' ? 'success' : ($report['status'] === 'Pending' ? 'warning' : 'danger');
                                    ?>
                                    <span class="badge bg-<?php echo $statusClass; ?>"><?php echo htmlspecialchars($report['status']); ?></span>
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
                order: [[0, 'desc']]
            });
        });
    </script>
</body>
</html>

