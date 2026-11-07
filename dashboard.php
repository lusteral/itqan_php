<?php
include 'header.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: student_login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// جلب المواد التي اشترك فيها الطالب
$enrollments = [];
$sql = "SELECT e.status, e.created_at, c.id as course_id, c.title, c.cover_img, c.code 
        FROM enrollments e 
        JOIN courses c ON e.course_id = c.id 
        WHERE e.user_id = ? 
        ORDER BY e.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
while($row = $res->fetch_assoc()) { $enrollments[] = $row; }
?>

<div class="max-w-4xl mx-auto px-4 py-10">
    <!-- ترحيب الطالب -->
    <div class="bg-gradient-to-l from-[#0B4B80] to-teal-600 rounded-3xl p-6 sm:p-8 text-white shadow-lg mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black mb-1">أهلاً بك، <?= htmlspecialchars($_SESSION['user_name']) ?> 👋</h1>
            <p class="text-xs text-teal-100 font-bold">هنا يمكنك متابعة اشتراكاتك والوصول إلى المحاضرات.</p>
        </div>
        <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center text-3xl border-2 border-white/30">
            🎓
        </div>
    </div>

    <!-- قائمة الاشتراكات -->
    <h2 class="text-xl font-black text-slate-900 mb-5">📚 مقرراتي (<?= count($enrollments) ?>)</h2>
    
    <div class="space-y-4">
        <?php if(!empty($enrollments)): ?>
            <?php foreach($enrollments as $e): ?>
                <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center gap-4 w-full">
                        <div class="w-14 h-14 bg-slate-100 rounded-xl flex items-center justify-center text-2xl shrink-0">
                            <?= !empty($e['cover_img']) ? '🖼️' : '📖' ?>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[10px] font-black bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md"><?= htmlspecialchars($e['code'] ?? 'CODE') ?></span>
                                <!-- حالة الطلب -->
                                <?php if($e['status'] === 'approved'): ?>
                                    <span class="text-[10px] font-black bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-md">✅ مشترك</span>
                                <?php else: ?>
                                    <span class="text-[10px] font-black bg-amber-100 text-amber-700 px-2 py-0.5 rounded-md">⏳ قيد المراجعة</span>
                                <?php endif; ?>
                            </div>
                            <h3 class="font-black text-sm text-slate-900"><?= htmlspecialchars($e['title']) ?></h3>
                        </div>
                    </div>
                    
                    <div class="w-full sm:w-auto shrink-0">
                        <?php if($e['status'] === 'approved'): ?>
                            <a href="course.php?id=<?= $e['course_id'] ?>" class="block w-full text-center bg-teal-50 border border-teal-200 text-teal-700 hover:bg-teal-100 text-xs font-black px-6 py-2.5 rounded-xl transition">دخول المقرر ▶</a>
                        <?php else: ?>
                            <a href="https://wa.me/<?= $wa_num ?>?text=أريد تفعيل اشتراكي في مادة <?= urlencode($e['title']) ?>" target="_blank" class="block w-full text-center bg-amber-50 border border-amber-200 text-amber-700 hover:bg-amber-100 text-xs font-black px-6 py-2.5 rounded-xl transition">طلب التفعيل 📲</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="bg-slate-50 rounded-2xl border border-slate-200 p-12 text-center">
                <span class="text-4xl mb-3 block">🛒</span>
                <p class="text-sm font-bold text-slate-500 mb-4">لم تشترك في أي مقرر حتى الآن.</p>
                <a href="index.php#courses" class="inline-block bg-slate-900 text-white text-xs font-black px-6 py-2.5 rounded-xl transition">تصفح المقررات الآن ‹</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>