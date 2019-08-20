CREATE TABLE `nginx_zone` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `server_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `server_alias` text CHARACTER SET utf8 COLLATE utf8_bin,
  `document_root` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `php_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `php_port` int(11) DEFAULT NULL,
  `ssl_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `ssl_sst_header` tinyint(1) NOT NULL DEFAULT '1',
  `ssl_acme` tinyint(1) NOT NULL DEFAULT '1',
  `redirect_http` tinyint(1) NOT NULL DEFAULT '1',
  `redirect_nonwww` tinyint(1) NOT NULL DEFAULT '0',
  `redirect_url` varchar(255) DEFAULT NULL,
  `additional_config` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `server_name` (`server_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
