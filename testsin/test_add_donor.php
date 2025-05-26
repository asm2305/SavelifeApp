<?php
include_once __DIR__ . '/helpers.php';

function testAddDonor() {
    $conn = getConnection();
    resetDatabase();

    $name = "Test Donor";
    $bloodType = "B+";

    // تنظيف المدخلات
    $name = sanitize_input($name, $conn);
    $bloodType = sanitize_input($bloodType, $conn);

    // استعلام الإدخال مع العمود الصحيح full_name
    $sql = "INSERT INTO donors (full_name, blood_type) VALUES ('$name', '$bloodType')";
    $result = $conn->query($sql);

    if (!$result) {
        echo "testAddDonor: FAILED - Insert query error: " . $conn->error . "\n";
        $conn->close();
        return false;
    }

    // تحقق من وجود السجل
    $check = $conn->query("SELECT * FROM donors WHERE full_name='$name' AND blood_type='$bloodType'");
    $passed = ($check && $check->num_rows > 0);

    echo "testAddDonor: " . ($passed ? "PASSED" : "FAILED") . "\n";

    $conn->close();
    return $passed;
}

testAddDonor();
