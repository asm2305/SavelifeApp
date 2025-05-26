<?php

use PHPUnit\Framework\TestCase;

class DonorTest extends TestCase
{
    private $conn;

    protected function setUp(): void
    {
        $this->conn = new mysqli("localhost", "root", "", "blood_donation_db");
        if ($this->conn->connect_error) {
            $this->fail("فشل الاتصال بقاعدة البيانات: " . $this->conn->connect_error);
        }

        // تهيئة الترميز للتعامل مع العربية
        $this->conn->set_charset("utf8mb4");

        require_once __DIR__ . '/../includes/functions.php';
    }

    public function testInsertDonorUsingDatabaseData()
    {
        // استعلام لجلب صف واحد (متبرع) من قاعدة البيانات
        $sql = "SELECT full_name, blood_type, location, latitude, longitude, contact_number, email, last_donation_date, terms_agreed FROM donors LIMIT 1";
        $result = $this->conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // إعداد البيانات بشكل مصفوفة كما في دالة insertDonor
            $data = [
                'fullName' => $row['full_name'],
                'bloodType' => $row['blood_type'],
                'location' => $row['location'],
                'latitude' => (float)$row['latitude'],
                'longitude' => (float)$row['longitude'],
                'contactNumber' => $row['contact_number'],
                'email' => $row['email'],
                'lastDonation' => $row['last_donation_date'],
                'terms' => (int)$row['terms_agreed'],
            ];

            $result = insertDonor($this->conn, $data);
            $this->assertTrue($result, "فشل في إدخال المتبرع باستخدام بيانات من قاعدة البيانات.");
        } else {
            $this->fail("لا يوجد بيانات متبرعين في قاعدة البيانات للاختبار.");
        }
    }
}
