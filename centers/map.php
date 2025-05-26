

<!-- Hero Section -->
<div class="hero-section text-center py-5 text-white mb-5" 
style="background-image: linear-gradient(rgba(30, 61, 97, 0.8), rgba(30, 61, 97, 0.8)), url('/SAVELIFEnew/s.jpeg'); background-size: cover; background-position: center;">

    <div class="container" >
        <h1 class="display-4" >خريطة مراكز التبرع</h1>
        <p class="lead">ابحث عن أقرب مركز تبرع </p>
    </div>
</div>

<div class="container">
    <div class="row">
        <!-- Map of centers -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body p-0">
                    <div id="map" style="height: 500px; width: 100%; border-radius: 5px;"></div>
                </div>
            </div>
        </div>
        
        <!-- List of centers -->
        <div class="col-md-4">

            
           <!-- View list of centers -->
            <div class="card">
                <div class="card-header text-white" style="background-color: #1E3D61;">
                    <h5 class="mb-0">قائمة المراكز</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php foreach ($centers as $center): ?>
                            <a href="?center_id=<?= $center['center_id'] ?>" 
                               class="list-group-item list-group-item-action <?= $center['needs_blood'] ? 'list-group-item-danger' : 'list-group-item-success' ?>">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong><?= htmlspecialchars($center['center_name']) ?></strong>
                                    <span><?= round(distanceFromUser($center), 1) ?> كم</span>
                                </div>
                                <small class="text-muted"><?= htmlspecialchars($center['location']) ?></small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
                     <!-- Navigation buttons -->
<div class="col-12 mb-4">
    <a href="\SAVELIFEnew\centers\list.php" class="btn btn-info mb-0">عرض جميع المراكز</a>
    <a href="\SAVELIFEnew\centers\register.php" class="btn btn-success">تسجيل مركز جديد</a>
</div>
        </div>
    </div>
   

    <!-- View details of the selected center -->
    <?php if (!empty($selected_center)): ?>
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-white" style="background-color: #1E3D61;">
                        <h5 class="mb-0">تفاصيل المركز:  <?= htmlspecialchars($selected_center['center_name']) ?></h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>الموقع :</strong> <?= htmlspecialchars($selected_center['location']) ?></p>
                                <p><strong>رقم التواصل :</strong> <?= htmlspecialchars($selected_center['contact_number']) ?></p>
                                <?php if ($selected_center['email']): ?>
                                    <p><strong>الايميل :</strong> <?= htmlspecialchars($selected_center['email']) ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <p><strong>الحالة :</strong> 
                                    <?php if ($selected_center['needs_blood']): ?>
                                        <span class="badge bg-danger">بحاجة ماسة إلى الدم</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">إمدادات كافية</span>
                                    <?php endif; ?>
                                </p>
                                <?php if ($selected_center['blood_types_needed']): ?>
                                    <p><strong>فصيلة الدم المطلوبة :</strong> 
                                        <?= htmlspecialchars($selected_center['blood_types_needed']) ?>
                                    </p>
                                <?php endif; ?>
                                <p><strong>آخر تحديث :</strong> 
                                    <?= date('M d, Y H:i', strtotime($selected_center['last_updated'])) ?>
                                </p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $selected_center['latitude'] ?>,<?= $selected_center['longitude'] ?>" 
                               target="_blank" class="btn text-white" style="background-color: #1E3D61;">
                                <i class="fas fa-directions me-2"></i> اعرض الاتجاهات 
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
