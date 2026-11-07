<?php
session_start();
require_once 'db.php';

// إذا كان الطالب مسجل دخوله بالفعل، حوله للرئيسية
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    
    if (!empty($name)) {
        // التحقق مما إذا كان الطالب موجوداً مسبقاً
        $stmt = $conn->prepare("SELECT * FROM users WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $user_id = $user['id'];
        } else {
            // إنشاء حساب جديد للطالب
            $stmt_ins = $conn->prepare("INSERT INTO users (name, is_admin) VALUES (?, 0)");
            $stmt_ins->bind_param("s", $name);
            $stmt_ins->execute();
            $user_id = $conn->insert_id;
        }
        
        // تسجيل الدخول بنجاح
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $name;
        
        // إعادة توجيه الطالب للصفحة التي كان يريدها (مثلاً صفحة الدفع)
        $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
        unset($_SESSION['redirect_after_login']);
        header("Location: $redirect");
        exit;
    } else {
        $error = 'يرجى إدخال اسمك الثلاثي.';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول | منصة إتقان</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <style> body { font-family: 'Tajawal', sans-serif; background-color: #F1F5F9; } </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-[32px] shadow-xl w-full max-w-sm p-8 border border-slate-200 text-center">
        <div class="w-16 h-16 bg-white border-2 border-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm">
            <span class="text-2xl font-black text-[#0B4B80]">إ</span>
        </div>
        
        <h2 class="text-xl font-black mb-2 text-slate-900">تسجيل الدخول للطلاب</h2>
        <p class="text-xs text-slate-500 font-bold mb-6">اكتب اسمك الثلاثي للوصول السريع لحسابك ومتابعة اشتراكاتك</p>
        
        <?php if($error): ?>
            <div class="bg-rose-100 text-rose-700 text-xs font-bold p-3 rounded-xl mb-4"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-3">
            <input type="text" name="name" placeholder="أدخل اسمك الثلاثي" required class="w-full p-3.5 border-2 border-slate-100 rounded-2xl text-xs font-bold outline-none focus:border-slate-800 text-center">
            <button type="submit" class="bravo-teal-btn w-full font-black py-3 rounded-full text-xs text-slate-900 border-2 border-slate-900 shadow-[0_4px_0_#0f172a] hover:translate-y-1 hover:shadow-none transition-all">دخول المنصة</button>
        </form>

        <div class="mt-6 text-xs font-bold text-slate-400">
            <a href="index.php" class="hover:text-teal-600">العودة للرئيسية</a>
        </div>
    </div>
</body>
</html>