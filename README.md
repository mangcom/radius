--สร้างตาราง หลังจากที่ติดตั้ง Freeradius และ Mariadb เสร็จ
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    passwords VARCHAR(255) NOT NULL
);

INSERT INTO admin_users (username, passwords) VALUES 
('admin', 'admin');
