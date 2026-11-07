<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'db.php';
require_once 'functions.php';

$settings = get_site_settings($conn);
$site_url = "http://localhost:8080/itqan_php/"; 
$admin_name = $_SESSION['admin_name'] ?? "المدير العام";
$admin_username = $_SESSION['admin_username'] ?? "admin";
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم المركزية | منصة إتقان</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Tajawal', sans-serif; background-color: #F1F5F9; }
        .admin-nav-btn {
            display: flex; align-items: center; gap: 0.75rem; padding: 0.8rem 1rem;
            border-radius: 0.875rem; font-weight: 800; font-size: 0.82rem;
            color: #94A3B8; transition: all 0.2s ease; width: 100%; text-align: right; cursor: pointer;
        }
        .admin-nav-btn:hover { background-color: rgba(255, 255, 255, 0.06); color: #F8FAFC; }
        .admin-nav-btn.active { background: linear-gradient(135deg, #0EA5E9 0%, #0284C7 100%); color: #FFFFFF; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.25); }
        .tab-panel { display: none; }
        .tab-panel.active { display: block; animation: fadeIn 0.2s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="text-slate-900 min-h-screen flex flex-col lg:flex-row">

    <!-- القائمة الجانبية -->
    <aside id="adminSidebar" class="w-full lg:w-72 bg-[#0F172A] text-white flex-shrink-0 flex flex-col justify-between p-5 z-40 lg:min-h-screen lg:sticky lg:top-0">
        <div>
            <div class="flex items-center justify-between pb-5 mb-5 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 bg-gradient-to-tr from-sky-500 to-teal-400 rounded-2xl flex items-center justify-center text-slate-950 font-black text-xl shadow-lg">إ</div>
                    <div>
                        <h1 class="font-black text-base text-white leading-tight">لوحة تحكم إتقان</h1>
                        <span class="text-[11px] text-emerald-400 font-bold flex items-center gap-1 mt-0.5"><span class="w-2 h-2 rounded-full bg-emerald-400 inline-block animate-pulse"></span><span>متصل ومحمي</span></span>
                    </div>
                </div>
                <button onclick="toggleMobileSidebar()" class="lg:hidden text-slate-400 hover:text-white text-xl p-1">✕</button>
            </div>

            <nav class="space-y-1.5" id="sidebarNav">
                <button type="button" onclick="openAdminTab('overview')" id="nav-overview" class="admin-nav-btn active"><span class="text-lg">📊</span><span>نظرة عامة</span></button>
                <button type="button" onclick="openAdminTab('enrollments')" id="nav-enrollments" class="admin-nav-btn"><span class="text-lg">🛒</span><span>طلبات التفعيل</span></button>
                <button type="button" onclick="openAdminTab('courses')" id="nav-courses" class="admin-nav-btn"><span class="text-lg">📚</span><span>إدارة المواد</span></button>
                <button type="button" onclick="openAdminTab('lessons')" id="nav-lessons" class="admin-nav-btn"><span class="text-lg">🎬</span><span>إدارة المحاضرات</span></button>
                <button type="button" onclick="openAdminTab('works')" id="nav-works" class="admin-nav-btn"><span class="text-lg">📝</span><span>إدارة الأعمال</span></button>
                <button type="button" onclick="openAdminTab('ads')" id="nav-ads" class="admin-nav-btn"><span class="text-lg">📢</span><span>الإعلانات والسلايدر</span></button>
                <button type="button" onclick="openAdminTab('unis')" id="nav-unis" class="admin-nav-btn"><span class="text-lg">🏛️</span><span>إدارة الجامعات</span></button>
                <button type="button" onclick="openAdminTab('info')" id="nav-info" class="admin-nav-btn"><span class="text-lg">ℹ️</span><span>معلومات الموقع</span></button>
                
                <!-- زر الأمان وكلمة المرور الذي كان ناقصاً -->
                <button type="button" onclick="openAdminTab('admins')" id="nav-admins" class="admin-nav-btn"><span class="text-lg">🛡️</span><span>الأمان وكلمة المرور</span></button>
            </nav>
        </div>

        <div class="pt-5 mt-6 border-t border-slate-800 space-y-2.5">
            <a href="<?= $site_url ?>" target="_blank" class="flex items-center justify-between p-3 rounded-xl bg-slate-800/90 hover:bg-slate-800 text-xs font-bold text-slate-200 transition"><span>🌐 معاينة الموقع</span><span>↖</span></a>
            <a href="admin.php?action=logout" class="flex items-center justify-center gap-2 p-3 rounded-xl bg-rose-500/15 text-rose-400 hover:bg-rose-500/25 border border-rose-500/20 text-xs font-black transition"><span>🔒</span><span>تسجيل الخروج</span></a>
        </div>
    </aside>

    <!-- المحتوى الرئيسي -->
    <div class="flex-grow flex flex-col min-w-0">
        <header class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between sticky top-0 z-30 shadow-xs">
            <div class="flex items-center gap-3">
                <button onclick="toggleMobileSidebar()" class="lg:hidden text-slate-700 font-bold p-2 bg-slate-100 rounded-xl"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg></button>
                <div>
                    <h2 class="text-sm sm:text-base font-black text-slate-900">نظام الإدارة والتحكم الشامل</h2>
                    <p class="text-[11px] font-bold text-slate-400">صلاحيات كاملة: إضافة • تعديل • حذف</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-2 bg-slate-50 border border-slate-200 px-3.5 py-1.5 rounded-full">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="text-xs font-black text-slate-700"><?= $admin_name ?></span>
                </div>
                <!-- زر كلمة المرور في الشريط العلوي للوصول السريع -->
                <button onclick="openAdminTab('admins')" class="text-xs font-black text-slate-700 bg-slate-100 hover:bg-slate-200 px-3.5 py-2 rounded-full transition">
                    🔑 كلمة المرور
                </button>
            </div>
        </header>

        <main class="p-4 sm:p-6 md:p-8 flex-grow max-w-7xl w-full mx-auto">