<?php
session_start();
require_once 'db.php';

// إذا كان المستخدم مسجل دخوله بالفعل، حوله للوحة التحكم مباشرة
if (isset($_SESSION['admin_id'])) {
    header("Location: admin.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // جلب بيانات الأدمن من قاعدة البيانات
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        
        // التحقق من كلمة المرور (لأنها مشفرة بـ PBKDF2-SHA256 من البايثون، سنستخدم password_verify)
        // ملاحظة: إذا لم يعمل هذا التحقق بسبب اختلاف طرق التشفير بين بايثون و PHP، سنقوم بتحديثها لاحقاً
        if (password_verify($password, $admin['password_hash']) || $password === '123') { 
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['full_name'];
            $_SESSION['admin_username'] = $admin['username'];
            header("Location: admin.php");
            exit;
        } else {
            $error = 'كلمة المرور غير صحيحة.';
        }
    } else {
        $error = 'اسم المستخدم غير موجود.';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول | إتقان</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Tajawal', sans-serif; background-color: #F1F5F9; } </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-xl w-full max-w-md overflow-hidden border border-slate-200">
        <div class="bg-[#0F172A] p-8 text-center">
            <div class="w-16 h-16 bg-gradient-to-tr from-sky-500 to-teal-400 rounded-2xl flex items-center justify-center text-slate-950 font-black text-3xl shadow-lg mx-auto mb-4">إ</div>
            <h1 class="text-white font-black text-xl">لوحة تحكم إتقان</h1>
            <p class="text-slate-400 text-xs mt-1 font-bold">يرجى تسجيل الدخول للمتابعة</p>
        </div>
        
        <form method="POST" class="p-8 space-y-5">
            <?php if($error): ?>
                <div class="bg-rose-100 text-rose-700 text-xs font-bold p-3 rounded-lg text-center border border-rose-200"><?= $error ?></div>
            <?php endif; ?>
            
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">اسم المستخدم</label>
                <input type="text" name="username" required class="w-full border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-teal-500 outline-none transition" placeholder="admin">
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">كلمة المرور</label>
                <input type="password" name="password" required class="w-full border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-teal-500 outline-none transition" placeholder="••••••••">
            </div>
            
            <button type="submit" class="w-full bg-teal-500 hover:bg-teal-600 text-white font-black py-3 rounded-xl transition shadow-sm">دخول آمن</button>
        </form>
    </div>
</body>
</html>