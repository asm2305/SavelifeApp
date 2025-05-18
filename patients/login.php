<?php
session_start();
include '../includes/connect.php';
include '../includes/header.php';

// Check that POST data has been sent
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   // Get the input data from the form
    $full_name = trim($_POST['full_name']);
    $contact_number = trim($_POST['contact_number']);

    // Check that fields are not empty
    if (!empty($full_name) && !empty($contact_number)) {
        // Secure SQL query using prepared statements to prevent SQL Injection attacks
        $stmt = $conn->prepare("SELECT * FROM patients WHERE full_name = ? AND contact_number = ?");
        
        // Check if the preparation query was successful
        if ($stmt === false) {
            die('خطأ في استعلام قاعدة البيانات: ' . $conn->error);
        }
        
        // Bind variables to the query
        $stmt->bind_param("ss", $full_name, $contact_number);  
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Check if the data exists in the database
        if ($result->num_rows > 0) {
            $patient = $result->fetch_assoc();
            
            // Store user data in the session
            $_SESSION['patient_id'] = $patient['patient_id'];
            $_SESSION['full_name'] = $patient['full_name'];
            $_SESSION['contact_number'] = $patient['contact_number'];
            
            // Redirect to the home page or the desired page after logging in
            header("Location: /SAVELIFEnew/patients/dashboard.php");
            exit();
        } else {
            // If no data matches the inputs
            $error_message = "الاسم أو رقم الاتصال غير صحيح!";
        }
    } else {
        // If the fields are empty
        $error_message = "يرجى ملء جميع الحقول!";
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            direction: rtl;
            text-align: right;
        }
        .hero-section {
            background-image: linear-gradient(rgba(30, 61, 97, 0.8), rgba(30, 61, 97, 0.8)), url('/SAVELIFEnew/s.jpeg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 5rem 0;
        }
        .hero-section h1, .hero-section p {
            text-align: center;
        }
        .card {
            width: 25rem;
            margin: 0 auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .card-header {
            background-color: #1E3D61;
            color: white;
            text-align: center;
            padding: 1rem;
            border-radius: 10px 10px 0 0;
        }
        .card-body {
            padding: 2rem;
        }
        .btn-custom {
            background-color: #1E3D61;
            color: white;
            width: 100%;
        }
    </style>
</head>
<body>

<!-- Hero Section -->
<div class="hero-section">
    <div class="container">
        <h1 class="display-4">تسجيل الدخول</h1>
        <p class="lead">يرجى إدخال بياناتك لتسجيل الدخول</p>
    </div>
</div>

<!-- Card for Login Form -->
<div class="container d-flex justify-content-center align-items-center" style="height: 60vh;">
    <div class="card">
        <div class="card-header">
            <h4>تسجيل الدخول</h4>
        </div>
        <div class="card-body">
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?= $error_message ?></div>
            <?php endif; ?>

            <!-- نموذج تسجيل الدخول -->
            <form action="login.php" method="POST">
                <div class="mb-3">
                    <label for="full_name" class="form-label">الاسم الكامل</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" required>
                </div>
                <div class="mb-3">
                    <label for="contact_number" class="form-label">رقم الاتصال</label>
                    <input type="text" class="form-control" id="contact_number" name="contact_number" required>
                </div>
                <button type="submit" class="btn btn-custom">تسجيل الدخول</button>
            </form>
        </div>
        <div class="card-footer text-center">
            <a href="../patients/register.php">لا تمتلك حساب؟ سجل الآن</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
