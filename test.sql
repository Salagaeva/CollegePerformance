# SQL Manager for MySQL 5.5.1.45563
# ---------------------------------------
# Host     : localhost
# Port     : 3306
# Database : test


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

SET FOREIGN_KEY_CHECKS=0;

CREATE DATABASE `test`
    CHARACTER SET 'utf8'
    COLLATE 'utf8_general_ci';

USE `test`;

#
# Structure for the `groups_college` table : 
#

CREATE TABLE `groups_college` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB
AUTO_INCREMENT=5 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'
;

#
# Structure for the `users` table : 
#

CREATE TABLE `users` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `login` VARCHAR(50) COLLATE utf8_general_ci NOT NULL,
  `password` VARCHAR(255) COLLATE utf8_general_ci NOT NULL,
  `role` ENUM('admin','teacher','student') COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `login` (`login`) USING BTREE
) ENGINE=InnoDB
AUTO_INCREMENT=12 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'
;

#
# Structure for the `students` table : 
#

CREATE TABLE `students` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(100) COLLATE utf8_general_ci NOT NULL,
  `group_id` INTEGER(11) DEFAULT NULL,
  `user_id` INTEGER(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `group_id` (`group_id`) USING BTREE,
  KEY `user_id` (`user_id`) USING BTREE,
  CONSTRAINT `students_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups_college` (`id`) ON DELETE SET NULL,
  CONSTRAINT `students_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB
AUTO_INCREMENT=5 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'
;

#
# Structure for the `subjects` table : 
#

CREATE TABLE `subjects` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB
AUTO_INCREMENT=8 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'
;

#
# Structure for the `teachers` table : 
#

CREATE TABLE `teachers` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(100) COLLATE utf8_general_ci NOT NULL,
  `user_id` INTEGER(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user_id` (`user_id`) USING BTREE,
  CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB
AUTO_INCREMENT=5 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'
;

#
# Structure for the `attendance` table : 
#

CREATE TABLE `attendance` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `student_id` INTEGER(11) DEFAULT NULL,
  `subject_id` INTEGER(11) DEFAULT NULL,
  `teacher_id` INTEGER(11) DEFAULT NULL,
  `date` DATE DEFAULT NULL,
  `status` ENUM('Присутствовал','Отсутствовал') COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE,
  KEY `subject_id` (`subject_id`) USING BTREE,
  KEY `teacher_id` (`teacher_id`) USING BTREE,
  CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendance_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB
AUTO_INCREMENT=1 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'
;

#
# Structure for the `grades` table : 
#

CREATE TABLE `grades` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `student_id` INTEGER(11) DEFAULT NULL,
  `subject_id` INTEGER(11) DEFAULT NULL,
  `teacher_id` INTEGER(11) DEFAULT NULL,
  `grade` INTEGER(11) DEFAULT NULL,
  `date` DATE DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `student_id` (`student_id`) USING BTREE,
  KEY `subject_id` (`subject_id`) USING BTREE,
  KEY `teacher_id` (`teacher_id`) USING BTREE,
  CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `grades_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `grades_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB
AUTO_INCREMENT=7 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'
;

#
# Structure for the `teacher_subjects` table : 
#

CREATE TABLE `teacher_subjects` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `teacher_id` INTEGER(11) DEFAULT NULL,
  `subject_id` INTEGER(11) DEFAULT NULL,
  `group_id` INTEGER(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `teacher_id` (`teacher_id`) USING BTREE,
  KEY `subject_id` (`subject_id`) USING BTREE,
  KEY `group_id` (`group_id`) USING BTREE,
  CONSTRAINT `teacher_subjects_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `teacher_subjects_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `teacher_subjects_ibfk_3` FOREIGN KEY (`group_id`) REFERENCES `groups_college` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB
AUTO_INCREMENT=5 ROW_FORMAT=DYNAMIC CHARACTER SET 'utf8' COLLATE 'utf8_general_ci'
;

#
# Data for the `groups_college` table  (LIMIT 0,500)
#

INSERT INTO `groups_college` (`id`, `name`) VALUES
  (1,'ИСиП-9-22'),
  (2,'ИСиП-9-23'),
  (3,'ИСиП-9-24'),
  (4,'ИСиП-9-25');
COMMIT;

#
# Data for the `users` table  (LIMIT 0,500)
#

INSERT INTO `users` (`id`, `login`, `password`, `role`) VALUES
  (1,'admin','0192023a7bbd73250516f069df18b500','admin'),
  (3,'student1','827ccb0eea8a706c4c34a16891f84e7b','student'),
  (6,'Salagaeva1','525df2584ff2b78b9b6c06bb1be35950','student'),
  (8,'Kashiytov3','d15a378f3875ea4fcb60aad922ae3f6e','student'),
  (10,'Stepochkina','5c9fa6a4245ab6bb8eb8bbb3baac442d','teacher'),
  (11,'Salagaev','a0d74fd2bde46862f1e720c9b5a9fc88','teacher');
COMMIT;

#
# Data for the `students` table  (LIMIT 0,500)
#

INSERT INTO `students` (`id`, `full_name`, `group_id`, `user_id`) VALUES
  (3,'Салагаева Дарья Алексеевна',1,6),
  (4,'Кашиутов Вадим Александрович ',3,8);
COMMIT;

#
# Data for the `subjects` table  (LIMIT 0,500)
#

INSERT INTO `subjects` (`id`, `name`) VALUES
  (1,'Базы данных'),
  (2,'Графический дизайн'),
  (3,'Экономика'),
  (4,'Компьютерные сети'),
  (5,'Физическая культура'),
  (6,'Философия'),
  (7,'Живопись');
COMMIT;

#
# Data for the `teachers` table  (LIMIT 0,500)
#

INSERT INTO `teachers` (`id`, `full_name`, `user_id`) VALUES
  (3,'Степочкина Ирина Владимировна ',10),
  (4,'Салагаев Алексей Сергеевич',11);
COMMIT;

#
# Data for the `grades` table  (LIMIT 0,500)
#

INSERT INTO `grades` (`id`, `student_id`, `subject_id`, `teacher_id`, `grade`, `date`) VALUES
  (5,3,3,3,3,'2026-02-12'),
  (6,3,4,4,2,'2026-02-12');
COMMIT;

#
# Data for the `teacher_subjects` table  (LIMIT 0,500)
#

INSERT INTO `teacher_subjects` (`id`, `teacher_id`, `subject_id`, `group_id`) VALUES
  (3,3,3,NULL),
  (4,4,4,NULL);
COMMIT;



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;