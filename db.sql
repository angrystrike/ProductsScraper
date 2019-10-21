DROP TABLE IF EXISTS `categories`;

CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `uri` varchar(100) NOT NULL,
  `image` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `ingridients`;
CREATE TABLE `ingridients` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(45) NOT NULL,
  `uri` varchar(100) NOT NULL,
  `image_name` varchar(100) NOT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ingridients_category_id_foreign_idx` (`category_id`),
  KEY `ingridients_origin_id_foreign_idx` (`parent_id`),
  CONSTRAINT `ingridients_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `ingridients_origin_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `ingridients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
