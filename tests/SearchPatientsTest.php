<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../includes/functions.php';

class SearchPatientsTest extends TestCase
{
    private $conn;

    protected function setUp(): void {
        // الاتصال بقاعدة بيانات الاختبار
        $this->conn = new mysqli("localhost", "root", "", "blood_donation_db");
        if ($this->conn->connect_error) {
            $this->fail("فشل الاتصال بقاعدة البيانات: " . $this->conn->connect_error);
        }
    }

    protected function tearDown(): void {
        $this->conn->close();
    }

    public function testSearchByBloodType()
    {
        $filters = [
            'blood_type' => 'O+',
            'location' => '',
            'urgency_level' => ''
        ];

        $results = searchPatients($this->conn, $filters);
        $this->assertIsArray($results);

        foreach ($results as $patient) {
            $this->assertEquals('O+', $patient['needed_blood_type']);
        }
    }

    public function testSearchByLocation()
    {
        $filters = [
            'blood_type' => '',
            'location' => 'الرياض',
            'urgency_level' => ''
        ];

        $results = searchPatients($this->conn, $filters);
        $this->assertIsArray($results);

        foreach ($results as $patient) {
            $this->assertStringContainsString('الرياض', $patient['location']);
        }
    }

    public function testSearchByUrgencyLevel()
    {
        $filters = [
            'blood_type' => '',
            'location' => '',
            'urgency_level' => 'حرج'
        ];

        $results = searchPatients($this->conn, $filters);
        $this->assertIsArray($results);

        foreach ($results as $patient) {
            $this->assertEquals('حرج', $patient['urgency_level']);
        }
    }

    public function testSearchWithNoFiltersReturnsResults()
    {
        $filters = [
            'blood_type' => '',
            'location' => '',
            'urgency_level' => ''
        ];

        $results = searchPatients($this->conn, $filters);
        $this->assertIsArray($results);
        $this->assertNotEmpty($results, "يجب أن تعيد النتائج عند عدم تطبيق فلاتر");
    }

    public function testSearchNoResults()
    {
        $filters = [
            'blood_type' => 'Z+', // فصيلة دم غير موجودة
            'location' => 'غير موجودة',
            'urgency_level' => 'غير معروف'
        ];

        $results = searchPatients($this->conn, $filters);
        $this->assertIsArray($results);
        $this->assertEmpty($results, "يجب ألا تعيد نتائج للفلاتر غير الموجودة");
    }
}
