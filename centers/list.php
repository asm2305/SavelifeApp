<?php
include '../includes/connect.php';
include '../includes/header.php';

// Get search parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$needs_blood = isset($_GET['needs_blood']) ? 1 : 0;

// Build query
$sql = "SELECT * FROM donation_centers WHERE 1=1";
$params = [];
$types = '';

if (!empty($search)) {
    $sql .= " AND (center_name LIKE ? OR location LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
}

if ($needs_blood) {
    $sql .= " AND needs_blood = 1";
}

$sql .= " ORDER BY center_name ASC";

// Prepare and execute
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container">
    <h1 class="text-center my-4">مراكز التبرع </h1>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">البحث عن مركز </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="list.php">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <input type="text" class="form-control" name="search" placeholder="البحث حسب الاسم أو الموقع" value="<?= htmlspecialchars($search) ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="needs_blood" name="needs_blood" <?= $needs_blood ? 'checked' : '' ?>>
                            <label class="form-check-label" for="needs_blood">
                                يحتاج دم
                            </label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">بحث </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">قائمة المراكز </h5>
                <a href="map.php" class="btn btn-light btn-sm">
                    <i class="fas fa-map-marked-alt me-1"></i> العرض على الخريطة 
                </a>
            </div>
        </div>
        <div class="card-body">
            <?php if ($result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>اسم المركز </th>
                                <th>الموقع </th>
                                <th>التواصل </th>
                                <th>الحالة </th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($center = $result->fetch_assoc()): ?>
                                <tr class="<?= $center['needs_blood'] ? 'table-danger' : 'table-success' ?>">
                                    <td><?= htmlspecialchars($center['center_name']) ?></td>
                                    <td><?= htmlspecialchars($center['location']) ?></td>
                                    <td><?= htmlspecialchars($center['contact_number']) ?></td>
                                    <td>
                                        <?php if ($center['needs_blood']): ?>
                                            <span class="badge bg-danger">Needs Blood</span>
                                            <?php if (!empty($center['blood_types_needed'])): ?>
                                                <small class="d-block"><?= htmlspecialchars($center['blood_types_needed']) ?></small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-success">Adequate Supply</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="map.php?center_id=<?= $center['center_id'] ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-map-marker-alt"></i> Map
                                        </a>
                                        <a href="tel:<?= htmlspecialchars($center['contact_number']) ?>" class="btn btn-sm btn-success">
                                            <i class="fas fa-phone"></i> اتصل
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    لم يتم العثور على مراكز تطابق معاييرك.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$stmt->close();
$conn->close();
include '../includes/footer.php';
?>
