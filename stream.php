<?php
session_start();
require_once 'db.php';

if (!isset($_GET['id'])) { die("خطأ"); }
$lesson_id = (int)$_GET['id'];

// جلب بيانات الدرس
$stmt = $conn->prepare("SELECT * FROM lessons WHERE id = ?");
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$l = $stmt->get_result()->fetch_assoc();

if (!$l || empty($l['video_filename'])) { die("الفيديو غير موجود."); }

// التحقق من الصلاحيات (أول درس مجاني، أو مشترك، أو أدمن)
$first_lesson_query = $conn->query("SELECT id FROM lessons WHERE course_id = {$l['course_id']} ORDER BY id ASC LIMIT 1")->fetch_assoc();
$is_first_lesson = ($first_lesson_query['id'] == $lesson_id);

$is_enrolled = false;
if (isset($_SESSION['user_id'])) {
    $stmt_check = $conn->prepare("SELECT status FROM enrollments WHERE user_id = ? AND course_id = ?");
    $stmt_check->bind_param("ii", $_SESSION['user_id'], $l['course_id']);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result()->fetch_assoc();
    if ($res_check && $res_check['status'] === 'approved') { $is_enrolled = true; }
}
$is_admin = isset($_SESSION['admin_id']);

if (!$is_first_lesson && !$is_enrolled && !$is_admin) {
    die("غير مصرح لك بمشاهدة هذا الفيديو.");
}

$file_path = 'uploads/' . $l['video_filename'];

if (file_exists($file_path)) {
    // إرسال الملف للمتصفح
    header('Content-Type: video/mp4');
    header('Content-Length: ' . filesize($file_path));
    readfile($file_path);
    exit;
} else {
    die("الملف غير موجود على السيرفر.");
}
?>