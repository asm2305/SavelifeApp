<?php 
include '../includes/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Data collection and refinement
    $fullName = sanitize_input($_POST["fullName"], $conn);
    $bloodType = sanitize_input($_POST["bloodType"], $conn);
    $location = sanitize_input($_POST["location"], $conn);
    $contactNumber = sanitize_input($_POST["contactNumber"], $conn);
    $email = isset($_POST["email"]) ? sanitize_input($_POST["email"], $conn) : null;
    $lastDonation = isset($_POST["lastDonation"]) ? sanitize_input($_POST["lastDonation"], $conn) : null;
    $terms = isset($_POST["terms"]) ? 1 : 0;
    
    // Check required fields
    if (empty($fullName) || empty($bloodType) || empty($location) || empty($contactNumber) || !$terms) {
        echo json_encode(['success' => false, 'message' => 'يجب ملء جميع الحقول المطلوبة']);
        exit;
    }
    
    // Verify phone number
    if (!preg_match('/^[0-9]{10,15}$/', $contactNumber)) {
    echo "
    <!DOCTYPE html>
    <html lang='ar'>
    <head>
        <meta charset='UTF-8'>
        <title>خطأ</title>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'رقم هاتف غير صحيح',
                text: 'رقم الهاتف يجب أن يحتوي من 10 إلى 15 رقمًا',
                confirmButtonText: 'الرجوع'
            }).then(() => {
                window.history.back();
            });
        </script>
    </body>
    </html>
    ";
    exit;
    }

    //Verify email if entered
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'يرجى إدخال عنوان بريد إلكتروني صالح']);
        exit;
    }
    
    // Geolocation (this is just an example - in a real application you should use a geographic service like Google Maps)
    $latitude = null;
    $longitude = null;
    if (!empty($location)) {
        // This is just an example - in a real application you can use the Google Maps API
        $latitude = 24.7136 + (rand(-100, 100) / 1000);
        $longitude = 46.6753 + (rand(-100, 100) / 1000);
    }
    
    // Prepare and execute SQL statement
    $stmt = $conn->prepare("INSERT INTO donors (full_name, blood_type, location, latitude, longitude, contact_number, email, last_donation_date, terms_agreed) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssi", $fullName, $bloodType, $location, $latitude, $longitude, $contactNumber, $email, $lastDonation, $terms);
    
    if ($stmt->execute()) {
    $donor_id = $stmt->insert_id;

    // Notify centers that need this type of blood
    notifyCentersForDonor($bloodType, $location, $conn);

    echo "
    <!DOCTYPE html>
    <html lang='ar'>
    <head>
        <meta charset='UTF-8'>
        <title>تم التسجيل</title>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'تم التسجيل بنجاح',
                text: 'شكرًا لتسجيلك كمتبرع بالدم',
                confirmButtonText: 'موافق '
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../index.php';
                }
            });
        </script>
    </body>
    </html>
    ";
    exit;

    } else {
        echo "
        <!DOCTYPE html>
        <html lang='ar'>
        <head>
         <meta charset='UTF-8'>
         <title>خطأ في التسجيل</title>
         <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
        <script>
          Swal.fire({
            icon: 'error',
            title: 'حدث خطأ',
            text: 'لم يتم حفظ البيانات. حاول مرة أخرى.',
            confirmButtonText: 'العودة'
        }).then((result) => {
            if (result.isConfirmed) {
                window.history.back();
            }
        });
       </script>
       </body>
       </html>";
        exit;
    }
    
    $stmt->close();
    $conn->close();
}

function notifyCentersForDonor($bloodType, $location, $conn) {
    // Find centers that need this type of blood in the same area
    $sql = "SELECT center_id, center_name, contact_number FROM donation_centers 
            WHERE needs_blood = 1 
            AND (blood_types_needed LIKE '%$bloodType%' OR blood_types_needed IS NULL)
            AND location LIKE '%$location%'";
    
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        // In the real app, you'll send actual notifications here.
        while($row = $result->fetch_assoc()) {
            //Register notification (in the actual app, you can send SMS or email)
            $logMessage = "تم إخطار المركز {$row['center_name']} حول المتبرع الجديد الذي لديه فصيلة دم $bloodType في $location";
            file_put_contents('../notification_log.txt', $logMessage . PHP_EOL, FILE_APPEND);
        }
    }
}
?>
