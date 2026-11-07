<?php
// 1. إظهار الأخطاء للمطور
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db.php';
require_once 'functions.php';

// 2. حماية لوحة التحكم
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
if (isset($_GET['action']) && $_GET['action'] === 'logout') { session_destroy(); header("Location: login.php"); exit; }

// ==========================================
// جلب الإحصائيات للنظرة العامة
// ==========================================
$stats_students = $conn->query("SELECT COUNT(*) as c FROM users WHERE is_admin = 0")->fetch_assoc()['c'];
$stats_courses = $conn->query("SELECT COUNT(*) as c FROM courses")->fetch_assoc()['c'];
$stats_enrollments = $conn->query("SELECT COUNT(*) as c FROM enrollments")->fetch_assoc()['c'];
$stats_pending = $conn->query("SELECT COUNT(*) as c FROM enrollments WHERE status='pending'")->fetch_assoc()['c'];

// ==========================================
// معالجة العمليات
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // --- تغيير كلمة المرور ---
    if ($_POST['action'] === 'change_password') {
        $current_pass = $_POST['current_password'];
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];

        if ($new_pass !== $confirm_pass) {
            $error_msg = "كلمة المرور الجديدة غير متطابقة!";
        } else {
            $admin_id = $_SESSION['admin_id'];
            $res = $conn->query("SELECT password_hash FROM admins WHERE id = $admin_id");
            $admin_data = $res->fetch_assoc();

            // التحقق (مع دعم كلمة المرور القديمة 123 بسبب اختلاف التشفير)
            if (password_verify($current_pass, $admin_data['password_hash']) || $current_pass === '123') {
                $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE admins SET password_hash = ? WHERE id = ?");
                $stmt->bind_param("si", $new_hash, $admin_id);
                $stmt->execute();
                $success_msg = "تم تغيير كلمة المرور بنجاح!";
            } else {
                $error_msg = "كلمة المرور الحالية غير صحيحة.";
            }
        }
    }

    // --- طلبات التفعيل ---
    if ($_POST['action'] === 'approve_enrollment') { $conn->query("UPDATE enrollments SET status = 'approved' WHERE id = " . (int)$_POST['id']); header("Location: admin.php?tab=enrollments&msg=updated"); exit; }
    if ($_POST['action'] === 'reject_enrollment') { $conn->query("DELETE FROM enrollments WHERE id = " . (int)$_POST['id']); header("Location: admin.php?tab=enrollments&msg=deleted"); exit; }

    // --- أ. المواد ---
    if ($_POST['action'] === 'add_course') { $stmt = $conn->prepare("INSERT INTO courses (title, code, price, faculty) VALUES (?, ?, ?, ?)"); $stmt->bind_param("ssds", $_POST['title'], $_POST['code'], $_POST['price'], $_POST['faculty']); $stmt->execute(); header("Location: admin.php?tab=courses&msg=added"); exit; }
    if ($_POST['action'] === 'update_course') { $stmt = $conn->prepare("UPDATE courses SET title=?, code=?, price=?, faculty=? WHERE id=?"); $stmt->bind_param("ssdsi", $_POST['title'], $_POST['code'], $_POST['price'], $_POST['faculty'], $_POST['id']); $stmt->execute(); header("Location: admin.php?tab=courses&msg=updated"); exit; }
    if ($_POST['action'] === 'delete_course') { $conn->query("DELETE FROM courses WHERE id = " . (int)$_POST['id']); header("Location: admin.php?tab=courses&msg=deleted"); exit; }

    // --- ب. الجامعات ---
    if ($_POST['action'] === 'add_uni') { $stmt = $conn->prepare("INSERT INTO universities (name, logo, desc) VALUES (?, ?, ?)"); $stmt->bind_param("sss", $_POST['name'], $_POST['logo'], $_POST['desc']); $stmt->execute(); header("Location: admin.php?tab=unis&msg=added"); exit; }
    if ($_POST['action'] === 'update_uni') { $stmt = $conn->prepare("UPDATE universities SET name=?, logo=?, desc=? WHERE id=?"); $stmt->bind_param("sssi", $_POST['name'], $_POST['logo'], $_POST['desc'], $_POST['id']); $stmt->execute(); header("Location: admin.php?tab=unis&msg=updated"); exit; }
    if ($_POST['action'] === 'delete_uni') { $conn->query("DELETE FROM universities WHERE id = " . (int)$_POST['id']); header("Location: admin.php?tab=unis&msg=deleted"); exit; }

    // --- ج. الأعمال ---
    if ($_POST['action'] === 'add_work') { $stmt = $conn->prepare("INSERT INTO semester_works (title, faculty, desc) VALUES (?, ?, ?)"); $stmt->bind_param("sss", $_POST['title'], $_POST['faculty'], $_POST['desc']); $stmt->execute(); header("Location: admin.php?tab=works&msg=added"); exit; }
    if ($_POST['action'] === 'update_work') { $stmt = $conn->prepare("UPDATE semester_works SET title=?, faculty=?, desc=? WHERE id=?"); $stmt->bind_param("sssi", $_POST['title'], $_POST['faculty'], $_POST['desc'], $_POST['id']); $stmt->execute(); header("Location: admin.php?tab=works&msg=updated"); exit; }
    if ($_POST['action'] === 'delete_work') { $conn->query("DELETE FROM semester_works WHERE id = " . (int)$_POST['id']); header("Location: admin.php?tab=works&msg=deleted"); exit; }

    // --- د. المحاضرات ---
    if ($_POST['action'] === 'add_lesson') { $stmt = $conn->prepare("INSERT INTO lessons (course_id, title, video_filename) VALUES (?, ?, ?)"); $stmt->bind_param("iss", $_POST['course_id'], $_POST['title'], $_POST['video_filename']); $stmt->execute(); header("Location: admin.php?tab=lessons&msg=added"); exit; }
    if ($_POST['action'] === 'update_lesson') { $stmt = $conn->prepare("UPDATE lessons SET course_id=?, title=?, video_filename=? WHERE id=?"); $stmt->bind_param("issi", $_POST['course_id'], $_POST['title'], $_POST['video_filename'], $_POST['id']); $stmt->execute(); header("Location: admin.php?tab=lessons&msg=updated"); exit; }
    if ($_POST['action'] === 'delete_lesson') { $conn->query("DELETE FROM lessons WHERE id = " . (int)$_POST['id']); header("Location: admin.php?tab=lessons&msg=deleted"); exit; }

    // --- هـ. الإعلانات ---
    if ($_POST['action'] === 'add_ad') { $stmt = $conn->prepare("INSERT INTO ads_banners (title, ad_type, image_url, is_active) VALUES (?, ?, ?, 1)"); $stmt->bind_param("sss", $_POST['title'], $_POST['ad_type'], $_POST['image_url']); $stmt->execute(); header("Location: admin.php?tab=ads&msg=added"); exit; }
    if ($_POST['action'] === 'update_ad') { $stmt = $conn->prepare("UPDATE ads_banners SET title=?, ad_type=?, image_url=? WHERE id=?"); $stmt->bind_param("sssi", $_POST['title'], $_POST['ad_type'], $_POST['image_url'], $_POST['id']); $stmt->execute(); header("Location: admin.php?tab=ads&msg=updated"); exit; }
    if ($_POST['action'] === 'delete_ad') { $conn->query("DELETE FROM ads_banners WHERE id = " . (int)$_POST['id']); header("Location: admin.php?tab=ads&msg=deleted"); exit; }

    // --- و. الإعدادات ---
    if ($_POST['action'] === 'update_settings') {
        $stmt = $conn->prepare("UPDATE site_settings SET value = ? WHERE `key` = ?");
        $stmt->bind_param("ss", $_POST['site_name'], $key); $key='site_name'; $stmt->execute();
        $stmt->bind_param("ss", $_POST['whatsapp_number'], $key); $key='whatsapp_number'; $stmt->execute();
        $stmt->bind_param("ss", $_POST['footer_text'], $key); $key='footer_text'; $stmt->execute();
        header("Location: admin.php?tab=info&msg=updated"); exit;
    }
}

