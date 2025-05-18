<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة التبرع بالدم</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/SAVELIFEnew/assets/css/style.css">

</head>
<body>
  <nav class="navbar navbar-expand bg-dark fixed-top" style="height: 100px;">
    <div class="container-fluid">
      <a class="navbar-brand d-flex align-items-center" href="/SAVELIFEnew/index.php">
    <img src="/SAVELIFEnew/Logo.PNG" alt="Logo" style="height: 50px;">
    <div style="width: 2px; height: 40px; background-color: white; margin: 0 15px;"></div>
    <img src="/SAVELIFEnew/2030.PNG" alt="Vision 2030" style="height: 10px;">
       </a>


        <!-- Menu Items -->
        <ul class="navbar-nav ms-auto d-flex flex-row">
                     <li class="nav-item">
                <a class="nav-link text-white px-3" href="/SAVELIFEnew/index.php">الرئيسية</a>
                 </li>
            <li class="nav-item">
                <a class="nav-link text-white px-3" href="/SAVELIFEnew/donors/register.php">تسجيل متبرع</a>
            </li>
			<li class="nav-item">
        <a class="nav-link" href="/SAVELIFEnew/patients/register.php">تسجيل مريض</a>
    </li>
            <li class="nav-item">
                <a class="nav-link text-white px-3" href="/SAVELIFEnew/patients/search.php">البحث عن متبرعين</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white px-3" href="/SAVELIFEnew/centers/map.php">مراكز التبرع</a>
                </li>
        </ul>
    </div>
</nav>

<div style="margin-top: 80px;"></div> <!-- To prevent content from hiding under fixed navbar -->

<!-- Offcanvas Sidebar -->
<div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="offcanvasNavbar">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Menu</h5>
        <button type="button" class="btn-close btn-close-white text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
       <ul class="navbar-nav ms-auto">
    <li class="nav-item">
        <a class="nav-link custom-green" href="/SAVELIFEnew/index.php">
            <i class="fas fa-home me-2"></i> Home
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link custom-green" href="/SAVELIFEnew/donors/register.php">
            <i class="fas fa-user-plus me-2"></i> Donor Registration
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link custom-green" href="/SAVELIFEnew/patients/search.php">
            <i class="fas fa-search me-2"></i> Find Donors
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link custom-green" href="/SAVELIFEnew/centers/map.php">
            <i class="fas fa-hospital me-2"></i> Donation Centers
        </a>
    </li>
</ul>

    </div>
</div>

<!-- Main Content -->
<main class="container py-4">
    <div id="alerts-container"></div>
