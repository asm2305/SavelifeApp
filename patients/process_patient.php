<?php
include '../includes/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $fullName = sanitize_input($_POST["fullName"], $conn);
    $neededBloodType = sanitize_input($_POST["neededBloodType"], $conn);
    $urgencyLevel = sanitize_input($_POST["urgencyLevel"], $conn);
    $hospitalName = isset($_POST["hospitalName"]) ? sanitize_input($_POST["hospitalName"], $conn) : null;
    $location = sanitize_input($_POST["location"], $conn);
    $contactNumber = sanitize_input($_POST["contactNumber"], $conn);
    $email = isset($_POST["email"]) ? sanitize_input($_POST["email"], $conn) : null;
    $consent = isset($_POST["consent"]) ? 1 : 0;

    // Validate
    if (empty($fullName) || empty($neededBloodType) || empty($urgencyLevel) || empty($location) || empty($contactNumber) || !$consent) {
        showAlert('error', 'الحقول مطلوبة', 'يجب ملء جميع الحقول المطلوبة', '../register_patient.php');
        exit;
    }

    if (!preg_match('/^[0-9]{10,15}$/', $contactNumber)) {
        showAlert('error', 'رقم غير صالح', 'تنسيق رقم الهاتف غير صحيح', '../register_patient.php');
        exit;
    }

    // Execute
    $stmt = $conn->prepare("INSERT INTO patients (full_name, needed_blood_type, urgency_level, hospital_name, location, contact_number, email) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $fullName, $neededBloodType, $urgencyLevel, $hospitalName, $location, $contactNumber, $email);

    if ($stmt->execute()) {
        $patient_id = $stmt->insert_id;
        notifyCompatibleDonors($neededBloodType, $location, $conn);
        showAlert('success', 'تم الإرسال', 'تم تقديم الطلب بنجاح!', '../index.php');
    } else {
        showAlert('error', 'خطأ في التسجيل', 'حدث خطأ أثناء حفظ البيانات: ' . $stmt->error, '../register_patient.php');
    }

    $stmt->close();
    $conn->close();
}

function notifyCompatibleDonors($bloodType, $location, $conn) {
    $compatibleTypes = getCompatibleBloodTypes($bloodType);
    $typesList = "'" . implode("','", $compatibleTypes) . "'";

    $sql = "SELECT donor_id, full_name, contact_number, email FROM donors 
            WHERE blood_type IN ($typesList) 
            AND location LIKE '%$location%' 
            AND health_status = 'ممتاز'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $logMessage = "تم إخطار المتبرع {$row['full_name']} بشأن فصيلة الدم $bloodType المطلوبة في $location";
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

// ✅ Function to show SweetAlert feedback
function showAlert($icon, $title, $text, $redirectUrl) {
    echo "
    <!DOCTYPE html>
    <html lang='ar'>
    <head>
        <meta charset='UTF-8'>
        <title>نتيجة العملية</title>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: '$icon',
                title: '$title',
                text: '$text',
                confirmButtonText: 'موافق'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '$redirectUrl';
                }
            });
        </script>
    </body>
    </html>";
}
?>
