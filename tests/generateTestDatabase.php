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
      `php_upstream` varchar(32) DEFAULT NULL,
      `ssl_enabled` tinyint(1) NOT NULL DEFAULT '1',
      `ssl_sst_header` tinyint(1) NOT NULL DEFAULT '1',
      `ssl_acme` tinyint(1) NOT NULL DEFAULT '1',
      `ssl_dir` varchar(100) DEFAULT NULL,
      `redirect_http` tinyint(1) NOT NULL DEFAULT '1',
      `redirect_nonwww` tinyint(1) NOT NULL DEFAULT '0',
      `redirect_url` varchar(255) DEFAULT NULL,
      `additional_config` text DEFAULT ''
    );
");

// -----------------------------------------------------------------------------

$db->query("INSERT INTO `nginx_zone` (`server_name`, `document_root`, `php_upstream`) VALUES ('server1.test', '/srv/server1.test', 'php56')");
$db->query("INSERT INTO `nginx_zone` (`server_name`, `document_root`, `php_upstream`) VALUES ('server2.test', '/srv/server2.test', 'php72')");
