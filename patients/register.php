<?php include '../includes/header.php'; ?>

<!-- Hero Section -->
<div class="hero-section text-center py-5 text-white mb-5" 
style="background-image: linear-gradient(rgba(30, 61, 97, 0.8), rgba(30, 61, 97, 0.8)), url('/SAVELIFEnew/s.jpeg'); background-size: cover; background-position: center;">
    <div class="container">
        <h1 class="display-4">تسجيل مريض</h1>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-white" style="background-color: #1E3D61;">
                    <h3 class="mb-0" text-end>تسجيل المريض</h3>
                </div>
                <div class="card-body">
                    <form id="patientForm" action="process_patient.php" method="POST">
                        <div class="row">
                            <div class="col-md-6">
                               <div class="form-group">
                                 <label for="fullName" class="form-label">الاسم الكامل *</label>
                                  <input type="text" class="form-control" id="fullName" name="fullName" required>
                               </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="neededBloodType">نوع فصيلة الدم *</label>
                                    <select class="form-control" id="neededBloodType" name="neededBloodType" required>
                                        <option value="">فصيلة الدم</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="urgencyLevel">مستوى الحاجة للدم</label>
                                    <select class="form-control" id="urgencyLevel" name="urgencyLevel" required>
                                        <option value="Urgent">مستعجل (خلال 24 ساعة)</option>
                                        <option value="High">مرتفع (خلال 3 أيام)</option>
                                        <option value="Medium">متوسط (خلال أسبوع)</option>
                                        <option value="Low">ضعيف (خلال أكثر من أسبوع)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="hospitalName">اسم المستشفى</label>
                                    <input type="text" class="form-control" id="hospitalName" name="hospitalName">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="location">اسم المنطقة *</label>
                                    <input type="text" class="form-control" id="location" name="location" required>
                                    <small class="text-muted">المدينة أو المنطقة التي تحتاج إلى الدم</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contactNumber">رقم التواصل *</label>
                                    <input type="tel" class="form-control" id="contactNumber" name="contactNumber" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">الايميل (اختياري)</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="consent" name="consent" required>
                                <label class="form-check-label" for="consent">
                                    أؤكد أن هذا الطلب حقيقي ولأغراض طبية *
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn text-white" style="background-color: #59B234; width: 100%;">إرسال الطلب</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('patientForm').addEventListener('submit', function(e) {
    const phone = document.getElementById('contactNumber').value;
    if (!/^[0-9]{10,15}$/.test(phone)) {
        alert('رقم الهاتف يجب أن يحتوي على 10-15 رقم فقط');
        e.preventDefault();
        return false;
    }
    
    const email = document.getElementById('email').value;
    if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        alert('يرجى إدخال عنوان بريد إلكتروني صالح');
        e.preventDefault();
        return false;
    }
    
    if (!document.getElementById('consent').checked) {
        alert('يجب عليك التأكد من أن هذا الطلب صحيح');
        e.preventDefault();
        return false;
    }
    
    return true;
});
</script>

<?php include '../includes/footer.php'; ?>
