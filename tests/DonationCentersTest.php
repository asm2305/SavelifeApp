<?php
use PHPUnit\Framework\TestCase;

class DonationCentersTest extends TestCase
{
    private $conn;

    protected function setUp(): void
    {
        $this->conn = new mysqli("localhost", "root", "", "blood_donation_db");

        if ($this->conn->connect_error) {
            $this->fail("فشل الاتصال بقاعدة البيانات: " . $this->conn->connect_error);
        }

        // إضافة مركز وهمي للاختبار
        $this->conn->query("
            INSERT INTO donation_centers (center_name, location, contact_number, email, needs_blood, blood_types_needed, latitude, longitude, last_updated)
            VALUES ('مركز اختبار', 'الرياض', '0555123456', 'test@example.com', 1, 'O+', 24.7136, 46.6753, NOW())
        ");
    }

    public function testCentersAreRetrieved()
    {
        $result = $this->conn->query("SELECT * FROM donation_centers");
        $this->assertGreaterThan(0, $result->num_rows, "يجب أن يتم استرجاع مراكز التبرع");
    }

    public function testSpecificCenterCanBeRetrieved()
    {
        $result = $this->conn->query("SELECT * FROM donation_centers WHERE center_name = 'مركز اختبار'");
        $this->assertEquals(1, $result->num_rows, "يجب العثور على المركز المضاف");

        $center = $result->fetch_assoc();
        $this->assertEquals('الرياض', $center['location']);
        $this->assertEquals('0555123456', $center['contact_number']);
        $this->assertEquals(1, $center['needs_blood']);
    }

    protected function tearDown(): void
    {
        // حذف بيانات المركز التجريبي
        $this->conn->query("DELETE FROM donation_centers WHERE center_name = 'مركز اختبار'");
        $this->conn->close();
    }
}
