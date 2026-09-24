-- ============================================================
--  Mount Heaven English School — Database Schema & Seed Data
-- ============================================================

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    username VARCHAR(60) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','editor') NOT NULL DEFAULT 'editor',
    active TINYINT(1) NOT NULL DEFAULT 1,
    last_login DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS login_attempts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip VARCHAR(45) NOT NULL,
    username VARCHAR(150) NOT NULL,
    attempted_at DATETIME NOT NULL,
    INDEX idx_attempt (ip, username, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(80) NOT NULL UNIQUE,
    `value` TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sliders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    subtitle VARCHAR(255) NULL,
    image VARCHAR(255) NOT NULL,
    button_text VARCHAR(60) NULL,
    button_url VARCHAR(255) NULL,
    sort_order INT NOT NULL DEFAULT 0,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS teachers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    designation VARCHAR(120) NULL,
    qualification VARCHAR(150) NULL,
    bio TEXT NULL,
    photo VARCHAR(255) NULL,
    sort_order INT NOT NULL DEFAULT 0,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS notices (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    body TEXT NULL,
    is_pinned TINYINT(1) NOT NULL DEFAULT 0,
    published_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS events (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NULL,
    event_date DATE NOT NULL,
    image VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS gallery_albums (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    cover VARCHAR(255) NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS gallery_photos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    album_id INT UNSIGNED NOT NULL,
    image VARCHAR(255) NOT NULL,
    caption VARCHAR(200) NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_photos_album FOREIGN KEY (album_id) REFERENCES gallery_albums(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS programs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    grades VARCHAR(120) NULL,
    description TEXT NULL,
    icon VARCHAR(60) NULL,
    photo VARCHAR(255) NULL,
    sort_order INT NOT NULL DEFAULT 0,
    active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS facilities (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(120) NOT NULL,
    description TEXT NULL,
    icon VARCHAR(60) NULL,
    photo VARCHAR(255) NULL,
    sort_order INT NOT NULL DEFAULT 0,
    active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(40) NULL,
    subject VARCHAR(200) NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS popup_banners (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NULL,
    image VARCHAR(255) NOT NULL,
    link_url VARCHAR(255) NULL,
    duration INT NOT NULL DEFAULT 5,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admissions_enquiries (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(150) NOT NULL,
    dob DATE NULL,
    dob_bs_label VARCHAR(60) NULL,
    gender VARCHAR(20) NULL,
    grade_applying VARCHAR(60) NOT NULL,
    parent_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(40) NOT NULL,
    address VARCHAR(255) NULL,
    previous_school VARCHAR(200) NULL,
    previous_class VARCHAR(60) NULL,
    emis_no VARCHAR(30) NULL,
    message TEXT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'new',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  Seed data
-- ============================================================

INSERT INTO settings (`key`, `value`) VALUES
('site_name', 'Mount Heaven English School'),
('site_tagline', 'Learning Today, Leading Tomorrow'),
('principal_photo', 'teachers/real-principal.jpg'),
('site_logo', 'settings/logo-20260916.png'),
('about_short', 'Mount Heaven English School is a co-educational English-medium institution dedicated to academic excellence, character building and holistic development of every child.'),
('about_history', 'Founded with a vision to provide quality English-medium education, Mount Heaven English School has grown into a trusted name known for its caring teachers, modern classrooms and consistent academic results. We believe every child carries a unique spark — our mission is to help it shine.'),
('about_mission', 'To nurture confident, compassionate and curious learners through value-based education, modern pedagogy and personal attention.'),
('about_vision', 'To be a centre of learning where every student grows into a responsible global citizen with strong moral values and a lifelong love for learning.'),
('principal_name', 'Mrs. Anita Sharma'),
('principal_designation', 'Principal'),
('principal_message', 'Dear Parents and Students, at Mount Heaven we do not just teach — we inspire. Our dedicated faculty, child-centred approach and strong value system ensure that every student discovers their potential and walks out ready to lead. I warmly invite you to be part of our family.'),
('address', 'Heaven Hills Road, Green Park, New Delhi 110016'),
('phone', '+91 98765 43210'),
('email', 'info@mountheaven.edu'),
('office_hours', 'Monday – Saturday: 8:00 AM – 3:00 PM'),
('facebook_url', 'https://facebook.com'),
('instagram_url', 'https://instagram.com'),
('youtube_url', 'https://youtube.com'),
('admission_open', '1'),
('admission_info', 'Admissions are open for Nursery to Grade X for the academic session 2026-27. Registration forms are available at the school office and can also be filled online. Limited seats — apply early!'),
('footer_note', '© {year} Mount Heaven English School. All rights reserved.'),
('stat_established', 'Since 1959'),
('stat_established_sub', 'Years of excellence'),
('stat_students', '800+'),
('stat_students_sub', 'Happy students'),
('stat_teachers', '35+'),
('stat_teachers_sub', 'Expert teachers'),
('stat_results', '100%'),
('stat_results_sub', 'Board results')
ON DUPLICATE KEY UPDATE `key` = VALUES(`key`);

INSERT INTO sliders (title, subtitle, image, button_text, button_url, sort_order, active) VALUES
('Welcome to Mount Heaven', 'Where young minds rise to new heights', 'sliders/real-campus.jpg', 'Explore Campus', '/about', 1, 1),
('Excellence in Education', 'Nursery to Grade X · CBSE-aligned curriculum', 'sliders/real-classroom.jpg', 'Our Academics', '/academics', 2, 1),
('Admissions Open 2026-27', 'Limited seats — register your child today', 'sliders/real-graduation.jpg', 'Apply Now', '/admissions', 3, 1);

INSERT INTO teachers (name, designation, qualification, bio, photo, sort_order, active) VALUES
('Mrs. Anita Sharma', 'Principal', 'M.A., B.Ed. — 22 years experience', 'A visionary educator leading the school with passion and purpose.', 'teachers/real-principal.jpg', 1, 1),
('Mr. Rajan Kapoor', 'Vice Principal & Mathematics', 'M.Sc., B.Ed.', 'Makes mathematics fun with real-life examples and activities.', 'teachers/real-viceprincipal.jpg', 2, 1),
('Ms. Priya Verma', 'English', 'M.A. English, B.Ed.', 'Inspires a love for reading and creative writing in students.', 'teachers/real-english.jpg', 3, 1),
('Mr. Arjun Mehta', 'Science', 'M.Sc. Physics, B.Ed.', 'Turns science labs into playgrounds of discovery.', 'teachers/real-science.jpg', 4, 1),
('Mrs. Kavita Joshi', 'Primary Coordinator', 'B.A., NTT, B.Ed.', 'The heart of our primary wing, loved by every little learner.', 'teachers/real-primary.jpg', 5, 1),
('Mr. Sanjay Raut', 'Sports & Physical Education', 'B.P.Ed.', 'Trains champions on the field and sportsmanship in life.', 'teachers/real-sports.jpg', 6, 1);

INSERT INTO notices (title, body, is_pinned, published_at) VALUES
('Admission Open for Session 2026-27', 'Registrations for Nursery to Grade X are now open. Collect forms from the school office between 9 AM and 1 PM, or apply through the admissions page on this website. Limited seats available.', 1, NOW()),
('Annual Sports Day — December 18', 'Our much-awaited Annual Sports Day will be held on the school grounds. Parents are warmly invited. Events begin at 9:00 AM sharp.', 0, NOW() - INTERVAL 5 DAY),
('Half-Yearly Examination Schedule Released', 'The detailed date-sheet for the half-yearly examinations has been shared with students and is available at the school office notice board.', 0, NOW() - INTERVAL 12 DAY),
('Parent-Teacher Meeting — Saturday', 'A PTM will be conducted this Saturday from 10:00 AM to 1:00 PM to discuss student progress reports. Attendance of at least one parent is mandatory.', 0, NOW() - INTERVAL 20 DAY);

INSERT INTO events (title, description, event_date, image) VALUES
('Annual Day Celebration', 'Music, dance, drama and awards — our students showcase a year of talent on the grand stage.', DATE_ADD(CURDATE(), INTERVAL 30 DAY), 'events/real-annualday.jpg'),
('Science Exhibition', 'Working models and experiments by students of Grades V–X. Open to parents and visitors.', DATE_ADD(CURDATE(), INTERVAL 45 DAY), 'events/real-sciencefair.jpg'),
('Inter-School Football Tournament', 'Our school team hosts the district-level football championship.', DATE_ADD(CURDATE(), INTERVAL 60 DAY), 'events/real-football.jpg'),
('Art & Craft Workshop', 'A hands-on workshop on painting, origami and clay modelling for primary students.', DATE_ADD(CURDATE(), INTERVAL 15 DAY), 'events/real-artcraft.jpg');

INSERT INTO gallery_albums (title, cover, sort_order) VALUES
('Campus & Classrooms', 'gallery/real-classroom.jpg', 1),
('Annual Sports Day', 'gallery/real-sports.jpg', 2),
('Science Exhibition', 'gallery/real-science.jpg', 3),
('Cultural Fest', 'gallery/real-culture.jpg', 4);

INSERT INTO gallery_photos (album_id, image, caption, sort_order) VALUES
(1, 'gallery/real-classroom.jpg', 'The main academic block', 1),
(1, 'gallery/real-library.jpg', 'Reading is believing', 2),
(2, 'gallery/real-sports.jpg', 'The 100m sprint finals', 1),
(2, 'gallery/real-playground.jpg', 'March past by the junior wing', 2),
(3, 'gallery/real-science.jpg', 'Young scientists at work', 1),
(3, 'gallery/real-lab.jpg', 'The chemistry corner', 2),
(4, 'gallery/real-culture.jpg', 'Grand finale performance', 1),
(4, 'gallery/real-music.jpg', 'The school choir', 2);

INSERT INTO programs (title, grades, description, icon, photo, sort_order, active) VALUES
('Pre-Primary (Nursery – UKG)', 'Nursery · LKG · UKG', 'Play-based foundational learning that builds curiosity, confidence and social skills in a joyful environment.', '🧩', 'programs/real-preprimary.jpg', 1, 1),
('Primary School (I – V)', 'Grade I – V', 'Strong focus on languages, mathematics and environmental science with activity-based learning and regular assessments.', '📚', 'programs/real-primary.jpg', 2, 1),
('Middle School (VI – VIII)', 'Grade VI – VIII', 'Subject-specialist teaching, laboratory work, projects and co-curricular activities that build analytical thinking.', '🔬', 'programs/real-middle.jpg', 3, 1),
('Secondary School (IX – X)', 'Grade IX – X', 'Rigorous board-oriented preparation with mentoring, doubt-clearing sessions and career guidance.', '🎯', 'programs/real-secondary.jpg', 4, 1);

INSERT INTO facilities (title, description, icon, photo, sort_order, active) VALUES
('Smart Classrooms', 'Every classroom is equipped with interactive digital boards and audio-visual learning aids.', '🖥️', 'facilities/real-classroom.jpg', 1, 1),
('Science & Computer Labs', 'Well-equipped Physics, Chemistry, Biology and computer laboratories with modern apparatus.', '🧪', 'facilities/real-lab.jpg', 2, 1),
('Library', 'A rich collection of books, journals and magazines to nurture the reading habit.', '📖', 'facilities/real-library.jpg', 3, 1),
('Sports Complex', 'Playgrounds for football, cricket, basketball plus indoor games and athletics track.', '⚽', 'facilities/real-sports.jpg', 4, 1),
('Transport Service', 'GPS-enabled school buses with trained staff covering all major routes of the city.', '🚌', 'facilities/real-bus.jpg', 5, 1),
('Health & Safety', 'On-campus medical room, regular health check-ups and complete CCTV surveillance.', '🛡️', 'facilities/real-medical.jpg', 6, 1);
