<?php include '../includes/header.php'; ?>

<!-- Hero Section -->
<div class="hero-section text-center py-5 text-white mb-5" 
style="background-image: linear-gradient(rgba(30, 61, 97, 0.8), rgba(30, 61, 97, 0.8)), url('/SAVELIFEnew/s.jpeg'); background-size: cover; background-position: center;">

    <div class="container">
        <h1 class="display-4">تسجيل المراكز</h1>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">تسجيل مركز التبرعات</h3>
                </div>
                <div class="card-body">
                    <form id="centerForm" action="process_center.php" method="POST">
                        <div class="form-group">
                            <label for="centerName">اسم المركز*</label>
                            <input type="text" class="form-control" id="centerName" name="centerName" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="location">الموقع*</label>
                                    <input type="text" class="form-control" id="location" name="location" required>
                                    <small class="text-muted">العنوان الكامل بما في ذلك المدينة</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contactNumber">رقم التواصل*</label>
                                    <input type="tel" class="form-control" id="contactNumber" name="contactNumber" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">الإيميل</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="needsBlood" name="needsBlood">
                                <label class="form-check-label" for="needsBlood">
                                    نحن بحاجة حاليًا للتبرع بالدم
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group" id="bloodTypesGroup" style="display: none;">
                            <label for="bloodTypesNeeded">ما هي فصائل الدم المطلوبة؟</label>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="typeAPlus" name="bloodTypes[]" value="A+">
                                        <label class="form-check-label" for="typeAPlus">A+</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="typeAMinus" name="bloodTypes[]" value="A-">
                                        <label class="form-check-label" for="typeAMinus">A-</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="typeBPlus" name="bloodTypes[]" value="B+">
                                        <label class="form-check-label" for="typeBPlus">B+</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="typeBMinus" name="bloodTypes[]" value="B-">
                                        <label class="form-check-label" for="typeBMinus">B-</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="typeOPlus" name="bloodTypes[]" value="O+">
                                        <label class="form-check-label" for="typeOPlus">O+</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="typeOMinus" name="bloodTypes[]" value="O-">
                                        <label class="form-check-label" for="typeOMinus">O-</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="typeABPlus" name="bloodTypes[]" value="AB+">
                                        <label class="form-check-label" for="typeABPlus">AB+</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="typeABMinus" name="bloodTypes[]" value="AB-">
                                        <label class="form-check-label" for="typeABMinus">AB-</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">سجل المركز</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Show/hide the required blood type field based on the checkbox selection
document.getElementById('needsBlood').addEventListener('change', function() {
    const bloodTypesGroup = document.getElementById('bloodTypesGroup');
    bloodTypesGroup.style.display = this.checked ? 'block' : 'none';
});

// Validate the form
document.getElementById('centerForm').addEventListener('submit', function(e) {
    const phone = document.getElementById('contactNumber').value;
    if (!/^[0-9]{10,15}$/.test(phone)) {
        alert('رقم الهاتف يجب أن يتكون من 10-15 رقمًا فقط');
        e.preventDefault();
        return false;
    }
    
    const email = document.getElementById('email').value;
    if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        alert('يرجى إدخال عنوان بريد إلكتروني صحيح');
        e.preventDefault();
        return false;
    }
    
    const needsBlood = document.getElementById('needsBlood').checked;
    const bloodTypesChecked = document.querySelectorAll('input[name="bloodTypes[]"]:checked').length > 0;
    
    if (needsBlood && !bloodTypesChecked) {
        alert('يرجى تحديد فصيلة دم واحدة على الأقل');
        e.preventDefault();
        return false;
    }
    
    return true;
});
</script>

<?php include '../includes/footer.php'; ?>
