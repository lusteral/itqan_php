<?php
// دالة لجلب إعدادات الموقع من قاعدة البيانات
function get_site_settings($conn) {
    $settings = [];
    $sql = "SELECT `key`, `value` FROM site_settings";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $settings[$row['key']] = $row['value'];
        }
    }
    return $settings;
}

// دالة لجلب الإعلانات (السلايدر والشريط العلوي)
function get_ads($conn, $type) {
    $ads = [];
    // نستخدم Prepared Statements للأمان
    $stmt = $conn->prepare("SELECT * FROM ads_banners WHERE ad_type = ? AND is_active = 1 ORDER BY sort_order ASC");
    $stmt->bind_param("s", $type);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $ads[] = $row;
        }
    }
    return $ads;
}
?>