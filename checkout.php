<?php
// 1. بدء الجلسة في أعلى الملف (قبل أي تصميم)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';

// 2. التحقق من وجود المعرف
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$course_id = (int)$_GET['id'];

// 3. التحقق من تسجيل دخول الطالب (قبل استدعاء الهيدر)
if (!isset($_SESSION['user_id'])) {
    // حفظ الصفحة التي يريد الذهاب إليها بعد تسجيل الدخول
    $_SESSION['redirect_after_login'] = "checkout.php?id=" . $course_id;
    header("Location: student_login.php");
    exit;
}

// 4. الآن يمكننا استدعاء الهيدر بأمان
include 'header.php';

// 5. جلب بيانات المادة
$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$c = $stmt->get_result()->fetch_assoc();

if (!$c) {
    echo "<div class='text-center py-20 font-black text-2xl'>المقرر غير موجود</div>";
    include 'footer.php';
    exit;
}

$banks = [];
$res_banks = $conn->query("SELECT * FROM bank_accounts");
while($row = $res_banks->fetch_assoc()) { $banks[] = $row; }

$student_name = $_SESSION['user_name'] ?? 'طالب جديد';
$wa_msg = "مرحباً، أود تفعيل اشتراكي في مادة ({$c['title']}) باسم الطالب ({$student_name}) وهذا إشعار التحويل.";
$wa_link = "https://wa.me/{$wa_num}?text=" . urlencode($wa_msg);

// معالجة الدفع
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id']; 
    $stmt_ins = $conn->prepare("INSERT INTO enrollments (user_id, course_id, status) VALUES (?, ?, 'pending') ON DUPLICATE KEY UPDATE status='pending'");
    $stmt_ins->bind_param("ii", $user_id, $course_id);
    $stmt_ins->execute();
    
    echo "<script>alert('تم استلام طلبك بنجاح! سيتم تفعيل حسابك قريباً.'); window.location.href='course.php?id={$course_id}';</script>";
    exit;
}
?>

<div class="max-w-md mx-auto px-4 py-8">
    <div class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-xl">
        <h2 class="text-xl font-black text-slate-900 mb-1 text-center">إتمام الاشتراك</h2>
        <p class="text-xs text-slate-500 mb-5 text-center">
            المادة: <b class="text-slate-800"><?= htmlspecialchars($c['title']) ?></b> | 
            المبلغ: <b class="text-teal-600 font-black"><?= number_format($c['price']) ?> ر.س</b>
        </p>

        <!-- أزرار التبديل بين طرق الدفع -->
        <div class="grid grid-cols-2 gap-2 mb-6 text-center text-xs font-black">
            <button onclick="switchPayTab('bank')" id="tab-btn-bank" class="bg-[#1E293B] text-white py-3 rounded-2xl transition shadow-xs">🏦 تحويل بنكي</button>
            <button onclick="switchPayTab('card')" id="tab-btn-card" class="bg-slate-100 text-slate-700 py-3 rounded-2xl transition">💳 بطاقة ائتمانية</button>
        </div>

        <!-- قسم التحويل البنكي -->
        <div id="tab-content-bank" class="space-y-4">
            <div class="space-y-3">
                <?php if(!empty($banks)): ?>
                    <?php foreach($banks as $b): ?>
                        <div class="bg-slate-50 border border-slate-200 p-4 rounded-2xl text-xs space-y-2">
                            <div class="flex justify-between items-center"><span class="text-slate-500 font-bold">البنك:</span><span class="font-black text-slate-900"><?= htmlspecialchars($b['bank_name']) ?></span></div>
                            <div class="flex justify-between items-center"><span class="text-slate-500 font-bold">المستفيد:</span><span class="font-black text-slate-900"><?= htmlspecialchars($b['beneficiary']) ?></span></div>
                            <div class="pt-1"><span class="text-slate-500 font-bold block mb-0.5">رقم الحساب:</span><div class="bg-white p-2 rounded-xl border border-slate-300 text-center font-mono font-black text-slate-800"><?= htmlspecialchars($b['account_num']) ?></div></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="bg-amber-50 text-amber-700 p-4 rounded-xl text-center font-bold text-xs border border-amber-200">لا توجد حسابات بنكية حالياً.</div>
                <?php endif; ?>
            </div>
            <a href="<?= $wa_link ?>" target="_blank" class="w-full flex items-center justify-center gap-2 bg-[#25D366] text-white font-black py-3.5 rounded-full text-xs border-2 border-slate-900 shadow-[0_4px_0_#0f172a] hover:translate-y-1 hover:shadow-none transition-all">
                <span>📲 إرسال إشعار التحويل</span>
            </a>
            <form method="POST">
                <button type="submit" class="w-full bg-[#0B4B80] text-white font-black py-3.5 rounded-full text-xs">تأكيد التحويل</button>
            </form>
        </div>

        <!-- قسم البطاقة الائتمانية -->
        <div id="tab-content-card" class="hidden space-y-4">
            <div class="bg-blue-50 border border-blue-100 p-4 rounded-2xl mb-4 text-center">
                <span class="text-2xl mb-1 block">💳</span>
                <p class="text-xs font-bold text-blue-800">الدفع الآمن عبر البطاقة</p>
            </div>
            <form method="POST" class="space-y-3">
                <div>
                    <label class="text-[10px] font-bold text-slate-500 mb-1 block">رقم البطاقة</label>
                    <input type="text" placeholder="0000 0000 0000 0000" required class="w-full border border-slate-300 p-3 rounded-xl text-sm text-center font-mono tracking-widest">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div><label class="text-[10px] font-bold text-slate-500 mb-1 block">تاريخ الانتهاء</label><input type="text" placeholder="MM/YY" required class="w-full border border-slate-300 p-3 rounded-xl text-sm text-center font-mono"></div>
                    <div><label class="text-[10px] font-bold text-slate-500 mb-1 block">CVV</label><input type="text" placeholder="123" required class="w-full border border-slate-300 p-3 rounded-xl text-sm text-center font-mono"></div>
                </div>
                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-black py-3.5 rounded-full text-xs mt-2 transition shadow-md">🔒 دفع <?= number_format($c['price']) ?> ر.س الآن</button>
            </form>
        </div>
    </div>
</div>

<script>
    function switchPayTab(tab) {
        if(tab === 'bank') {
            document.getElementById('tab-content-bank').classList.remove('hidden');
            document.getElementById('tab-content-card').classList.add('hidden');
            document.getElementById('tab-btn-bank').className = 'bg-[#1E293B] text-white py-3 rounded-2xl transition shadow-xs';
            document.getElementById('tab-btn-card').className = 'bg-slate-100 text-slate-700 py-3 rounded-2xl transition';
        } else {
            document.getElementById('tab-content-card').classList.remove('hidden');
            document.getElementById('tab-content-bank').classList.add('hidden');
            document.getElementById('tab-btn-card').className = 'bg-[#1E293B] text-white py-3 rounded-2xl transition shadow-xs';
            document.getElementById('tab-btn-bank').className = 'bg-slate-100 text-slate-700 py-3 rounded-2xl transition';
        }
    }
</script>

<?php include 'footer.php'; ?>