$settings = get_site_settings($conn);
include 'admin_header.php';

function showAlert() {
    if (isset($_GET['msg'])) {
        $msg = $_GET['msg'] === 'added' ? 'تمت الإضافة بنجاح!' : ($_GET['msg'] === 'deleted' ? 'تم الحذف بنجاح!' : 'تم التحديث بنجاح!');
        $color = $_GET['msg'] === 'deleted' ? 'rose' : 'emerald';
        echo "<div class='bg-{$color}-100 text-{$color}-700 p-4 rounded-xl mb-4 font-bold border border-{$color}-200'>{$msg}</div>";
    }
}
?>

<!-- 1. نظرة عامة (Dashboard) -->
<div id="tab-overview" class="tab-panel active space-y-6">
    <h2 class="text-2xl font-black mb-4 text-slate-800">مرحباً بك في لوحة التحكم 👋</h2>
    
    <!-- بطاقات الإحصائيات -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-3xl p-6 text-white shadow-lg relative overflow-hidden">
            <div class="relative z-10"><p class="text-blue-100 font-bold text-xs mb-1">إجمالي الطلاب</p><h3 class="text-3xl font-black"><?= $stats_students ?></h3></div>
            <div class="absolute -bottom-4 -right-4 text-6xl opacity-20">👥</div>
        </div>
        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-3xl p-6 text-white shadow-lg relative overflow-hidden">
            <div class="relative z-10"><p class="text-emerald-100 font-bold text-xs mb-1">إجمالي المواد</p><h3 class="text-3xl font-black"><?= $stats_courses ?></h3></div>
            <div class="absolute -bottom-4 -right-4 text-6xl opacity-20">📚</div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-fuchsia-600 rounded-3xl p-6 text-white shadow-lg relative overflow-hidden">
            <div class="relative z-10"><p class="text-purple-100 font-bold text-xs mb-1">إجمالي الاشتراكات</p><h3 class="text-3xl font-black"><?= $stats_enrollments ?></h3></div>
            <div class="absolute -bottom-4 -right-4 text-6xl opacity-20">🛒</div>
        </div>
        <div class="bg-gradient-to-br from-amber-400 to-orange-500 rounded-3xl p-6 text-slate-900 shadow-lg relative overflow-hidden">
            <div class="relative z-10"><p class="text-amber-900 font-bold text-xs mb-1">طلبات بانتظار الموافقة</p><h3 class="text-3xl font-black"><?= $stats_pending ?></h3></div>
            <div class="absolute -bottom-4 -right-4 text-6xl opacity-20">⏳</div>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 mt-6">
        <p class="text-slate-600 font-bold">استخدم القائمة الجانبية للتنقل. تذكر مراجعة "طلبات التفعيل" لتأكيد اشتراكات الطلاب.</p>
    </div>
