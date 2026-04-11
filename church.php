<?php
require_once '../config/database.php';

// Get all churches
function getAllChurches() {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM Churches ORDER BY name");
    $stmt->execute();
    $result = $stmt->get_result();
    $churches = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
    return $churches;
}

// Get church by ID
function getChurchById($id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM Churches WHERE church_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $church = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $church;
}

// Add new church
function addChurch($name, $municipality, $latitude, $longitude, $year_built, $architectural_style) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("INSERT INTO Churches (name, municipality, latitude, longitude, year_built, architectural_style) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssddis", $name, $municipality, $latitude, $longitude, $year_built, $architectural_style);
    $success = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $success;
}

// Update church
function updateChurch($id, $name, $municipality, $latitude, $longitude, $year_built, $architectural_style) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("UPDATE Churches SET name = ?, municipality = ?, latitude = ?, longitude = ?, year_built = ?, architectural_style = ? WHERE church_id = ?");
    $stmt->bind_param("ssddisi", $name, $municipality, $latitude, $longitude, $year_built, $architectural_style, $id);
    $success = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $success;
}

// Delete church
function deleteChurch($id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("DELETE FROM Churches WHERE church_id = ?");
    $stmt->bind_param("i", $id);
    $success = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $success;
}

// Get churches with risk data for map
function getChurchesWithRisk() {
    $conn = getDBConnection();

    $query = "
        SELECT 
            c.*,
            dr.risk_level,
            rl.level_name,
            rl.color_code,
            dr.report_date
        FROM Churches c
        LEFT JOIN DamageReports dr 
            ON dr.report_id = (
                SELECT dr2.report_id
                FROM DamageReports dr2
                WHERE dr2.church_id = c.church_id
                AND dr2.status = 'Approved'
                ORDER BY dr2.report_date DESC, dr2.report_id DESC
                LIMIT 1
            )
        LEFT JOIN RiskLevels rl ON dr.risk_level = rl.risk_id
        ORDER BY c.name
    ";

    $result = $conn->query($query);
    $churches = $result->fetch_all(MYSQLI_ASSOC);
    $conn->close();

    return $churches;
}
?>