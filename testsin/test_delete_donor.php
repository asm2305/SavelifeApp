<?php
include_once __DIR__ . '/helpers.php';

function testDeleteDonor() {
    $conn = getConnection();
    resetDatabase();

    // أضف متبرع أولاً بالاسم الصحيح
    $conn->query("INSERT INTO donors (full_name, blood_type) VALUES ('ToDelete', 'O+')");

    // احذف المتبرع
    $result = $conn->query("DELETE FROM donors WHERE full_name='ToDelete'");

    if (!$result) {
        echo "testDeleteDonor: FAILED - Delete query error: " . $conn->error . "\n";
        $conn->close();
        return false;
    }

    // تحقق من عدم وجود المتبرع
    $check = $conn->query("SELECT * FROM donors WHERE full_name='ToDelete'");
    $passed = ($check && $check->num_rows === 0);

    echo "testDeleteDonor: " . ($passed ? "PASSED" : "FAILED") . "\n";

    $conn->close();
    return $passed;
}

testDeleteDonor();
