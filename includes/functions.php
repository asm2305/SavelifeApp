<?php

function insertDonor($conn, $data)
{
    $stmt = $conn->prepare("INSERT INTO donors 
        (full_name, blood_type, location, latitude, longitude, contact_number, email, last_donation_date, terms_agreed) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "ssssssssi",
        $data['fullName'],
        $data['bloodType'],
        $data['location'],
        $data['latitude'],
        $data['longitude'],
        $data['contactNumber'],
        $data['email'],
        $data['lastDonation'],
        $data['terms']
    );

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

// في includes/functions.php

function searchDonors($conn, $bloodType = '', $location = '', $healthStatusArabic = '', $healthStatusMap = []) {
    $healthStatusEnglish = isset($healthStatusMap[$healthStatusArabic]) ? $healthStatusMap[$healthStatusArabic] : '';

    $sql = "SELECT * FROM donors WHERE 1=1";
    $params = [];

    if (!empty($bloodType)) {
        $sql .= " AND blood_type = ?";
        $params[] = $bloodType;
    }
    if (!empty($location)) {
        $sql .= " AND location LIKE ?";
        $params[] = "%$location%";
    }
    if (!empty($healthStatusEnglish)) {
        $sql .= " AND health_status = ?";
        $params[] = $healthStatusEnglish;
    }

    $sql .= " ORDER BY 
              CASE health_status
                WHEN 'Excellent' THEN 1
                WHEN 'Good' THEN 2
                WHEN 'Fair' THEN 3
                ELSE 4
              END, registration_date DESC";

    $stmt = $conn->prepare($sql);

    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();

    $result = $stmt->get_result();

    $donors = [];
    while ($row = $result->fetch_assoc()) {
        $donors[] = $row;
    }

    return $donors;
}


// تنقية البيانات مع mysqli real_escape_string (تمرير الاتصال لقاعدة البيانات)
function sanitize_input($data, $conn) {
    return htmlspecialchars($conn->real_escape_string(trim($data)));
}

function validatePatientData($data): array {
    $errors = [];

    // تحقق الاسم الكامل
    if (empty($data['fullName'])) {
        $errors[] = 'الاسم الكامل مطلوب';
    }

    // تحقق نوع فصيلة الدم
    if (empty($data['neededBloodType'])) {
        $errors[] = 'نوع فصيلة الدم مطلوب';
    }

    // تحقق مستوى الحاجة
    if (empty($data['urgencyLevel'])) {
        $errors[] = 'مستوى الحاجة مطلوب';
    }

    // تحقق الموقع
    if (empty($data['location'])) {
        $errors[] = 'اسم المنطقة مطلوب';
    }

    // تحقق رقم التواصل
    if (empty($data['contactNumber'])) {
        $errors[] = 'رقم التواصل مطلوب';
    } else {
        // تنظيف الرقم من أي شيء غير رقم
        $cleanNumber = preg_replace('/[^\d]/', '', $data['contactNumber']);
        if (strlen($cleanNumber) < 8 || strlen($cleanNumber) > 15) {
            $errors[] = 'تنسيق رقم الهاتف غير صحيح';
        }
    }

    // تحقق البريد الإلكتروني (اختياري)
    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'البريد الإلكتروني غير صالح';
    }

    // تحقق الموافقة
    if (empty($data['consent'])) {
        $errors[] = 'الموافقة مطلوبة';
    }

    return $errors;
}



function insertPatient($conn, $data): bool {
    $stmt = $conn->prepare("INSERT INTO patients (full_name, needed_blood_type, urgency_level, hospital_name, location, contact_number, email) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    // إذا القيمة null اجعلها سلسلة فارغة
    $hospitalName = $data['hospitalName'] ?? '';
    $email = $data['email'] ?? '';
    
    $stmt->bind_param("sssssss",
        $data['fullName'],
        $data['neededBloodType'],
        $data['urgencyLevel'],
        $hospitalName,
        $data['location'],
        $data['contactNumber'],
        $email
    );
    
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}


// دالة إشعار المتبرعين المتوافقين (يمكنك تعديلها لاحقاً لتسهيل اختبارها)
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
            file_put_contents(__DIR__ . '/../notification_log.txt', $logMessage . PHP_EOL, FILE_APPEND);
        }
    }
}

// دالة الحصول على فصائل الدم المتوافقة
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
function searchPatients($conn, $filters): array
{
    $bloodType = $filters['blood_type'] ?? '';
    $location = $filters['location'] ?? '';
    $urgencyLevel = $filters['urgency_level'] ?? '';

    $sql = "SELECT * FROM patients WHERE 1=1";
    $params = [];

    if (!empty($bloodType)) {
        $sql .= " AND needed_blood_type = ?";
        $params[] = $bloodType;
    }
    if (!empty($location)) {
        $sql .= " AND location LIKE ?";
        $params[] = "%$location%";
    }
    if (!empty($urgencyLevel)) {
        $sql .= " AND urgency_level = ?";
        $params[] = $urgencyLevel;
    }

    $sql .= " ORDER BY 
          CASE urgency_level
            WHEN 'حرج' THEN 1
            WHEN 'عالي' THEN 2
            WHEN 'متوسط' THEN 3
            WHEN 'منخفض' THEN 4
            ELSE 5
          END, registration_date DESC";

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $patients = [];
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
    return $patients;
}
