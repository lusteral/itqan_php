<?php
// بدء الجلسة لمعرفة هل الطالب مسجل دخول أم لا
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';
require_once 'functions.php';

$settings = get_site_settings($conn);
$wa_num = isset($settings['whatsapp_number']) ? $settings['whatsapp_number'] : '966593521664';
$ticker_ads = get_ads($conn, 'ticker');
$slider_ads = get_ads($conn, 'slider');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= isset($settings['site_name']) ? htmlspecialchars($settings['site_name']) : 'إتقان | المنصة التعليمية' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <style>
        html, body { font-family: 'Tajawal', sans-serif; background-color: #FFFFFF; overflow-x: hidden !important; max-width: 100vw !important; width: 100%; margin: 0; padding: 0; }
        *, *::before, *::after { box-sizing: border-box; }
        img, svg, video { max-width: 100%; height: auto; }
        #splash-screen { position: fixed; inset: 0; background: #ffffff; z-index: 99999; display: flex; flex-direction: column; align-items: center; justify-content: center; transition: opacity 0.5s ease, transform 0.5s ease; }
        .draw-path { stroke-dasharray: 1000; stroke-dashoffset: 1000; animation: drawLine 1.6s cubic-bezier(0.65, 0, 0.35, 1) forwards; }
        .draw-path-delay { stroke-dasharray: 600; stroke-dashoffset: 600; animation: drawLine 1.4s cubic-bezier(0.65, 0, 0.35, 1) 0.3s forwards; }
        .fill-fade { opacity: 0; animation: fillFadeIn 0.6s ease 1.1s forwards; }
        @keyframes drawLine { to { stroke-dashoffset: 0; } }
        @keyframes fillFadeIn { to { opacity: 1; } }
        .bravo-teal-btn { background-color: #4AD7C5; border: 2px solid #0F172A; box-shadow: 0 4px 0 #0F172A; transition: all 0.1s ease; }
        .bravo-teal-btn:active { transform: translateY(3px); box-shadow: 0 1px 0 #0F172A; }
        .bravo-yellow-btn { background-color: #F8CD46; border: 2px solid #0F172A; box-shadow: 0 4px 0 #0F172A; transition: all 0.1s ease; }
        .bravo-yellow-btn:active { transform: translateY(3px); box-shadow: 0 1px 0 #0F172A; }
        .bravo-whatsapp-btn { background-color: #25D366; color: #FFFFFF; border: 2px solid #0F172A; box-shadow: 0 4px 0 #0F172A; transition: all 0.1s ease; }
        .bravo-whatsapp-btn:active { transform: translateY(3px); box-shadow: 0 1px 0 #0F172A; }
        .bravo-red-btn { background-color: #E11D48; border: 2px solid #0F172A; box-shadow: 0 4px 0 #0F172A; }
        @keyframes marquee { 0% { transform: translateX(100%); } 100% { transform: translateX(-100%); } }
        .animate-marquee { display: inline-block; white-space: nowrap; animation: marquee 25s linear infinite; }
        .animate-marquee:hover { animation-play-state: paused; }
        @keyframes whatsappPulse { 0% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.6), 0 4px 0 #0f172a; } 70% { box-shadow: 0 0 0 14px rgba(37, 211, 102, 0), 0 4px 0 #0f172a; } 100% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0), 0 4px 0 #0f172a; } }
        .whatsapp-pulse-btn { animation: whatsappPulse 2s infinite; }
    </style>
</head>
<body class="text-slate-900 min-h-screen flex flex-col justify-between">

    <div id="splash-screen">
        <div class="relative w-40 sm:w-48 h-40 sm:h-48 flex items-center justify-center">
            <svg class="w-full h-full" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path class="draw-path" d="M100 25 L160 55 V110 C160 148 100 178 100 178 C100 178 40 148 40 110 V55 Z" stroke="#0B4B80" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                <path class="draw-path-delay" d="M100 45 L145 65 L100 85 L55 65 Z" stroke="#4AD7C5" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                <ellipse class="draw-path" cx="100" cy="115" rx="55" ry="18" stroke="#F59E0B" stroke-width="4" transform="rotate(-15 100 115)"/>
                <path class="draw-path-delay" d="M60 140 C80 135 100 145 100 145 C100 145 120 135 140 140" stroke="#0B4B80" stroke-width="5" stroke-linecap="round"/>
                <text class="fill-fade" x="100" y="195" text-anchor="middle" font-family="'Tajawal', sans-serif" font-size="20" font-weight="900" fill="#0B4B80">إتـقـان</text>
            </svg>
        </div>
    </div>

    <!-- ⚡ بنر الشريط الإعلاني -->
    <div id="promo-banner" class="bg-gradient-to-r from-amber-400 via-amber-300 to-emerald-300 text-slate-950 py-2 px-3 sm:px-4 text-xs font-black shadow-sm relative overflow-hidden border-b border-amber-400">
        <div class="max-w-7xl mx-auto w-full flex items-center justify-between gap-2 sm:gap-4">
            <div class="flex items-center gap-1.5 shrink-0">
                <span class="bg-slate-900 text-amber-300 text-[10px] font-black px-2.5 py-0.5 rounded-full border border-amber-400 shadow-xs animate-pulse"><?= isset($settings['promo_badge']) ? $settings['promo_badge'] : '🔥 عروض حصريّة' ?></span>
                <span class="hidden md:inline-block font-black text-slate-900 text-xs"><?= isset($settings['promo_discount']) ? $settings['promo_discount'] : 'خصم 20%' ?></span>
            </div>

            <div class="overflow-hidden relative flex-grow text-center font-bold text-[11px] sm:text-[12px] text-slate-900 mx-1 sm:mx-2 min-w-0">
                <div class="inline-flex items-center gap-6 animate-marquee">
                    <?php if (!empty($ticker_ads)): ?>
                        <?php foreach ($ticker_ads as $t): ?>
                            <span><?= htmlspecialchars($t['title']) ?></span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span>⚡ تصفح الملازم والأعمال الفصلية!</span>
                        <span>🎓 تواصل معنا مباشرة عبر الواتساب</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="hidden sm:flex items-center gap-2 shrink-0">
                <a href="https://wa.me/<?= $wa_num ?>" target="_blank" class="bg-slate-900 hover:bg-slate-800 text-white text-[11px] font-black px-3 py-1 rounded-full flex items-center gap-1.5 shadow-xs transition">
                    <span>طلب بالواتساب</span>
                </a>
            </div>
        </div>
    </div>

    <!-- شريط التنقل العلوي -->
    <nav class="bg-white sticky top-0 z-40 border-b border-slate-100 shadow-xs">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- الشعار -->
                <div class="flex items-center gap-2">
                    <a href="index.php" class="flex items-center gap-1 font-black text-xl text-[#0B4B80] tracking-tighter">
                        <span>ITQAN</span>
                        <span class="text-xs bg-[#4AD7C5] px-1.5 py-0.5 rounded-md border border-slate-900 text-slate-900 font-black">Me</span>
                    </a>
                </div>

                <!-- الروابط -->
                <div class="hidden md:flex items-center gap-1">
                    <a href="index.php" class="px-3 py-2 text-xs font-bold text-slate-700 hover:text-teal-600 hover:bg-slate-50 rounded-full transition">الرئيسية</a>
                    <a href="semester-works.php" class="px-3 py-2 text-xs font-bold text-slate-700 hover:text-teal-600 hover:bg-slate-50 rounded-full transition">الأعمال والواجبات</a>
                    <a href="index.php#unis" class="px-3 py-2 text-xs font-bold text-slate-700 hover:text-teal-600 hover:bg-slate-50 rounded-full transition">الجامعات</a>
                    <a href="index.php#courses" class="px-3 py-2 text-xs font-bold text-slate-700 hover:text-teal-600 hover:bg-slate-50 rounded-full transition">المواد</a>
                    <a href="#" class="px-3 py-2 text-xs font-bold text-slate-700 hover:text-teal-600 hover:bg-slate-50 rounded-full transition">عن الأكاديمية</a>
                </div>

                <!-- الأزرار اليمنى -->
                <div class="flex items-center gap-2">
                    <a href="https://wa.me/<?= $wa_num ?>" target="_blank" class="hidden sm:flex items-center gap-1.5 text-xs font-black text-slate-900 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 px-3 py-1.5 rounded-full transition shadow-xs">
                        <span>تواصل واتساب</span>
                    </a>

                    <!-- زر دخول الطالب / الخروج / حسابي -->
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="flex items-center gap-2 bg-teal-50 border border-teal-200 px-3 py-1.5 rounded-full">
                            <a href="dashboard.php" class="text-xs font-black text-teal-800 hover:text-teal-600 transition">👤 حسابي</a>
                            <span class="text-slate-300">|</span>
                            <a href="logout.php" class="text-[10px] font-bold text-rose-500 hover:underline">خروج</a>
                        </div>
                    <?php else: ?>
                        <a href="student_login.php" class="text-xs font-black text-slate-700 bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-full transition">دخول الطلاب</a>
                    <?php endif; ?>

                    <!-- زر فتح قائمة الهاتف -->
                    <button type="button" onclick="toggleMobileNav()" class="md:hidden p-2 rounded-xl text-slate-700 hover:bg-slate-100 transition focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>
            </div>

            <!-- قائمة الهاتف المنسدلة -->
            <div id="mobileNavMenu" class="hidden md:hidden border-t border-slate-100 py-3 space-y-1">
                <a href="index.php" onclick="toggleMobileNav()" class="block px-4 py-2.5 rounded-xl text-xs font-bold text-slate-800 hover:bg-teal-50 hover:text-teal-700 transition">🏠 الرئيسية</a>
                <a href="semester-works.php" onclick="toggleMobileNav()" class="block px-4 py-2.5 rounded-xl text-xs font-bold text-slate-800 hover:bg-teal-50 hover:text-teal-700 transition">📝 الأعمال والواجبات والمشاريع</a>
                <a href="index.php#unis" onclick="toggleMobileNav()" class="block px-4 py-2.5 rounded-xl text-xs font-bold text-slate-800 hover:bg-teal-50 hover:text-teal-700 transition">🏛️ الجامعات المعتمدة</a>
                <a href="index.php#courses" onclick="toggleMobileNav()" class="block px-4 py-2.5 rounded-xl text-xs font-bold text-slate-800 hover:bg-teal-50 hover:text-teal-700 transition">📚 المواد والمقررات</a>
                <div class="pt-2 border-t border-slate-100 px-2 flex gap-2">
                    <a href="https://wa.me/<?= $wa_num ?>" target="_blank" class="flex-grow bg-[#25D366] text-white text-center py-2.5 rounded-xl text-xs font-black flex items-center justify-center gap-1.5 shadow-sm">
                        <span>محادثة واتساب 📲</span>
                    </a>
                    <?php if(isset($_SESSION['admin_id'])): ?>
                        <a href="admin.php" class="bg-slate-900 text-amber-300 py-2.5 px-3.5 rounded-xl text-xs font-black text-center">
                            لوحة الإدارة 🔐
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- 🖼️ السلايدر الرئيسي -->
    <section class="w-full relative overflow-hidden bg-slate-950 border-b border-slate-800">
        <div class="relative w-full overflow-hidden bg-slate-950">
            <div id="projectCarousel" class="flex transition-transform duration-500 ease-out w-full">
                <?php if (!empty($slider_ads)): ?>
                    <?php foreach ($slider_ads as $s): ?>
                    <div class="w-full flex-shrink-0 relative h-[310px] sm:h-[400px] md:h-[500px]">
                        <?php 
                            $img_src = (strpos($s['image_url'], 'http') === 0) ? $s['image_url'] : ('uploads/' . $s['image_url']); 
                            if(empty($s['image_url'])) $img_src = 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1600&q=80';
                        ?>
                        <img src="<?= $img_src ?>" alt="<?= htmlspecialchars($s['title']) ?>" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/50 to-slate-950/20 flex flex-col justify-end">
                            <div class="max-w-7xl mx-auto w-full px-4 sm:px-8 pb-9 sm:pb-14 flex flex-col md:flex-row md:items-end justify-between gap-3">
                                <div class="max-w-2xl">
                                    <?php if ($s['badge']): ?>
                                    <span class="bg-amber-400 text-slate-950 text-[10px] sm:text-xs font-black px-3 py-0.5 rounded-full inline-block mb-1.5 shadow-sm"><?= htmlspecialchars($s['badge']) ?></span>
                                    <?php endif; ?>
                                    <h3 class="text-base sm:text-2xl md:text-3xl font-black text-white leading-snug"><?= htmlspecialchars($s['title']) ?></h3>
                                    <?php if ($s['subtitle']): ?>
                                    <p class="text-xs sm:text-sm text-slate-200 font-bold mt-1 line-clamp-2"><?= htmlspecialchars($s['subtitle']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="w-full flex-shrink-0 relative h-[310px] sm:h-[400px] md:h-[500px]">
                        <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1600&q=80" alt="افتراضي" class="w-full h-full object-cover">
                    </div>
                <?php endif; ?>
            </div>
            <!-- أسهم التنقل -->
            <button onclick="prevSlide()" class="absolute top-1/2 right-2 sm:right-6 -translate-y-1/2 w-8 h-8 sm:w-11 sm:h-11 rounded-full bg-slate-900/80 hover:bg-amber-400 hover:text-slate-950 text-white font-black text-sm sm:text-xl flex items-center justify-center backdrop-blur-md border border-white/20 transition shadow-lg z-20">&rsaquo;</button>
            <button onclick="nextSlide()" class="absolute top-1/2 left-2 sm:left-6 -translate-y-1/2 w-8 h-8 sm:w-11 sm:h-11 rounded-full bg-slate-900/80 hover:bg-amber-400 hover:text-slate-950 text-white font-black text-sm sm:text-xl flex items-center justify-center backdrop-blur-md border border-white/20 transition shadow-lg z-20">&lsaquo;</button>
            <!-- نقاط التنقل -->
            <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex items-center gap-1.5 z-20">
                <?php $slide_count = (!empty($slider_ads)) ? count($slider_ads) : 1; ?>
                <?php for($i = 0; $i < $slide_count; $i++): ?>
                <span onclick="goToSlide(<?= $i ?>)" class="slide-dot w-2.5 h-2.5 rounded-full <?= $i == 0 ? 'bg-amber-400' : 'bg-white/40' ?> cursor-pointer transition"></span>
                <?php endfor; ?>
            </div>
        </div>
    </section>

    <main class="flex-grow w-full">