<?php
require_once '../controllers/ReportController.php';

// Ensure request has report ID
if (!isset($_GET['report_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Report ID is required']);
    exit;
}

$reportController = new ReportController();
$reportDetails = $reportController->getReportDetails($_GET['report_id']);

if (!$reportDetails) {
    http_response_code(404);
    echo json_encode(['error' => 'Report not found']);
    exit;
}

// Return report details as JSON
header('Content-Type: application/json');
echo json_encode($reportDetails); 