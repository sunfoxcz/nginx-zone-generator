CREATE TABLE nginx_zone (
    id SERIAL PRIMARY KEY,
    server_name varchar(100) NOT NULL UNIQUE,
    server_alias text,
    document_root varchar(255) DEFAULT NULL,
    php_upstream varchar(32) DEFAULT NULL,
    ssl_enabled boolean NOT NULL DEFAULT true,
    ssl_sst_header boolean NOT NULL DEFAULT true,
    ssl_acme boolean NOT NULL DEFAULT true,
    ssl_dir varchar(100) DEFAULT NULL,
    redirect_http boolean NOT NULL DEFAULT true,
    redirect_www boolean NOT NULL DEFAULT false,
    redirect_url varchar(255) DEFAULT NULL,
    additional_config text DEFAULT ''
);
