-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 07, 2026 at 04:23 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `itqan_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(255) DEFAULT 'مدير النظام',
  `role` varchar(50) DEFAULT 'مدير عام',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`, `full_name`, `role`, `created_at`, `last_login`) VALUES
(1, 'admin', 'pbkdf2:sha256:1000000$OCFRwHTvX9GFykQg$2c16e9e120661a6d0c70b066e7097e8260fe1c795463c8e001c894f4f56c44a1', 'المدير العام', 'مدير عام', '2026-10-22 01:09:15', '2026-10-22 21:32:52');

-- --------------------------------------------------------

--
-- Table structure for table `ads_banners`
--

CREATE TABLE `ads_banners` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `subtitle` text DEFAULT '',
  `badge` text DEFAULT '',
  `badge_color` varchar(50) DEFAULT 'amber',
  `image_url` text DEFAULT '',
  `link_url` text DEFAULT '',
  `ad_type` varchar(50) DEFAULT 'slider',
  `is_active` int(11) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ads_banners`
--

INSERT INTO `ads_banners` (`id`, `title`, `subtitle`, `badge`, `badge_color`, `image_url`, `link_url`, `ad_type`, `is_active`, `sort_order`, `created_at`) VALUES
(1, 'تصميم وتطوير لوحات التحكم والمواقع المتكاملة', 'أنظمة Full-Stack بكود مصدري منظم وتصميم تجاوبي معتمد', '💻 برمجة وتطوير Web Apps', 'amber', 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1200&q=80', '', 'slider', 1, 1, '2026-10-22 01:09:15'),
(2, 'تطوير تطبيقات الجوال واجهات Flutter & React Native', 'تطبيقات ذكية، ربط قواعد البيانات، وواجهات UI/UX احترافية', '📱 تطبيقات Android & iOS', 'teal', 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?auto=format&fit=crop&w=1200&q=80', '', 'slider', 1, 2, '2026-10-22 01:09:15'),
(3, 'مشاريع خوارزميات الذكاء الاصطناعي وتحليل البيانات', 'نماذج تعلم الآلة، المعالجة البرمجية، وتوثيق الأكواد', '🤖 الذكاء الاصطناعي و Python', 'purple', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=1200&q=80', '', 'slider', 1, 3, '2026-10-22 01:09:15'),
(4, 'تصميم الدوائر الإلكترونية والتحليل الإنشائي والمعملي', 'تقارير ودراسات هندسية دقيقة متوافقة مع متطلبات الكليات', '⚙️ المشاريع الهندسيّة والتقنيّة', 'sky', 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=1200&q=80', '', 'slider', 1, 4, '2026-10-22 01:09:15'),
(5, '⚡ تصفح الملازم والأعمال الفصلية والمشاريع الاحترافية المميزة لجميع الكليات!', '', '', 'amber', '', '', 'ticker', 1, 1, '2026-10-22 01:09:15'),
(6, '🎓 تواصل معنا مباشرة عبر الواتساب للحصول على استشارة أو حل واجب خاص 📲', '', '', 'amber', '', '', 'ticker', 1, 2, '2026-10-22 01:09:15'),
(7, '⭐ ضمان جودة الشروحات الأكاديمية أعلى الدرجات A+', '', '', 'amber', '', '', 'ticker', 1, 3, '2026-10-22 01:09:15');

-- --------------------------------------------------------

--
-- Table structure for table `bank_accounts`
--

CREATE TABLE `bank_accounts` (
  `id` int(11) NOT NULL,
  `bank_name` text DEFAULT NULL,
  `beneficiary` text DEFAULT NULL,
  `account_num` varchar(100) DEFAULT NULL,
  `iban` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bank_accounts`
--

INSERT INTO `bank_accounts` (`id`, `bank_name`, `beneficiary`, `account_num`, `iban`) VALUES
(1, 'بنك الجزيرة', 'عدنان محمد سعيد عبدالكريم', '003381468188001', 'SA2160100003381468188001');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `uni_id` int(11) DEFAULT NULL,
  `instructor_id` int(11) DEFAULT 1,
  `title` text DEFAULT NULL,
  `code` varchar(100) DEFAULT '',
  `desc` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `old_price` decimal(10,2) DEFAULT 0.00,
  `mid_price` decimal(10,2) DEFAULT 0.00,
  `mid_old_price` decimal(10,2) DEFAULT 0.00,
  `final_price` decimal(10,2) DEFAULT 0.00,
  `final_old_price` decimal(10,2) DEFAULT 0.00,
  `cover_img` text DEFAULT NULL,
  `faculty` varchar(255) DEFAULT 'كلية الهندسة',
  `target_audience` varchar(255) DEFAULT 'طلاب وطالبات'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `uni_id`, `instructor_id`, `title`, `code`, `desc`, `price`, `old_price`, `mid_price`, `mid_old_price`, `final_price`, `final_old_price`, `cover_img`, `faculty`, `target_audience`) VALUES
