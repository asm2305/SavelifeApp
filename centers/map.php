<?php 
include '../includes/connect.php';  
include '../includes/header.php';  

$centers = [];  
$sql = "SELECT * FROM donation_centers";  
$result = $conn->query($sql);  
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $centers[] = $row;  
    }
}

$selected_center = null;  
if (isset($_GET['center_id'])) {
    $center_id = intval($_GET['center_id']); 
    $sql = "SELECT * FROM donation_centers WHERE center_id = $center_id";  
    $result = $conn->query($sql);  
    if ($result->num_rows > 0) {
        $selected_center = $result->fetch_assoc();  
    }
}
?>

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
// Initialize the map
function initMap() {
    var defaultCenter = { lat: 24.7136, lng: 46.6753 };  
    <?php if (!empty($selected_center)): ?>
        defaultCenter = { lat: <?= $selected_center['latitude'] ?>, lng: <?= $selected_center['longitude'] ?> };
    <?php elseif (!empty($centers[0]['latitude']) && !empty($centers[0]['longitude'])): ?>
        defaultCenter = { lat: <?= $centers[0]['latitude'] ?>, lng: <?= $centers[0]['longitude'] ?> };
    <?php endif; ?>

    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 12,
        center: defaultCenter
    });

    // Add markers to centers on the map
    <?php foreach ($centers as $center): ?>
        <?php if (!empty($center['latitude']) && !empty($center['longitude'])): ?>
            var marker = new google.maps.Marker({
                position: { lat: <?= $center['latitude'] ?>, lng: <?= $center['longitude'] ?> },
                map: map,
                title: '<?= addslashes($center['center_name']) ?>',
                icon: {
                    url: '<?= $center['needs_blood'] ? 'http://maps.google.com/mapfiles/ms/icons/red-dot.png' : 'http://maps.google.com/mapfiles/ms/icons/green-dot.png' ?>'
                }
            });
            var infoWindow = new google.maps.InfoWindow({
                content: '<div style="padding:10px;">' +
                         '<h5><?= addslashes($center['center_name']) ?></h5>' +
                         '<p><?= addslashes($center['location']) ?></p>' +
                         '<p>الحالة: <strong><?= $center['needs_blood'] ? 'بحاجة ماسة للدم' : 'إمدادات كافية' ?></strong></p>' +
                         '<a href="?center_id=<?= $center['center_id'] ?>" class="btn btn-sm text-white" style="background-color: #1E3D61;">عرض التفاصيل</a>' +
                         '</div>'
            });
            marker.addListener('click', function() {
                infoWindow.open(map, marker);
            });
            <?php if (!empty($selected_center) && $selected_center['center_id'] == $center['center_id']): ?>
                infoWindow.open(map, marker);
                map.setZoom(15);
            <?php endif; ?>
        <?php endif; ?>
    <?php endforeach; ?>

   // Get the user's current location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var userLocation = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            new google.maps.Marker({
                position: userLocation,
                map: map,
                title: 'موقعك',
                icon: {
                    url: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                }
            });
            <?php if (empty($selected_center)): ?>
                map.setCenter(userLocation);
                map.setZoom(14);
            <?php endif; ?>
        }, function(error) {
            console.log('خطأ في تحديد الموقع: ', error);
        });
    }
}
</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDKM3z56Odz0ZQlGRNaTRl20x8g_FuXfHg&callback=initMap"></script>

<?php
// Function to calculate the distance between the user and the center (randomly placed here for display)
function distanceFromUser($center) {
    return rand(1, 20); 
}
include '../includes/footer.php'; 
?>
