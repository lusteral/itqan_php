<?php
// استدعاء الهيدر (الذي يحتوي على الاتصال بقاعدة البيانات)
include 'header.php';

// جلب الإعدادات
$academy_name = $settings['academy_name'] ?? 'أكاديمية إتقان';
$hero_badge_1 = $settings['hero_badge_1'] ?? '🏆 المنصة التعليمية الأولى';
$hero_badge_2 = $settings['hero_badge_2'] ?? 'أكاديمية إتقان 2026';
$hero_title = $settings['hero_title'] ?? 'شروحات جامعية، أعمال فصلية، ومشاريع احترافية بامتياز A+ 🎯';
$hero_desc = $settings['hero_desc'] ?? 'نقدم أفضل الملخصات، الواجبات، الأنشطة والأعمال الفصلية المعتمدة لجميع الجامعات والمقررات باللغتين العربية والإنجليزية.';

// التحقق من تسجيل دخول الأدمن (إذا كان موجوداً، سيظهر أزرار التعديل)
$is_admin = isset($_SESSION['admin_id']);

// جلب الجامعات
$unis = [];
$res_unis = $conn->query("SELECT * FROM universities ORDER BY id DESC");
while($row = $res_unis->fetch_assoc()) { $unis[] = $row; }

// جلب المواد (مع اسم الجامعة وعدد الدروس)
$courses = [];
$sql_courses = "SELECT c.*, u.name as uni_name, (SELECT COUNT(*) FROM lessons l WHERE l.course_id = c.id) as lesson_count FROM courses c LEFT JOIN universities u ON c.uni_id = u.id ORDER BY c.id DESC";
$res_courses = $conn->query($sql_courses);
while($row = $res_courses->fetch_assoc()) { $courses[] = $row; }

