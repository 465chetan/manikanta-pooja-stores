-- ============================================================
-- SRI MANIKANTA POOJA STORES â€” DATABASE SCHEMA v2.0
-- UPI Payment System (No Razorpay)
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
SET time_zone = "+05:30";

-- â”€â”€ Admin Users â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`          VARCHAR(100) NOT NULL,
  `email`         VARCHAR(150) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role`          ENUM('superadmin','manager') DEFAULT 'manager',
  `is_active`     TINYINT(1) DEFAULT 1,
  `last_login`    DATETIME,
  `created_at`    DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- â”€â”€ Customers â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
CREATE TABLE IF NOT EXISTS `users` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `full_name`     VARCHAR(100) NOT NULL,
  `mobile`        VARCHAR(15) NOT NULL UNIQUE,
  `email`         VARCHAR(150) UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `is_verified`   TINYINT(1) DEFAULT 0,
  `otp_code`      VARCHAR(10),
  `otp_expires`   DATETIME,
  `otp_attempts`  TINYINT DEFAULT 0,
  `is_active`     TINYINT(1) DEFAULT 1,
  `created_at`    DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_mobile` (`mobile`),
  INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- â”€â”€ Delivery Addresses â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
CREATE TABLE IF NOT EXISTS `addresses` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`      INT UNSIGNED NOT NULL,
  `label`        VARCHAR(50) DEFAULT 'Home',
  `full_name`    VARCHAR(100) NOT NULL,
  `mobile`       VARCHAR(15) NOT NULL,
  `address_line` VARCHAR(255) NOT NULL,
  `landmark`     VARCHAR(150),
  `city`         VARCHAR(100) NOT NULL,
  `state`        VARCHAR(100) DEFAULT 'Telangana',
  `pincode`      VARCHAR(10) NOT NULL,
  `is_default`   TINYINT(1) DEFAULT 0,
  `created_at`   DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- â”€â”€ Categories â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
CREATE TABLE IF NOT EXISTS `categories` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `slug`       VARCHAR(50) NOT NULL UNIQUE,
  `name`       VARCHAR(100) NOT NULL,
  `telugu`     VARCHAR(100),
  `emoji`      VARCHAR(10),
  `image`      VARCHAR(255),
  `sort_order` INT DEFAULT 0,
  `is_active`  TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- â”€â”€ Products â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
