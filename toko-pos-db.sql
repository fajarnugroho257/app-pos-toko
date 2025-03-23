/*
SQLyog Ultimate v12.4.3 (64 bit)
MySQL - 8.0.30 : Database - toko-pos-db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`toko-pos-db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `toko-pos-db`;

/*Table structure for table `app_heading_menu` */

DROP TABLE IF EXISTS `app_heading_menu`;

CREATE TABLE `app_heading_menu` (
  `app_heading_id` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `app_heading_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `app_heading_icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `app_heading_urut` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`app_heading_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `app_heading_menu` */

insert  into `app_heading_menu`(`app_heading_id`,`app_heading_name`,`app_heading_icon`,`app_heading_urut`,`created_at`,`updated_at`) values
('H0000','Dashboard','fas fa-tachometer-alt','1','2024-11-19 01:24:28','2024-11-25 02:44:25'),
('H0001','Master Data Menu','fas fa-database','2','2024-11-19 01:24:28','2024-11-25 02:44:39'),
('H0002','Master Data User','fas fa-users','8','2024-11-19 01:24:28','2024-12-17 18:46:04'),
('H0005','Data Toko','fas fa-database','3','2024-11-25 02:32:28','2024-11-25 02:45:26'),
('H0006','Master Data Barang','fas fa-binoculars','5','2024-11-25 02:48:00','2024-12-05 20:25:43'),
('H0007','Akun Pengguna','fas fa-users','6','2024-11-25 20:09:17','2024-11-28 15:48:51'),
('H0008','Transaksi','fas fa-file-invoice-dollar','4','2024-11-28 15:49:06','2024-12-05 20:25:30'),
('H0009','Laporan','fas fa-book','7','2024-12-17 18:46:17','2024-12-17 18:46:32');

/*Table structure for table `app_menu` */

DROP TABLE IF EXISTS `app_menu`;

CREATE TABLE `app_menu` (
  `menu_id` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `app_heading_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `menu_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_url` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_parent` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`menu_id`),
  KEY `app_menu_app_heading_id_foreign` (`app_heading_id`),
  CONSTRAINT `app_menu_app_heading_id_foreign` FOREIGN KEY (`app_heading_id`) REFERENCES `app_heading_menu` (`app_heading_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `app_menu` */

insert  into `app_menu`(`menu_id`,`app_heading_id`,`menu_name`,`menu_url`,`menu_parent`,`created_at`,`updated_at`) values
('M0000','H0000','Dashboard','dashboard','0','2024-11-19 01:24:28','2024-11-19 01:24:28'),
('M0001','H0001','Heading Aplikasi','headingApp','0','2024-11-19 01:24:28','2024-11-19 01:24:28'),
('M0002','H0001','Menu Aplikasi','menuApp','0','2024-11-19 01:24:28','2024-11-19 01:24:28'),
('M0003','H0002','Role Pengguna','rolePengguna','0','2024-11-19 01:24:28','2024-11-19 01:24:28'),
('M0004','H0002','Role Menu','roleMenu','0','2024-11-19 01:24:28','2024-11-19 01:24:28'),
('M0005','H0002','Data User','dataUser','0','2024-11-19 01:24:28','2024-11-19 01:24:28'),
('M0008','H0005','Toko Pusat','tokoPusat','0','2024-11-25 02:33:04','2024-11-25 02:33:04'),
('M0009','H0005','Toko Cabang','tokoCabang','0','2024-11-25 02:33:20','2024-11-25 02:33:20'),
('M0010','H0006','Master Barang','masterBarang','0','2024-11-25 02:50:07','2024-11-25 02:50:07'),
('M0011','H0006','Barang Cabang','barangCabang','0','2024-11-25 02:50:20','2024-11-25 02:50:20'),
('M0012','H0007','Kasir','akunKasir','0','2024-11-25 20:09:51','2024-11-25 20:09:51'),
('M0013','H0008','Penjualan','transaksi','0','2024-11-28 15:50:18','2024-11-28 15:50:18'),
('M0014','H0009','Log Barang Cabang','logBarang','0','2024-12-05 14:41:18','2025-02-07 20:19:32'),
('M0015','H0009','Laba Rugi','labaRugi','0','2024-12-17 18:47:38','2024-12-17 18:47:38'),
('M0016','H0009','Log Barang Pusat','logBarangMaster','0','2025-02-07 20:20:14','2025-02-07 20:20:14'),
('M0017','H0002','Penempatan','userPusat','0','2025-02-26 20:31:47','2025-02-26 20:32:08');

/*Table structure for table `app_role` */

DROP TABLE IF EXISTS `app_role`;

CREATE TABLE `app_role` (
  `role_id` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `app_role` */

insert  into `app_role`(`role_id`,`role_name`,`created_at`,`updated_at`) values
('R0001','developer','2024-11-19 01:24:27','2024-11-19 01:24:27'),
('R0002','admin','2024-11-19 01:24:27','2024-11-19 01:24:27'),
('R0003','pengguna','2024-11-19 01:24:27','2024-11-19 01:24:27'),
('R0004','Admin Toko Pusat','2024-11-25 02:35:28','2024-11-25 02:35:28'),
('R0005','Kasir','2024-11-25 20:19:40','2024-11-25 20:19:40'),
('R0006','Admin Gudang','2025-02-06 18:41:22','2025-02-06 18:41:22'),
('R0007','Master Data Gudang','2025-02-26 20:18:54','2025-02-26 20:18:54');

/*Table structure for table `app_role_menu` */

DROP TABLE IF EXISTS `app_role_menu`;

CREATE TABLE `app_role_menu` (
  `role_menu_id` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_id` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`role_menu_id`),
  UNIQUE KEY `app_role_menu_menu_id_role_id_unique` (`menu_id`,`role_id`),
  KEY `app_role_menu_role_id_foreign` (`role_id`),
  CONSTRAINT `app_role_menu_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `app_menu` (`menu_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `app_role_menu_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `app_role` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `app_role_menu` */

insert  into `app_role_menu`(`role_menu_id`,`menu_id`,`role_id`,`created_at`,`updated_at`) values
('RM00001','M0001','R0001','2024-11-19 01:24:28','2024-11-19 01:24:28'),
('RM00002','M0002','R0001','2024-11-19 01:24:28','2024-11-19 01:24:28'),
('RM00003','M0003','R0001','2024-11-19 01:24:28','2024-11-19 01:24:28'),
('RM00004','M0004','R0001','2024-11-19 01:24:28','2024-11-19 01:24:28'),
('RM00005','M0005','R0001','2024-11-19 01:24:28','2024-11-19 01:24:28'),
('RM00006','M0000','R0001','2024-11-19 01:24:28','2024-11-19 01:24:28'),
('RM00007','M0000','R0002','2024-11-19 01:24:28','2024-11-19 01:24:28'),
('RM00008','M0000','R0003','2024-11-19 01:24:28','2024-11-19 01:24:28'),
('RM00013','M0009','R0004','2024-11-25 02:35:42','2024-11-25 02:35:42'),
('RM00016','M0000','R0004','2024-11-25 02:35:53','2024-11-25 02:35:53'),
('RM00017','M0008','R0001','2024-11-25 02:50:53','2024-11-25 02:50:53'),
('RM00019','M0011','R0004','2024-11-25 13:12:01','2024-11-25 13:12:01'),
('RM00020','M0012','R0004','2024-11-25 20:10:04','2024-11-25 20:10:04'),
('RM00022','M0013','R0005','2024-11-28 15:51:31','2024-11-28 15:51:31'),
('RM00023','M0014','R0004','2024-12-05 14:41:39','2024-12-05 14:41:39'),
('RM00024','M0013','R0004','2024-12-05 20:26:32','2024-12-05 20:26:32'),
('RM00025','M0015','R0004','2024-12-17 18:48:11','2024-12-17 18:48:11'),
('RM00026','M0000','R0006','2025-02-06 18:41:30','2025-02-06 18:41:30'),
('RM00027','M0010','R0006','2025-02-06 18:41:42','2025-02-06 18:41:42'),
('RM00028','M0011','R0006','2025-02-06 18:41:44','2025-02-06 18:41:44'),
('RM00029','M0014','R0006','2025-02-06 18:41:49','2025-02-06 18:41:49'),
('RM00030','M0015','R0006','2025-02-06 18:41:52','2025-02-06 18:41:52'),
('RM00031','M0013','R0006','2025-02-06 18:41:54','2025-02-06 18:41:54'),
('RM00033','M0000','R0007','2025-02-26 20:19:03','2025-02-26 20:19:03'),
('RM00034','M0010','R0007','2025-02-26 20:19:08','2025-02-26 20:19:08'),
('RM00035','M0011','R0007','2025-02-26 20:19:10','2025-02-26 20:19:10'),
('RM00036','M0017','R0001','2025-02-26 20:32:22','2025-02-26 20:32:22');

/*Table structure for table `barang_cabang` */

DROP TABLE IF EXISTS `barang_cabang`;

CREATE TABLE `barang_cabang` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `barang_id` bigint unsigned NOT NULL,
  `cabang_id` bigint unsigned NOT NULL,
  `barang_stok` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cabang_barang_harga` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barang_st` enum('yes','no') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `barang_cabang_barang_id_foreign` (`barang_id`),
  KEY `barang_cabang_cabang_id_foreign` (`cabang_id`),
  CONSTRAINT `barang_cabang_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `barang_master` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `barang_cabang_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `toko_cabang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `barang_cabang` */

insert  into `barang_cabang`(`id`,`barang_id`,`cabang_id`,`barang_stok`,`cabang_barang_harga`,`barang_st`,`created_at`,`updated_at`) values
(84,26,3,'0',NULL,'yes','2025-02-12 10:59:04','2025-02-12 14:06:10'),
(85,26,5,'0',NULL,'yes','2025-02-12 14:07:50','2025-02-12 14:07:56'),
(86,26,6,'0',NULL,'yes','2025-02-12 14:09:21','2025-02-12 16:44:16'),
(87,12,5,'0',NULL,'yes','2025-02-12 14:11:34','2025-02-12 14:11:45'),
(88,12,6,'15',NULL,'yes','2025-02-12 16:42:42','2025-02-26 20:10:48'),
(89,11,5,'0',NULL,'yes','2025-02-18 15:10:44','2025-02-18 15:11:32'),
(90,11,3,'0',NULL,'yes','2025-02-18 15:13:07','2025-02-18 15:14:17'),
(91,11,6,'0',NULL,'yes','2025-02-18 15:18:30','2025-02-26 19:43:59'),
(92,17,6,'0',NULL,'yes','2025-02-18 16:14:18','2025-02-26 19:54:12'),
(93,27,10,'6',NULL,'yes','2025-03-01 11:38:53','2025-03-01 13:08:17'),
(94,27,11,'10',NULL,'yes','2025-03-01 11:41:44','2025-03-01 13:15:50'),
(95,28,10,'40',NULL,'yes','2025-03-01 14:21:12','2025-03-01 14:23:33');

/*Table structure for table `barang_cabang_log` */

DROP TABLE IF EXISTS `barang_cabang_log`;

CREATE TABLE `barang_cabang_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pusat_id` bigint unsigned NOT NULL,
  `cabang_id` bigint unsigned NOT NULL,
  `barang_cabang_id` bigint unsigned NOT NULL,
  `barang_awal` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barang_perubahan` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barang_transaksi` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barang_transaksi_id` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barang_akhir` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barang_st` enum('perubahan','transaksi') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `barang_cabang_log_user_id_foreign` (`user_id`),
  KEY `barang_cabang_log_pusat_id_foreign` (`pusat_id`),
  KEY `barang_cabang_log_cabang_id_foreign` (`cabang_id`),
  KEY `barang_cabang_log_barang_cabang_id_foreign` (`barang_cabang_id`),
  CONSTRAINT `barang_cabang_log_barang_cabang_id_foreign` FOREIGN KEY (`barang_cabang_id`) REFERENCES `barang_cabang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `barang_cabang_log_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `toko_cabang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `barang_cabang_log_pusat_id_foreign` FOREIGN KEY (`pusat_id`) REFERENCES `toko_pusat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `barang_cabang_log_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=189 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `barang_cabang_log` */

insert  into `barang_cabang_log`(`id`,`user_id`,`pusat_id`,`cabang_id`,`barang_cabang_id`,`barang_awal`,`barang_perubahan`,`barang_transaksi`,`barang_transaksi_id`,`barang_akhir`,`barang_st`,`created_at`,`updated_at`) values
(176,'U0005',2,6,88,'0','30',NULL,NULL,'30','perubahan','2025-02-26 20:09:50','2025-02-26 20:09:50'),
(177,'U0010',2,6,88,'30',NULL,'15','100','15','transaksi','2025-02-26 20:10:48','2025-02-26 20:10:48'),
(178,'U0020',5,10,93,'0','50',NULL,NULL,'50','perubahan','2025-03-01 11:39:27','2025-03-01 11:39:27'),
(179,'U0019',5,11,94,'0','30',NULL,NULL,'30','perubahan','2025-03-01 11:41:55','2025-03-01 11:41:55'),
(180,'U0021',5,10,93,'50',NULL,'7','1','43','transaksi','2025-03-01 11:43:38','2025-03-01 11:43:38'),
(181,'U0021',5,10,93,'43',NULL,'6','2','37','transaksi','2025-03-01 12:57:17','2025-03-01 12:57:17'),
(182,'U0021',5,10,93,'37',NULL,'16','3','21','transaksi','2025-03-01 13:01:37','2025-03-01 13:01:37'),
(183,'U0021',5,10,93,'21',NULL,'10','4','11','transaksi','2025-03-01 13:07:50','2025-03-01 13:07:50'),
(184,'U0021',5,10,93,'11',NULL,'5','5','6','transaksi','2025-03-01 13:08:17','2025-03-01 13:08:17'),
(185,'U0022',5,11,94,'30',NULL,'15','6','15','transaksi','2025-03-01 13:14:48','2025-03-01 13:14:48'),
(186,'U0022',5,11,94,'15',NULL,'5','7','10','transaksi','2025-03-01 13:15:50','2025-03-01 13:15:50'),
(187,'U0020',5,10,95,'0','50',NULL,NULL,'50','perubahan','2025-03-01 14:21:32','2025-03-01 14:21:32'),
(188,'U0021',5,10,95,'50',NULL,'10','8','40','transaksi','2025-03-01 14:23:33','2025-03-01 14:23:33');

/*Table structure for table `barang_master` */

DROP TABLE IF EXISTS `barang_master`;

CREATE TABLE `barang_master` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pusat_id` bigint unsigned NOT NULL,
  `barang_barcode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barang_nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barang_master_stok` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `barang_stok_minimal` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '75',
  `barang_harga_beli` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barang_harga_jual` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `barang_master_stok_hasil` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `barang_stok_perubahan` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `barang_grosir_harga_jual` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `barang_grosir_keuntungan` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `barang_grosir_persentase` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `barang_grosir_pembelian` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `barang_keuntungan` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `barang_persentase` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `barang_master_pusat_id_foreign` (`pusat_id`),
  CONSTRAINT `barang_master_pusat_id_foreign` FOREIGN KEY (`pusat_id`) REFERENCES `toko_pusat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `barang_master` */

insert  into `barang_master`(`id`,`pusat_id`,`barang_barcode`,`slug`,`barang_nama`,`barang_master_stok`,`barang_stok_minimal`,`barang_harga_beli`,`barang_harga_jual`,`barang_master_stok_hasil`,`barang_stok_perubahan`,`barang_grosir_harga_jual`,`barang_grosir_keuntungan`,`barang_grosir_persentase`,`barang_grosir_pembelian`,`barang_keuntungan`,`barang_persentase`,`created_at`,`updated_at`) values
(5,1,'8994834000355','good-day','Good Day','0','75','7500','8000','0','0','0','0','0','0','0','0','2024-11-25 17:29:00','2024-12-03 10:57:05'),
(8,2,'8994834000331','gorio','Gorio','0','75','400','500','0','0','0','0','0','0','0','0','2024-11-28 14:15:21','2024-11-28 14:53:38'),
(10,2,'8997035563544','pocari','pocari','0','75','6000','6600','0','50','6480','480','8','30','600','10','2024-11-28 14:44:06','2025-02-11 16:36:57'),
(11,2,'8996129809131','cloe','Cloe','0','75','2500','3000','0','0','2875','375','15','10','500','20','2024-11-28 14:44:44','2025-02-26 19:04:47'),
(12,2,'8995078500083','555','555','70','10','1000','1500','0','100','1400','400','40','15','500','50','2024-11-28 14:46:01','2025-02-26 20:09:50'),
(13,2,'8996001600269','le-mineral','Le Mineral','0','75','2500','3500','0','0','0','0','0','0','0','0','2024-11-28 14:46:40','2024-11-28 14:46:40'),
(14,2,'8886008101053','aqua','Aqua','0','75','3800','4000','0','-10','3900','100','3','25','200','5','2024-11-28 14:47:16','2025-02-12 10:10:12'),
(15,2,'8998866200318','mie-sedap','Mie Sedap','0','75','3000','4000','0','0','0','0','0','0','0','0','2024-11-28 14:57:46','2024-11-28 14:57:46'),
(17,2,'8992982206001','nestle-pure-life','Nestle  Pure Life','0','10','4500','4950','0','100','4905','405','9','10','450','10','2024-12-05 12:45:16','2025-02-26 18:42:33'),
(18,2,'8994834000334','rokok-djarum-super','Rokok Djarum Super','0','10','23400','25000','0','15','24500','1100','5','10','1600','7','2024-12-05 15:29:20','2025-02-09 12:06:07'),
(19,3,'8995078500083','555-2','555','0','75','400','500','0','0','0','0','0','0','0','0','2025-01-28 08:59:56','2025-01-28 10:10:24'),
(20,3,'8993007000253','indpmolk','indpmolk','0','75','600','1000','0','0','0','0','0','0','0','0','2025-01-28 09:59:12','2025-01-28 09:59:12'),
(21,3,'8992702000018','indomilk-kaleng','indomilk kaleng','0','75','6000','7500','0','0','0','0','0','0','0','0','2025-01-28 10:20:52','2025-01-28 10:20:52'),
(23,2,'1234512345123','test-edit-ya','TEST EDIT YA','0','10','1200','1500','0','0','0','0','0','0','0','0','2025-02-06 21:12:26','2025-02-06 21:29:39'),
(24,2,'8996129809137','good-day-2','Good Day','0','80','3000','4200','0','0','4200','1200','40','1000','1200','40','2025-02-11 16:45:41','2025-02-18 16:19:20'),
(25,2,'8996129809130','mie-gelas','Mie Gelas','0','100','1300','1800','0','0','1755','455','35','20','500','38','2025-02-11 16:55:39','2025-02-11 17:08:12'),
(26,2,'5678901234567','test-edit','TEST EDIT','0','10','40000','44000','0','0','43600','3600','9','10','4000','10','2025-02-12 10:53:46','2025-02-12 16:40:53'),
(27,5,'8995078500083','good-day-3','Good Day','20','25','3300','3500','0','0','3400','100','3','7','200','6','2025-03-01 11:33:25','2025-03-01 11:41:55'),
(28,5,'8996129809131','aqua-1l','Aqua 1L','50','10','4200','4600','0','0','4600','400','10','1000000','400','10','2025-03-01 14:20:16','2025-03-01 14:22:40');

/*Table structure for table `barang_master_log` */

DROP TABLE IF EXISTS `barang_master_log`;

CREATE TABLE `barang_master_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pusat_id` bigint unsigned NOT NULL,
  `cabang_id` bigint unsigned DEFAULT NULL,
  `barang_master_id` bigint unsigned NOT NULL,
  `barang_master_awal` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barang_master_perubahan` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barang_master_akhir` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barang_st` enum('pengiriman','pengurangan','penambahan') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `barang_master_log_user_id_foreign` (`user_id`),
  KEY `barang_master_log_pusat_id_foreign` (`pusat_id`),
  KEY `barang_master_log_barang_master_id_foreign` (`barang_master_id`),
  KEY `barang_master_log_cabang_id_foreign` (`cabang_id`),
  CONSTRAINT `barang_master_log_barang_master_id_foreign` FOREIGN KEY (`barang_master_id`) REFERENCES `barang_master` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `barang_master_log_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `toko_cabang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `barang_master_log_pusat_id_foreign` FOREIGN KEY (`pusat_id`) REFERENCES `toko_pusat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `barang_master_log_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `barang_master_log` */

insert  into `barang_master_log`(`id`,`user_id`,`pusat_id`,`cabang_id`,`barang_master_id`,`barang_master_awal`,`barang_master_perubahan`,`barang_master_akhir`,`barang_st`,`created_at`,`updated_at`) values
(41,'U0005',2,NULL,12,'0','100','100','penambahan','2025-02-26 20:08:48','2025-02-26 20:08:48'),
(42,'U0005',2,6,12,'100','-30','70','pengiriman','2025-02-26 20:09:50','2025-02-26 20:09:50'),
(43,'U0020',5,NULL,27,'0','100','100','penambahan','2025-03-01 11:34:10','2025-03-01 11:34:10'),
(44,'U0020',5,10,27,'100','-50','50','pengiriman','2025-03-01 11:39:27','2025-03-01 11:39:27'),
(45,'U0019',5,11,27,'50','-30','20','pengiriman','2025-03-01 11:41:55','2025-03-01 11:41:55'),
(46,'U0020',5,NULL,28,'0','100','100','penambahan','2025-03-01 14:20:44','2025-03-01 14:20:44'),
(47,'U0020',5,10,28,'100','-50','50','pengiriman','2025-03-01 14:21:32','2025-03-01 14:21:32');

/*Table structure for table `cart` */

DROP TABLE IF EXISTS `cart`;

CREATE TABLE `cart` (
  `cart_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pusat_id` bigint unsigned NOT NULL,
  `cabang_id` bigint unsigned NOT NULL,
  `cart_st` enum('yes','no','draft') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`cart_id`),
  KEY `cart_pusat_id_foreign` (`pusat_id`),
  KEY `cart_cabang_id_foreign` (`cabang_id`),
  CONSTRAINT `cart_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `toko_cabang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cart_pusat_id_foreign` FOREIGN KEY (`pusat_id`) REFERENCES `toko_pusat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `cart` */

insert  into `cart`(`cart_id`,`pusat_id`,`cabang_id`,`cart_st`,`created_at`,`updated_at`) values
('202503011143298917',5,10,'yes','2025-03-01 11:43:29','2025-03-01 11:43:38'),
('202503011257122928',5,10,'yes','2025-03-01 12:57:12','2025-03-01 12:57:17'),
('202503011301318435',5,10,'yes','2025-03-01 13:01:31','2025-03-01 13:01:37'),
('202503011307456360',5,10,'yes','2025-03-01 13:07:45','2025-03-01 13:07:50'),
('202503011308123758',5,10,'yes','2025-03-01 13:08:12','2025-03-01 13:08:17'),
('202503011314402631',5,11,'yes','2025-03-01 13:14:40','2025-03-01 13:14:48'),
('202503011315247544',5,11,'yes','2025-03-01 13:15:24','2025-03-01 13:15:50'),
('202503011423281916',5,10,'yes','2025-03-01 14:23:28','2025-03-01 14:23:33');

/*Table structure for table `cart_data` */

DROP TABLE IF EXISTS `cart_data`;

CREATE TABLE `cart_data` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barang_cabang_id` bigint unsigned NOT NULL,
  `cart_barcode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cart_harga_beli` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cart_harga_jual` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cart_nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cart_qty` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cart_subtotal` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cart_urut` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cart_diskon` enum('yes','no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cart_data_cart_id_foreign` (`cart_id`),
  KEY `cart_data_barang_cabang_id_foreign` (`barang_cabang_id`),
  CONSTRAINT `cart_data_barang_cabang_id_foreign` FOREIGN KEY (`barang_cabang_id`) REFERENCES `barang_cabang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cart_data_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `cart_data` */

insert  into `cart_data`(`id`,`cart_id`,`barang_cabang_id`,`cart_barcode`,`cart_harga_beli`,`cart_harga_jual`,`cart_nama`,`cart_qty`,`cart_subtotal`,`cart_urut`,`cart_diskon`,`created_at`,`updated_at`) values
(1,'202503011143298917',93,'8995078500083','3300','3400','Good Day','7','23800','1','yes','2025-03-01 11:43:29','2025-03-01 11:43:29'),
(2,'202503011257122928',93,'8995078500083','3300','3500','Good Day','6','21000','2','no','2025-03-01 12:57:12','2025-03-01 12:57:12'),
(3,'202503011301318435',93,'8995078500083','3300','3400','Good Day','16','54400','1','yes','2025-03-01 13:01:31','2025-03-01 13:01:31'),
(4,'202503011307456360',93,'8995078500083','3300','3400','Good Day','10','34000','2','yes','2025-03-01 13:07:45','2025-03-01 13:07:45'),
(5,'202503011308123758',93,'8995078500083','3300','3500','Good Day','5','17500','3','no','2025-03-01 13:08:12','2025-03-01 13:08:12'),
(6,'202503011314402631',94,'8995078500083','3300','3400','Good Day','15','51000','1','yes','2025-03-01 13:14:40','2025-03-01 13:14:40'),
(7,'202503011315247544',94,'8995078500083','3300','3500','Good Day','5','17500','2','no','2025-03-01 13:15:24','2025-03-01 13:15:24'),
(8,'202503011423281916',95,'8996129809131','4200','4600','Aqua 1L','10','46000','2','no','2025-03-01 14:23:28','2025-03-01 14:23:28');

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values
(1,'2019_12_14_000001_create_personal_access_tokens_table',1),
(2,'2024_08_28_081035_app_role',1),
(3,'2024_08_29_081211_app_menu',1),
(4,'2024_08_29_081213_users',1),
(5,'2024_08_29_081217_app_role_menu',1),
(6,'2024_08_29_092422_alter_column_role_name',1),
(7,'2024_08_30_020607_delete_column_role_name',1),
(8,'2024_08_30_024511_app_heading_menu',1),
(9,'2024_08_30_024724_add_column_heading_id',1),
(10,'2024_08_30_025128_add_foreign_key__heading_id',1),
(11,'2024_08_30_065332_alter_column',1),
(12,'2024_08_30_115218_add_column_nam',1),
(13,'2024_09_02_013705_primarykey_in_app_role_menu',1),
(14,'2024_09_02_025927_add_column_icon',1),
(15,'2024_11_25_015614_create_toko_pusat',2),
(17,'2024_11_25_020138_create_toko_cabang',3),
(18,'2024_11_25_020732_create_users_data',4),
(19,'2024_11_25_021558_create_barang_master',5),
(21,'2024_11_25_021729_create_barang_cabang',6),
(22,'2024_11_25_022343_add_column_pusat_id',7),
(23,'2024_11_25_022753_rename_column',8),
(24,'2024_11_25_023044_rename_column',9),
(25,'2024_11_25_024015_add_column_urut',10),
(26,'2024_11_25_140451_add_column_harga',11),
(27,'2024_11_25_140643_add_column_harga',12),
(28,'2024_11_25_201509_add_column_image',13),
(29,'2024_11_28_134637_add_column',14),
(30,'2024_11_28_143003_add_column',14),
(31,'2024_12_01_112800_create_cart',15),
(33,'2024_12_01_141643_drop_table_cart',16),
(34,'2024_12_01_142208_create_cart',17),
(35,'2024_12_01_142757_create_cart_data',18),
(36,'2024_12_03_153357_add_column',19),
(37,'2024_12_04_102159_create_transaksi_cart',20),
(38,'2024_12_05_144633_create_barang_cabang_log',21),
(39,'2024_12_17_191735_add_column',22),
(40,'2024_12_24_151035_add_column',23),
(41,'2025_02_06_185254_toko_pusat_user',24),
(42,'2025_02_06_205412_remove_user_id_from_toko_pusat',25),
(43,'2025_02_06_221545_add_column_barang_stok',26),
(44,'2025_02_06_223104_create_table_barang_master_log',27),
(46,'2025_02_12_101346_add_column_table_barang_master_log',28),
(47,'2025_02_26_193147_add_column_trans_cart',29),
(48,'2025_02_26_193935_remove_column_trans_cart',30),
(49,'2025_02_26_194116_add_column_cart_diskon',31);

/*Table structure for table `personal_access_tokens` */

DROP TABLE IF EXISTS `personal_access_tokens`;

CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `personal_access_tokens` */

/*Table structure for table `toko_cabang` */

DROP TABLE IF EXISTS `toko_cabang`;

CREATE TABLE `toko_cabang` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pusat_id` bigint unsigned NOT NULL,
  `cabang_nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cabang_alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `toko_cabang_pusat_id_foreign` (`pusat_id`),
  CONSTRAINT `toko_cabang_pusat_id_foreign` FOREIGN KEY (`pusat_id`) REFERENCES `toko_pusat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `toko_cabang` */

insert  into `toko_cabang`(`id`,`pusat_id`,`cabang_nama`,`slug`,`cabang_alamat`,`created_at`,`updated_at`) values
(1,1,'Al Hilal Cabang 1','al-hilal-cabang-1','Jl. Kh','2024-11-25 04:17:53','2024-11-25 04:40:57'),
(3,2,'Al Mart Cab. Jogja','al-mart-cab-jogja','Jl. Kh','2024-11-25 04:39:23','2024-11-25 04:39:37'),
(4,1,'Al Hilal Cabang 2','al-hilal-cabang-2','Jl. Temanggung','2024-11-25 04:41:11','2024-11-25 04:41:11'),
(5,2,'Al Mart Cab. Magelang','al-mart-cab-magelang','Jl. Mertoyudan','2024-11-25 04:41:51','2024-11-25 04:41:51'),
(6,2,'Al Mart Cab. Purworejo','al-mart-cab-purworejo','Jl. Purworejo','2024-11-25 20:36:45','2024-11-25 20:36:45'),
(7,3,'Sakti 1','sakti-1','Jl. Sakti 1','2025-01-28 08:48:40','2025-01-28 08:48:40'),
(8,3,'Sakti 2','sakti-2','Jl. Sakti 2','2025-01-28 08:48:51','2025-01-28 08:48:51'),
(10,5,'Coba 1 Mart','coba-1-mart','Jl. Temanggung','2025-03-01 11:26:53','2025-03-01 11:26:53'),
(11,5,'Coba Mart  2','coba-mart-2','Jl. Mertoyudan','2025-03-01 11:27:11','2025-03-01 11:27:11');

/*Table structure for table `toko_pusat` */

DROP TABLE IF EXISTS `toko_pusat`;

CREATE TABLE `toko_pusat` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pusat_nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pusat_pemilik` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pusat_alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `toko_pusat` */

insert  into `toko_pusat`(`id`,`pusat_nama`,`slug`,`pusat_pemilik`,`pusat_alamat`,`user_image`,`created_at`,`updated_at`) values
(1,'Pusat Al Hilal Mart','pusat-al-hilal-mart','Bagas','Jl. Magelang no.19','Screenshot 2024-12-23 155557.jpg','2024-11-25 03:10:07','2024-12-24 15:59:18'),
(2,'Toko AlfaMart','toko-alfamart','Suci Alma Sofi','Jl. Jogja no.1929','ilustrasi-toko.jpg','2024-11-25 04:30:51','2025-03-01 10:35:38'),
(3,'Sakti Mart','sakti-mart','Dio sakti','Jl. Tmg','png-transparent-computer-icons-editing-others-angle-logo-edit-icon.png','2025-01-28 08:48:06','2025-01-28 10:35:34'),
(5,'Coba mart','coba-mart','Dodi','Jakarta','images.jpg','2025-03-01 09:00:00','2025-03-01 11:30:40');

/*Table structure for table `toko_pusat_user` */

DROP TABLE IF EXISTS `toko_pusat_user`;

CREATE TABLE `toko_pusat_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pusat_id` bigint unsigned NOT NULL,
  `user_id` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `toko_pusat_user_pusat_id_foreign` (`pusat_id`),
  KEY `toko_pusat_user_user_id_foreign` (`user_id`),
  CONSTRAINT `toko_pusat_user_pusat_id_foreign` FOREIGN KEY (`pusat_id`) REFERENCES `toko_pusat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `toko_pusat_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `toko_pusat_user` */

insert  into `toko_pusat_user`(`id`,`pusat_id`,`user_id`,`created_at`,`updated_at`) values
(1,1,'U0004','2025-02-06 19:02:02','2025-02-06 19:02:02'),
(2,2,'U0005','2025-02-06 19:02:02','2025-02-06 19:02:02'),
(3,3,'U0011','2025-02-06 19:02:02','2025-02-06 19:02:02'),
(4,2,'U0014',NULL,NULL),
(5,2,'U0016',NULL,NULL),
(8,2,'U0017','2025-03-01 10:02:44','2025-03-01 10:02:44'),
(9,2,'U0018','2025-03-01 10:07:01','2025-03-01 10:07:01'),
(10,5,'U0019','2025-03-01 11:25:53','2025-03-01 11:25:53'),
(11,5,'U0020','2025-03-01 11:30:13','2025-03-01 11:30:13');

/*Table structure for table `transaksi_cart` */

DROP TABLE IF EXISTS `transaksi_cart`;

CREATE TABLE `transaksi_cart` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trans_pelanggan` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trans_total` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trans_bayar` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trans_kembalian` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trans_date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaksi_cart_cart_id_foreign` (`cart_id`),
  KEY `transaksi_cart_user_id_foreign` (`user_id`),
  CONSTRAINT `transaksi_cart_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `transaksi_cart_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `transaksi_cart` */

insert  into `transaksi_cart`(`id`,`cart_id`,`user_id`,`trans_pelanggan`,`trans_total`,`trans_bayar`,`trans_kembalian`,`trans_date`,`created_at`,`updated_at`) values
(1,'202503011143298917','U0021','didi','23800','25000','1200','2025-03-01 11:43:37','2025-03-01 11:43:37','2025-03-01 11:43:37'),
(2,'202503011257122928','U0021',NULL,'21000','22000','1000','2025-03-01 12:57:17','2025-03-01 12:57:17','2025-03-01 12:57:17'),
(3,'202503011301318435','U0021',NULL,'54400','60000','5600','2025-03-01 13:01:37','2025-03-01 13:01:37','2025-03-01 13:01:37'),
(4,'202503011307456360','U0021',NULL,'34000','35000','1000','2025-03-01 13:07:50','2025-03-01 13:07:50','2025-03-01 13:07:50'),
(5,'202503011308123758','U0021',NULL,'17500','20000','2500','2025-03-01 13:08:17','2025-03-01 13:08:17','2025-03-01 13:08:17'),
(6,'202503011314402631','U0022',NULL,'51000','55000','4000','2025-03-01 13:14:48','2025-03-01 13:14:48','2025-03-01 13:14:48'),
(7,'202503011315247544','U0022',NULL,'17500','20000','2500','2025-03-01 13:15:50','2025-03-01 13:15:50','2025-03-01 13:15:50'),
(8,'202503011423281916','U0021',NULL,'46000','50000','4000','2025-03-01 14:23:33','2025-03-01 14:23:33','2025-03-01 14:23:33');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `user_id` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_id` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `users_role_id_foreign` (`role_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `app_role` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`user_id`,`name`,`role_id`,`username`,`password`,`created_at`,`updated_at`) values
('U0001','Developer','R0001','dev','$2y$12$9CGKG2vsuUNq6YZ2nKBJUuCl8eRy7nNXuKk0UPPexH.trgRNgf3SC','2024-11-19 01:24:27','2024-11-19 01:24:27'),
('U0002','Admin','R0002','admin','$2y$12$ZUn2YQMt4FgQrnkwSNfJv.gq7zvyMz1o8ijMpZb.zir581M05QAj2','2024-11-19 01:24:28','2024-11-19 01:24:28'),
('U0003','Pengguna','R0003','pengguna','$2y$12$5PxqlFlI5VhDW.a77/byTuu7cYyOI0d3M0foe7Fm/QpXfMj7O.sDi','2024-11-19 01:24:28','2024-11-19 01:24:28'),
('U0004','Pusat Al Hilal Mart','R0004','pusat123','$2y$12$KoxRnM5WUhOTiw5kbC3zd.NfGkoGF9GFxwh.iycewVSsjwKL6oqi6','2024-11-25 02:36:37','2024-12-24 15:59:18'),
('U0005','Admin Toko AlfaMart','R0004','alma1234','$2y$12$Ra2dv6ZHGj6Uwrpmr0sxweqOSAaM1g5dmw0L7P12zwyaCyogTjreW','2024-11-25 04:30:22','2025-03-01 09:28:34'),
('U0006','Kasir Al - Mart PWR','R0005','ksralmart','$2y$12$06JhATwOb8.GWVHPabcnMOXD6c9og1Jx2ZkRlUwOlgNyLL9IRENxq','2024-11-28 15:16:01','2024-12-03 14:54:09'),
('U0007','Kasir Al Hilal','R0005','ksralhilal','$2y$12$tc3v3ukj1w6p/vHhnaqOMOSmzgsG30llDSzY5tKoww.JvETDIj4Xm','2024-12-03 11:08:14','2024-12-03 11:08:14'),
('U0008','Kasir Al Hilal 2','R0005','ksralhilal2','$2y$12$Bz/ZcK0XW2iGBHvLFnPviunvfyTwREf9vclr3/tbfzB.LVSkBWhVa','2024-12-03 11:41:18','2024-12-03 11:41:18'),
('U0009','Kasir Al - Mart Cabang MGL','R0005','ksralmart2','$2y$12$o1YwS82wghrT2R6jO2MgZupuBsQSSPWB8x5IRS47f3hRMnGilPztm','2024-12-05 20:06:23','2024-12-05 20:06:23'),
('U0010','Kasir  2 Al - Mart Purworejo','R0005','ksr2almartpwr','$2y$12$slYMzyGDKEJpk6V42VCMtuniJbcQ2aqq74F4gH4YVvu0d2l3EdGwC','2024-12-06 09:23:24','2024-12-06 09:23:24'),
('U0011','Sakti Mart','R0004','sakti','$2y$12$t2dGBvWO5DtneWlOG05c9ORfvpiyX29nrtzIqQuBwjwZhR4VoeyU.','2025-01-28 08:47:30','2025-01-28 10:35:34'),
('U0012','piancuk','R0005','piancuk','$2y$12$vruCYVYjeIoYw7/FwkrN9eoK7Qp3Ohiwv2DcoDP5iUj/JO1ysVHwy','2025-01-28 09:04:15','2025-01-28 09:04:15'),
('U0013','piancuk2','R0005','piancuk2','$2y$12$rT8f826YrODV670ci3yRH.SwEGr9WsOq00hmLnv67AVfj/bSqxvuK','2025-01-28 10:33:59','2025-01-28 10:33:59'),
('U0014','Gudang Almart','R0006','gudang','$2y$12$HncTbPuSPIf2L89wmlL.dOoXRQTWV./VF9rn8Xz6cXqNYx7wiD7TS','2025-02-06 18:42:16','2025-03-01 11:02:43'),
('U0015','MUC','R0005','muc123','$2y$12$XGSSPcYY13XQLizX5X5y8OfAdbNuUCxdEs8dQJGMStOX9FlK0AFF.','2025-02-06 21:41:45','2025-02-06 21:41:45'),
('U0016','Gudang Almart','R0006','gudangalma','$2y$12$MIMoQFZG4rswY2YcGdpTFONAT5p9CoOksoRYmxxzMNBSXEFpn4Hqa','2025-02-10 11:05:06','2025-03-01 10:24:45'),
('U0017','Master Gudang','R0007','mgudang','$2y$12$V1UVMJFM963ti2GyTWDJ7OlywVeAe.YZmGEXZVqhSkyzF6DWhK0JO','2025-02-26 20:20:03','2025-02-26 20:20:03'),
('U0018','Admin 2 Almart','R0004','admin2almart','$2y$12$vizPRyb74jC7nyi1uhzlKuZYfS1EVRqe5EnuasKI1vUdep7JtG.Fq','2025-03-01 10:06:21','2025-03-01 10:06:21'),
('U0019','Admin Toko Coba Mart','R0004','admcobamart','$2y$12$rsOJ6XrtC/eCj2QyaUWxCOTHIGo6a9IrYNlaTCBDiUxKslfTGerZm','2025-03-01 11:25:15','2025-03-01 11:25:15'),
('U0020','Admin Gudang Coba Mart','R0006','admgudang','$2y$12$8yK1pZtrRLepcx3a.t4ryeK/Wn9Cad4Zinp5W8Q9He3Wh9PE8stou','2025-03-01 11:29:33','2025-03-01 11:29:33'),
('U0021','Kasir 1','R0005','ksr1cobamart','$2y$12$1PLS4bHA5IASbJBPWPcoIuX5Qz6qhipdErybCDjlkXu9WrJN1fwSS','2025-03-01 11:37:20','2025-03-01 11:37:20'),
('U0022','Joko','R0005','ksr2cobamart','$2y$12$gQWSCsYs0gicxN/qCgwvOO0gF9xIcvmG2jtEio7ob6Px1qKnSPDKm','2025-03-01 13:14:03','2025-03-01 13:14:03');

/*Table structure for table `users_data` */

DROP TABLE IF EXISTS `users_data`;

CREATE TABLE `users_data` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cabang_id` bigint unsigned DEFAULT NULL,
  `user_id` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_nama_lengkap` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_alamat` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_jk` enum('L','P') COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_st` enum('yes','no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'yes',
  `user_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_data_cabang_id_foreign` (`cabang_id`),
  KEY `users_data_user_id_foreign` (`user_id`),
  CONSTRAINT `users_data_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `toko_cabang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `users_data_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users_data` */

insert  into `users_data`(`id`,`cabang_id`,`user_id`,`user_nama_lengkap`,`user_alamat`,`user_jk`,`user_st`,`user_image`,`created_at`,`updated_at`) values
(2,6,'U0006','Kasir Al - Mart PWR','Purworejo','P','yes','avatar3.png','2024-11-28 15:16:01','2024-12-03 14:54:09'),
(3,1,'U0007','Kasir Al Hilal','Magelang','P','yes','icon.jpg','2024-12-03 11:08:14','2024-12-03 11:08:14'),
(4,4,'U0008','Kasir Al Hilal 2','Magelang','L','yes','nasi-goreng.jpg','2024-12-03 11:41:18','2024-12-03 11:41:18'),
(5,5,'U0009','Kasir Al - Mart Cabang MGL','Magelang','L','yes','6e572ea980b99c4763c37bd36790fc70.jpg_720x720q80.jpg','2024-12-05 20:06:23','2024-12-05 20:06:23'),
(6,6,'U0010','Kasir  2 Al - Mart Purworejo','Purworejo','P','yes','813100242_d9a311c7-f430-42f0-9e0b-81233e1e1748_1024_1024.jpg','2024-12-06 09:23:24','2024-12-06 09:46:04'),
(7,7,'U0012','piancuk','Yogyakarta','L','yes','Screenshot 2024-10-28 111240.jpg','2025-01-28 09:04:15','2025-01-28 09:04:15'),
(8,8,'U0013','piancuk2','Metoyudan','L','yes','images.png','2025-01-28 10:33:59','2025-01-28 10:33:59'),
(9,6,'U0015','MUC','Tempuran','L','yes','Kartu_Tim_2125010000001.jpg','2025-02-06 21:41:45','2025-02-06 21:41:45'),
(11,NULL,'U0014','Gudang Almart','Magelang','P','yes','Screenshot 2024-12-23 155557.jpg','2025-03-01 11:02:43','2025-03-01 11:09:45'),
(12,NULL,'U0005','Admin Toko AlfaMart','Magelang','L','yes','Profil-1.jpg','2025-03-01 11:22:41','2025-03-01 11:22:41'),
(13,NULL,'U0020','Admin Gudang Coba Mart','Tempuran','L','yes','images.png','2025-03-01 11:31:11','2025-03-01 11:31:11'),
(14,10,'U0021','Kasir 1','Yogyakarta','P','yes','Screenshot 2024-12-23 160039.jpg','2025-03-01 11:37:20','2025-03-01 11:37:20'),
(15,11,'U0022','Joko','Purworejo','L','yes','Profil-1.jpg','2025-03-01 13:14:03','2025-03-01 13:14:03');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
