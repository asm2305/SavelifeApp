<?php
header('Content-Type: application/json');
include '../includes/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $center_id = isset($_POST['center_id']) ? intval($_POST['center_id']) : 0;
    $needs_blood = isset($_POST['needs_blood']) ? intval($_POST['needs_blood']) : 0;
    $blood_types = isset($_POST['blood_types']) ? $_POST['blood_types'] : '';
    
    if ($center_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'معرف المركز غير صالح']);
        exit;
    }
    
   // Update center status
    $sql = "UPDATE donation_centers SET 
            needs_blood = ?,
            blood_types_needed = ?,
            last_updated = CURRENT_TIMESTAMP
            WHERE center_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $needs_blood, $blood_types, $center_id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'تم تحديث حالة المركز بنجاح'
        ]);
        
        // If the center needs blood, notify nearby donors.
        if ($needs_blood) {
            $center_sql = "SELECT location, blood_types_needed FROM donation_centers WHERE center_id = $center_id";
            $center_result = $conn->query($center_sql);
            
            if ($center_result->num_rows > 0) {
                $center = $center_result->fetch_assoc();
                $blood_types = explode(', ', $center['blood_types_needed']);
                
                foreach ($blood_types as $blood_type) {
                    notifyDonorsForCenter($blood_type, $center['location'], $center_id, $conn);
                }
            }
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'حدث خطأ أثناء تحديث حالة المركز: ' . $stmt->error
        ]);
    }
    
    $stmt->close();
    $conn->close();
}

function notifyDonorsForCenter($blood_type, $location, $center_id, $conn) {
    $compatibleTypes = getCompatibleBloodTypes($blood_type);
    $typesList = "'" . implode("','", $compatibleTypes) . "'";
    
// Find donors with compatible blood types in the same location
    $sql = "SELECT donor_id, full_name, contact_number, email FROM donors 
            WHERE blood_type IN ($typesList) 
            AND location LIKE '%$location%'
            AND health_status = 'Good'";
    
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        // In the actual application, real notifications will be sent here
        while($row = $result->fetch_assoc()) {
            // Record notification (in actual app, send text messages or email)
            $logMessage = "تم إشعار المتبرع {$row['full_name']} عن الحاجة إلى فصيلة الدم $blood_type في المركز $center_id";
            file_put_contents('../notification_log.txt', $logMessage . PHP_EOL, FILE_APPEND);
        }
    }
}

function getCompatibleBloodTypes($bloodType) {
    $compatibility = [
        'A+' => ['A+', 'A-', 'O+', 'O-'],
        'A-' => ['A-', 'O-'],
        'B+' => ['B+', 'B-', 'O+', 'O-'],
        'B-' => ['B-', 'O-'],
        'AB+' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'],
        'AB-' => ['A-', 'B-', 'AB-', 'O-'],
        'O+' => ['O+', 'O-'],
        'O-' => ['O-']
    ];
    
    return $compatibility[$bloodType] ?? [];
}
?>