(1, 1, 1, 'الذكاء الاصطناعي وتعلم الآلة (AI 401)', 'CSC401', 'شرح متكامل لمبادئ الذكاء الاصطناعي وخوارزميات تعلم الآلة والشبكات العصبية مع تطبيق عملي بلغة Python.', 220.00, 280.00, 110.00, 140.00, 140.00, 180.00, '', 'كلية علوم الحاسب والمعلومات', 'طلاب وطالبات'),
(2, 2, 1, 'الأمن السيبراني وتشفير البيانات (SEC 311)', 'COE311', 'شرح مبادئ التشفير، أمن الشبكات، بروتوكولات الحماية، والتحليل الجنائي الرقمي مع حل التجميعات النهائية.', 200.00, 250.00, 100.00, 130.00, 130.00, 160.00, '', 'كلية هندسة الحاسب', 'طلاب وطالبات'),
(3, 3, 1, 'تطوير تطبيقات الويب والهواتف الذكية (WEB 210)', 'CPIT210', 'دورة مكثفة لشرح HTML5, CSS3, JavaScript, React وتكامل البرمجة مع قواعد البيانات والـ API.', 180.00, 230.00, 90.00, 120.00, 120.00, 150.00, '', 'كلية الحاسبات وتقنية المعلومات', 'طلاب وطالبات'),
(4, 4, 1, 'حساب التكالمين والتفاضل (Math 101)', 'MATH101', 'شرح شامل ومبسط لمقرر التفاضل والتكامل مع حل أسئلة الاختيارات والتجميعات السابقة والامتحانات النهائية.', 150.00, 201.50, 80.00, 100.00, 100.00, 120.00, 'cover_1792684128_9e1ad801.png', 'كلية الهندسة والعلوم', 'طلاب وطالبات'),
(5, NULL, 1, 'اساسيات البرمجة بلغة Python', 'CS101', NULL, 150.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 'كلية علوم الحاسوب والمعلومات', 'طلاب وطالبات'),
(7, NULL, 1, 'شبكات الحاسوب والاتصالات', 'NET310', NULL, 200.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 'كلية الهندسة', 'طلاب وطالبات');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `package_type` varchar(50) DEFAULT 'full',
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `user_id`, `course_id`, `package_type`, `status`, `created_at`) VALUES
(1, 1, 7, 'full', 'pending', '2026-11-06 09:25:07'),
(2, 1, 5, 'full', 'approved', '2026-11-06 10:07:48');

-- --------------------------------------------------------

--
-- Table structure for table `instructors`
--

CREATE TABLE `instructors` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `avatar_img` text DEFAULT NULL,
  `banner_img` text DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT 5.0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `instructors`
--

INSERT INTO `instructors` (`id`, `name`, `bio`, `avatar_img`, `banner_img`, `rating`) VALUES
(1, 'أكاديمية إتقان التعليمية', 'نخبة من الأكاديميين والمحاضرين المتخصصين في تبسيط وشرح المقررات الجامعية وتلخيصها ومتابعة الطلاب لتحقيق الامتياز A+.', NULL, NULL, 5.0);

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `video_filename` text DEFAULT NULL,
  `pdf_filename` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`id`, `course_id`, `title`, `video_filename`, `pdf_filename`) VALUES
