<?php
include '../includes/connect.php';
include '../includes/header.php';
?>

<?php
$bloodType = isset($_GET['blood_type']) ? $_GET['blood_type'] : '';
$location = isset($_GET['location']) ? $_GET['location'] : '';
$urgencyLevel = isset($_GET['urgency_level']) ? $_GET['urgency_level'] : '';

// Map database urgency levels to Arabic for display
$urgencyLevelsArabic = [
    'حرج' => 'Critical',
    'عالي' => 'High',
    'متوسط' => 'Medium',
    'منخفض' => 'Low'
];

// Convert Arabic selection back to database values
$urgencyLevelDB = $urgencyLevel;


$sql = "SELECT * FROM patients WHERE 1=1";
$params = [];

if (!empty($bloodType)) {
    $sql .= " AND needed_blood_type = ?";
    $params[] = $bloodType;
}
if (!empty($location)) {
    $sql .= " AND location LIKE ?";
    $params[] = "%$location%";
}
if (!empty($urgencyLevelDB)) {
    $sql .= " AND urgency_level = ?";
    $params[] = $urgencyLevelDB;
}

$sql .= " ORDER BY 
          CASE urgency_level
            WHEN 'حرج' THEN 1
            WHEN 'عالي' THEN 2
            WHEN 'متوسط' THEN 3
            WHEN 'منخفض' THEN 4
            ELSE 5
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
        <h1 class="display-4">البحث عن المرضى المحتاجين للدم</h1>
        <p class="lead">ابحث عن المرضى المحتاجين لتبرعات دم</p>
    </div>
</div>

<div class="container">
    <div class="card mb-4">
        <div class="card-header text-white" style="background-color: #1E3D61;">
            <h5 class="mb-0">تصفية البحث</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="search_patients.php">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="blood_type">نوع فصيلة الدم المطلوبة</label>
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
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="urgency_level">مستوى الاستعجال</label>
                            <select class="form-control" id="urgency_level" name="urgency_level">
                                <option value="">جميع المستويات</option>
                                <?php
                                $statusesArabic = ['حرج', 'عالي', 'متوسط', 'منخفض'];
                                foreach ($statusesArabic as $status) {
                                    $selected = $urgencyLevel == $status ? 'selected' : '';
                                    echo "<option value='$status' $selected>$status</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <button type="submit" class="btn text-white" style="background-color: #59B234;">بحث</button>
                    <a href="search_patients.php" class="btn btn-secondary">إلغاء البحث</a>
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
                                
                                <th>فصيلة الدم المطلوبة</th>
                                <th>مستوى الاستعجال</th>
                                <th>اسم المستشفى</th>
                                <th>الموقع</th>
                                <th>رقم التواصل</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($patient = $result->fetch_assoc()): ?>
                                <tr>
                                    
                                    <td><span class="badge" style="background-color: #D9534F; color: white;"><?= htmlspecialchars($patient['needed_blood_type']) ?></span></td>
                                    <td>
                                        <?php 
                                        $statusClass = '';
                                        if ($patient['urgency_level'] == 'حرج') $statusClass = 'text-danger';
                                        elseif ($patient['urgency_level'] == 'عالي') $statusClass = 'text-warning';
                                        elseif ($patient['urgency_level'] == 'متوسط') $statusClass = 'text-primary';
                                        elseif ($patient['urgency_level'] == 'منخفض') $statusClass = 'text-success';
                                        ?>
                                        <span class="<?= $statusClass ?>"><?= htmlspecialchars($patient['urgency_level']) ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($patient['hospital_name'] ?? 'غير محدد') ?></td>
                                    <td><?= htmlspecialchars($patient['location'] ?? 'غير محدد') ?></td>
                                    <td><?= htmlspecialchars($patient['contact_number']) ?></td>
                                   
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    لم يتم العثور على مرضى يتطابقون مع معايير البحث الخاصة بك.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
