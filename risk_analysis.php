<?php
require_once '../config/session.php';
require_once '../config/database.php';
require_once '../functions/report.php';
require_once '../functions/church.php';
requireLogin();

$conn = getDBConnection();

// Basic stats
$totalChurches = count(getAllChurches());
$totalReports = count(getAllReports());
$riskSummary = getRiskSummary();

// Municipality stats
$municipalityQuery = "
    SELECT municipality, COUNT(*) as total
    FROM Churches
    GROUP BY municipality
    ORDER BY total DESC
";
$municipalityResult = $conn->query($municipalityQuery);

// Report status stats
$statusQuery = "
    SELECT status, COUNT(*) as total
    FROM DamageReports
    GROUP BY status
";
$statusResult = $conn->query($statusQuery);

// High + Critical alerts
$alertQuery = "
    SELECT COUNT(*) as total
    FROM DamageReports
    WHERE risk_level IN (3,4) AND status='Approved'
";
$alertResult = $conn->query($alertQuery);
$alerts = $alertResult->fetch_assoc()['total'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Risk Analysis - SADIWA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container mt-4">
	
    <h2 class="mb-4">📊 Risk Analysis Dashboard</h2>
	
    <!-- 🔹 SUMMARY CARDS -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-primary text-center">
                <div class="card-body">
                    <h5>Total Churches</h5>
                    <h2><?php echo $totalChurches; ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-success text-center">
                <div class="card-body">
                    <h5>Total Reports</h5>
                    <h2><?php echo $totalReports; ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-danger text-center">
                <div class="card-body">
                    <h5>⚠️ High & Critical Alerts</h5>
                    <h2><?php echo $alerts; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- 🔹 RISK LEVEL SUMMARY -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Risk Level Distribution</h5>
        </div>
        <div class="card-body">

            <?php foreach ($riskSummary as $risk): 
                $percentage = ($totalChurches > 0) 
                    ? ($risk['church_count'] / $totalChurches * 100) 
                    : 0;
            ?>

            <div class="mb-3">
                <strong><?php echo $risk['level_name']; ?></strong>
                (<?php echo $risk['church_count']; ?> churches)

                <div class="progress">
                    <div class="progress-bar"
                        style="width: <?php echo $percentage; ?>%;
                               background-color: <?php echo $risk['color_code']; ?>">
                        <?php echo round($percentage, 1); ?>%
                    </div>
                </div>
            </div>
            
            

            <?php endforeach; ?>

        </div>
    </div>

    <!-- 🔹 MUNICIPALITY STATS -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Churches per Municipality</h5>
        </div>
        <div class="card-body">

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Municipality</th>
                        <th>Total Churches</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $municipalityResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['municipality']; ?></td>
                        <td><?php echo $row['total']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        </div>
    </div>

    <!-- 🔹 REPORT STATUS -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Report Status Overview</h5>
        </div>
        <div class="card-body">

            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $statusResult->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <span class="badge bg-<?php 
                                echo $row['status'] == 'Approved' ? 'success' :
                                     ($row['status'] == 'Pending' ? 'warning' : 'danger'); ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                        <td><?php echo $row['total']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>