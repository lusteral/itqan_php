<?php
include 'header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { header("Location: index.php"); exit; }
$course_id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT c.*, u.name as uname FROM courses c LEFT JOIN universities u ON c.uni_id = u.id WHERE c.id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$c = $stmt->get_result()->fetch_assoc();

if (!$c) { echo "<div class='text-center py-20 font-black text-2xl'>المقرر غير موجود</div>"; include 'footer.php'; exit; }

$lessons = [];
$stmt_l = $conn->prepare("SELECT * FROM lessons WHERE course_id = ? ORDER BY id ASC");
$stmt_l->bind_param("i", $course_id);
$stmt_l->execute();
$res_l = $stmt_l->get_result();
while($row = $res_l->fetch_assoc()) { $lessons[] = $row; }

// التحقق من اشتراك الطالب
$enrolled = false;
if (isset($_SESSION['user_id'])) {
    $stmt_check = $conn->prepare("SELECT status FROM enrollments WHERE user_id = ? AND course_id = ?");
    $stmt_check->bind_param("ii", $_SESSION['user_id'], $course_id);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result()->fetch_assoc();
    if ($res_check && $res_check['status'] === 'approved') { $enrolled = true; }
}

$academy_name = $settings['academy_name'] ?? 'أكاديمية إتقان التعليمية';
$academy_bio = $settings['academy_bio'] ?? 'نخبة من الأكاديميين...';
$students_count = $settings['students_count'] ?? '+1000';
$academy_rating = $settings['academy_rating'] ?? '5.0';
$legal_warning = $settings['legal_warning'] ?? 'تحذير قانوني : لا يجوز نشره أو مشاركته';

$cover_img = $c['cover_img'] ?? '';
$cover_tag = $cover_img ? "<img src='uploads/{$cover_img}' class='w-full h-56 object-cover rounded-[32px]'>" : "<div class='w-full h-56 bg-gradient-to-b from-[#38bdf8] to-[#60a5fa] rounded-[32px] flex items-center justify-center text-5xl'>🎯</div>";
$code_val = $c['code'] ?? '';
$faculty_val = $c['faculty'] ?? 'كلية الهندسة';
$target_val = $c['target_audience'] ?? 'طلاب وطالبات';
$old_p = $c['old_price'] ?? 0;
$mid_p = $c['mid_price'] ?? 100;
$mid_old = $c['mid_old_price'] ?? 150;
$fin_p = $c['final_price'] ?? 120;
$fin_old = $c['final_old_price'] ?? 170;
$is_admin = isset($_SESSION['admin_id']);
?>

