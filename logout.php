<?php
// بدء الجلسة إذا لم تكن بدأت بالفعل
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// تدمير جميع بيانات الجلسة
$_SESSION = array();

// إذا كنت تريد حذف كوكي الجلسة أيضًا
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// تدمير الجلسة نهائيًا
session_destroy();

// توجيه المستخدم إلى صفحة تسجيل الدخول
header("Location: http://localhost/SAVELIFEnew/patients/login.php");
exit();
?>