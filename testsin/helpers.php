<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blood_donation_db";

function getConnection() {
    global $servername, $username, $password, $dbname;
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}

function sanitize_input($data, $conn) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

function resetDatabase() {
    $conn = getConnection();
    // احذف البيانات في الجداول المهمة لتنظيف البيئة بين الاختبارات
    $conn->query("DELETE FROM donors");
    // أضف هنا جداول أخرى لو فيه
    $conn->close();
}
