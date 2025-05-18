<?php
include '../includes/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $centerName = sanitize_input($_POST["centerName"], $conn);
    $location = sanitize_input($_POST["location"], $conn);
    $contactNumber = sanitize_input($_POST["contactNumber"], $conn);
    $email = isset($_POST["email"]) ? sanitize_input($_POST["email"], $conn) : null;
    $needsBlood = isset($_POST["needsBlood"]) ? 1 : 0;
    $bloodTypesNeeded = '';

    if ($needsBlood && isset($_POST["bloodTypes"])) {
        $bloodTypesNeeded = implode(', ', $_POST["bloodTypes"]);
    }

    // Validate required fields
    if (empty($centerName) || empty($location) || empty($contactNumber)) {
        showAlert('error', 'خطأ في التسجيل', 'يجب تعبئة جميع الحقول المطلوبة');
        exit;
    }

    // Validate phone number
    if (!preg_match('/^[0-9]{10,15}$/', $contactNumber)) {
        showAlert('error', 'رقم غير صالح', 'رقم التواصل يجب أن يحتوي على 10 إلى 15 رقمًا');
        exit;
    }

    // Geocode location (simple placeholder)
    $latitude = 24.7136 + (rand(-100, 100) / 1000);
    $longitude = 46.6753 + (rand(-100, 100) / 1000);

    // Prepare and execute SQL
    $stmt = $conn->prepare("INSERT INTO donation_centers (center_name, location, latitude, longitude, contact_number, email, needs_blood, blood_types_needed) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssddssis", $centerName, $location, $latitude, $longitude, $contactNumber, $email, $needsBlood, $bloodTypesNeeded);

    if ($stmt->execute()) {
        showAlert('success', 'تم التسجيل بنجاح', 'تم تسجيل المركز بنجاح، شكرًا لك!', '../index.php');
    } else {
        showAlert('error', 'فشل التسجيل', 'حدث خطأ أثناء حفظ البيانات: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();
}

// Function to show SweetAlert
function showAlert($icon, $title, $text, $redirect = null) {
    echo "
    <!DOCTYPE html>
    <html lang='ar'>
    <head>
        <meta charset='UTF-8'>
        <title>نتيجة التسجيل</title>
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
                " . ($redirect ? "window.location.href = '$redirect';" : "window.history.back();") . "
            });
        </script>
    </body>
    </html>";
}
?>
