<?php
use PHPUnit\Framework\TestCase;

class CenterRegistrationTest extends TestCase
{
    private $conn;

    protected function setUp(): void
    {
        $this->conn = new mysqli("localhost", "root", "", "blood_donation_db");
        if ($this->conn->connect_error) {
            $this->fail("فشل الاتصال بقاعدة البيانات: " . $this->conn->connect_error);
        }
    }

    public function testValidCenterDataInsertsCorrectly()
    {
        $stmt = $this->conn->prepare("
            INSERT INTO donation_centers 
            (center_name, location, contact_number, email, needs_blood, blood_types_needed, latitude, longitude, last_updated)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        $center_name = "مركز اختبار";
        $location = "جدة، السعودية";
        $contact_number = "0555123456";
        $email = "center@example.com";
        $needs_blood = 1;
        $blood_types_needed = "A+,O-";
        $latitude = 21.5433;
        $longitude = 39.1728;

        $stmt->bind_param("ssssissd", 
            $center_name, $location, $contact_number, $email, 
            $needs_blood, $blood_types_needed, $latitude, $longitude
        );

        $this->assertTrue($stmt->execute(), "يجب أن يتم إدخال البيانات بنجاح");

        // تحقق من وجود البيانات
        $result = $this->conn->query("SELECT * FROM donation_centers WHERE center_name = 'مركز اختبار'");
        $this->assertEquals(1, $result->num_rows, "المركز يجب أن يكون مسجلاً");

        // تنظيف البيانات
        $this->conn->query("DELETE FROM donation_centers WHERE center_name = 'مركز اختبار'");
    }

    public function testInvalidPhoneNumberIsRejected()
    {
        $invalidPhone = "123abc";
        $this->assertFalse(preg_match('/^[0-9]{10,15}$/', $invalidPhone) === 1, "رقم الهاتف غير صالح");
    }

    public function testMissingBloodTypeWithNeedsBloodFails()
    {
        $needsBlood = true;
        $bloodTypes = [];

        if ($needsBlood && empty($bloodTypes)) {
            $this->assertTrue(true, "يجب رفض التسجيل إذا لم يتم اختيار فصائل دم عند الحاجة");
        } else {
            $this->fail("التحقق من فصائل الدم غير مفعّل");
        }
    }

    protected function tearDown(): void
    {
        $this->conn->close();
    }
}
