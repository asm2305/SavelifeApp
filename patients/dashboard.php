<?php
session_start();
include '../includes/connect.php';
include '../includes/header.php';

// Check if the session contains patient data
if (isset($_SESSION['patient_id'])) {
    $patient_id = $_SESSION['patient_id'];
    $patient_name = $_SESSION['full_name'];
    $contact_number = $_SESSION['contact_number'];
    
    // Query to fetch patient data using Prepared Statements
    $stmt = $conn->prepare("SELECT * FROM patients WHERE patient_id = ?");
    $stmt->bind_param("i", $patient_id);  
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $patient = $result->fetch_assoc();
    } else {
        $error_message = "المريض غير موجود.";
    }
} else {
    header("Location: /SAVELIFEnew/patients/login.php");
    exit();
}

// Fetch the location stored in the session to match it with donation centers
$patient_location = $patient['location']; 

// Query to find nearby donation centers based on location
$sql_centers = "SELECT * FROM donation_centers WHERE location LIKE '%$patient_location%' LIMIT 5";
$result_centers = $conn->query($sql_centers);
?>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">صفحة المريض</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($patient)): ?>
                        <h4><?= htmlspecialchars($patient['full_name']) ?></h4>
                        <p class="mb-1"><strong>فصيلة الدم :</strong> <span class="badge bg-danger"><?= htmlspecialchars($patient['needed_blood_type']) ?></span></p>
                        <p class="mb-1"><strong>الموقع :</strong> <?= htmlspecialchars($patient['location']) ?></p>
                        <p class="mb-1"><strong>التواصل :</strong> <?= htmlspecialchars($patient['contact_number']) ?></p>
                        <?php if ($patient['email']): ?>
                            <p class="mb-1"><strong>الايميل :</strong> <?= htmlspecialchars($patient['email']) ?></p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p>المريض غير موجود</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Display donors compatible with the patient's blood type -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">المتبرعين المتوافقين مع فصيلة دمي</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($patient['needed_blood_type'])): 
                        $needed_blood_type = $patient['needed_blood_type'];
                        $stmt = $conn->prepare("SELECT * FROM donors WHERE blood_type = ? ORDER BY last_donation_date DESC LIMIT 5");
                        $stmt->bind_param("s", $needed_blood_type);  
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if ($result->num_rows > 0): ?>
                            <div class="list-group">
                                <?php while($donor = $result->fetch_assoc()): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="mb-1"><?= htmlspecialchars($donor['full_name']) ?></h6>
                                                <small class="text-muted"><?= htmlspecialchars($donor['location']) ?></small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-danger"><?= htmlspecialchars($donor['blood_type']) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>

                        <?php else: ?>
                            <p>لا توجد متبرعين حالياً لفصيلتك.</p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- View nearby donation centers -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">مراكز التبرع القريبة</h5>
                </div>
                <div class="card-body">
                    <?php if ($result_centers->num_rows > 0): ?>
                        <div class="list-group">
                            <?php while($center = $result_centers->fetch_assoc()): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="mb-1"><?= htmlspecialchars($center['center_name']) ?></h6>
                                            <small class="text-muted"><?= htmlspecialchars($center['location']) ?></small>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p>لا توجد مراكز تبرع قريبة حالياً.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
