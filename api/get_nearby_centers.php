<?php
header('Content-Type: application/json');
include '../includes/connect.php';

// Get requset data
$data = json_decode(file_get_contents('php://input'), true);
$lat = isset($data['lat']) ? floatval($data['lat']) : 0;
$lng = isset($data['lng']) ? floatval($data['lng']) : 0;
$radius = isset($data['radius']) ? intval($data['radius']) : 20; // المسافة الافتراضية 20 كم

// Validate input
if ($lat == 0 || $lng == 0) {
    echo json_encode(['success' => false, 'message' => 'إحداثيات غير صالحة']);
    exit;
}