CREATE TABLE IF NOT EXISTS `products` (
  `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category_id`    INT UNSIGNED NOT NULL,
  `name`           VARCHAR(255) NOT NULL,
  `telugu_name`    VARCHAR(255),
  `slug`           VARCHAR(255) NOT NULL UNIQUE,
  `description`    TEXT,
  `price`          DECIMAL(10,2) NOT NULL,
  `original_price` DECIMAL(10,2),
  `stock_qty`      INT DEFAULT 100,
  `sku`            VARCHAR(100) UNIQUE,
  `images`         TEXT,
  `sizes`          TEXT,
  `tags`           TEXT,
  `badge`          VARCHAR(20),
  `is_featured`    TINYINT(1) DEFAULT 0,
  `is_active`      TINYINT(1) DEFAULT 1,
  `rating`         DECIMAL(3,1) DEFAULT 0,
  `review_count`   INT DEFAULT 0,
  `created_at`     DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`),
  INDEX `idx_category` (`category_id`),
  INDEX `idx_slug` (`slug`),
  INDEX `idx_featured` (`is_featured`),
  INDEX `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- â”€â”€ Orders â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
CREATE TABLE IF NOT EXISTS `orders` (
  `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_number`     VARCHAR(25) NOT NULL UNIQUE,
  `user_id`          INT UNSIGNED NOT NULL,
  `address_id`       INT UNSIGNED,
  `address_snapshot` TEXT,
  `subtotal`         DECIMAL(10,2) NOT NULL,
  `delivery_charge`  DECIMAL(10,2) DEFAULT 0.00,
  `discount`         DECIMAL(10,2) DEFAULT 0.00,
  `total`            DECIMAL(10,2) NOT NULL,
  `payment_method`   ENUM('cod','upi') NOT NULL DEFAULT 'cod',
  `payment_status`   ENUM('pending','verifying','paid','failed','refunded') DEFAULT 'pending',
  `order_status`     ENUM('pending','confirmed','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `notes`            TEXT,
  `admin_notes`      TEXT,
  `cancelled_reason` VARCHAR(255),
  `created_at`       DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
  FOREIGN KEY (`address_id`) REFERENCES `addresses`(`id`) ON DELETE SET NULL,
  INDEX `idx_user` (`user_id`),
  INDEX `idx_status` (`order_status`),
  INDEX `idx_payment_status` (`payment_status`),
  INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- â”€â”€ Order Items â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
CREATE TABLE IF NOT EXISTS `order_items` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id`   INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `name`       VARCHAR(255) NOT NULL,
  `variant`    VARCHAR(100),
  `price`      DECIMAL(10,2) NOT NULL,
  `qty`        INT NOT NULL DEFAULT 1,
  `image`      VARCHAR(255),
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`),
  INDEX `idx_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- â”€â”€ UPI Payments â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
CREATE TABLE IF NOT EXISTS `payments` (
  `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id`        INT UNSIGNED NOT NULL UNIQUE,
  `utr_number`      VARCHAR(30),
  `upi_id_used`     VARCHAR(100),
  `amount`          DECIMAL(10,2) NOT NULL,
  `screenshot_path` VARCHAR(255),
  `status`          ENUM('pending','verifying','verified','failed') DEFAULT 'pending',
  `verified_by`     INT UNSIGNED,
  `verified_at`     DATETIME,
  `admin_note`      TEXT,
  `created_at`      DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  INDEX `idx_utr` (`utr_number`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- â”€â”€ Notifications â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
CREATE TABLE IF NOT EXISTS `notifications` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT UNSIGNED,
  `type`       VARCHAR(60) NOT NULL,
  `title`      VARCHAR(255) NOT NULL,
  `message`    TEXT,
  `is_read`    TINYINT(1) DEFAULT 0,
  `data`       TEXT,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_user_unread` (`user_id`, `is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- â”€â”€ Rate Limiting â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
CREATE TABLE IF NOT EXISTS `rate_limits` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `identifier`    VARCHAR(100) NOT NULL,
  `action`        VARCHAR(60) NOT NULL,
  `attempts`      INT DEFAULT 1,
  `blocked_until` DATETIME,
  `last_attempt`  DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `idx_identifier_action` (`identifier`, `action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- SEED DATA
-- ============================================================

-- â”€â”€ Categories â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
INSERT IGNORE INTO `categories` (`slug`,`name`,`telugu`,`emoji`,`image`,`sort_order`) VALUES
('agarbatti','Agarbatti & Incense','à°…à°—à°°à±à°¬à°¤à±à°¤à°¿','ðŸª”','images/cat_agarbatti.png',1),
('camphor','Camphor (Kapoor)','à°•à°°à±à°ªà±‚à°°à°‚','ðŸ”¥','images/cat_diyas.png',2),
('kumkum','Kumkum & Haldi','à°•à±à°‚à°•à±à°® à°ªà°¸à±à°ªà±','ðŸŸ¡','images/cat_kumkum.png',3),
('oils','Pooja Oils & Ghee','à°¨à±‚à°¨à±† & à°¨à±†à°¯à±à°¯à°¿','ðŸ«™','images/cat_oils.png',4),
('diyas','Diyas & Lamps','à°¦à±€à°ªà°¾à°²à±','ðŸ•¯ï¸','images/cat_diyas.png',5),
('photos','God Photos & Frames','à°¦à±‡à°µà±à°¡à°¿ à°«à±‹à°Ÿà±‹à°²à±','ðŸ–¼ï¸','images/cat_idols.png',6),
('idols','Idols (Vigrahas)','à°µà°¿à°—à±à°°à°¹à°¾à°²à±','ðŸº','images/cat_idols.png',7),
('thali','Puja Thali & Vessels','à°ªà±‚à°œà°¾ à°ªà°¾à°¤à±à°°à°²à±','âš±ï¸','images/cat_thali.png',8),
('malas','Malas & Rosaries','à°®à°¾à°²à°²à±','ðŸ“¿','images/cat_malas.png',9),
('havan','Havan Samagri','à°¹à°µà°¨à± à°¸à°¾à°®à°—à±à°°à°¿','ðŸ”±','images/cat_festival.png',10),
('festivals','Festival Kits','à°ªà°‚à°¡à±à°— à°•à°¿à°Ÿà±à°²à±','ðŸŽ‰','images/cat_festival.png',11),
('wedding','Wedding Items','à°µà°¿à°µà°¾à°¹ à°¸à°¾à°®à°—à±à°°à°¿','ðŸ’','images/cat_wedding.png',12);

-- â”€â”€ Products â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
INSERT IGNORE INTO `products` (`category_id`,`name`,`telugu_name`,`slug`,`description`,`price`,`original_price`,`stock_qty`,`images`,`sizes`,`tags`,`badge`,`is_featured`,`rating`,`review_count`) VALUES
(1,'Zed Black 3-IN-1 Premium Agarbatti','à°œà±†à°¡à± à°¬à±à°²à°¾à°•à± 3-IN-1 à°…à°—à°°à±à°¬à°¤à±à°¤à°¿','zed-black-3in1-agarbatti','Zed Black Premium 3-IN-1 Agarbatti. Long-lasting divine scent for daily puja.',60,80,200,'["images/1000090824.jpg"]','["Standard Pack"]','["zed black","premium","daily puja"]','popular',1,4.8,124),
(1,'Balaji 100 Divine Agarbathi 4IN1','à°¬à°¾à°²à°¾à°œà±€ 100 à°¡à°¿à°µà±ˆà°¨à± à°…à°—à°°à±à°¬à°¤à±à°¤à°¿','balaji-divine-4in1','Balaji 100 Divine Agarbathi â€“ Super Strong Premium Fragrance 4IN1 blend.',70,90,150,'["images/1000090825.jpg"]','["Standard Pack"]','["balaji","4-in-1","super strong"]','popular',1,4.7,98),
(1,'Darshan White Stone Incense Sticks','à°¦à°°à±à°¶à°¨à± à°µà±ˆà°Ÿà± à°¸à±à°Ÿà±‹à°¨à± à°…à°—à°°à±à°¬à°¤à±à°¤à°¿','darshan-white-stone','Darshan White Stone Incense Sticks â€“ enchanting perfumes with natural oils.',60,80,180,'["images/1000090826.jpg"]','["Standard Pack"]','["darshan","white stone","natural oils"]','new',1,4.9,203),
(1,'Darshan Black Stone Incense Sticks','à°¦à°°à±à°¶à°¨à± à°¬à±à°²à°¾à°•à± à°¸à±à°Ÿà±‹à°¨à± à°…à°—à°°à±à°¬à°¤à±à°¤à°¿','darshan-black-stone','Darshan Black Stone Incense Sticks â€“ unique fragrance for meditation.',60,80,160,'["images/1000090827.jpg"]','["Standard Pack"]','["darshan","black stone","meditation"]',NULL,1,4.8,156),
(1,'Ambica Durbar Bathi Herbal','à°…à°‚à°¬à°¿à°•à°¾ à°¦à°°à±à°¬à°¾à°°à± à°¬à°¤à±à°¤à°¿','ambica-durbar-bathi-herbal','Ambica Durbar Bathi â€“ India only Herbal Durbar Bathi. Hand rolled using 63 herbs.',60,75,200,'["images/1000090829.jpg"]','["Standard Pack"]','["ambica","durbar","herbal","63 herbs"]','popular',1,4.8,178),
(1,'Balaji Bindu Premium Incense Sticks','à°¬à°¾à°²à°¾à°œà±€ à°¬à°¿à°‚à°¦à± à°ªà±à°°à±€à°®à°¿à°¯à°‚ à°…à°—à°°à±à°¬à°¤à±à°¤à°¿','balaji-bindu-premium','Balaji Bindu Premium Incense Sticks â€“ Zipper-lock pouch for extra freshness.',70,90,150,'["images/1000090833.jpg"]','["Standard Pouch"]','["balaji","bindu","premium"]','popular',1,4.9,201);

INSERT IGNORE INTO `products` (`category_id`,`name`,`telugu_name`,`slug`,`description`,`price`,`original_price`,`stock_qty`,`images`,`sizes`,`tags`,`badge`,`is_featured`,`rating`,`review_count`) VALUES
(2,'Pure Camphor Tablets','à°•à°°à±à°ªà±‚à°°à°‚','pure-camphor-tablets','Pure white camphor tablets for aarti and puja. Burns completely without residue.',30,45,500,'["images/1000090829.jpg"]','["10g","50g","100g","250g"]','["aarti","daily puja"]','popular',1,4.9,312),
(2,'Camphor Powder Loose','à°•à°°à±à°ªà±‚à°°à°‚ à°ªà±Šà°¡à°¿','camphor-powder-loose','Fine camphor powder for havan and special rituals.',55,70,200,'["images/1000090830.jpg"]','["50g","100g","250g"]','["havan","powder","ritual"]',NULL,0,4.6,89),
(2,'Camphor Aarti Lamp Refill','à°•à°°à±à°ªà±‚à°°à± à°†à°°à°¤à°¿','camphor-aarti-lamp-refill','Premium camphor cubes for brass aarti lamps. Pure grade camphor.',120,150,100,'["images/1000090831.jpg"]','["Pack of 12","Pack of 24"]','["aarti lamp","premium"]','new',0,4.8,67);

INSERT IGNORE INTO `products` (`category_id`,`name`,`telugu_name`,`slug`,`description`,`price`,`original_price`,`stock_qty`,`images`,`sizes`,`tags`,`badge`,`is_featured`,`rating`,`review_count`) VALUES
(3,'Premium Red Kumkum','à°•à±à°‚à°•à±à°®','premium-red-kumkum','Pure bright red kumkum from natural turmeric. Essential for tilak and goddess worship.',25,35,500,'["images/1000090832.jpg"]','["25g","50g","100g","250g"]','["tilak","goddess","daily puja"]','popular',1,4.9,445),
(3,'Pure Haldi Turmeric Powder','à°ªà°¸à±à°ªà±','pure-haldi-turmeric','Bright yellow turmeric powder for tilak and haldi ceremony.',30,42,400,'["images/1000090833.jpg"]','["50g","100g","250g","500g"]','["haldi","tilak","pure"]',NULL,1,4.8,278),
(3,'Kumkum Haldi Combo Pack','à°•à±à°‚à°•à±à°® à°ªà°¸à±à°ªà± à°•à°¾à°‚à°¬à±‹','kumkum-haldi-combo','Value combo pack with premium red kumkum and pure haldi. Perfect for festivals.',55,80,200,'["images/1000090824.jpg"]','["50g+50g","100g+100g"]','["combo","value pack","festival"]','sale',0,4.7,132),
(3,'Sindoor Vermilion','à°¸à°¿à°‚à°§à±‚à°°à±','sindoor-vermilion','Auspicious deep red sindoor for Goddess Lakshmi puja. Natural ingredients.',20,30,300,'["images/1000090825.jpg"]','["5g","10g","25g","50g"]','["sindoor","lakshmi","marriage"]',NULL,0,4.6,198);

INSERT IGNORE INTO `products` (`category_id`,`name`,`telugu_name`,`slug`,`description`,`price`,`original_price`,`stock_qty`,`images`,`sizes`,`tags`,`badge`,`is_featured`,`rating`,`review_count`) VALUES
(4,'Pure Sesame Til Pooja Oil','à°¨à±à°µà±à°µà±à°² à°¨à±‚à°¨à±†','pure-sesame-til-oil','Cold-pressed pure sesame oil for diya and lamp.',120,160,150,'["images/1000090826.jpg"]','["100ml","250ml","500ml","1L"]','["sesame oil","shani puja"]',NULL,0,4.8,167),
(4,'Pure Cow Ghee Desi','à°†à°µà± à°¨à±†à°¯à±à°¯à°¿','pure-cow-ghee-desi','Pure A2 desi cow ghee for havan, lamp and prasad.',450,580,80,'["images/1000090827.jpg"]','["100g","250g","500g","1kg"]','["cow ghee","havan","prasad"]','popular',1,4.9,234),
(4,'Coconut Pooja Oil','à°•à±Šà°¬à±à°¬à°°à°¿ à°¨à±‚à°¨à±†','coconut-pooja-oil','Pure cold-pressed coconut oil for lamp. Ideal for Vishnu puja.',90,120,120,'["images/1000090828.jpg"]','["100ml","250ml","500ml","1L"]','["coconut oil","vishnu puja"]',NULL,0,4.7,143);

INSERT IGNORE INTO `products` (`category_id`,`name`,`telugu_name`,`slug`,`description`,`price`,`original_price`,`stock_qty`,`images`,`sizes`,`tags`,`badge`,`is_featured`,`rating`,`review_count`) VALUES
(5,'Brass Deepam 5 inch','à°ªà°¿à°¤à°² à°¦à±€à°ªà°‚','brass-deepam-5inch','Traditional 5-inch brass deepam for daily worship. Heavy base.',299,399,60,'["images/1000090830.jpg"]','["Single","Set of 2","Set of 5"]','["brass diya","deepam","daily puja"]','popular',1,4.9,189),
(5,'Clay Diyas Mitti','à°®à°Ÿà±à°Ÿà°¿ à°¦à±€à°ªà°¾à°²à±','clay-diyas-mitti','Handmade earthen clay diyas for Diwali and festivals. Eco-friendly.',60,80,300,'["images/1000090831.jpg"]','["Pack of 10","Pack of 20","Pack of 50"]','["diwali","clay diya","eco-friendly"]','popular',1,4.6,267),
(5,'Pancha Mukhi Deepam','à°ªà°‚à°šà°®à±à°–à°¿ à°¦à±€à°ªà°‚','pancha-mukhi-deepam','Five-faced brass deepam for special pujas and navaratri.',450,599,30,'["images/1000090832.jpg"]','["Medium","Large"]','["pancha mukhi","navaratri"]','new',0,4.8,78);

INSERT IGNORE INTO `products` (`category_id`,`name`,`telugu_name`,`slug`,`description`,`price`,`original_price`,`stock_qty`,`images`,`sizes`,`tags`,`badge`,`is_featured`,`rating`,`review_count`) VALUES
(7,'Brass Ganesha Idol 4 inch','à°—à°£à±‡à°¶ à°µà°¿à°—à±à°°à°¹à°‚','brass-ganesha-idol-4inch','Hand-crafted pure brass Lord Ganesha idol, 4-inch seated.',799,1099,40,'["images/1000090828.jpg"]','["2 inch","4 inch","6 inch","8 inch"]','["ganesha","brass idol","handcrafted"]','popular',1,4.9,198),
(7,'Lakshmi Idol Silver Plated','à°²à°•à±à°·à±à°®à±€à°¦à±‡à°µà°¿ à°µà°¿à°—à±à°°à°¹à°‚','lakshmi-idol-silver-plated','Silver-plated Goddess Lakshmi idol. Exquisite craftsmanship.',1299,1699,20,'["images/1000090829.jpg"]','["3 inch","5 inch","7 inch"]','["lakshmi","silver plated","gifting"]','new',1,4.8,87),
(7,'Saraswati Brass Idol','à°¸à°°à°¸à±à°µà°¤à°¿ à°µà°¿à°—à±à°°à°¹à°‚','saraswati-brass-idol','Goddess Saraswati brass idol with Veena. Antique brass finish.',899,1200,25,'["images/1000090830.jpg"]','["4 inch","6 inch","8 inch"]','["saraswati","navratri","education"]',NULL,0,4.7,64);

INSERT IGNORE INTO `products` (`category_id`,`name`,`telugu_name`,`slug`,`description`,`price`,`original_price`,`stock_qty`,`images`,`sizes`,`tags`,`badge`,`is_featured`,`rating`,`review_count`) VALUES
(8,'Brass Puja Thali Set','à°ªà±‚à°œà°¾ à°ªà°³à±à°³à±†à°‚ à°¸à±†à°Ÿà±','brass-puja-thali-set','Complete 7-piece brass puja thali set. Includes diya, bell, incense holder, kumkum holder.',699,950,50,'["images/1000090831.jpg"]','["Small","Medium","Large"]','["thali set","brass","gifting"]','popular',1,4.9,256),
(8,'Brass Kalash Sacred Pot','à°•à°²à°¶à°‚','brass-kalash-sacred-pot','Traditional brass kalash for Gruhapravesam and major pujas.',399,550,40,'["images/1000090832.jpg"]','["Small","Medium","Large"]','["kalash","gruhapravesam","wedding"]',NULL,0,4.8,134);

INSERT IGNORE INTO `products` (`category_id`,`name`,`telugu_name`,`slug`,`description`,`price`,`original_price`,`stock_qty`,`images`,`sizes`,`tags`,`badge`,`is_featured`,`rating`,`review_count`) VALUES
(9,'Rudraksha Mala 108 beads','à°°à±à°¦à±à°°à°¾à°•à±à°· à°®à°¾à°²','rudraksha-mala-108','Authentic 5-mukhi Rudraksha mala with 108 beads for japa.',599,850,30,'["images/1000090824.jpg"]','["6mm","8mm","10mm"]','["rudraksha","japa","meditation","shiva"]','popular',1,4.9,312),
(9,'Tulsi Mala Holy Basil','à°¤à±à°²à°¸à°¿ à°®à°¾à°²','tulsi-mala-holy-basil','Sacred Tulsi mala for Vishnu and Krishna japa. 108+1 beads.',150,220,80,'["images/1000090825.jpg"]','["Standard 108 beads"]','["tulsi","krishna","vishnu","japa"]',NULL,0,4.8,178),
(9,'Marigold Flower Garland','à°¬à°‚à°¤à°¿ à°ªà±‚à°² à°®à°¾à°²','marigold-flower-garland','Artificial marigold flower garland for deity decoration.',50,70,200,'["images/1000090826.jpg"]','["2 ft","4 ft","6 ft"]','["marigold","decoration","garland"]',NULL,0,4.6,234);

INSERT IGNORE INTO `products` (`category_id`,`name`,`telugu_name`,`slug`,`description`,`price`,`original_price`,`stock_qty`,`images`,`sizes`,`tags`,`badge`,`is_featured`,`rating`,`review_count`) VALUES
(10,'Havan Samagri Mix Premium','à°¹à°µà°¨à± à°¸à°¾à°®à°—à±à°°à°¿','havan-samagri-premium','Premium blend of 51 sacred herbs for havan and homam.',199,280,100,'["images/1000090827.jpg"]','["100g","250g","500g","1kg"]','["havan","homam","51 herbs","premium"]','popular',1,4.8,167),
(10,'Copper Havan Kund','à°¹à°µà°¨à± à°•à±à°‚à°¡à±','copper-havan-kund','Pure copper havan kund for Agni puja and homam at home.',899,1299,20,'["images/1000090828.jpg"]','["Small 6x6","Medium 9x9","Large 12x12"]','["copper","havan kund","homam"]',NULL,0,4.9,89);

INSERT IGNORE INTO `products` (`category_id`,`name`,`telugu_name`,`slug`,`description`,`price`,`original_price`,`stock_qty`,`images`,`sizes`,`tags`,`badge`,`is_featured`,`rating`,`review_count`) VALUES
(11,'Vinayaka Chavithi Complete Kit','à°µà°¿à°¨à°¾à°¯à°• à°šà°µà°¿à°¤à°¿ à°•à°¿à°Ÿà±','vinayaka-chavithi-kit','All-in-one Ganesh Chaturthi kit with modak plate, durva grass, dhoop, incense, kumkum.',549,799,60,'["images/1000090829.jpg"]','["Basic","Premium","Deluxe"]','["ganesh chaturthi","complete kit","festival"]','popular',1,4.9,423),
(11,'Diwali Puja Complete Kit','à°¦à±€à°ªà°¾à°µà°³à°¿ à°ªà±‚à°œà°¾ à°•à°¿à°Ÿà±','diwali-puja-kit','Complete Diwali puja kit with diyas, camphor, Lakshmi idol, kumkum, dhoop.',649,950,50,'["images/1000090830.jpg"]','["Standard","Premium"]','["diwali","lakshmi puja","complete kit"]','popular',1,4.8,267),
(11,'Ugadi Puja Kit','à°‰à°—à°¾à°¦à°¿ à°ªà±‚à°œà°¾ à°•à°¿à°Ÿà±','ugadi-puja-kit','Complete Telugu New Year Ugadi kit with neem flowers, jaggery and 6 rasa items.',349,499,40,'["images/1000090831.jpg"]','["Small","Family Pack"]','["ugadi","telugu new year","festival"]','new',0,4.7,178);

INSERT IGNORE INTO `products` (`category_id`,`name`,`telugu_name`,`slug`,`description`,`price`,`original_price`,`stock_qty`,`images`,`sizes`,`tags`,`badge`,`is_featured`,`rating`,`review_count`) VALUES
(12,'Telugu Wedding Samagri Kit','à°µà°¿à°µà°¾à°¹ à°¸à°¾à°®à°—à±à°°à°¿ à°•à°¿à°Ÿà±','telugu-wedding-samagri-kit','Complete Telugu wedding puja kit. Includes coconut, mango leaves, sacred thread, turmeric.',1499,2200,20,'["images/1000090824.jpg"]','["Basic","Complete","Premium Deluxe"]','["wedding","vivah","complete","telugu"]','popular',1,4.9,312),
(12,'Kankana Dhara Thread Set','à°•à°‚à°•à°£ à°§à°¾à°°à°£ à°¸à±†à°Ÿà±','kankana-dhara-thread-set','Sacred kankanam wrist thread for bride and groom. Yellow silk thread.',299,420,60,'["images/1000090825.jpg"]','["Standard 2 sets","Family 5 sets"]','["kankanam","wedding","sacred thread"]',NULL,0,4.8,134),
(12,'Mangalsutra Thread Dhaarana','à°®à°‚à°—à°³à°¸à±‚à°¤à±à°°à°‚ à°¦à°¾à°°à°‚','mangalsutra-thread-dhaarana','Traditional yellow mangalsutra dhaaranu thread with turmeric and kumkum.',150,220,100,'["images/1000090826.jpg"]','["Standard","With Pendant Space"]','["mangalsutra","wedding","auspicious"]',NULL,0,4.9,298);

-- â”€â”€ Default Admin Account â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
-- Password: Admin@2025 (CHANGE IMMEDIATELY AFTER FIRST LOGIN!)
INSERT IGNORE INTO `admin_users` (`name`,`email`,`password_hash`,`role`) VALUES
('Store Owner','admin@srimanikanta.com','$2y$10$Vhr06SdtZsPUwnXJm46Olu/IV5FFpA85QHGTzOHjn8OIMnYBOVlhC','superadmin');

