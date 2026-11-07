<?php
include 'header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { header("Location: index.php"); exit; }
$lesson_id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT l.*, c.title as cname FROM lessons l LEFT JOIN courses c ON l.course_id = c.id WHERE l.id = ?");
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$l = $stmt->get_result()->fetch_assoc();

if (!$l) { echo "<div class='text-center py-20 font-black text-2xl'>الدرس غير موجود</div>"; include 'footer.php'; exit; }

// التحقق من الصلاحيات
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
$can_access = $is_first_lesson || $is_enrolled || $is_admin;

if (!$can_access) {
    echo "<div class='text-center py-20 font-black text-rose-600'>يجب الاشتراك في المقرر لمشاهدة هذا الدرس.</div>";
    include 'footer.php';
    exit;
}

$video_file = $l['video_filename'] ?? '';
$pdf_file = $l['pdf_filename'] ?? '';
?>

<div class="max-w-4xl mx-auto px-4 py-8 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-teal-600"><?= htmlspecialchars($l['cname'] ?? '') ?></span>
            <h1 class="text-xl font-black text-slate-900 mt-0.5"><?= htmlspecialchars($l['title']) ?></h1>
        </div>
        <a href="course.php?id=<?= $l['course_id'] ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-full text-xs font-black">العودة للمادة &lsaquo;</a>
    </div>

    <div class="relative bg-slate-950 rounded-3xl overflow-hidden shadow-xl border border-slate-800">
        <?php if($video_file): ?>
            <video id="courseVideo" controls controlsList="nodownload" class="w-full max-h-[500px]">
            <source src="stream.php?id=<?= $lesson_id ?>" type="video/mp4">
                متصفحك لا يدعم تشغيل الفيديو.
            </video>
        <?php else: ?>
            <div class="p-12 text-center text-slate-400 font-bold text-sm">لا يوجد فيديو مرفق لهذا الدرس.</div>
        <?php endif; ?>
    </div>

    <div class="flex flex-wrap justify-center gap-3">
        <?php if($video_file): ?>
            <a href="uploads/<?= $video_file ?>" download class="bg-teal-500 hover:bg-teal-600 text-slate-900 font-black px-6 py-3 rounded-full text-xs flex items-center gap-2 shadow-md transition">
                <span>📥 تحميل الفيديو (للمشاهدة بدون إنترنت)</span>
            </a>
        <?php endif; ?>

        <?php if($pdf_file): ?>
            <a href="uploads/<?= $pdf_file ?>" download class="bg-blue-500 hover:bg-blue-600 text-white font-black px-6 py-3 rounded-full text-xs flex items-center gap-2 shadow-md transition">
                <span>📄 تحميل الملخص (PDF)</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- كود حفظ واستئناف الفيديو (Cookies / LocalStorage) -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const video = document.getElementById('courseVideo');
        if(video) {
            const videoId = 'video_progress_<?= $lesson_id ?>';
            const savedTime = localStorage.getItem(videoId);
            if (savedTime) { video.currentTime = parseFloat(savedTime); }
            setInterval(function() { if (!video.paused && !video.ended) { localStorage.setItem(videoId, video.currentTime); } }, 5000);
        }
    });
</script>

<?php include 'footer.php'; ?>