</div>

<!-- 2. طلبات التفعيل -->
<div id="tab-enrollments" class="tab-panel">
    <h2 class="text-xl font-black text-slate-800 mb-6">🛒 طلبات تفعيل الاشتراكات</h2>
    <?php showAlert(); ?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-right border-collapse">
            <thead><tr class="bg-slate-50 text-slate-500 text-xs font-black border-b border-slate-200"><th class="p-4">الطالب</th><th class="p-4">المادة</th><th class="p-4">التاريخ</th><th class="p-4">الحالة</th><th class="p-4">الإجراءات</th></tr></thead>
            <tbody class="text-sm font-bold text-slate-700">
                <?php
                $sql_enroll = "SELECT e.id, e.status, e.created_at, u.name as student_name, c.title as course_title FROM enrollments e JOIN users u ON e.user_id = u.id JOIN courses c ON e.course_id = c.id ORDER BY e.id DESC";
                $res_enroll = $conn->query($sql_enroll);
                if($res_enroll && $res_enroll->num_rows > 0) {
                    while($row = $res_enroll->fetch_assoc()) {
                        $status_badge = $row['status'] === 'approved' ? "<span class='text-emerald-600 bg-emerald-50 px-2 py-1 rounded'>✅ مشترك</span>" : "<span class='text-amber-600 bg-amber-50 px-2 py-1 rounded'>⏳ معلق</span>";
                        echo "<tr class='border-b border-slate-100'>
                            <td class='p-4'>".htmlspecialchars($row['student_name'])."</td>
                            <td class='p-4 text-xs text-sky-600'>".htmlspecialchars($row['course_title'])."</td>
                            <td class='p-4 text-xs font-mono'>".date('Y-m-d', strtotime($row['created_at']))."</td>
                            <td class='p-4'>{$status_badge}</td>
                            <td class='p-4 flex gap-2'>";
                            if($row['status'] !== 'approved') {
                                echo "<form method='POST' class='inline'><input type='hidden' name='action' value='approve_enrollment'><input type='hidden' name='id' value='{$row['id']}'><button type='submit' class='bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1 rounded text-xs transition'>موافقة</button></form>";
                            }
                            echo "<form method='POST' class='inline' onsubmit='return confirm(\"حذف الطلب؟\")'><input type='hidden' name='action' value='reject_enrollment'><input type='hidden' name='id' value='{$row['id']}'><button type='submit' class='bg-rose-500 hover:bg-rose-600 text-white px-3 py-1 rounded text-xs transition'>حذف</button></form>
                            </td></tr>";
                    }
                } else { echo "<tr><td colspan='5' class='p-4 text-center text-slate-400'>لا توجد طلبات حالياً.</td></tr>"; }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- 3. إدارة الجامعات -->
<div id="tab-unis" class="tab-panel">
    <div class="flex justify-between items-center mb-6"><h2 class="text-xl font-black text-slate-800">🏛️ إدارة الجامعات</h2><button onclick="openModal('add-uni-modal')" class="bg-teal-500 hover:bg-teal-600 text-white font-bold px-4 py-2 rounded-xl text-sm transition">+ إضافة جامعة</button></div>
    <?php showAlert(); ?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-right border-collapse">
            <thead><tr class="bg-slate-50 text-slate-500 text-xs font-black border-b border-slate-200"><th class="p-4">الرقم</th><th class="p-4">شعار</th><th class="p-4">الاسم</th><th class="p-4">الإجراءات</th></tr></thead>
            <tbody class="text-sm font-bold text-slate-700">
                <?php $res = $conn->query("SELECT * FROM universities"); while($row = $res->fetch_assoc()) { echo "<tr class='border-b border-slate-100'><td class='p-4'>{$row['id']}</td><td class='p-4 text-2xl'>{$row['logo']}</td><td class='p-4'>".htmlspecialchars($row['name'] ?? '')."</td><td class='p-4'><button onclick=\"openModal('edit-uni-{$row['id']}')\" class='text-sky-500 hover:text-sky-700 font-bold text-xs ml-2'>تعديل</button><form method='POST' class='inline' onsubmit='return confirm(\"هل أنت متأكد؟\")'><input type='hidden' name='action' value='delete_uni'><input type='hidden' name='id' value='{$row['id']}'><button type='submit' class='text-rose-500 hover:text-rose-700 font-bold text-xs'>حذف</button></form></td></tr>"; } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- 4. إدارة المواد -->
