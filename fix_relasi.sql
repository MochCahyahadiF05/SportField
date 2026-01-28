-- Tambah kolom jenis_id baru sebagai foreign key
ALTER TABLE lapangan ADD COLUMN jenis_id INT UNSIGNED;

-- Update jenis_id berdasarkan nama jenis yang ada
UPDATE lapangan l 
JOIN jenis_olahraga j ON l.jenis = j.nama 
SET l.jenis_id = j.id;

-- Tambah foreign key constraint
ALTER TABLE lapangan 
ADD CONSTRAINT fk_lapangan_jenis_id 
FOREIGN KEY (jenis_id) REFERENCES jenis_olahraga(id) ON DELETE RESTRICT ON UPDATE CASCADE;

-- Drop kolom jenis lama
ALTER TABLE lapangan DROP COLUMN jenis;

-- Rename jenis_id ke jenis
ALTER TABLE lapangan CHANGE COLUMN jenis_id jenis INT UNSIGNED;

-- Verify
SELECT id, nama, jenis FROM lapangan ORDER BY id;
