<?php include 'includes/header.php'; ?>

<style>
/* Fullscreen hero section with background image */
.hero-section {
    position: relative;
    height: 100vh;
    width: 100%;
    background: url('/Savelife/blood.jpg') no-repeat center center/cover;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Semi-transparent full overlay */
.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(30, 61, 97, 0.8); /* transparent navy blue */
}

/* Content centered on top of overlay */
.hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
    color: white;
    padding: 20px;
}

.hero-content h1 {
    font-size: 3.5rem;
    font-weight: bold;
    margin-bottom: 20px;
}

.hero-content p {
    font-size: 1.2rem;
}

.hero-content a.btn {
    margin: 10px;
}
</style>

<div class="hero-section">
    <div class="hero-overlay"></div>

    <div class="hero-content">
        <h1>التبرع صدقة جارية… تمتد خيرًا في الحياة</h1>
        <p>انضم إلى مجتمع المتبرعين لدينا وساعد المرضى المحتاجين</p>
        <a href="donors/register.php" class="btn btn-light btn-lg">تبرع الان</a>
        <a href="patients/search.php" class="btn btn-outline-light btn-lg">البحث عن متبرعين</a>
    </div>
</div>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <i class="fas fa-tint fa-3x" style="color: #59B234; margin-bottom: 15px;"></i>
                    <h3>المتبرعون بالدم</h3>
                    <p>سجل كمتبرع بالدم وساعد في إنقاذ الأرواح في مجتمعك</p>
                    <a href="donors/register.php" class="btn" style="color: #1E3D61; border: 1px solid #1E3D61;">مستخدم جديد </a>
                    <a href="donors/login.php" class="btn" style="color: #1E3D61; border: 1px solid #1E3D61;"> تسجيل دخول</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <i class="fas fa-procedures fa-3x" style="color: #59B234; margin-bottom: 15px;"></i>
                    <h3>المرضى</h3>
                    <p>ابحث عن متبرعين بالدم متوافقين لاحتياجاتك الطبية</p>
                    <a href="patients/register.php" class="btn" style="color: #1E3D61; border: 1px solid #1E3D61;">مستخدم جديد </a>
                    <a href="patients/login.php" class="btn" style="color: #1E3D61; border: 1px solid #1E3D61;">تسجيل دخول</a>
                    <a href="patients/search.php" class="btn" style="color: #1E3D61; border: 1px solid #1E3D61;">البحث عن متبرعين</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 text-center">
                <div class="card-body">
                    <i class="fas fa-hospital fa-3x" style="color: #59B234; margin-bottom: 15px;"></i>
                    <h3>مراكز التبرع</h3>
                    <p>ابحث عن مراكز التبرع بالدم بالقرب منك</p>
                    <a href="centers/map.php" class="btn" style="color: #1E3D61; border: 1px solid #1E3D61;">عرض المراكز</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header text-white" style="background-color: #1E3D61;">
                    <h5 class="mb-0">المتبرعون الجدد</h5>
                </div>
                <div class="card-body">
                    <?php
                    include 'includes/connect.php';
                    $sql = "SELECT full_name, blood_type, location FROM donors ORDER BY registration_date DESC LIMIT 5";
                    $result = $conn->query($sql);
                    
                    if ($result->num_rows > 0): ?>
                        <div class="list-group">
                            <?php while($row = $result->fetch_assoc()): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?= htmlspecialchars($row['full_name']) ?></strong>
                                            <small class="d-block text-muted"><?= htmlspecialchars($row['location']) ?></small>
                                        </div>
                                        <span class="badge" style="background-color: #1E3D61; color: white;"><?= htmlspecialchars($row['blood_type']) ?></span>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                        <div class="mt-3 text-center">
                            <a href="donors/list.php" class="btn text-white" style="background-color: #1E3D61;">عرض كل المتبرعين</a>
                        </div>
                    <?php else: ?>
                        <p>No donors registered yet.</p>
                    <?php endif; ?>
                    <?php $conn->close(); ?>
                </div>
            </div>
        </div>

<?php include 'includes/footer.php'; ?>