<div id="tab-courses" class="tab-panel">
    <div class="flex justify-between items-center mb-6"><h2 class="text-xl font-black text-slate-800">📚 إدارة المواد</h2><button onclick="openModal('add-course-modal')" class="bg-teal-500 hover:bg-teal-600 text-white font-bold px-4 py-2 rounded-xl text-sm transition">+ إضافة مادة</button></div>
    <?php showAlert(); ?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-right border-collapse">
            <thead><tr class="bg-slate-50 text-slate-500 text-xs font-black border-b border-slate-200"><th class="p-4">الرقم</th><th class="p-4">الاسم</th><th class="p-4">الكود</th><th class="p-4">السعر</th><th class="p-4">الإجراءات</th></tr></thead>
            <tbody class="text-sm font-bold text-slate-700">
                <?php $res = $conn->query("SELECT * FROM courses"); while($row = $res->fetch_assoc()) { echo "<tr class='border-b border-slate-100'><td class='p-4'>{$row['id']}</td><td class='p-4'>".htmlspecialchars($row['title'] ?? '')."</td><td class='p-4 text-xs font-mono'>".htmlspecialchars($row['code'] ?? '')."</td><td class='p-4 text-emerald-600'>".number_format($row['price'],2)." ر.س</td><td class='p-4'><button onclick=\"openModal('edit-course-{$row['id']}')\" class='text-sky-500 hover:text-sky-700 font-bold text-xs ml-2'>تعديل</button><form method='POST' class='inline' onsubmit='return confirm(\"هل أنت متأكد؟\")'><input type='hidden' name='action' value='delete_course'><input type='hidden' name='id' value='{$row['id']}'><button type='submit' class='text-rose-500 hover:text-rose-700 font-bold text-xs'>حذف</button></form></td></tr>"; } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- 5. إدارة الأعمال -->
<div id="tab-works" class="tab-panel">
    <div class="flex justify-between items-center mb-6"><h2 class="text-xl font-black text-slate-800">📝 إدارة الأعمال</h2><button onclick="openModal('add-work-modal')" class="bg-teal-500 hover:bg-teal-600 text-white font-bold px-4 py-2 rounded-xl text-sm transition">+ إضافة عمل</button></div>
    <?php showAlert(); ?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-right border-collapse">
            <thead><tr class="bg-slate-50 text-slate-500 text-xs font-black border-b border-slate-200"><th class="p-4">الرقم</th><th class="p-4">العنوان</th><th class="p-4">الكلية</th><th class="p-4">الإجراءات</th></tr></thead>
            <tbody class="text-sm font-bold text-slate-700">
                <?php $res = $conn->query("SELECT * FROM semester_works"); while($row = $res->fetch_assoc()) { echo "<tr class='border-b border-slate-100'><td class='p-4'>{$row['id']}</td><td class='p-4'>".htmlspecialchars($row['title'] ?? '')."</td><td class='p-4 text-xs'>".htmlspecialchars($row['faculty'] ?? '')."</td><td class='p-4'><button onclick=\"openModal('edit-work-{$row['id']}')\" class='text-sky-500 hover:text-sky-700 font-bold text-xs ml-2'>تعديل</button><form method='POST' class='inline' onsubmit='return confirm(\"هل أنت متأكد؟\")'><input type='hidden' name='action' value='delete_work'><input type='hidden' name='id' value='{$row['id']}'><button type='submit' class='text-rose-500 hover:text-rose-700 font-bold text-xs'>حذف</button></form></td></tr>"; } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- 6. إدارة المحاضرات -->
<div id="tab-lessons" class="tab-panel">
    <div class="flex justify-between items-center mb-6"><h2 class="text-xl font-black text-slate-800">🎬 إدارة المحاضرات</h2><button onclick="openModal('add-lesson-modal')" class="bg-teal-500 hover:bg-teal-600 text-white font-bold px-4 py-2 rounded-xl text-sm transition">+ إضافة محاضرة</button></div>
    <?php showAlert(); ?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-right border-collapse">
            <thead><tr class="bg-slate-50 text-slate-500 text-xs font-black border-b border-slate-200"><th class="p-4">الرقم</th><th class="p-4">المادة</th><th class="p-4">العنوان</th><th class="p-4">الإجراءات</th></tr></thead>
            <tbody class="text-sm font-bold text-slate-700">
                <?php $res = $conn->query("SELECT lessons.*, courses.title AS c_name FROM lessons LEFT JOIN courses ON lessons.course_id = courses.id"); while($row = $res->fetch_assoc()) { echo "<tr class='border-b border-slate-100'><td class='p-4'>{$row['id']}</td><td class='p-4 text-sky-600'>".htmlspecialchars($row['c_name'] ?? '')."</td><td class='p-4'>".htmlspecialchars($row['title'] ?? '')."</td><td class='p-4'><button onclick=\"openModal('edit-lesson-{$row['id']}')\" class='text-sky-500 hover:text-sky-700 font-bold text-xs ml-2'>تعديل</button><form method='POST' class='inline' onsubmit='return confirm(\"هل أنت متأكد؟\")'><input type='hidden' name='action' value='delete_lesson'><input type='hidden' name='id' value='{$row['id']}'><button type='submit' class='text-rose-500 hover:text-rose-700 font-bold text-xs'>حذف</button></form></td></tr>"; } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- 7. إدارة الإعلانات -->
