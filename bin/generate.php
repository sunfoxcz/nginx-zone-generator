#!/usr/bin/env php
<?php declare(strict_types=1);

namespace Sunfox\NginxZoneGenerator;

use Nette\Utils\Strings;

// -----------------------------------------------------------------------------

require __DIR__ . '/../vendor/autoload.php';

// -----------------------------------------------------------------------------

$container = Bootstrap::boot()->createContainer();

/** @var \Nette\Database\Context $db */
$db = $container->getService('database.default.context');

/** @var \Latte\Engine $latte */
$latte = $container->getByType(\Latte\Engine::class);

$parameters = $container->getParameters();

// -----------------------------------------------------------------------------

$zones = $db->table('nginx_zone');

foreach ($zones as $zone) {
    $names = Strings::match($zone->server_name, '/^(?:(?<subdomain>.+)\.)?(?<domain>(?:[^\.]+)\.(?:.+))$/');
    $domain = $names['domain'];
    $subdomain = $names['subdomain'];
    $fileName = $domain . ($subdomain && $subdomain !== 'www' ? '.' . $subdomain : '');

    echo "Generating zone \033[32m{$zone->server_name}\033[0m\n";

    if ($zone->ssl_enabled && $zone->ssl_acme && !is_link("/etc/letsencrypt/live/{$zone->server_name}/fullchain.pem")) {
        $sslDomains = [$zone->server_name];
        if ($subdomain === 'www') {
            $sslDomains[] = $domain;
        }
        if ($zone->server_alias) {
            foreach (explode(',', $zone->server_alias) as $sslDomain) {
                $sslDomains[] = $sslDomain;
            }
        }

        /*
        echo " \033[32m*\033[0m generating SSL certificate for domain(s) " . implode(',', $sslDomains) . "\n";

        $output = [];
        $ret = NULL;
        $cmd = "sudo certbot --nginx --quiet certonly -d " . implode(',', $sslDomains);
        exec($cmd, $output, $ret);
        if ($ret !== 0) {
            echo "Can't generate certificate for domain(s)  " . implode(',', $sslDomains) . ":\n{$output}";
            exit(1);
        }
        */
        echo "Certificate is missing, run 'sudo certbot --nginx --quiet certonly -d " . implode(',', $sslDomains) . "' and run this script again\n";
    }

    if ($zone->redirect_url) {
        $zone = $latte->renderToString(__DIR__ . '/../templates/redirect.latte', [
            'server_name' => $zone->server_name,
            'server_alias' => str_replace(',', ' ', $zone->server_alias),
            'domain' => $domain,
            'redirect_url' => Strings::trim($zone->redirect_url, '/'),
            'redirect_nonwww' => $zone->redirect_nonwww,
            'ssl_enabled' => $zone->ssl_enabled,
            'ssl_sst_header' => $zone->ssl_sst_header,
            'ssl_acme' => $zone->ssl_acme,
            'cert_generated' => is_link("/etc/letsencrypt/live/{$zone->server_name}/fullchain.pem"),
        ]);
        file_put_contents("{$parameters['zoneDir']}/$fileName", $zone);
        continue;
    }

    $zone = $latte->renderToString(__DIR__ . '/../templates/zone.latte', [
        'server_name' => $zone->server_name,
        'server_alias' => str_replace(',', ' ', $zone->server_alias),
        'domain' => $domain,
        'subdomain' => $subdomain,
        'document_root' => $zone->document_root,
        'php_enabled' => $zone->php_enabled,
        'php_port' => $zone->php_port,
        'ssl_enabled' => $zone->ssl_enabled,
        'ssl_sst_header' => $zone->ssl_sst_header,
        'ssl_acme' => $zone->ssl_acme,
        'cert_generated' => is_link("/etc/letsencrypt/live/{$zone->server_name}/fullchain.pem"),
        'redirect_http' => $zone->redirect_http,
        'redirect_nonwww' => $zone->redirect_nonwww,
        'additional_config' => $zone->additional_config,
    ]);
    file_put_contents("{$parameters['zoneDir']}/$fileName", $zone);
}

$output = [];
$ret = NULL;
exec('service nginx configtest', $output, $ret);
if ($ret === 0) {
    exec('service nginx reload');
} else {
    echo "Nginx config test failed, check logs for details\n";
    exit(1);
}
