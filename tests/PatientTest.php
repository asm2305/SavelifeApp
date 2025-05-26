<?php

use PHPUnit\Framework\TestCase;

class PatientTest extends TestCase
{
    private $conn;

    protected function setUp(): void {
        $this->conn = new mysqli("localhost", "root", "", "blood_donation_db");
        if ($this->conn->connect_error) {
            $this->fail("فشل الاتصال بقاعدة البيانات: " . $this->conn->connect_error);
        }
        require_once __DIR__ . '/../includes/functions.php';
    }

    public function testValidPatientDataInsertsSuccessfully()
    {
        $data = [
            'fullName' => 'محمد أحمد',
            'neededBloodType' => 'O+',
            'urgencyLevel' => 'حرج',
            'hospitalName' => 'مستشفى الرياض',
            'location' => 'الرياض',
            'contactNumber' => '0555123456',
            'email' => 'mohamed@example.com',
            'consent' => 1
        ];

        $errors = validatePatientData($data);
        $this->assertEmpty($errors, "يجب أن لا توجد أخطاء في البيانات الصالحة");

        $inserted = insertPatient($this->conn, $data);
        $this->assertTrue($inserted, "يجب حفظ بيانات المريض بنجاح");

        $stmt = $this->conn->prepare("SELECT * FROM patients WHERE contact_number = ?");
        $stmt->bind_param("s", $data['contactNumber']);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->assertGreaterThan(0, $result->num_rows, "المريض يجب أن يكون موجود في قاعدة البيانات");
        $stmt->close();
    }

    public function testInvalidPatientDataReturnsErrors()
    {
        $data = [
            'fullName' => '',
            'neededBloodType' => '',
            'urgencyLevel' => '',
            'location' => '',
            'contactNumber' => '123abc',
            'email' => 'invalid-email',
            'consent' => 0
        ];

        $errors = validatePatientData($data);
        $this->assertNotEmpty($errors);
        $this->assertContains('الاسم الكامل مطلوب', $errors);
        $this->assertContains('نوع فصيلة الدم مطلوب', $errors);
        $this->assertContains('مستوى الحاجة مطلوب', $errors);
        $this->assertContains('اسم المنطقة مطلوب', $errors);
        $this->assertContains('تنسيق رقم الهاتف غير صحيح', $errors);
        $this->assertContains('البريد الإلكتروني غير صالح', $errors);
        $this->assertContains('الموافقة مطلوبة', $errors);
    }

    public function testPatientDataFromDatabase()
    {
        $result = $this->conn->query("SELECT * FROM patients LIMIT 1");
        $this->assertNotEquals(0, $result->num_rows, "يجب أن تحتوي قاعدة البيانات على بيانات مريض للاختبار");

        $data = $result->fetch_assoc();

        $patientData = [
            'fullName' => $data['full_name'] ?? 'محمد أحمد',
            'neededBloodType' => $data['needed_blood_type'] ?? 'O+',
            'urgencyLevel' => $data['urgency_level'] ?? 'حرج',
            'hospitalName' => $data['hospital_name'] ?? 'مستشفى الرياض',
            'location' => $data['location'] ?? 'الرياض',
            'contactNumber' => $data['contact_number'] ?? '0555123456',
            'email' => $data['email'] ?? 'mohamed@example.com',
            'consent' => 1
        ];

        $errors = validatePatientData($patientData);
        if (!empty($errors)) {
            print_r($errors);
        }
        $this->assertEmpty($errors, "بيانات المريض في قاعدة البيانات يجب أن تكون صحيحة");

        $inserted = insertPatient($this->conn, $patientData);
        $this->assertTrue($inserted, "يجب إدخال بيانات المريض بنجاح");
    }

    protected function tearDown(): void {
        $this->conn->query("DELETE FROM patients WHERE contact_number = '0555123456'");
        $this->conn->close();
    }
}
