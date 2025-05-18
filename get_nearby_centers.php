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

// Calculate the distance between the current location and nearby donation centers
$sql = "SELECT *, 
        (6371 * acos(cos(radians($lat)) * cos(radians(latitude)) * 
        cos(radians(longitude) - radians($lng)) + sin(radians($lat)) * 
        sin(radians(latitude)))) AS distance 
        FROM donation_centers 
        HAVING distance < $radius 
        ORDER BY distance ASC, needs_blood DESC";

$result = $conn->query($sql);
$centers = [];

if ($result->num_rows > 0) {
    // If donation centers are found, we store the results in the array
    while($row = $result->fetch_assoc()) {
        $centers[] = [
            'id' => $row['center_id'],
            'name' => $row['center_name'],
            'location' => $row['location'],
            'lat' => floatval($row['latitude']),
            'lng' => floatval($row['longitude']),
            'contact' => $row['contact_number'],
            'needs_blood' => (bool)$row['needs_blood'],
            'blood_types_needed' => $row['blood_types_needed'],
            'distance' => round($row['distance'], 2) // Distance between user and center (in kilometers)
        ];
    }
}

// Send the result as JSON
echo json_encode([
    'success' => true,
    'centers' => $centers
]);

// Close the connection to the database
$conn->close();
?>
