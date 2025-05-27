<?php
session_start();
include '../includes/connect.php';
include '../includes/header.php';

// Check if the session contains donor dataif (isset($_SESSION['donor_id'])) {
// Get donor data from session
    $donor_id = $_SESSION['donor_id'];
    $donor_name = $_SESSION['full_name']; 
    $contact_number = $_SESSION['contact_number'];
    
// Query to fetch donor data from database
    $sql = "SELECT * FROM donors WHERE donor_id = $donor_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $donor = $result->fetch_assoc();
    }
 else {
// If there is no data in the session, redirect to the login page
    header("Location: \SAVELIFEnew\donors\login.php");
    exit();
}

// Fetch the location stored in the session to match it with donation centers
$donor_location = $donor['location']; 

// Inquiry to find nearby donation centers
$sql_centers = "SELECT * FROM donation_centers WHERE location LIKE '%$donor_location%' LIMIT 5";
$result_centers = $conn->query($sql_centers);
?>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                               <div class="card-header bg-darkblue text-white">
                    <h5 class="mb-0">صفحة المتبرع </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($donor)): ?>
                        <h4><?= htmlspecialchars($donor['full_name']) ?></h4>
                        <p class="mb-1"><strong>فصيلة الدم :</strong> <span class="badge bg-danger"><?= htmlspecialchars($donor['blood_type']) ?></span></p>
                        <p class="mb-1"><strong>الموقع :</strong> <?= htmlspecialchars($donor['location']) ?></p>
                        <p class="mb-1"><strong>التواصل :</strong> <?= htmlspecialchars($donor['contact_number']) ?></p>
                        <?php if ($donor['email']): ?>
                            <p class="mb-1"><strong>الايميل :</strong> <?= htmlspecialchars($donor['email']) ?></p>
                        <?php endif; ?>
                        <?php if ($donor['last_donation_date']): ?>
                            <p class="mb-1"><strong>تاريخ اخر تبرع :</strong> <?= date('M d, Y', strtotime($donor['last_donation_date'])) ?></p>
                        <?php endif; ?>
                        <p class="mb-0"><strong>الحالة :</strong> <?= htmlspecialchars($donor['health_status']) ?></p>
                    <?php else: ?>
                        <p>المتبرع غير موجود </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Nearby Donation Centers -->
        <div class="col-md-8">
            <div class="card mb-4">
                               <div class="card-header bg-darkblue text-white">

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
            
            <!-- Blood requests compatible with the donor's blood type -->
            <div class="card">
                              <div class="card-header bg-darkblue text-white">
                    <h5 class="mb-0">طلبات الدم الأخيرة التي تطابق فصيلتك</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($donor['blood_type'])): 
                        $blood_type = $donor['blood_type'];
                        $sql = "SELECT * FROM patients WHERE needed_blood_type = '$blood_type' ORDER BY registration_date DESC LIMIT 5";
                        $result = $conn->query($sql);
                        
                        if ($result->num_rows > 0): ?>
                            <div class="list-group">
                                <?php while($patient = $result->fetch_assoc()): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="mb-1"><?= htmlspecialchars($patient['full_name']) ?></h6>
                                                <small class="text-muted"><?= htmlspecialchars($patient['hospital_name']) ?></small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-danger"><?= htmlspecialchars($patient['needed_blood_type']) ?></span>
                                                <div>
                                                    <small class="<?= $patient['urgency_level'] == 'Urgent' ? 'text-danger' : 'text-warning' ?>">
                                                        <?= htmlspecialchars($patient['urgency_level']) ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <p>لا توجد طلبات دم حالياً لفصيلتك.</p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
