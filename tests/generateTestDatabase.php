#!/usr/bin/env php
<?php declare(strict_types=1);

namespace Sunfox\NginxZoneGenerator;

// -----------------------------------------------------------------------------

require __DIR__ . '/../vendor/autoload.php';

// -----------------------------------------------------------------------------

$container = Bootstrap::boot()->createContainer();

/** @var \Nette\Database\Context $db */
$db = $container->getService('database.default.context');

// -----------------------------------------------------------------------------

$db->query('DROP TABLE IF EXISTS nginx_zone');
$db->query("
    CREATE TABLE `nginx_zone` (
      `id` INTEGER PRIMARY KEY AUTOINCREMENT,
      `server_name` varchar(100) NOT NULL UNIQUE,
      `server_alias` text DEFAULT '',
      `document_root` varchar(255) DEFAULT NULL,
      `php_enabled` tinyint(1) NOT NULL DEFAULT '1',
      `php_port` int(11) DEFAULT NULL,
      `ssl_enabled` tinyint(1) NOT NULL DEFAULT '1',
      `ssl_sst_header` tinyint(1) NOT NULL DEFAULT '1',
      `ssl_acme` tinyint(1) NOT NULL DEFAULT '1',
      `redirect_http` tinyint(1) NOT NULL DEFAULT '1',
      `redirect_nonwww` tinyint(1) NOT NULL DEFAULT '0',
      `redirect_url` varchar(255) DEFAULT NULL,
      `additional_config` text
    );
");

// -----------------------------------------------------------------------------

$db->query("INSERT INTO `nginx_zone` (`server_name`, `document_root`, `php_port`) VALUES ('server1.test', '/srv/server1.test', 9000)");
$db->query("INSERT INTO `nginx_zone` (`server_name`, `document_root`, `php_port`) VALUES ('server2.test', '/srv/server2.test', 9000)");