<div class="max-w-md md:max-w-3xl mx-auto px-4 py-4 pb-28">
    <div class="flex items-center gap-1.5 text-[11px] font-bold text-slate-400 mb-3">
        <a href="index.php" class="hover:underline">الرئيسية</a><span>&rsaquo;</span><span class="text-slate-600"><?= htmlspecialchars($c['uname'] ?? '') ?></span>
    </div>

    <div class="mb-4"><?= $cover_tag ?></div>

    <div class="flex items-center justify-between mb-2">
        <h1 class="text-xl font-black text-slate-900"><?= htmlspecialchars($c['title']) ?> <?= htmlspecialchars($code_val) ?></h1>
        <div class="flex items-center gap-1 text-xs font-bold text-slate-700"><span><?= htmlspecialchars($academy_rating) ?></span><span class="text-amber-400">★</span></div>
    </div>

    <div class="bg-rose-50 border border-rose-100 rounded-2xl p-2.5 text-center text-[11px] font-bold text-rose-600 mb-4"><?= htmlspecialchars($legal_warning) ?></div>

    <div class="flex items-center gap-2 mb-4 flex-wrap">
        <span class="bg-[#F8CD46] text-slate-900 text-xs font-black px-4 py-1 rounded-full"><?= htmlspecialchars($c['uname'] ?? '') ?></span>
        <span class="bg-[#4AD7C5] text-slate-900 text-xs font-black px-4 py-1 rounded-full"><?= htmlspecialchars($faculty_val) ?></span>
        <span class="bg-[#8B5CF6] text-white text-xs font-black px-4 py-1 rounded-full"><?= htmlspecialchars($target_val) ?></span>
    </div>

    <div class="flex items-center justify-between mb-4">
        <div class="text-2xl font-black text-slate-900">
            <span id="display-price"><?= number_format($c['price'] ?? 0) ?></span> <span class="text-sm font-bold text-slate-500">ر.س</span>
            <span id="display-old-price" class="text-slate-400 line-through text-sm font-bold mr-1"><?= ($old_p > 0) ? number_format($old_p) : '' ?></span>
        </div>
        <button onclick="togglePackageModal()" class="bg-white border border-slate-200 rounded-2xl px-4 py-2 text-xs font-bold text-slate-700 flex items-center gap-2 shadow-xs"><span id="selected-package-label">المنهج كامل</span><span class="text-[10px] text-slate-400">▼</span></button>
    </div>

    <div class="space-y-3 mb-6">
        <a href="checkout.php?id=<?= $c['id'] ?>" class="bravo-teal-btn w-full block text-center py-3 rounded-full font-black text-slate-900 text-xs">الاشتراك في المقرر &lsaquo;</a>
    </div>

    <div class="grid grid-cols-4 gap-1.5 mb-6 text-center text-xs font-black">
        <button id="tab-btn-1" onclick="switchTab(1)" class="bg-[#1E293B] text-white py-2.5 rounded-2xl transition shadow-xs">محتوى المنهج</button>
        <button id="tab-btn-2" onclick="switchTab(2)" class="bg-[#F4F6F8] text-slate-700 py-2.5 rounded-2xl transition">تفاصيل المقرر</button>
        <button id="tab-btn-3" onclick="switchTab(3)" class="bg-[#F4F6F8] text-slate-700 py-2.5 rounded-2xl transition">الجهة الشارحة</button>
        <button id="tab-btn-4" onclick="switchTab(4)" class="bg-[#F4F6F8] text-slate-700 py-2.5 rounded-2xl transition">التقييمات</button>
    </div>

    <div id="tab-content-1" class="space-y-4">
        <div class="bg-white rounded-3xl border border-slate-100 shadow-xs overflow-hidden">
            <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between"><span class="font-black text-xs text-slate-800">قائمة الشروحات والملخصات</span><span class="text-xs font-bold text-slate-400"><?= count($lessons) ?> درس</span></div>
            <div class="divide-y divide-slate-100">
                <?php if(!empty($lessons)): ?>
                    <?php foreach($lessons as $idx => $l): 
                        $is_first = ($idx === 0);
                        $can_access = $is_first || $enrolled || $is_admin;
                        $btn_video = !empty($l['video_filename']) ? ($can_access ? "<a href='watch.php?id={$l['id']}' class='bravo-teal-btn text-[11px] font-black px-3.5 py-1.5 rounded-full text-slate-900'>الشرح ▶</a>" : "<span class='text-[11px] text-slate-400 font-bold bg-slate-100 px-2.5 py-1.5 rounded-full border border-slate-200'>🔒 مقفل</span>") : "";
                        $btn_pdf = !empty($l['pdf_filename']) ? ($can_access ? "<a href='uploads/{$l['pdf_filename']}' target='_blank' class='bg-blue-50 text-blue-700 border border-blue-200 text-[11px] font-black px-3 py-1.5 rounded-full hover:bg-blue-100'>الملخص 📄</a>" : "<span class='text-[11px] text-slate-400 font-bold bg-slate-100 px-2.5 py-1.5 rounded-full border border-slate-200'>🔒 ملخص</span>") : "";
                        $preview_badge = $is_first ? "<span class='text-[10px] bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-md font-black mr-2'>تجريبي مجاناً</span>" : "";
                    ?>
                    <div class="p-3.5 flex items-center justify-between hover:bg-slate-50 transition">
                        <div class="flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-slate-100 text-slate-800 text-xs font-black flex items-center justify-center"><?= $idx + 1 ?></span><span class="text-xs font-bold text-slate-800"><?= htmlspecialchars($l['title']) ?></span><?= $preview_badge ?></div>
                        <div class="flex items-center gap-1.5"><?= $btn_pdf ?><?= $btn_video ?></div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?><p class="p-6 text-xs text-slate-400 text-center">لا توجد دروس مرفوعة بعد</p><?php endif; ?>
            </div>
        </div>
    </div>

    <div id="tab-content-2" class="hidden space-y-4"><div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-xs"><h3 class="font-black text-sm text-slate-900 mb-2">📖 وصف المقرر:</h3><p class="text-xs text-slate-600 leading-relaxed"><?= nl2br(htmlspecialchars($c['desc'] ?? '')) ?></p></div></div>
    
    <div id="tab-content-3" class="hidden space-y-4"><div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xs text-center"><div class="w-14 h-14 rounded-full bg-[#0B4B80] text-white flex items-center justify-center font-black text-xl mx-auto mb-3">إ</div><h2 class="text-lg font-black text-slate-900 mb-1"><?= htmlspecialchars($academy_name) ?></h2><div class="flex justify-center gap-4 text-xs font-bold text-slate-500 mb-4"><span>👥 <?= htmlspecialchars($students_count) ?> طالب</span><span>📚 أعلى جودة</span></div><p class="text-xs text-slate-600 leading-relaxed mb-4"><?= htmlspecialchars($academy_bio) ?></p></div></div>
    
    <div id="tab-content-4" class="hidden space-y-4"><div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xs text-center"><span class="text-4xl font-black text-slate-900 block mb-1"><?= htmlspecialchars($academy_rating) ?></span><div class="flex justify-center text-amber-400 text-lg mb-2">★★★★★</div><span class="text-xs text-slate-400 font-bold block mb-4">تقييم الطلاب المعتمد</span></div></div>
</div>

<div id="package-modal" class="fixed inset-0 z-50 flex items-end justify-center hidden">
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs" onclick="togglePackageModal()"></div>
    <div class="relative bg-white w-full max-w-md rounded-t-[36px] p-6 z-10 shadow-2xl space-y-4">
        <div class="w-12 h-1 bg-slate-200 rounded-full mx-auto mb-2"></div><h3 class="text-center font-black text-sm text-slate-800 mb-4">اختر باقة المقرر</h3>
        <div onclick="selectPackage(<?= $c['price'] ?>, <?= $old_p ?>, 'المنهج كامل')" class="flex items-center justify-between p-4 rounded-2xl border-2 border-teal-400 bg-teal-50/50 cursor-pointer"><span class="font-black text-xs text-slate-800">المنهج كامل</span><span class="font-black text-sm text-slate-900"><?= number_format($c['price']) ?> ر.س</span></div>
        <div onclick="selectPackage(<?= $mid_p ?>, <?= $mid_old ?>, 'الميد')" class="flex items-center justify-between p-4 rounded-2xl border border-slate-200 hover:border-slate-800 transition cursor-pointer"><span class="font-black text-xs text-slate-800">الميد</span><span class="font-black text-sm text-slate-900"><?= number_format($mid_p) ?> ر.س</span></div>
        <div onclick="selectPackage(<?= $fin_p ?>, <?= $fin_old ?>, 'الفاينل')" class="flex items-center justify-between p-4 rounded-2xl border border-slate-200 hover:border-slate-800 transition cursor-pointer"><span class="font-black text-xs text-slate-800">الفاينل</span><span class="font-black text-sm text-slate-900"><?= number_format($fin_p) ?> ر.س</span></div>
    </div>
</div>

<div class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-slate-100 p-3 z-40">
    <div class="max-w-md mx-auto flex items-center justify-between gap-3">
        <a href="checkout.php?id=<?= $c['id'] ?>" class="bravo-teal-btn flex-grow py-3 rounded-full text-center text-slate-900 font-black text-xs shadow-md">الاشتراك في المقرر &lsaquo;</a>
        <div class="bg-slate-100 px-3.5 py-2 rounded-2xl text-[11px] font-bold text-slate-600 whitespace-nowrap">📖 <?= count($lessons) ?> درس</div>
        <a href="https://wa.me/<?= $wa_num ?>" target="_blank" class="w-10 h-10 bg-[#25D366] border-2 border-slate-900 rounded-full flex items-center justify-center shrink-0 shadow-xs hover:scale-105 transition"><svg class="w-5 h-5 fill-white" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg></a>
    </div>
</div>

<script>
    function switchTab(num) { for (var i = 1; i <= 4; i++) { document.getElementById('tab-content-' + i).classList.add('hidden'); document.getElementById('tab-btn-' + i).className = 'bg-[#F4F6F8] text-slate-700 py-2.5 rounded-2xl transition'; } document.getElementById('tab-content-' + num).classList.remove('hidden'); document.getElementById('tab-btn-' + num).className = 'bg-[#1E293B] text-white py-2.5 rounded-2xl transition shadow-xs'; }
    function togglePackageModal() { document.getElementById('package-modal').classList.toggle('hidden'); }
    function selectPackage(price, oldPrice, label) { document.getElementById('display-price').innerText = price; document.getElementById('display-old-price').innerText = oldPrice > 0 ? oldPrice : ''; document.getElementById('selected-package-label').innerText = label; togglePackageModal(); }
</script>
<?php include 'footer.php'; ?>