<?php
require_once '../config/session.php';
require_once '../functions/church.php';
require_once '../functions/report.php';
requireLogin();

$churchId = $_GET['id'] ?? null;
if (!$churchId) {
    header('Location: churches.php');
    exit();
}

$church = getChurchById($churchId);
if (!$church) {
    header('Location: churches.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // DELETE
    if (isset($_POST['delete_id'])) {
        $reportId = $_POST['delete_id'];

        $conn = getDBConnection();
        $stmt = $conn->prepare("DELETE FROM DamageReports WHERE report_id = ? AND contributor_id = ?");
        $stmt->bind_param("ii", $reportId, $_SESSION['user_id']);
        $stmt->execute();

        header("Location: church_details.php?id=" . $churchId);
        exit();
    }

    // EDIT
    if (isset($_POST['edit_id'])) {
        $reportId = $_POST['edit_id'];
        $date = $_POST['edit_date'];
        $desc = $_POST['edit_description'];
        $risk = $_POST['edit_risk'];

        $imagePath = null;

        // IMAGE REPLACE
        if (!empty($_FILES['edit_image']['name'])) {
            $fileName = uniqid() . '_' . $_FILES['edit_image']['name'];
            move_uploaded_file($_FILES['edit_image']['tmp_name'], "../uploads/" . $fileName);
            $imagePath = $fileName;
        }

        $conn = getDBConnection();

        if ($imagePath) {
            $stmt = $conn->prepare("
                UPDATE DamageReports 
                SET report_date=?, damage_description=?, risk_level=?, image_path=? 
                WHERE report_id=? AND contributor_id=?
            ");
            $stmt->bind_param("ssisii", $date, $desc, $risk, $imagePath, $reportId, $_SESSION['user_id']);
        } else {
            $stmt = $conn->prepare("
                UPDATE DamageReports 
                SET report_date=?, damage_description=?, risk_level=? 
                WHERE report_id=? AND contributor_id=?
            ");
            $stmt->bind_param("ssiii", $date, $desc, $risk, $reportId, $_SESSION['user_id']);
        }

        $stmt->execute();

        header("Location: church_details.php?id=" . $churchId);
        exit();
    }
}
$reports = getReportsByChurch($churchId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($church['name']); ?> - SADIWA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        #miniMap { height: 300px; }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-4">
       
        
        <h2 class="mb-4"><?php echo htmlspecialchars($church['name']); ?></h2>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Church Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Municipality:</th>
                                <td><?php echo htmlspecialchars($church['municipality']); ?></td>
                            </tr>
                            <tr>
                                <th>Year Built:</th>
                                <td><?php echo htmlspecialchars($church['year_built'] ?? 'Unknown'); ?></td>
                            </tr>
                            <tr>
                                <th>Architectural Style:</th>
                                <td><?php echo htmlspecialchars($church['architectural_style'] ?? 'Unknown'); ?></td>
                            </tr>
                            <tr>
                                <th>Coordinates:</th>
                                <td><?php echo htmlspecialchars($church['latitude']); ?>, <?php echo htmlspecialchars($church['longitude']); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Location</h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="miniMap"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Damage Reports</h5>
            </div>
            <div class="card-body">
                <?php if (count($reports) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Image</th>
                                <th>Risk Level</th>
                                <th>Contributor</th>
                                <th>Status</th>
                            	<th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reports as $report): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($report['report_date']); ?></td>
                                <td><?php echo htmlspecialchars($report['damage_description']); ?></td>
                                <td>
                                    <?php if (!empty($report['image_path'])): ?>
                                        <span class="badge" 
                                            style="background-color: #0dcaf0; cursor: pointer;"
                                            onclick="viewImage('../uploads/<?php echo $report['image_path']; ?>')">
                                            View Image
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">No Image</span>
                                    <?php endif; ?>
                                </td>
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
                         		<td>
<?php if ($report['contributor_id'] == $_SESSION['user_id']): ?>

    <!-- EDIT BUTTON -->
    <button class="badge bg-warning text-dark"
        style="cursor:pointer;"
        onclick="openEditModal(
            '<?php echo $report['report_id']; ?>',
            '<?php echo $report['report_date']; ?>',
            '<?php echo htmlspecialchars($report['damage_description']); ?>',
            '<?php echo $report['risk_level']; ?>'
        )">
        Edit
    </button>

    <!-- DELETE BUTTON -->
    <form method="POST" style="display:inline;" 
          onsubmit="return confirm('Delete this report?');">
        <input type="hidden" name="delete_id" value="<?php echo $report['report_id']; ?>">
        <button type="submit" class="badge bg-danger border-0">
            Delete
        </button>
    </form>

<?php else: ?>
    <span class="text-muted">—</span>
<?php endif; ?>
</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted">No damage reports found for this church.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var map = L.map('miniMap').setView([<?php echo $church['latitude']; ?>, <?php echo $church['longitude']; ?>], 14);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        L.marker([<?php echo $church['latitude']; ?>, <?php echo $church['longitude']; ?>])
            .bindPopup('<strong><?php echo htmlspecialchars($church['name']); ?></strong>')
            .addTo(map);
    </script>
	<script>
function openEditModal(id, date, desc, risk) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_date').value = date;
    document.getElementById('edit_description').value = desc;
    document.getElementById('edit_risk').value = risk;

    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>

    <script>
    function viewImage(imageSrc) {
        document.getElementById('modalImage').src = imageSrc;
        var modal = new bootstrap.Modal(document.getElementById('imageModal'));
        modal.show();
    }
    </script>
        <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Damage Image</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center">
            <img id="modalImage" src="" class="img-fluid rounded">
        </div>
        </div>
    </div>
    </div>

<div class="modal fade" id="editModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" enctype="multipart/form-data">
        <div class="modal-header">
          <h5>Edit Report</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <input type="hidden" name="edit_id" id="edit_id">

          <label>Date</label>
          <input type="date" name="edit_date" id="edit_date" class="form-control mb-2">

          <label>Description</label>
          <textarea name="edit_description" id="edit_description" class="form-control mb-2"></textarea>

          <label>Risk Level</label>
          <select name="edit_risk" id="edit_risk" class="form-control mb-2">
              <?php foreach (getRiskLevels() as $level): ?>
                  <option value="<?php echo $level['risk_id']; ?>">
                      <?php echo $level['level_name']; ?>
                  </option>
              <?php endforeach; ?>
          </select>

          <label>Replace Image</label>
          <input type="file" name="edit_image" class="form-control">

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>