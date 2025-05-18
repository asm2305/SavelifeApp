<?php include '../includes/header.php'; ?>



<!-- Hero Section -->
<div class="hero-section text-center py-5 text-white mb-5" 
style="background-image: linear-gradient(rgba(30, 61, 97, 0.8), rgba(30, 61, 97, 0.8)), url('/SAVELIFEnew/s.jpeg'); background-size: cover; background-position: center;">

    <div class="container" >
        <h1 class="display-4" >تسجيل المتبرع</h1>
        <p class="lead">سجل كمتبرع بالدم وساعد في إنقاذ الأرواح</p>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-white" style="background-color: #1E3D61;">
                    <h3 class="mb-0">تسجيل متبرع</h3>
                </div>
                <div class="card-body">
                    <form id="donorForm" action="process_donor.php" method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fullName">الاسم كامل*</label>
                                    <input type="text" class="form-control" id="fullName" name="fullName" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bloodType">فصيلة الدم</label>
                                    <select class="form-control custom-select-hover" id="bloodType" name="bloodType" required>
                                        <option value="">اختيار فصيلة الدم</option>
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
                                    <label for="location">المنطقة *</label>
                                    <input type="text" class="form-control" id="location" name="location" required>
                                    <small class="text-muted">المدينة أو المنطقة</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contactNumber">رقم التواصل * </label>
                                    <input type="tel" class="form-control" id="contactNumber" name="contactNumber" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">الايميل </label>
                                    <input type="email" class="form-control" id="email" name="email">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lastDonation">تاريخ آخر تبرع</label>
                                    <input type="date" class="form-control" id="lastDonation" name="lastDonation">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">
                                    أوافق على الشروط والأحكام*
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn text-white btn-block" style="background-color: #59B234;">تسجيل كمتبرع </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
