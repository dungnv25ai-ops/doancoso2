DROP DATABASE IF EXISTS `ql`;
CREATE DATABASE `ql` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `ql`;

CREATE TABLE `taikhoan` (`tendangnhap` varchar(20) NOT NULL, `matkhau` varchar(50) NOT NULL, `ten` varchar(100) NOT NULL, `vaitro` varchar(20) NOT NULL, PRIMARY KEY (`tendangnhap`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `khoahoc` (`tenkh` varchar(100) NOT NULL, `hocphi` decimal(12,2) DEFAULT NULL, `sotuanhoc` int(11) DEFAULT NULL, `sobuoihoc` int(11) DEFAULT NULL, PRIMARY KEY (`tenkh`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `giaovien` (`magv` varchar(20) NOT NULL, `tengv` varchar(100) DEFAULT NULL, `sdt` varchar(15) DEFAULT NULL, `email` varchar(100) DEFAULT NULL, `gioitinh` varchar(10) DEFAULT NULL, `monday` varchar(100) DEFAULT NULL, `kinhnghiem` int(11) DEFAULT NULL, `luong` decimal(12,2) DEFAULT NULL, `tienthuong` decimal(12,2) DEFAULT 0.00, `tienphat` decimal(12,2) DEFAULT 0.00, `danhan` int(11) DEFAULT 0, PRIMARY KEY (`magv`), CONSTRAINT `fk_gv_tk` FOREIGN KEY (`magv`) REFERENCES `taikhoan` (`tendangnhap`) ON DELETE CASCADE ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sinhvien` (`masv` varchar(20) NOT NULL, `ten` varchar(100) DEFAULT NULL, `gioitinh` varchar(10) DEFAULT NULL, `sdt` varchar(15) DEFAULT NULL, `email` varchar(100) DEFAULT NULL, PRIMARY KEY (`masv`), CONSTRAINT `fk_sv_tk` FOREIGN KEY (`masv`) REFERENCES `taikhoan` (`tendangnhap`) ON DELETE CASCADE ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sv_monhoc` (`id_dk` int(11) NOT NULL AUTO_INCREMENT, `masv` varchar(20) NOT NULL, `tenkh` varchar(100) NOT NULL, `danop` decimal(12,2) DEFAULT 0.00, `trangthai_hp` varchar(50) DEFAULT 'Chưa nộp', PRIMARY KEY (`id_dk`), CONSTRAINT `fk_dk_sv` FOREIGN KEY (`masv`) REFERENCES `sinhvien` (`masv`) ON DELETE CASCADE ON UPDATE CASCADE, CONSTRAINT `fk_dk_kh` FOREIGN KEY (`tenkh`) REFERENCES `khoahoc` (`tenkh`) ON DELETE CASCADE ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lophoc` (`id_lop` int(11) NOT NULL AUTO_INCREMENT, `tenlop` varchar(100) NOT NULL, `monhoc` varchar(100) NOT NULL, `magv` varchar(20) NOT NULL, `ngaybatdau` date DEFAULT NULL, `ngayketthuc` date DEFAULT NULL, `tudo` int(1) DEFAULT 1, PRIMARY KEY (`id_lop`), CONSTRAINT `fk_lop_kh` FOREIGN KEY (`monhoc`) REFERENCES `khoahoc` (`tenkh`) ON DELETE CASCADE ON UPDATE CASCADE, CONSTRAINT `fk_lop_gv` FOREIGN KEY (`magv`) REFERENCES `giaovien` (`magv`) ON DELETE RESTRICT ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lophoc_lich` (`id_lich` int(11) NOT NULL AUTO_INCREMENT, `id_lop` int(11) NOT NULL, `thu` varchar(20) NOT NULL, `ca` varchar(10) NOT NULL, `phong` varchar(20) NOT NULL, PRIMARY KEY (`id_lich`), CONSTRAINT `fk_lich_lop` FOREIGN KEY (`id_lop`) REFERENCES `lophoc` (`id_lop`) ON DELETE CASCADE ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `dangky_lop` (`id_dk_lop` int(11) NOT NULL AUTO_INCREMENT, `masv` varchar(20) NOT NULL, `id_lop` int(11) NOT NULL, `trangthai` int(1) DEFAULT 0, PRIMARY KEY (`id_dk_lop`), CONSTRAINT `fk_dklop_sv` FOREIGN KEY (`masv`) REFERENCES `sinhvien` (`masv`) ON DELETE CASCADE ON UPDATE CASCADE, CONSTRAINT `fk_dklop_lop` FOREIGN KEY (`id_lop`) REFERENCES `lophoc` (`id_lop`) ON DELETE CASCADE ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `diem` (`id_diem` int(11) NOT NULL AUTO_INCREMENT, `masv` varchar(20) NOT NULL, `monhoc` varchar(100) NOT NULL, `diem_tx1` float DEFAULT NULL, `diem_tx2` float DEFAULT NULL, `diem_tx3` float DEFAULT NULL, `diem_gk` float DEFAULT NULL, `diem_ck` float DEFAULT NULL, PRIMARY KEY (`id_diem`), CONSTRAINT `fk_diem_sv` FOREIGN KEY (`masv`) REFERENCES `sinhvien` (`masv`) ON DELETE CASCADE ON UPDATE CASCADE, CONSTRAINT `fk_diem_kh` FOREIGN KEY (`monhoc`) REFERENCES `khoahoc` (`tenkh`) ON DELETE CASCADE ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `thongbao` (`stt` int(11) NOT NULL AUTO_INCREMENT, `ngaydang` date DEFAULT NULL, `loaitb` varchar(50) NOT NULL DEFAULT 'Chung', `nguoidang` varchar(100) DEFAULT NULL, `nd` text DEFAULT NULL, PRIMARY KEY (`stt`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `taikhoan` VALUES ('admin', '1', 'Quản trị viên', 'qtv');

DELIMITER $$

CREATE PROCEDURE GenerateBigData()
BEGIN
    DECLARE i INT DEFAULT 1;
    DECLARE j INT;
    DECLARE sv_idx INT;
    DECLARE gv_id VARCHAR(20);
    DECLARE sv_id VARCHAR(20);
    DECLARE lop_id INT;
    DECLARE mon_hoc VARCHAR(100);

    INSERT INTO `taikhoan` VALUES ('ql01', '1', 'Quản lý Đào tạo 1', 'ql'), ('ql02', '1', 'Quản lý Đào tạo 2', 'ql'), ('ql03', '1', 'Quản lý Đào tạo 3', 'ql');
    INSERT INTO `khoahoc` VALUES ('Java', 5000000, 8, 2), ('Python', 4500000, 8, 2), ('C#', 4000000, 8, 2), ('ReactJS', 6000000, 8, 2), ('NodeJS', 5500000, 8, 2);

    INSERT INTO `taikhoan` VALUES ('001gv', '1', 'GV Nguyễn Văn A', 'gv'), ('002gv', '1', 'GV Trần Thị B', 'gv'), ('003gv', '1', 'GV Lê Văn C', 'gv'), ('004gv', '1', 'GV Phạm Văn D', 'gv'), ('005gv', '1', 'GV Vũ Thị E', 'gv');
    INSERT INTO `giaovien` (`magv`, `tengv`, `monday`, `luong`) VALUES
    ('001gv', 'GV Nguyễn Văn A', 'Java, Python', 15000000), ('002gv', 'GV Trần Thị B', 'Python, C#', 15000000), ('003gv', 'GV Lê Văn C', 'C#', 15000000), ('004gv', 'GV Phạm Văn D', 'ReactJS', 15000000), ('005gv', 'GV Vũ Thị E', 'NodeJS', 15000000);

    WHILE i <= 5 DO
        SET gv_id = CONCAT('00', i, 'gv');
        IF i = 1 THEN SET mon_hoc = 'Java';
        ELSEIF i = 2 THEN SET mon_hoc = 'Python';
        ELSEIF i = 3 THEN SET mon_hoc = 'C#';
        ELSEIF i = 4 THEN SET mon_hoc = 'ReactJS';
        ELSE SET mon_hoc = 'NodeJS';
        END IF;

        SET j = 1;
        WHILE j <= 100 DO
            SET sv_id = CONCAT(gv_id, LPAD(j, 3, '0'));
            INSERT INTO `taikhoan` VALUES (sv_id, '1', CONCAT('Sinh viên ', i, '-', j), 'sv');
            INSERT INTO `sinhvien` (`masv`, `ten`, `gioitinh`, `sdt`) VALUES (sv_id, CONCAT('Sinh viên ', i, '-', j), IF(j % 2 = 0, 'Nữ', 'Nam'), CONCAT('090', i, LPAD(j, 5, '0')));
            SET j = j + 1;
        END WHILE;

        SET j = 1;
        WHILE j <= 3 DO
            INSERT INTO `lophoc` (`tenlop`, `monhoc`, `magv`, `ngaybatdau`, `ngayketthuc`, `tudo`)
            VALUES (CONCAT('Lớp ', mon_hoc, ' 0', j), mon_hoc, gv_id, '2026-06-01', '2026-08-01', 1);
            SET lop_id = LAST_INSERT_ID();

            IF j = 1 THEN
                INSERT INTO `lophoc_lich` (`id_lop`, `thu`, `ca`, `phong`) VALUES
                (lop_id, 'Thứ 2', '1', CONCAT('Phòng ', (i % 4) + 1)),
                (lop_id, 'Thứ 4', '1', CONCAT('Phòng ', (i % 4) + 1));
            ELSEIF j = 2 THEN
                INSERT INTO `lophoc_lich` (`id_lop`, `thu`, `ca`, `phong`) VALUES
                (lop_id, 'Thứ 3', '2', CONCAT('Phòng ', (i % 4) + 1)),
                (lop_id, 'Thứ 5', '2', CONCAT('Phòng ', (i % 4) + 1));
            ELSE
                INSERT INTO `lophoc_lich` (`id_lop`, `thu`, `ca`, `phong`) VALUES
                (lop_id, 'Thứ 6', '3', CONCAT('Phòng ', (i % 4) + 1)),
                (lop_id, 'Thứ 7', '3', CONCAT('Phòng ', (i % 4) + 1));
            END IF;

            SET sv_idx = 1;
            WHILE sv_idx <= 30 DO
                SET sv_id = CONCAT(gv_id, LPAD((j - 1) * 30 + sv_idx, 3, '0'));
                INSERT INTO `dangky_lop` (`masv`, `id_lop`, `trangthai`) VALUES (sv_id, lop_id, 1);
                INSERT INTO `diem` (`masv`, `monhoc`, `diem_tx1`, `diem_gk`) VALUES (sv_id, mon_hoc, ROUND(7 + (RAND() * 3), 1), ROUND(6 + (RAND() * 4), 1));
                IF sv_idx % 2 = 0 THEN
                    INSERT INTO `sv_monhoc` (`masv`, `tenkh`, `danop`, `trangthai_hp`) VALUES (sv_id, mon_hoc, 5000000, 'Đã nộp đủ');
                ELSE
                    INSERT INTO `sv_monhoc` (`masv`, `tenkh`, `danop`, `trangthai_hp`) VALUES (sv_id, mon_hoc, 0, 'Chưa nộp');
                END IF;
                SET sv_idx = sv_idx + 1;
            END WHILE;

            SET j = j + 1;
        END WHILE;

        SET i = i + 1;
    END WHILE;

    INSERT INTO `thongbao` (`ngaydang`, `loaitb`, `nguoidang`, `nd`) VALUES (CURDATE(), 'Chung', 'Quản trị viên', 'Cập nhật lại Thời khóa biểu Ma trận và chia phòng chuẩn.');
END$$

DELIMITER ;

CALL GenerateBigData();
DROP PROCEDURE GenerateBigData;
