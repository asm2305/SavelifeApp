

<!-- Hero Section -->
<div class="hero-section text-center py-5 text-white mb-5" 
style="background-image: linear-gradient(rgba(30, 61, 97, 0.8), rgba(30, 61, 97, 0.8)), url('/SAVELIFEnew/s.jpeg'); background-size: cover; background-position: center;">

    <div class="container" >
        <h1 class="display-4" >المتبرعين بالدم</h1>
        <p class="lead">اسكتشف المتبرعين المسجلين لدينا وتواصل معهم بسهولة</p>
    </div>
</div>

<div class="container">
    <div class="card mb-4">
        <div class="card-header text-white" style="background-color: #1E3D61;">
            <h5 class="mb-0">البحث عن المتبرعين</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="list.php">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="blood_type">فصيلة الدم</label>
                            <select class="form-control" id="blood_type" name="blood_type">
                                <option value="">جميع الأنواع</option>
                                <?php
                                $typesArr = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
                                foreach ($typesArr as $type) {
                                    $selected = $blood_type == $type ? 'selected' : '';
                                    echo "<option value='$type' $selected>$type</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="location">الموقع</label>
                            <input type="text" class="form-control" id="location" name="location" 
                                   value="<?= htmlspecialchars($location) ?>" placeholder="المدينة أو المنطقة">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn text-white w-100" style="background-color: #1E3D61;">بحث</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header text-white" style="background-color: #1E3D61;">
            <h5 class="mb-0">قائمة المتبرعين</h5>
        </div>
        <div class="card-body">
            <?php if ($result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>الاسم</th>
                                <th>فصيلة الدم</th>
                                <th>الموقع</th>
                                <th>التواصل</th>
                                <th>آخر تبرع</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($donor = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($donor['full_name']) ?></td>
                                    <td><span class="badge" style="background-color: #59B234; color: white;"><?= htmlspecialchars($donor['blood_type']) ?></span></td>
                                    <td><?= htmlspecialchars($donor['location']) ?></td>
                                    <td>
                                        <?= htmlspecialchars($donor['contact_number']) ?>
                                        <?php if (!empty($donor['email'])): ?>
                                            <small class="d-block"><?= htmlspecialchars($donor['email']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= $donor['last_donation_date'] ? date('M d, Y', strtotime($donor['last_donation_date'])) : 'لم يتبرع بعد' ?>
                                    </td>
                                    <td>
                                        <a href="tel:<?= htmlspecialchars($donor['contact_number']) ?>" class="btn btn-sm btn-success">
                                            <i class="fas fa-phone"></i> اتصل
                                        </a>
                                        <?php if (!empty($donor['email'])): ?>
                                            <a href="mailto:<?= htmlspecialchars($donor['email']) ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-envelope"></i> إرسال بريد إلكتروني
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    لم يتم العثور على متبرعين مطابقين للمعايير التي تم تحديدها.
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