<div id="tab-ads" class="tab-panel">
    <div class="flex justify-between items-center mb-6"><h2 class="text-xl font-black text-slate-800">📢 الإعلانات والسلايدر</h2><button onclick="openModal('add-ad-modal')" class="bg-teal-500 hover:bg-teal-600 text-white font-bold px-4 py-2 rounded-xl text-sm transition">+ إضافة إعلان</button></div>
    <?php showAlert(); ?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-right border-collapse">
            <thead><tr class="bg-slate-50 text-slate-500 text-xs font-black border-b border-slate-200"><th class="p-4">الرقم</th><th class="p-4">العنوان</th><th class="p-4">النوع</th><th class="p-4">الإجراءات</th></tr></thead>
            <tbody class="text-sm font-bold text-slate-700">
                <?php $res = $conn->query("SELECT * FROM ads_banners ORDER BY id DESC"); while($row = $res->fetch_assoc()) { echo "<tr class='border-b border-slate-100'><td class='p-4'>{$row['id']}</td><td class='p-4'>".htmlspecialchars($row['title'] ?? '')."</td><td class='p-4 text-xs font-mono'>".htmlspecialchars($row['ad_type'] ?? '')."</td><td class='p-4'><button onclick=\"openModal('edit-ad-{$row['id']}')\" class='text-sky-500 hover:text-sky-700 font-bold text-xs ml-2'>تعديل</button><form method='POST' class='inline' onsubmit='return confirm(\"هل أنت متأكد؟\")'><input type='hidden' name='action' value='delete_ad'><input type='hidden' name='id' value='{$row['id']}'><button type='submit' class='text-rose-500 hover:text-rose-700 font-bold text-xs'>حذف</button></form></td></tr>"; } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- 8. معلومات الموقع -->
<div id="tab-info" class="tab-panel">
    <h2 class="text-xl font-black text-slate-800 mb-6">ℹ️ إعدادات الموقع العامة</h2>
    <?php showAlert(); ?>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <form action="admin.php" method="POST" class="space-y-4">
            <input type="hidden" name="action" value="update_settings">
            <div><label class="block text-xs font-bold text-slate-700 mb-1">اسم الموقع</label><input type="text" name="site_name" value="<?= isset($settings['site_name']) ? htmlspecialchars($settings['site_name']) : '' ?>" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm"></div>
            <div><label class="block text-xs font-bold text-slate-700 mb-1">رقم الواتساب</label><input type="text" name="whatsapp_number" value="<?= isset($settings['whatsapp_number']) ? htmlspecialchars($settings['whatsapp_number']) : '' ?>" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm"></div>
            <div><label class="block text-xs font-bold text-slate-700 mb-1">نص الفوتر</label><input type="text" name="footer_text" value="<?= isset($settings['footer_text']) ? htmlspecialchars($settings['footer_text']) : '' ?>" class="w-full border border-slate-300 rounded-lg p-2.5 text-sm"></div>
            <button type="submit" class="bg-sky-500 hover:bg-sky-600 text-white font-bold px-6 py-2.5 rounded-xl transition">حفظ التعديلات</button>
        </form>
    </div>
</div>

