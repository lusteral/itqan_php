<?php
include 'header.php';

// جلب جميع الأعمال
$works = [];
$res_works = $conn->query("SELECT * FROM semester_works ORDER BY id DESC");
while($row = $res_works->fetch_assoc()) { $works[] = $row; }
$is_admin = isset($_SESSION['admin_id']);
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
    <!-- عنوان الصفحة -->
    <div class="text-center mb-10 space-y-2">
        <span class="text-3xl block">📝</span>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900">الأعمال والمشاريع الاحترافية المميزة</h1>
        <p class="text-xs sm:text-sm text-slate-500 max-w-lg mx-auto">تصفح التكاليف والأنشطة، مشاريع التخرج، والملخصات الفصلية المعتمدة لجميع التخصصات.</p>
    </div>

    <!-- شبكة الأعمال -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">
        <?php if(!empty($works)): ?>
            <?php foreach($works as $w): 
                $wa_msg = "مرحباً، أود الطلب والاستفسار عن العمل الفصلي ({$w['title']}) باسم الطالب عبر منصة إتقان.";
                $wa_link = "https://wa.me/{$wa_num}?text=" . urlencode($wa_msg);
                $file_btn = !empty($w['file_name']) ? "<a href='uploads/{$w['file_name']}' target='_blank' class='bg-slate-100 hover:bg-teal-50 text-slate-800 hover:text-teal-700 border border-slate-200 text-xs font-black px-4 py-2.5 rounded-2xl transition'>تحميل المرفق 📥</a>" : "";
            ?>
                <div class="group bg-white rounded-[28px] border border-slate-200/80 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden">
                    <div>
                        <div class="relative overflow-hidden h-52 bg-gradient-to-br from-teal-600 via-emerald-600 to-sky-700 flex flex-col items-center justify-center text-white p-6">
                            <div class="w-16 h-16 rounded-2xl bg-white/15 flex items-center justify-center text-3xl mb-2">🚀</div>
                            <span class="text-xs font-black text-teal-100">مشروع أكاديمي معتمد</span>
                        </div>
                        <div class="p-5">
                            <span class="bg-amber-400 text-slate-950 text-[11px] font-black px-3 py-1 rounded-full inline-block mb-2"><?= htmlspecialchars($w['faculty'] ?? 'الكلية') ?></span>
                            <h3 class="font-black text-lg text-slate-900 mb-2"><?= htmlspecialchars($w['title'] ?? '') ?></h3>
                            <p class="text-xs text-slate-500 leading-relaxed mb-4 line-clamp-3"><?= htmlspecialchars($w['desc'] ?? '') ?></p>
                        </div>
                    </div>
                    <div class="px-5 pb-5 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                        <a href="<?= $wa_link ?>" target="_blank" class="flex-grow bg-gradient-to-l from-emerald-600 to-teal-500 text-white text-xs font-black py-2.5 px-4 rounded-2xl flex items-center justify-center gap-1.5 shadow-sm">طلب عبر الواتساب</a>
                        <?= $file_btn ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="bg-slate-50 p-8 rounded-3xl text-center text-xs text-slate-400 col-span-3">لا توجد أعمال فصلية منشورة حالياً</div>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>