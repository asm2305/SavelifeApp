<?php

use PHPUnit\Framework\TestCase;

class DonorSearchTest extends TestCase
{
    private $conn;
    private $healthStatusArabic = [
        'ممتازة' => 'Excellent',
        'جيدة' => 'Good',
        'متوسطة' => 'Fair',
        'غير متاح مؤقتاً' => 'Temporarily Unavailable'
    ];

    protected function setUp(): void
    {
        $this->conn = new mysqli("localhost", "root", "", "blood_donation_db");
        if ($this->conn->connect_error) {
            $this->fail("فشل الاتصال بقاعدة البيانات: " . $this->conn->connect_error);
        }

        require_once __DIR__ . '/../includes/functions.php';
    }

    public function testSearchDonorsWithFilters()
    {
        $bloodType = 'O+';
        $location = 'الرياض';
        $healthStatus = 'ممتازة'; // سيتم تحويلها داخل الدالة

        $donors = searchDonors($this->conn, $bloodType, $location, $healthStatus, $this->healthStatusArabic);

        $this->assertIsArray($donors, "النتيجة يجب أن تكون مصفوفة");
        foreach ($donors as $donor) {
            $this->assertEquals($bloodType, $donor['blood_type']);
            $this->assertStringContainsString($location, $donor['location']);
            $this->assertEquals('Excellent', $donor['health_status']);
        }
    }

    public function testSearchDonorsWithoutFilters()
    {
        $donors = searchDonors($this->conn);

        $this->assertIsArray($donors);
        $this->assertNotEmpty($donors, "يجب أن تعيد نتائج حتى بدون فلترة");
    }

    protected function tearDown(): void
    {
        $this->conn->close();
    }
}
