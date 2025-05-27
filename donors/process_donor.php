
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
    