// جلب الأعمال
$works = [];
$res_works = $conn->query("SELECT * FROM semester_works ORDER BY id DESC");
while($row = $res_works->fetch_assoc()) { $works[] = $row; }
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-14">
    
    <!-- 🏆 البوكس الترحيبي الإبداعي -->
    <div class="w-full flex justify-start">
        <div class="w-full lg:w-3/4 xl:w-2/3 relative overflow-hidden bg-gradient-to-l from-[#064E3B] via-[#0F766E] to-[#1E3A8A] rounded-[24px] sm:rounded-[32px] p-5 sm:p-9 text-white shadow-2xl border-r-4 sm:border-r-8 border-amber-400">
            <div class="absolute -top-16 -right-16 w-56 h-56 bg-amber-400/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-20 left-10 w-64 h-64 bg-teal-300/20 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 text-right space-y-3 sm:space-y-4">
                <div class="flex items-center justify-start gap-2 flex-wrap">
                    <span class="bg-gradient-to-r from-amber-300 to-amber-500 text-slate-950 font-black text-[11px] sm:text-xs px-3.5 py-1 rounded-full shadow-md"><?= htmlspecialchars($hero_badge_1) ?></span>
                    <span class="bg-white/15 backdrop-blur-md text-amber-200 text-[11px] sm:text-xs font-black px-3.5 py-1 rounded-full border border-white/25">✨ <?= htmlspecialchars($hero_badge_2) ?></span>
                </div>
                <h1 class="text-xl sm:text-3xl md:text-4xl font-black text-white leading-snug"><?= htmlspecialchars($hero_title) ?></h1>
                <p class="text-xs sm:text-sm text-emerald-50/95 font-bold leading-relaxed max-w-2xl"><?= htmlspecialchars($hero_desc) ?></p>
                <div class="pt-2 sm:pt-3 flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center justify-start gap-2.5">
                    <a href="https://wa.me/<?= $wa_num ?>" target="_blank" class="bg-amber-400 hover:bg-amber-300 text-slate-950 flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-black text-xs sm:text-sm shadow-lg transition">
                        <span>طلب عمل خاص / تواصل واتساب</span>
                    </a>
                    <a href="#works" class="bg-white/15 hover:bg-white/25 backdrop-blur-md text-white border border-white/30 px-5 py-3 rounded-2xl text-center text-xs sm:text-sm font-black transition">تصفح الأعمال الاحترافية ‹</a>
                    <a href="#courses" class="bg-white/10 hover:bg-white/20 backdrop-blur-md text-amber-200 border border-amber-300/30 px-5 py-3 rounded-2xl text-center text-xs sm:text-sm font-black transition">استعراض المقررات ‹</a>
                </div>
            </div>
        </div>
    </div>

    <!-- 📝 قسم الأعمال والمشاريع -->
    <div id="works" class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-slate-200 pb-4">
            <div>
                <span class="text-xs font-black text-teal-600 bg-teal-50 px-3 py-1 rounded-full border border-teal-200 inline-block mb-2">✨ معرض التميز الأكاديمي</span>
                <h2 class="text-xl sm:text-3xl font-black text-slate-900">الأعمال والمشاريع الاحترافية المميزة</h2>
                <p class="text-xs sm:text-sm text-slate-500 font-bold mt-1">نماذج احترافية من مشاريع التخرج، البحوث، حلول الواجبات، والتقارير الهندسية والبرمجية</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <?php if($is_admin): ?>
                    <a href="admin.php?tab=works" class="bg-slate-900 hover:bg-slate-800 text-amber-300 text-xs font-black px-4 py-2 rounded-full transition">⚙️ إدارة وإضافة الأعمال +</a>
                <?php endif; ?>
                <a href="semester-works.php" class="text-xs font-black text-white bg-teal-600 hover:bg-teal-700 px-5 py-2.5 rounded-2xl shadow-xs transition">عرض صفحة جميع الأعمال (<?= count($works) ?>) ‹</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-7">
            <?php if(!empty($works)): ?>
                <?php foreach($works as $idx => $w): ?>
                    <?php 
                        $wa_work_msg = "مرحباً، أود الاستفسار عن العمل الفصلي/المشروع ({$w['title']}) عبر منصة إتقان.";
                        $wa_work_link = "https://wa.me/{$wa_num}?text=" . urlencode($wa_work_msg);
                    ?>
                    <div class="group bg-white rounded-[28px] border border-slate-200/80 shadow-sm hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between overflow-hidden">
                        <div>
                            <div class="relative overflow-hidden h-52 bg-gradient-to-br from-teal-600 via-emerald-600 to-sky-700 flex flex-col items-center justify-center text-white">
                                <div class="w-16 h-16 rounded-2xl bg-white/15 backdrop-blur-md flex items-center justify-center text-3xl mb-2 border border-white/25">🚀</div>
                                <span class="text-xs font-black text-teal-100">مشروع أكاديمي معتمد</span>
                            </div>
                            <div class="p-5">
                                <h3 class="font-black text-base sm:text-lg text-slate-900 group-hover:text-emerald-700 transition mb-2"><?= htmlspecialchars($w['title'] ?? '') ?></h3>
                                <p class="text-xs text-slate-500 line-clamp-3 mb-4"><?= htmlspecialchars($w['desc'] ?? '') ?></p>
                            </div>
                        </div>
                        <div class="px-5 pb-5 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                            <a href="<?= $wa_work_link ?>" target="_blank" class="flex-grow bg-gradient-to-l from-emerald-600 to-teal-500 text-white text-xs font-black py-2.5 rounded-2xl flex items-center justify-center gap-2 transition">اطلب عمل مماثل</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-xs text-slate-400 py-8 col-span-3">لا توجد أعمال مضافة حالياً</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- 📚 قسم المقررات -->
    <div id="courses" class="space-y-6 pt-4">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-slate-200 pb-4">
            <div>
                <span class="text-xs font-black text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-200 inline-block mb-2">🎓 شروحات وملازم جامعية</span>
                <h2 class="text-xl sm:text-3xl font-black text-slate-900">أبرز المقررات والملخصات المعتمدة</h2>
                <p class="text-xs sm:text-sm text-slate-500 font-bold mt-1">شروحات فيديو مفصلة، ملازم مراجعة، وتجميعات اختبارات لضمان أعلى الدرجات</p>
            </div>
            <?php if($is_admin): ?>
                <a href="admin.php?tab=courses" class="bg-slate-900 hover:bg-slate-800 text-amber-300 text-xs font-black px-4 py-2 rounded-full transition">⚙️ إدارة وإضافة المقررات +</a>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-7">
            <?php if(!empty($courses)): ?>
                <?php foreach($courses as $c): ?>
                    <div class="group bg-white rounded-[28px] border border-slate-200/80 shadow-sm hover:shadow-2xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between overflow-hidden relative">
                        <div>
                            <div class="relative overflow-hidden h-52 bg-gradient-to-br from-indigo-600 via-blue-600 to-cyan-500 flex flex-col items-center justify-center p-6 text-center">
                                <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl border border-white/30 flex items-center justify-center text-3xl mb-2">📚</div>
                                <span class="text-white/90 text-xs font-black tracking-wide"><?= htmlspecialchars($c['code'] ?? 'ITQAN COURSE') ?></span>
                            </div>
                            <div class="p-5">
                                <h3 class="font-black text-lg text-slate-900 group-hover:text-teal-700 transition leading-snug mb-2"><?= htmlspecialchars($c['title'] ?? '') ?></h3>
                                <p class="text-xs text-slate-500 line-clamp-2 mb-4"><?= htmlspecialchars($c['desc'] ?? '') ?></p>
                            </div>
                        </div>
                        <div class="px-5 pb-5 pt-3 border-t border-slate-100 flex items-center justify-between gap-3">
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold block">السعر المعتمد</span>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-xl font-black text-slate-900"><?= number_format($c['price'] ?? 0, 0) ?></span>
                                    <span class="text-xs font-bold text-teal-600">ر.س</span>
                                </div>
                            </div>
                            <a href="course.php?id=<?= $c['id'] ?>" class="bg-gradient-to-l from-[#0F172A] to-[#0B4B80] hover:from-teal-500 hover:to-emerald-500 text-white hover:text-slate-950 text-xs font-black px-5 py-3 rounded-2xl shadow-md transition-all">تصفح المقرر ‹</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-xs text-slate-400 py-8 col-span-3">لا توجد مواد مضافة بعد</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- 🏛️ الجامعات المعتمدة -->
    <div id="unis" class="space-y-6 pt-6">
        <div class="text-center space-y-1 max-w-xl mx-auto">
            <span class="text-xs font-black text-amber-700 bg-amber-100 px-3.5 py-1 rounded-full inline-block mb-1">🏛️ الجامعات السعودية</span>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900">الجامعات المعتمدة بالمنصة</h2>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-4 sm:gap-5 pt-8">
            <?php if(!empty($unis)): ?>
                <?php foreach($unis as $idx => $u): 
                    $card_bg = ($idx % 2 == 1) ? "bg-[#1E293B] text-white" : "bg-[#F4F6F8] text-slate-900";
                    $text_title = ($idx % 2 == 1) ? "text-white" : "text-slate-900";
                    $text_sub = ($idx % 2 == 1) ? "text-slate-300" : "text-slate-500";
                ?>
                    <a href="uni.php?id=<?= $u['id'] ?>" class="relative block <?= $card_bg ?> rounded-[32px] pt-12 pb-6 px-6 text-center shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-slate-200/60">
                        <div class="absolute -top-7 left-1/2 -translate-x-1/2 w-16 h-16 bg-white rounded-2xl shadow-md flex items-center justify-center overflow-hidden border-4 border-white">
                            <span class="text-3xl"><?= htmlspecialchars($u['logo'] ?? '🏛️') ?></span>
                        </div>
                        <h3 class="font-black text-base mb-1 <?= $text_title ?>"><?= htmlspecialchars($u['name'] ?? '') ?></h3>
                        <p class="text-xs font-bold <?= $text_sub ?>">مادة تعليمية ‹</p>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-xs text-slate-400 py-8 col-span-4">لا توجد جامعات مضافة حالياً</p>
            <?php endif; ?>
        </div>
    </div>

</div>

<?php include 'footer.php'; ?>