<!-- 9. الأمان وتغيير كلمة المرور -->
<div id="tab-admins" class="tab-panel">
    <div class="flex items-center gap-3 mb-8 border-b border-slate-200 pb-4">
        <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center text-2xl shadow-sm">🛡️</div>
        <div>
            <h2 class="text-xl font-black text-slate-800">الأمان وتغيير كلمة المرور</h2>
            <p class="text-xs font-bold text-slate-500 mt-1">حافظ على أمان لوحة التحكم الخاصة بك عن طريق تحديث كلمة المرور بشكل دوري.</p>
        </div>
    </div>
    
    <?php if(isset($success_msg)): ?><div class="bg-emerald-50 text-emerald-700 p-4 rounded-xl mb-6 font-bold border border-emerald-200 flex items-center gap-2"><span class="text-lg">✅</span> <?= $success_msg ?></div><?php endif; ?>
    <?php if(isset($error_msg)): ?><div class="bg-rose-50 text-rose-700 p-4 rounded-xl mb-6 font-bold border border-rose-200 flex items-center gap-2"><span class="text-lg">⚠️</span> <?= $error_msg ?></div><?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-200">
            <form action="admin.php?tab=admins" method="POST" class="space-y-5">
                <input type="hidden" name="action" value="change_password">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">كلمة المرور الحالية <span class="text-rose-500">*</span></label>
                    <div class="relative"><input type="password" name="current_password" required class="w-full pr-10 pl-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#0EA5E9] focus:border-[#0EA5E9] outline-none transition text-sm font-mono" placeholder="••••••••"><span class="absolute right-3.5 top-3.5 text-slate-400">🔒</span></div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">كلمة المرور الجديدة <span class="text-rose-500">*</span></label>
                    <div class="relative"><input type="password" name="new_password" required class="w-full pr-10 pl-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#0EA5E9] focus:border-[#0EA5E9] outline-none transition text-sm font-mono" placeholder="••••••••"><span class="absolute right-3.5 top-3.5 text-slate-400">🔑</span></div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">تأكيد كلمة المرور الجديدة <span class="text-rose-500">*</span></label>
                    <div class="relative"><input type="password" name="confirm_password" required class="w-full pr-10 pl-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#0EA5E9] focus:border-[#0EA5E9] outline-none transition text-sm font-mono" placeholder="••••••••"><span class="absolute right-3.5 top-3.5 text-slate-400">🔑</span></div>
                </div>
                <div class="pt-2"><button type="submit" class="w-full bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white font-black py-3.5 rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2"><span>💾</span><span>تحديث كلمة المرور</span></button></div>
            </form>
        </div>
        <div class="bg-gradient-to-br from-slate-800 to-[#0F172A] p-6 sm:p-8 rounded-3xl shadow-sm text-white relative overflow-hidden">
            <div class="absolute -top-10 -left-10 w-40 h-40 bg-sky-500/20 rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <h3 class="text-lg font-black text-amber-400 mb-4 flex items-center gap-2"><span>💡</span> نصائح لحماية حسابك</h3>
                <ul class="space-y-4 text-xs font-bold text-slate-300">
                    <li class="flex items-start gap-3"><span class="text-emerald-400 mt-0.5">✓</span><span>استخدم مزيجاً من الحروف الكبيرة والصغيرة والأرقام والرموز الخاصة.</span></li>
                    <li class="flex items-start gap-3"><span class="text-emerald-400 mt-0.5">✓</span><span>لا تستخدم نفس كلمة المرور في مواقع أو حسابات أخرى.</span></li>
                    <li class="flex items-start gap-3"><span class="text-emerald-400 mt-0.5">✓</span><span>قم بتغيير كلمة المرور كل 3 إلى 6 أشهر لضمان الأمان.</span></li>
                    <li class="flex items-start gap-3"><span class="text-emerald-400 mt-0.5">✓</span><span>لا تشارك بيانات الدخول الخاصة بلوحة التحكم مع أي شخص.</span></li>
                </ul>
                <div class="mt-8 bg-white/10 p-4 rounded-2xl border border-white/10">
                    <p class="text-[10px] text-slate-400 font-bold leading-relaxed">ملاحظة: يتم تشفير كلمة المرور الخاصة بك باستخدام خوارزميات تشفير قوية (Bcrypt) ولا يمكن لأحد الاطلاع عليها حتى مدير النظام.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 10. النوافذ المنبثقة (Modals) -->
