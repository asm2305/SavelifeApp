<?php
include '../includes/connect.php';
include '../includes/header.php';
?>

<?php
$bloodType = isset($_GET['blood_type']) ? $_GET['blood_type'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';
$healthStatus = isset($_GET['health_status']) ? $_GET['health_status'] : '';

// Map English health status to Arabic for display
$healthStatusArabic = [
    'ممتازة' => 'Excellent',
    'جيدة' => 'Good',
    'متوسطة' => 'Fair',
    'غير متاح مؤقتاً' => 'Temporarily Unavailable'
];

// Convert Arabic selection back to English for query
$healthStatusEnglish = isset($healthStatusArabic[$healthStatus]) ? $healthStatusArabic[$healthStatus] : '';


$sql = "SELECT * FROM donors WHERE 1=1";
$params = [];

if (!empty($bloodType)) {
    $sql .= " AND blood_type = ?";
    $params[] = $bloodType;
}
if (!empty($location)) {
    $sql .= " AND location LIKE ?";
    $params[] = "%$location%";
}
if (!empty($healthStatusEnglish)) {
    $sql .= " AND health_status = ?";
    $params[] = $healthStatusEnglish;
}

$sql .= " ORDER BY 
          CASE health_status
            WHEN 'Excellent' THEN 1
            WHEN 'Good' THEN 2
            WHEN 'Fair' THEN 3
            ELSE 4
          END, registration_date DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!-- Hero Section -->
<div class="hero-section text-center py-5 text-white mb-5" 
style="background-image: linear-gradient(rgba(30, 61, 97, 0.8), rgba(30, 61, 97, 0.8)), url('/SAVELIFEnew/s.jpeg'); background-size: cover; background-position: center;">
    <div class="container">
        <h1 class="display-4">البحث عن المتبرعين بالدم</h1>
        <p class="lead">ابحث عن متبرعين بالدم مناسبين لاحتياجاتك الطبية</p>
    </div>
</div>

<div class="container">
    <div class="card mb-4">
        <div class="card-header text-white" style="background-color: #1E3D61;">
            <h5 class="mb-0">تصفية البحث</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="search.php">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="blood_type">نوع فصيلة الدم</label>
                            <select class="form-control" id="blood_type" name="blood_type">
                                <option value="">جميع الفصائل</option>
                                <?php
                                $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
                                foreach ($bloodTypes as $type) {
                                    $selected = $bloodType == $type ? 'selected' : '';
                                    echo "<option value='$type' $selected>$type</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="location">الموقع</label>
                            <input type="text" class="form-control" id="location" name="location" 
                                   value="<?= htmlspecialchars($location) ?>" placeholder="المدينة أو المنطقة">
                        </div>
                    </div>
                
                </div>
                <div class="text-center mt-3">
                    <button type="submit" class="btn text-white" style="background-color: #59B234;">بحث</button>
                    <a href="search.php" class="btn btn-secondary">إلغاء البحث</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header text-white" style="background-color: #1E3D61;">
            <h5 class="mb-0">نتائج البحث</h5>
        </div>
        <div class="card-body">
            <?php if ($result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                
                                <th>فصيلة الدم</th>
                               
                                <th>الموقع</th>
                                <th>رقم التواصل</th>
                                <th>آخر تبرع</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($donor = $result->fetch_assoc()): 
                                // Map health status back to Arabic for display
                                $healthStatusDisplay = array_search($donor['health_status'], $healthStatusArabic) !== false ? 
                                                     array_search($donor['health_status'], $healthStatusArabic) : $donor['health_status'];
                            ?>
                                <tr>
                                    
                                    <td><span class="badge" style="background-color: #59B234; color: white;"><?= htmlspecialchars($donor['blood_type']) ?></span></td>
                             
                                    <td><?= htmlspecialchars($donor['location']) ?></td>
                                    <td><?= htmlspecialchars($donor['contact_number']) ?></td>
                                    <td><?= $donor['last_donation_date'] ? date('M d, Y', strtotime($donor['last_donation_date'])) : 'لم يتبرع بعد' ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    لم يتم العثور على متبرعين يتطابقون مع معايير البحث الخاصة بك.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
