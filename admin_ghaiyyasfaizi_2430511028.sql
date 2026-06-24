

CREATE TABLE IF NOT EXISTS users (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(120)  NOT NULL,
  username    VARCHAR(60)   NOT NULL UNIQUE,
  password    VARCHAR(255)  NOT NULL,      
  created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


INSERT IGNORE INTO users (name, username, password) VALUES
  ('Administrator', 'admin',
   'yas516');

CREATE TABLE IF NOT EXISTS services (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama        VARCHAR(150)  NOT NULL,
  game        VARCHAR(80)   NOT NULL,
  kategori    VARCHAR(80)   NOT NULL,
  harga       INT UNSIGNED  NOT NULL DEFAULT 0,
  rating      DECIMAL(3,1)  NOT NULL DEFAULT 0.0,
  status      ENUM('Aktif','Nonaktif') NOT NULL DEFAULT 'Aktif',
  deskripsi   TEXT,
  created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
                            ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS service_images (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  service_id  INT UNSIGNED NOT NULL,
  image_data  LONGTEXT     NOT NULL,  
  sort_order  TINYINT      NOT NULL DEFAULT 0,
  FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS orders (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama        VARCHAR(120)  NOT NULL,
  service_id  INT UNSIGNED,            
  tanggal     DATE          NOT NULL,
  status      ENUM('Menunggu','Diproses','Selesai','Dibatalkan') NOT NULL DEFAULT 'Menunggu',
  ttd         LONGTEXT,                
  created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
                            ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS sessions (
  token       CHAR(64)     NOT NULL PRIMARY KEY,
  user_id     INT UNSIGNED NOT NULL,
  expires_at  DATETIME     NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;


INSERT IGNORE INTO services (nama, game, kategori, harga, rating, status, deskripsi) VALUES
  ('Joki Rank Mythic',  'Mobile Legends',  'Rank Boost',    75000, 4.9, 'Aktif',    'Naik dari Epic ke Mythic, joki rank tinggi, garansi aman.'),
  ('Joki Radiant',      'Valorant',        'Rank Boost',   150000, 4.8, 'Aktif',    'Push rank Valorant hingga Radiant dengan tim pro.'),
  ('Farming Primogem',  'Genshin Impact',  'Farming Item',  60000, 4.7, 'Aktif',    'Farming primogem & material harian.'),
  ('Joki Top Global',   'PUBG Mobile',     'Win Streak',    90000, 4.6, 'Nonaktif', 'Naikkan tier ke Top Global Conqueror.'),
  ('Joki Rank Heroic',  'Free Fire',       'Rank Boost',    40000, 4.5, 'Aktif',    'Joki rank Free Fire sampai Heroic.');


INSERT IGNORE INTO orders (nama, service_id, tanggal, status) VALUES
  ('Yvonne', 1, '2026-06-01', 'Selesai'),
  ('Nanase',     2, '2026-06-05', 'Diproses'),
  ('y4ss',     3, '2026-06-09', 'Menunggu');