<!-- ========================================== -->
<div id="add-uni-modal" class="hidden fixed inset-0 bg-slate-900/50 z-50 flex items-center justify-center p-4"><div class="bg-white rounded-2xl shadow-xl w-full max-w-lg"><div class="p-4 border-b flex justify-between"><h3 class="font-black">إضافة جامعة</h3><button onclick="closeModal('add-uni-modal')" class="text-xl">&times;</button></div><form action="admin.php" method="POST" class="p-6 space-y-4"><input type="hidden" name="action" value="add_uni"><div><label class="text-xs font-bold">اسم الجامعة</label><input type="text" name="name" required class="w-full border p-2 rounded"></div><div><label class="text-xs font-bold">شعار</label><input type="text" name="logo" class="w-full border p-2 rounded"></div><div><label class="text-xs font-bold">الوصف</label><textarea name="desc" class="w-full border p-2 rounded"></textarea></div><button type="submit" class="bg-teal-500 text-white font-bold px-6 py-2 rounded">حفظ</button></form></div></div>
<div id="add-course-modal" class="hidden fixed inset-0 bg-slate-900/50 z-50 flex items-center justify-center p-4"><div class="bg-white rounded-2xl shadow-xl w-full max-w-lg"><div class="p-4 border-b flex justify-between"><h3 class="font-black">إضافة مادة</h3><button onclick="closeModal('add-course-modal')" class="text-xl">&times;</button></div><form action="admin.php" method="POST" class="p-6 space-y-4"><input type="hidden" name="action" value="add_course"><div><label class="text-xs font-bold">اسم المادة</label><input type="text" name="title" required class="w-full border p-2 rounded"></div><div class="grid grid-cols-2 gap-4"><div><label class="text-xs font-bold">الكود</label><input type="text" name="code" class="w-full border p-2 rounded"></div><div><label class="text-xs font-bold">السعر</label><input type="number" step="0.01" name="price" required class="w-full border p-2 rounded"></div></div><div><label class="text-xs font-bold">الكلية</label><input type="text" name="faculty" value="كلية الهندسة" class="w-full border p-2 rounded"></div><button type="submit" class="bg-teal-500 text-white font-bold px-6 py-2 rounded">حفظ</button></form></div></div>
<div id="add-work-modal" class="hidden fixed inset-0 bg-slate-900/50 z-50 flex items-center justify-center p-4"><div class="bg-white rounded-2xl shadow-xl w-full max-w-lg"><div class="p-4 border-b flex justify-between"><h3 class="font-black">إضافة عمل</h3><button onclick="closeModal('add-work-modal')" class="text-xl">&times;</button></div><form action="admin.php" method="POST" class="p-6 space-y-4"><input type="hidden" name="action" value="add_work"><div><label class="text-xs font-bold">العنوان</label><input type="text" name="title" required class="w-full border p-2 rounded"></div><div><label class="text-xs font-bold">الكلية</label><input type="text" name="faculty" required class="w-full border p-2 rounded"></div><div><label class="text-xs font-bold">الوصف</label><textarea name="desc" class="w-full border p-2 rounded"></textarea></div><button type="submit" class="bg-teal-500 text-white font-bold px-6 py-2 rounded">حفظ</button></form></div></div>
<div id="add-lesson-modal" class="hidden fixed inset-0 bg-slate-900/50 z-50 flex items-center justify-center p-4"><div class="bg-white rounded-2xl shadow-xl w-full max-w-lg"><div class="p-4 border-b flex justify-between"><h3 class="font-black">إضافة محاضرة</h3><button onclick="closeModal('add-lesson-modal')" class="text-xl">&times;</button></div><form action="admin.php" method="POST" class="p-6 space-y-4"><input type="hidden" name="action" value="add_lesson"><div><label class="text-xs font-bold">المادة</label><select name="course_id" required class="w-full border p-2 rounded"><?php $res_c = $conn->query("SELECT id, title FROM courses"); while($c = $res_c->fetch_assoc()){ echo "<option value='{$c['id']}'>".htmlspecialchars($c['title'] ?? '')."</option>"; } ?></select></div><div><label class="text-xs font-bold">عنوان المحاضرة</label><input type="text" name="title" required class="w-full border p-2 rounded"></div><div><label class="text-xs font-bold">اسم ملف الفيديو</label><input type="text" name="video_filename" class="w-full border p-2 rounded"></div><button type="submit" class="bg-teal-500 text-white font-bold px-6 py-2 rounded">حفظ</button></form></div></div>
<div id="add-ad-modal" class="hidden fixed inset-0 bg-slate-900/50 z-50 flex items-center justify-center p-4"><div class="bg-white rounded-2xl shadow-xl w-full max-w-lg"><div class="p-4 border-b flex justify-between"><h3 class="font-black">إضافة إعلان</h3><button onclick="closeModal('add-ad-modal')" class="text-xl">&times;</button></div><form action="admin.php" method="POST" class="p-6 space-y-4"><input type="hidden" name="action" value="add_ad"><div><label class="text-xs font-bold">العنوان</label><input type="text" name="title" required class="w-full border p-2 rounded"></div><div class="grid grid-cols-2 gap-4"><div><label class="text-xs font-bold">النوع</label><select name="ad_type" class="w-full border p-2 rounded"><option value="slider">سلايدر</option><option value="ticker">شريط متحرك</option><option value="banner">بانر</option></select></div><div><label class="text-xs font-bold">رابط الصورة</label><input type="text" name="image_url" class="w-full border p-2 rounded"></div></div><button type="submit" class="bg-teal-500 text-white font-bold px-6 py-2 rounded">حفظ</button></form></div></div>

