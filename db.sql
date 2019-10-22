DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
                              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                              `name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
                              `uri` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
                              `image` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
                              `img_origin_link` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
                              `slug` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `ingredients`;
CREATE TABLE `ingredients` (
                               `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                               `parent_id` int(10) unsigned DEFAULT NULL,
                               `name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
                               `uri` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
                               `image` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                               `img_origin_link` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                               `short_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                               `description` text COLLATE utf8mb4_unicode_ci,
                               `category_id` int(10) unsigned NOT NULL,
                               `slug` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                               PRIMARY KEY (`id`),
                               KEY `ingredients_category_id_foreign_idx` (`category_id`),
                               KEY `ingredients_origin_id_foreign_idx` (`parent_id`),
                               CONSTRAINT `ingredients_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
                               CONSTRAINT `ingredients_origin_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `ingredients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
