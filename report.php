<?php
require_once '../config/database.php';

// Get all damage reports
function getAllReports() {
    $conn = getDBConnection();
    $query = "
        SELECT 
            dr.*,
            c.name as church_name,
            u.username as contributor_name,
            rl.level_name as risk_name,
            rl.color_code
        FROM DamageReports dr
        JOIN Churches c ON dr.church_id = c.church_id
        JOIN Users u ON dr.contributor_id = u.user_id
        JOIN RiskLevels rl ON dr.risk_level = rl.risk_id
        ORDER BY dr.report_date DESC
    ";
    $result = $conn->query($query);
    $reports = $result->fetch_all(MYSQLI_ASSOC);
    $conn->close();
    return $reports;
}

// Get reports by church ID
function getReportsByChurch($churchId) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("
        SELECT 
            dr.*,
            u.username as contributor_name,
            rl.level_name as risk_name,
            rl.color_code
        FROM DamageReports dr
        JOIN Users u ON dr.contributor_id = u.user_id
        JOIN RiskLevels rl ON dr.risk_level = rl.risk_id
        WHERE dr.church_id = ? AND dr.status = 'Approved'
        ORDER BY dr.report_date DESC
    ");
    $stmt->bind_param("i", $churchId);
    $stmt->execute();
    $result = $stmt->get_result();
    $reports = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
    return $reports;
}

// Add damage report
function addReport($churchId, $contributorId, $reportDate, $description, $riskLevel, $imagePath = null) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("INSERT INTO DamageReports (church_id, contributor_id, report_date, damage_description, risk_level, image_path) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissis", $churchId, $contributorId, $reportDate, $description, $riskLevel, $imagePath);
    $success = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $success;
}

// Update report status
function updateReportStatus($reportId, $status) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("UPDATE DamageReports SET status = ? WHERE report_id = ?");
    $stmt->bind_param("si", $status, $reportId);
    $success = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $success;
}

// Get risk levels
function getRiskLevels() {
    $conn = getDBConnection();
    $result = $conn->query("SELECT * FROM RiskLevels ORDER BY risk_id");
    $levels = $result->fetch_all(MYSQLI_ASSOC);
    $conn->close();
    return $levels;
}

// Get risk summary statistics
function getRiskSummary() {
    $conn = getDBConnection();
    $query = "
        SELECT 
            rl.level_name,
            rl.color_code,
            COUNT(DISTINCT c.church_id) as church_count
        FROM RiskLevels rl
        LEFT JOIN (
            SELECT church_id, MAX(risk_level) as max_risk
            FROM DamageReports
            WHERE status = 'Approved'
            GROUP BY church_id
        ) latest ON rl.risk_id = latest.max_risk
        LEFT JOIN Churches c ON latest.church_id = c.church_id
        GROUP BY rl.risk_id
        ORDER BY rl.risk_id
    ";
    $result = $conn->query($query);
    $summary = $result->fetch_all(MYSQLI_ASSOC);
    $conn->close();
    return $summary;
}
?>