<?php
// نوافذ التعديل التلقائية
$res = $conn->query("SELECT * FROM universities"); while($row = $res->fetch_assoc()) { echo "<div id='edit-uni-{$row['id']}' class='hidden fixed inset-0 bg-slate-900/50 z-50 flex items-center justify-center p-4'><div class='bg-white rounded-2xl shadow-xl w-full max-w-lg'><div class='p-4 border-b flex justify-between'><h3 class='font-black'>تعديل جامعة</h3><button onclick=\"closeModal('edit-uni-{$row['id']}')\" class='text-xl'>&times;</button></div><form action='admin.php' method='POST' class='p-6 space-y-4'><input type='hidden' name='action' value='update_uni'><input type='hidden' name='id' value='{$row['id']}'><div><label class='text-xs font-bold'>الاسم</label><input type='text' name='name' value='".htmlspecialchars($row['name'] ?? '')."' required class='w-full border p-2 rounded'></div><div><label class='text-xs font-bold'>شعار</label><input type='text' name='logo' value='".htmlspecialchars($row['logo'] ?? '')."' class='w-full border p-2 rounded'></div><div><label class='text-xs font-bold'>الوصف</label><textarea name='desc' class='w-full border p-2 rounded'>".htmlspecialchars($row['desc'] ?? '')."</textarea></div><button type='submit' class='bg-sky-500 text-white font-bold px-6 py-2 rounded'>تحديث</button></form></div></div>"; }
$res = $conn->query("SELECT * FROM courses"); while($row = $res->fetch_assoc()) { echo "<div id='edit-course-{$row['id']}' class='hidden fixed inset-0 bg-slate-900/50 z-50 flex items-center justify-center p-4'><div class='bg-white rounded-2xl shadow-xl w-full max-w-lg'><div class='p-4 border-b flex justify-between'><h3 class='font-black'>تعديل مادة</h3><button onclick=\"closeModal('edit-course-{$row['id']}')\" class='text-xl'>&times;</button></div><form action='admin.php' method='POST' class='p-6 space-y-4'><input type='hidden' name='action' value='update_course'><input type='hidden' name='id' value='{$row['id']}'><div><label class='text-xs font-bold'>الاسم</label><input type='text' name='title' value='".htmlspecialchars($row['title'] ?? '')."' required class='w-full border p-2 rounded'></div><div class='grid grid-cols-2 gap-4'><div><label class='text-xs font-bold'>الكود</label><input type='text' name='code' value='".htmlspecialchars($row['code'] ?? '')."' class='w-full border p-2 rounded'></div><div><label class='text-xs font-bold'>السعر</label><input type='number' step='0.01' name='price' value='{$row['price']}' required class='w-full border p-2 rounded'></div></div><div><label class='text-xs font-bold'>الكلية</label><input type='text' name='faculty' value='".htmlspecialchars($row['faculty'] ?? '')."' class='w-full border p-2 rounded'></div><button type='submit' class='bg-sky-500 text-white font-bold px-6 py-2 rounded'>تحديث</button></form></div></div>"; }
$res = $conn->query("SELECT * FROM semester_works"); while($row = $res->fetch_assoc()) { echo "<div id='edit-work-{$row['id']}' class='hidden fixed inset-0 bg-slate-900/50 z-50 flex items-center justify-center p-4'><div class='bg-white rounded-2xl shadow-xl w-full max-w-lg'><div class='p-4 border-b flex justify-between'><h3 class='font-black'>تعديل عمل</h3><button onclick=\"closeModal('edit-work-{$row['id']}')\" class='text-xl'>&times;</button></div><form action='admin.php' method='POST' class='p-6 space-y-4'><input type='hidden' name='action' value='update_work'><input type='hidden' name='id' value='{$row['id']}'><div><label class='text-xs font-bold'>العنوان</label><input type='text' name='title' value='".htmlspecialchars($row['title'] ?? '')."' required class='w-full border p-2 rounded'></div><div><label class='text-xs font-bold'>الكلية</label><input type='text' name='faculty' value='".htmlspecialchars($row['faculty'] ?? '')."' class='w-full border p-2 rounded'></div><div><label class='text-xs font-bold'>الوصف</label><textarea name='desc' class='w-full border p-2 rounded'>".htmlspecialchars($row['desc'] ?? '')."</textarea></div><button type='submit' class='bg-sky-500 text-white font-bold px-6 py-2 rounded'>تحديث</button></form></div></div>"; }
$res = $conn->query("SELECT * FROM lessons"); while($row = $res->fetch_assoc()) { echo "<div id='edit-lesson-{$row['id']}' class='hidden fixed inset-0 bg-slate-900/50 z-50 flex items-center justify-center p-4'><div class='bg-white rounded-2xl shadow-xl w-full max-w-lg'><div class='p-4 border-b flex justify-between'><h3 class='font-black'>تعديل محاضرة</h3><button onclick=\"closeModal('edit-lesson-{$row['id']}')\" class='text-xl'>&times;</button></div><form action='admin.php' method='POST' class='p-6 space-y-4'><input type='hidden' name='action' value='update_lesson'><input type='hidden' name='id' value='{$row['id']}'><div><label class='text-xs font-bold'>العنوان</label><input type='text' name='title' value='".htmlspecialchars($row['title'] ?? '')."' required class='w-full border p-2 rounded'></div><div><label class='text-xs font-bold'>اسم ملف الفيديو</label><input type='text' name='video_filename' value='".htmlspecialchars($row['video_filename'] ?? '')."' class='w-full border p-2 rounded'></div><button type='submit' class='bg-sky-500 text-white font-bold px-6 py-2 rounded'>تحديث</button></form></div></div>"; }
$res = $conn->query("SELECT * FROM ads_banners"); while($row = $res->fetch_assoc()) { echo "<div id='edit-ad-{$row['id']}' class='hidden fixed inset-0 bg-slate-900/50 z-50 flex items-center justify-center p-4'><div class='bg-white rounded-2xl shadow-xl w-full max-w-lg'><div class='p-4 border-b flex justify-between'><h3 class='font-black'>تعديل إعلان</h3><button onclick=\"closeModal('edit-ad-{$row['id']}')\" class='text-xl'>&times;</button></div><form action='admin.php' method='POST' class='p-6 space-y-4'><input type='hidden' name='action' value='update_ad'><input type='hidden' name='id' value='{$row['id']}'><div><label class='text-xs font-bold'>العنوان</label><input type='text' name='title' value='".htmlspecialchars($row['title'] ?? '')."' required class='w-full border p-2 rounded'></div><div class='grid grid-cols-2 gap-4'><div><label class='text-xs font-bold'>النوع</label><select name='ad_type' class='w-full border p-2 rounded'><option value='slider' ".($row['ad_type']=='slider'?'selected':'').">سلايدر</option><option value='ticker' ".($row['ad_type']=='ticker'?'selected':'').">شريط متحرك</option><option value='banner' ".($row['ad_type']=='banner'?'selected':'').">بانر</option></select></div><div><label class='text-xs font-bold'>رابط الصورة</label><input type='text' name='image_url' value='".htmlspecialchars($row['image_url'] ?? '')."' class='w-full border p-2 rounded'></div></div><button type='submit' class='bg-sky-500 text-white font-bold px-6 py-2 rounded'>تحديث</button></form></div></div>"; }
?>

<?php include 'admin_footer.php'; ?>