(1, NULL, 'NLP', 'NLP.mp4', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `ip_address` varchar(45) NOT NULL,
  `attempts` int(11) DEFAULT 0,
  `locked_until` decimal(15,0) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `semester_works`
--

CREATE TABLE `semester_works` (
  `id` int(11) NOT NULL,
  `title` text DEFAULT NULL,
  `faculty` varchar(255) DEFAULT NULL,
  `desc` text DEFAULT NULL,
  `file_name` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cover_img` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `semester_works`
--

INSERT INTO `semester_works` (`id`, `title`, `faculty`, `desc`, `file_name`, `created_at`, `cover_img`) VALUES
(9, 'مشروع تطبيق إدارة المهام بلغة React', 'كلية علوم الحاسب والمعلومات', 'تطبيق متكامل لإدارة المهام اليومية باستخدام React و Redux مع واجهة مستخدم حديثة وتفاعلية', NULL, '2026-10-12 05:30:14', 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=800&q=80'),
(10, 'تقرير دراسة جدوى لمشروع تجارة إلكترونية', 'كلية إدارة الأعمال', 'دراسة جدوى شاملة لمشروع متجر إلكتروني متخصص في المنتجات التقنية مع تحليل السوق والميزانية', NULL, '2026-10-12 05:30:14', 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&fit=crop&w=800&q=80'),
(11, 'تصميم قاعدة بيانات لنظام إدارة المستشفيات', 'كلية هندسة البرمجيات', 'تصميم ER-Diagram متكامل لنظام إدارة المستشفيات مع جداول المرضى والأطباء والمواعيد', NULL, '2026-10-12 05:30:14', 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=800&q=80'),
(12, 'مشروع تحليل البيانات باستخدام Python', 'كلية علوم البيانات', 'تحليل مجموعة بيانات العملاء باستخدام Pandas و Matplotlib مع تقارير تفاعلية', NULL, '2026-10-12 05:30:14', 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=800&q=80'),
(13, 'تصميم واجهة مستخدم لتطبيق توصيل طلبات', 'كلية التصميم الجرافيكي', 'تصميم UI/UX احترافي لتطبيق توصيل طلبات باستخدام Figma مع موديلات تفاعلية', NULL, '2026-10-12 05:30:14', 'https://images.unsplash.com/photo-1586717791821-3f44a5638d48?auto=format&fit=crop&w=800&q=80'),
(14, 'تقرير هندسي لمبنى سكني متعدد الطوابق', 'كلية الهندسة المدنية', 'تقرير هندسي شامل يتضمن المخططات الإنشائية والتحليل الإحصائي للمواد', NULL, '2026-10-12 05:30:14', 'work_cover_1792684564_1cb9baf9.png');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`key`, `value`) VALUES
('academy_bio', 'نخبة من الأكاديميين والمحاضرين المتخصصين في تبسيط وشرح المقررات الجامعية وتلخيصها ومتابعة الطلاب لتحقيق الامتياز A+.'),
('academy_name', 'أكاديمية إتقان التعليمية'),
('academy_rating', '5.0'),
('footer_text', 'منصة إتقان الأكاديمية © 2023 - 2026'),
('hero_badge_1', '🏆 المنصة التعليمية الأولى'),
('hero_badge_2', 'أكاديمية إتقان 2026'),
('hero_desc', 'نقدم أفضل الملخصات، الواجبات، الأنشطة والأعمال الفصلية المعتمدة لجميع الجامعات والمقررات باللغتين العربية والإنجليزية.'),
('hero_title', 'شروحات جامعية، أعمال فصلية، ومشاريع احترافية بامتياز A+ 🎯'),
('legal_warning', 'تحذير قانوني : لا يجوز نشره أو مشاركته'),
('promo_badge', '🔥 عروض حصريّة'),
('promo_discount', 'خصم 20% لفترة محدودة'),
('site_name', 'إتقان | المنصة التعليمية'),
('slider_badge', 'معرض صور المشاريع 📸'),
('slider_title', 'منصة إتقان للأعمال والمشاريع الأكاديمية'),
('students_count', '+1000'),
('whatsapp_number', '966593521664');

-- --------------------------------------------------------

--
-- Table structure for table `universities`
--

CREATE TABLE `universities` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `logo` text DEFAULT NULL,
  `desc` text DEFAULT NULL,
  `logo_img` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `universities`
--

INSERT INTO `universities` (`id`, `name`, `logo`, `desc`, `logo_img`) VALUES
(1, 'جامعة الملك سعود (KSU)', '👑', 'مقررات وشروحات جامعة الملك سعود - الرياض', NULL),
(2, 'جامعة الملك فهد للبترول والمعادن (KFUPM)', '⚙️', 'شروحات ومشاريع جامعة الملك فهد - الظهران', NULL),
(3, 'جامعة الملك عبدالعزيز (KAU)', '🏢', 'مقررات وتكاليف جامعة الملك عبدالعزيز - جدة', NULL),
(4, 'جامعة الملك خالد (KKU)', '⛰️', 'شروحات واختبارات جامعة الملك خالد - أبها', NULL),
(5, 'جامعة أم القرى (UQU)', '🕋', 'شروحات ومقررات جامعة أم القرى - مكة المكرمة', NULL),
(6, 'جامعة الأميرة نورة (PNU)', '🎓', 'مقررات وحاسبات جامعة الأميرة نورة - الرياض', NULL),
(7, 'جامعة جدة (UJ)', '🏛️', 'مقررات وملخصات جامعة جدة', NULL),
(8, 'جامعة الإمام عبدالرحمن بن فيصل (IAU)', '🌊', 'شروحات ومشاريع جامعة الإمام عبدالرحمن - الدمام', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `is_admin` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `is_admin`) VALUES
(1, 'adel', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `ads_banners`
--
ALTER TABLE `ads_banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`course_id`);

--
-- Indexes for table `instructors`
--
ALTER TABLE `instructors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`ip_address`);

--
-- Indexes for table `semester_works`
--
ALTER TABLE `semester_works`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `universities`
--
ALTER TABLE `universities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ads_banners`
--
ALTER TABLE `ads_banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `instructors`
--
ALTER TABLE `instructors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `semester_works`
--
ALTER TABLE `semester_works`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `universities`
--
ALTER TABLE `universities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
